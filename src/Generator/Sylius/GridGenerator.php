<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Generator\AbstractGenerator;
use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

final class GridGenerator extends AbstractGenerator implements GeneratorInterface
{
    /** @var SyliusResourceGeneratorInterface */
    private $syliusConfigGenerator;

    public function __construct(SyliusResourceGeneratorInterface $syliusConfigGenerator)
    {
        $this->syliusConfigGenerator = $syliusConfigGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('class')
            ->setRequired('grid_dir')
        ;
    }

    protected function generateWithResolvedOptions(array $options)
    {
        $this->ensureClassExists($options['class']);

        $className = NameResolver::resolveOnlyClassName($options['class']);
        $resourcePrefix =  $this->syliusConfigGenerator->getParameterResolver()->getPrefix($options['class']);
        $resourceName = NameResolver::camelToUnderScore($className);
        $configFileName = NameResolver::camelToUnderScore($className) . '.yml';
        $fileLocate = NameResolver::replaceDoubleSlash($options['grid_dir'] . '/' . $configFileName);

        $gridEntryFile = $options['grid_dir'] . '/' . 'main.yml';
        if (!\file_exists($gridEntryFile)) {
            throw new \InvalidArgumentException(\sprintf('%s not found.', $gridEntryFile));
        }

        $this->manager->persist(new Code(Yaml::dump([
            'sylius_grid' => [
                'grids' => [
                    \sprintf('%s_%s', $resourcePrefix, $resourceName) => [
                        'driver' => [
                            'name' => 'doctrine/orm',
                            'options' => [
                                'class' => sprintf('%%%s.model.%s.class%%', $resourcePrefix, $resourceName),
                                'repository' => [
                                    'method' => 'createAdminListQueryBuilder',
                                    'arguments' => [
                                        'criteria' => '$criteria',
                                    ]
                                ]
                            ]
                        ],
                        'sorting' => [
                            'id' => 'desc',
                        ],
                        'fields' => [
                            'id' => [
                                'type' => 'string',
                                'label' => 'ID',
                                'sortable' => '~',
                            ]
                        ],
                        'filters' => [
                            'keyword' => [
                                'type' => 'string',
                                'options' => [
                                    'strict' => true,
                                    'fields' => ['name']
                                ]
                            ]
                        ],
                        'actions' => [
                            'main' => [
                                'create' => [
                                    'type' => 'create',
                                ],
                            ],
                            'item' => [
                                'update' => [
                                    'type' => 'update',
                                ],
                                'delete' => [
                                    'type' => 'delete',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], 10), $fileLocate));

        $configFileContent = Yaml::parse(\file_get_contents($gridEntryFile));
        $configFileContent['imports'][] = [
            'resource' => './' . $configFileName
        ];

        $this->manager->persist(new Code(Yaml::dump($configFileContent, 2), $gridEntryFile));
    }
}
