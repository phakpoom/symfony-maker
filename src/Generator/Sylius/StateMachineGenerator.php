<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Model\SymfonyServiceXml;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class StateMachineGenerator extends AbstractSyliusGenerator
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        parent::configurationOptions($resolver);

        $resolver
            ->setRequired('all_service_file_path')
            ->setRequired('config_dir')
            ->setRequired('class')
            ->setRequired('namespace')
            ->setRequired('state_callback_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $this->ensureClassExists($options['class']);
        $className = NameResolver::resolveOnlyClassName($options['class']);
        $callbackFileLocate = NameResolver::replaceDoubleSlash($options['state_callback_dir'] . '/' . $className . 'Callback.php');

        $classNamespace = new PhpNamespace($options['namespace']);

        $callbackClass = $classNamespace->addClass($className . 'Callback');
        $interfaceModel = $options['class'] . 'Interface';
        $classNamespace->addUse($interfaceModel);

        // add method
        $method = $callbackClass->addMethod('onCreate');
        $method->setVisibility('public');
        $method->addParameter(\lcfirst($className))->setType($options['class'] . 'Interface');
        $method->setReturnType('void');
        $method->setBody(<<<STRING
   // do
STRING
);

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $callbackFileLocate));

        $allServicePath = $options['config_dir'] . $options['all_service_file_path'];
        // add entry callback
        $serviceXml = new SymfonyServiceXml($allServicePath);
        $namespaceName = NameResolver::resolveOnlyClassName($options['namespace']);
        $serviceXml->addPrototype($options['namespace'] . '\\', "../../{$namespaceName}/*");

        $this->manager->persist(new Code($serviceXml->__toString(), $allServicePath));

        // add state_machine
        $name = NameResolver::camelToUnderScore($className);
        $configs = [
            'bonn_ui' => [
                'state_machine' =>  [
                    'graphs' => [
                        $name => [
                            'states' => [
                                'new' => [
                                    'color' => 'secondary',
                                    'translation' => [
                                        'key' => 'app.state.new'
                                    ]
                                ]
                            ],
                            'transitions' => [
                                'create' => [
                                    'color' => 'secondary',
                                    'translation' => [
                                        'key' => 'app.transition.create'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'winzou_state_machine' => [
                $name => [
                    'class' => $options['class'],
                    'property_path' => 'state',
                    'graph' => $name,
                    'states' => ['new'],
                    'transitions' => [
                        'create' => [
                            'from' => ['new'],
                            'to' => 'created'
                        ]
                    ],
                    'callbacks' => [
                        'after' => [
                            'create' => [
                                'on' => ['create'],
                                'do' => [
                                    '@' . \str_replace('\\', '\\', $options['namespace'] . '\\' . $callbackClass->getName()), 'onCreate'
                                ],
                                'args' => ['object']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->manager->persist(new Code(Yaml::dump($configs, 10),
            sprintf('%s/app/state_machine/%s.yml', rtrim($options['config_dir'], '/'), $name)));
    }
}
