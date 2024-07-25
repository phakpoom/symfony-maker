<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Generator\AbstractGenerator;
use Bonn\Maker\Generator\GeneratorInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

final class RoutingGenerator extends AbstractGenerator implements GeneratorInterface
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
            ->setRequired('routing_dir')
        ;
    }

    protected function generateWithResolvedOptions(array $options)
    {
        $this->ensureClassExists($options['class']);

        $className = NameResolver::resolveOnlyClassName($options['class']);
        $resourcePrefix =  $this->syliusConfigGenerator->getParameterResolver()->getPrefix($options['class']);
        $resourceName = NameResolver::camelToUnderScore($className);
        $configFileName = NameResolver::camelToUnderScore($className) . '.yml';
        $fileLocate = NameResolver::replaceDoubleSlash($options['routing_dir'] . '/' . $configFileName);

        $routingEntryFile = $options['routing_dir'] . '/' . 'main.yml';
        if (!\file_exists($routingEntryFile)) {
            throw new \InvalidArgumentException(\sprintf('%s not found.', $routingEntryFile));
        }

        $resourceNameWithPrefix = \sprintf('%s_%s', $resourcePrefix, $resourceName);
        $this->manager->persist($t = new Code(Yaml::dump([
            $resourceNameWithPrefix => [
                'defaults' => [
                    '_role_exprs' => [
                        [
                            '_role_expr' => "admin_context.ableAccessInMenu('$resourceName')"
                        ]
                    ],
                ],
                'resource' => \trim("
alias: $resourcePrefix.$resourceName
section: admin
templates: \"@BonnAdmin/Crud\"
redirect: index
grid: $resourceNameWithPrefix
permission: true
vars:
    all:
        templates:
            form: \"_Admin/$className/_form.html.twig\"
                    "),
                "type" => "sylius.resource"
            ]
        ], 10, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK), $fileLocate));

        $configFileContent = Yaml::parse(\file_get_contents($routingEntryFile));
        $configFileContent[$resourceNameWithPrefix] = [
            'resource' => './' . $configFileName,
        ];

        $this->manager->persist(new Code(Yaml::dump($configFileContent, 10), $routingEntryFile));
    }
}
