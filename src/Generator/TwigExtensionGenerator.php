<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Model\SymfonyServiceXml;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwigExtensionGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'twig_service_file_path' => null,
                'all_service_file_path' => null,
                'config_dir' => null,
            ])
            ->setRequired('name')
            ->setRequired('namespace')
            ->setRequired('twig_extension_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $fileLocate = NameResolver::replaceDoubleSlash($options['twig_extension_dir'] . '/' . $options['name'] . 'Extension.php');

        $classNamespace = new PhpNamespace($options['namespace']);

        $class = $classNamespace->addClass($options['name'] . 'Extension');

        $method = $class->addMethod('getFilters');
        $method->setReturnType('array');
        $method->setComment("\n{@inheritdoc}\n");
        $method->setBody(<<<PHP
return [
    //new \Twig_Filter('name', [\$this, 'method']),
];
PHP
        );

        $method = $class->addMethod('getFunctions');
        $method->setReturnType('array');
        $method->setComment("\n{@inheritdoc}\n");
        $method->setBody(<<<PHP
return [
    //new \Twig_Function('name', [\$this, 'method']),
];
PHP
        );

        $class->addExtend('Twig_Extension');

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $fileLocate));

        if (null === $options['twig_service_file_path'] || null === $options['all_service_file_path'] || null === $options['config_dir']) {
            return;
        }

        $allServicePath = $options['config_dir'] . $options['all_service_file_path'];
        $twigServicePath = $options['config_dir'] . $options['twig_service_file_path'];

        // import service form
        if (!file_exists($allServicePath)) {
            throw new \InvalidArgumentException($allServicePath . 'is not a file');
        }

        $serviceXml = new SymfonyServiceXml($allServicePath);
        if (!$serviceXml->hasImport(ltrim($options['twig_service_file_path'], '/'))) {
            $serviceXml->addImport(ltrim($options['twig_service_file_path'], '/'));
            $this->manager->persist(new Code($serviceXml->__toString(), $allServicePath));
        }

        // register form service
        if (file_exists($twigServicePath)) {
            $twigXml = new SymfonyServiceXml($twigServicePath);
        } else {
            $twigXml = new SymfonyServiceXml();
        }

        $resourcePrefix =  NameResolver::camelToUnderScore(explode('\\', $options['namespace'])[0]);
        $resourceName =  NameResolver::camelToUnderScore($options['name']);
        $serviceContext = $twigXml->addService(sprintf('%s.twig.%s_extension', $resourcePrefix, $resourceName),
            $classNamespace->getName() . '\\' . $class->getName()
        );

        $serviceContext->addChild('tag', null, [
            'name' => 'twig.extension'
        ]);

        $this->manager->persist(new Code($twigXml->__toString(), $twigServicePath));
    }
}
