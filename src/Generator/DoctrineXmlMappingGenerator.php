<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\Utils\DOMIndent;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DoctrineXmlMappingGenerator implements DoctrineGeneratorInterface
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
                'with_timestamp_able' => false,
                'with_code' => false,
                'with_toggle' => false,
            ])
            ->setRequired('class')
            ->setRequired('props')
            ->setNormalizer('props', function (Options $options, $value) {
                foreach ((array) $value as $item) {
                    if (!$item instanceof PropTypeInterface) {
                        throw new InvalidOptionsException('all of props must be PropTypeInterface');
                    }
                }

                return $value;
            })
            ->setRequired('doctrine_mapping_dir')
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

        $fullClassName = $options['class'];
        $props = $options['props'];
        $onlyClassName = NameResolver::resolveOnlyClassName($fullClassName);

        $root = self::createDoctrineMappingXml();
        $mappedSuper = $root->addChild('mapped-superclass');
        $mappedSuper->addAttribute('name', $fullClassName);
        $mappedSuper->addAttribute('table', strtolower(explode('\\', $fullClassName)[0]) . '_' . NameResolver::camelToUnderScore($onlyClassName));
        $id = $mappedSuper->addChild('id');
        $id->addAttribute('name', 'id');
        $id->addAttribute('type', 'integer');
        $id->addChild('generator')->addAttribute('strategy', 'AUTO');
        // Extension
        if ($options['with_timestamp_able']) {
            $root->addAttribute('xmlns:xmlns:gedmo', 'http://gediminasm.org/schemas/orm/doctrine-extensions-mapping');
            $field = $mappedSuper->addChild('field');
            $field->addAttribute('name', 'createdAt');
            $field->addAttribute('type', 'datetime');
            $field->addChild('xmlns:gedmo:timestampable')->addAttribute('on', 'create');
            $field = $mappedSuper->addChild('field');
            $field->addAttribute('name', 'updatedAt');
            $field->addAttribute('type', 'datetime');
            $field->addAttribute('nullable', 'true');
            $field->addChild('xmlns:gedmo:timestampable')->addAttribute('on', 'update');
        }
        if ($options['with_code']) {
            $field = $mappedSuper->addChild('field');
            $field->addAttribute('name', 'code');
            $field->addAttribute('type', 'string');
            $field->addAttribute('length', '20');
            $field->addAttribute('unique', 'true');
            $field->addAttribute('nullable', 'false');
        }
        if ($options['with_toggle']) {
            $field = $mappedSuper->addChild('field');
            $field->addAttribute('name', 'enabled');
            $field->addAttribute('type', 'boolean');
        }

        /** @var PropTypeInterface $prop */
        foreach ($props as $prop) {
            $prop->addDoctrineMapping($fullClassName, $mappedSuper, $this->codeManager, $options);
        }

        $dom = new DOMIndent($root->asXML());

        $this->codeManager->persist(new Code($dom->saveXML(), $options['doctrine_mapping_dir'] . '/' . $onlyClassName . '.orm.xml'));
    }

    /**
     * @deprecated
     */
    public static function createDomWithRoot(\SimpleXMLElement $root)
    {
        return new DOMIndent($root->asXML());
    }

    public static function createDoctrineMappingXml(): \SimpleXMLElement
    {
        $doctrineMapping = new \SimpleXMLElement('<doctrine-mapping />');
        $doctrineMapping->addAttribute('xmlns', 'http://doctrine-project.org/schemas/orm/doctrine-mapping');
        $doctrineMapping->addAttribute('xmlns:xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $doctrineMapping->addAttribute('xmlns:xsi:schemaLocation', 'http://doctrine-project.org/schemas/orm/doctrine-mapping http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd');

        return $doctrineMapping;
    }
}
