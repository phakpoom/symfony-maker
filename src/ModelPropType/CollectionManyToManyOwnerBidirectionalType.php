<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Utils\NameResolver;

/**
 * @commandValueRequired
 * @commandValueDescription Enter Interface of collection
 */
class CollectionManyToManyOwnerBidirectionalType extends CollectionManyToManyOwnerUnidirectionalType implements PropTypeInterface, NamespaceModifyableInterface, ConstructResolveInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'collection (m-m) (owner bidirectional)';
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options)
    {
        $onlyClassName = NameResolver::resolveOnlyClassName($className);
        parent::addDoctrineMapping($className, $XMLElement, $codeManager, $options);

        // cannot use end() for get last
        $manyToManyLast = null;
        foreach ($XMLElement->{'many-to-many'} as $manyToMany) {
            $manyToManyLast = $manyToMany;
        }

        $manyToManyLast->addAttribute('inversed-by', \lcfirst(NameResolver::resolveToPlural($onlyClassName)));
    }
}
