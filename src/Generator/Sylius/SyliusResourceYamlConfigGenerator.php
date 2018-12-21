<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;

class SyliusResourceYamlConfigGenerator extends AbstractSyliusGenerator
{
    /**
     * @param array $options
     */
    protected function _generateWithResolvedOptions(array $options)
    {
        $className = NameResolver::resolveOnlyClassName($options['class']);
        $reflection = new \ReflectionClass($options['class']);
        $resourceName = $options['resource_name'] . '.' . NameResolver::camelToUnderScore($className);
        $arr = [];
        $arr['sylius_resource']['resources'][$resourceName] = [];
        $resourceArr = &$arr['sylius_resource']['resources'][$resourceName];
        $resourceArr['classes']['model'] = $options['class'];
        $resourceArr['classes']['interface'] = $options['class'] . 'Interface';
//        if ($options['with_factory']) {
//            $resourceArr['classes']['factory'] = FactoryGenerator::getFactoryNameSpace($options['class']) . '\\' . $className . 'Factory';
//        }
//        if ($options['with_form']) {
//            $resourceArr['classes']['form'] = FormGenerator::getFormTypeNameSpace($options['class']) . '\\' . $className . 'Type';
//        }
//        if ($options['with_repo']) {
//            $resourceArr['classes']['repository'] = RepositoryGenerator::getRepositoryNameSpace($options['class']) . '\\' . $className . 'Repository';
//        }

        if (in_array("Sylius\\Component\\Resource\\Model\\TranslatableInterface", $reflection->getInterfaceNames())) {
            $resourceArr['translation']['classes']['model'] = $options['class'] . 'Translation';
            $resourceArr['translation']['classes']['interface'] = $options['class'] . 'TranslationInterface';
        }

        $this->manager->persist(new Code("", $options['resource_dir'] . 'config/app/sylius_resource/' . NameResolver::camelToUnderScore($className) .'.yml'));
    }
}
