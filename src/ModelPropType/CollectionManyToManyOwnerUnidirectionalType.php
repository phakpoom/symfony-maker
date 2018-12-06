<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Utils\NameResolver;

/**
 * @commandValueRequired
 * @commandValueDescription Enter Interface of collection
 */
class CollectionManyToManyOwnerUnidirectionalType extends CollectionType implements PropTypeInterface, NamespaceModifyableInterface, ConstructResolveInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'collection (m-m) (owner unidirectional)';
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options)
    {
        $onlyClassName = NameResolver::resolveOnlyClassName($className);
        $field = $XMLElement->addChild('many-to-many');
        $field->addAttribute('field', $this->name);
        $field->addAttribute('target-entity', $this->fullInterfaceName);
        $field->addAttribute('fetch', 'EXTRA_LAZY');
        $cascade = $field->addChild('cascade');
        $cascade->addChild('cascade-all');
        $joinTable = $field->addChild('join-table');
        $joinTable->addAttribute('name', strtolower(explode('\\', $this->fullInterfaceName)[0]) . '_'
            . NameResolver::camelToUnderScore($onlyClassName) . '_' . NameResolver::camelToUnderScore($this->name));
        $joinColumns = $joinTable->addChild('join-columns');
        $joinColumn = $joinColumns->addChild('join-column');
        $joinColumn->addAttribute('name', NameResolver::camelToUnderScore($onlyClassName) . '_id');
        $joinColumn->addAttribute('referenced-column-name', 'id');
        $joinColumn->addAttribute('nullable', 'false');
        $joinColumn->addAttribute('unique', 'false');
        $joinColumn->addAttribute('on-delete', 'CASCADE');
        $inverseJoinColumn = $joinTable->addChild('inverse-join-columns');
        $joinColumn = $inverseJoinColumn->addChild('join-column');
        $joinColumn->addAttribute('name', NameResolver::resolveToSingular(NameResolver::camelToUnderScore($this->name)) . '_id');
        $joinColumn->addAttribute('referenced-column-name', 'id');
        $joinColumn->addAttribute('nullable', 'false');
        $joinColumn->addAttribute('unique', 'false');
        $joinColumn->addAttribute('on-delete', 'CASCADE');
    }
}
