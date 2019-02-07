<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Model\SymfonyServiceXml;
use Bonn\Maker\Utils\DOMIndent;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FormGenerator implements GeneratorInterface
{
    /** @var CodeManagerInterface */
    private $codeManager;

    public function __construct(CodeManagerInterface $codeManager)
    {
        $this->codeManager = $codeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'form_service_file_path' => null,
                'all_service_file_path' => null,
                'config_dir' => null,
            ])
            ->setRequired('class')
            ->setRequired('namespace')
            ->setRequired('form_dir')
        ;
    }

    /**
     * @param array $options
     */
    public function generate($options = [])
    {
        $optionResolver = new OptionsResolver();
        $this->configurationOptions($optionResolver);
        $options = $optionResolver->resolve($options);

        $className = NameResolver::resolveOnlyClassName($options['class']);

        $fileLocate = NameResolver::replaceDoubleSlash($options['form_dir'] . '/' . $className . 'Type.php');

        $classNamespace = new PhpNamespace($options['namespace']);

        $formClass = $classNamespace->addClass($className . 'Type');


        $classNamespace->addUse('Sylius\\Bundle\\ResourceBundle\\Form\\Type\\AbstractResourceType');
        $classNamespace->addUse('Symfony\\Component\\Form\\FormBuilderInterface');
        $formClass->addExtend('Sylius\\Bundle\\ResourceBundle\\Form\\Type\\AbstractResourceType');

        $buildFormMethod = $formClass->addMethod('buildForm')->setComment("\n{@inheritdoc}\n");
        $buildFormMethod->setVisibility('public')->setBody('// $builder');
        $buildFormMethod->setReturnType('void');
        $buildFormMethod->addParameter('builder')->setTypeHint('Symfony\\Component\\Form\\FormBuilderInterface');
        $buildFormMethod->addParameter('options')->setTypeHint('array');

        $this->codeManager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $fileLocate));

        if (null === $options['form_service_file_path'] || null === $options['all_service_file_path'] || null === $options['config_dir']) {
            return;
        }

        $allServicePath = $options['config_dir'] . $options['all_service_file_path'];
        $formServicePath = $options['config_dir'] . $options['form_service_file_path'];

        // import service form
        if (!file_exists($allServicePath)) {
            throw new \InvalidArgumentException($allServicePath . 'is not a file');
        }

        $serviceXml = new SymfonyServiceXml($allServicePath);
        if (!$serviceXml->hasImport(ltrim($options['form_service_file_path'], '/'))) {
            $serviceXml->addImport(ltrim($options['form_service_file_path'], '/'));
            $this->codeManager->persist(new Code($serviceXml->__toString(), $allServicePath));
        }

        // register form service
        if (file_exists($formServicePath)) {
            $formXml = new SymfonyServiceXml($formServicePath);
        } else {
            $formXml = new SymfonyServiceXml();
        }

        $resourcePrefix =  NameResolver::camelToUnderScore(explode('\\', $options['class'])[0]);
        $resourceName =  NameResolver::camelToUnderScore($className);
        $serviceContext = $formXml->addService(sprintf('%s.form_type.%s_type', $resourcePrefix, $resourceName), $options['class']);

        $serviceContext->addChild('argument', sprintf('%%%s.model.%s.class%%', $resourcePrefix, $resourceName));

        $serviceContext
            ->addChild('argument', true, [
                'type' => 'collection'
            ])
            ->addChild('argument', $resourcePrefix);

        $serviceContext->addChild('tag', null, [
            'name' => 'form.type'
        ]);

        $this->codeManager->persist(new Code($formXml->__toString(), $formServicePath));
    }
}
