<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtensionGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'entry_service_file_path' => null,
                'all_service_file_path' => null,
                'config_dir' => null,
            ])
            ->setRequired('name')
            ->setRequired('namespace')
            ->setRequired('class_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $fileLocate = NameResolver::replaceDoubleSlash($options['class_dir'] . '/' . $options['name'] . 'Extension.php');
        $resourcePrefix = NameResolver::resolveResourcePrefix($options['namespace']);

        $classNamespace = new PhpNamespace($options['namespace']);
        $classNamespace->addUse(AbstractExtension::class);
        $classNamespace->addUse(TwigFunction::class);
        $classNamespace->addUse(TwigFilter::class);

        $class = $classNamespace->addClass($options['name'] . 'Extension');

        $method = $class->addMethod('getFilters');
        $method->setReturnType('array');
        $method->setComment("\n{@inheritdoc}\n");
        $method->setBody(<<<PHP
return [
    //new TwigFilter('name', [\$this, 'method']),
];
PHP
        );

        $method = $class->addMethod('getFunctions');
        $method->setReturnType('array');
        $method->setComment("\n{@inheritdoc}\n");
        $method->setBody(<<<PHP
return [
    //new TwigFunction('name', [\$this, 'method']),
];
PHP
        );

        $class->addExtend(AbstractExtension::class);

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $fileLocate));

        if (null === $options['entry_service_file_path'] || null === $options['all_service_file_path'] || null === $options['config_dir']) {
            return;
        }

        // import service form
        $this->addImportEntryToServiceFile($options['config_dir'], $options['entry_service_file_path'], $options['all_service_file_path']);

        $xml = $this->getConfigXmlFile($options['config_dir'], $options['entry_service_file_path']);

        $resourceName =  NameResolver::camelToUnderScore($options['name']);
        $serviceContext = $xml->addService(
            sprintf('%s.twig.%s_extension', $resourcePrefix, $resourceName),
            $classNamespace->getName() . '\\' . $class->getName(),
            ['autowire' => 'true']
        );

        $serviceContext->addChild('tag', null, [
            'name' => 'twig.extension'
        ]);

        $this->manager->persist(new Code($xml->__toString(), $options['config_dir'] . $options['entry_service_file_path']));
    }
}
