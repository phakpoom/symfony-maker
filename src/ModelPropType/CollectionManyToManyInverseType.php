<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Utils\NameResolver;

/**
 * @commandValueRequired
 * @commandValueDescription Enter Interface of collection
 */
class CollectionManyToManyInverseType extends CollectionType implements PropTypeInterface, NamespaceModifyableInterface, ConstructResolveInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'collection (m-m) (inverse-side)';
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
        $field->addAttribute('mapped-by', \lcfirst(NameResolver::resolveToPlural($onlyClassName)));
        $field->addAttribute('fetch', 'EXTRA_LAZY');
    }
}
