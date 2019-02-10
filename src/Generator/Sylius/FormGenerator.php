<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Model\SymfonyServiceXml;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FormGenerator extends AbstractSyliusGenerator implements GeneratorInterface
{
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
    protected function generateWithResolvedOptions(array $options)
    {
        $this->ensureClassExists($options['class']);

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

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $fileLocate));

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
            $this->manager->persist(new Code($serviceXml->__toString(), $allServicePath));
        }

        // register form service
        if (file_exists($formServicePath)) {
            $formXml = new SymfonyServiceXml($formServicePath);
        } else {
            $formXml = new SymfonyServiceXml();
        }

        $resourcePrefix =  NameResolver::camelToUnderScore(explode('\\', $options['class'])[0]);
        $resourceName =  NameResolver::camelToUnderScore($className);
        $serviceContext = $formXml->addService(sprintf('%s.form_type.%s_type', $resourcePrefix, $resourceName),
            $classNamespace->getName() . '\\' . $formClass->getName());

        $serviceContext->addChild('argument', sprintf('%%%s.model.%s.class%%', $resourcePrefix, $resourceName));

        $serviceContext
            ->addChild('argument', true, [
                'type' => 'collection'
            ])
            ->addChild('argument', $resourcePrefix);

        $serviceContext->addChild('tag', null, [
            'name' => 'form.type'
        ]);

        $this->manager->persist(new Code($formXml->__toString(), $formServicePath));
    }
}
