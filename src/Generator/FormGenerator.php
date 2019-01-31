<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use FluidXml\FluidContext;
use FluidXml\FluidXml;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
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
                'resource_name' => null,
                'form_service_file_path' => null,
                'all_service_file_path' => null,
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

        if (null === $options['form_service_file_path'] || null === $options['all_service_file_path']) {
            return;
        }



        // import service form
        if (!file_exists($options['all_service_file_path'])) {
            throw new \InvalidArgumentException($options['all_service_file_path'] . ' not a directory');
        }

        $serviceXml = FluidXml::load($options['all_service_file_path']);
        $serviceXml->namespace('container', XmlFileLoader::NS);
        $alreadyImported = false;
        $serviceXml->query('//container:imports/container:import')->each(function($index, \DOMElement $dom) {
            var_dump($dom->getAttribute('resource'));
        });

        echo ($serviceXml);exit;
    }
}
