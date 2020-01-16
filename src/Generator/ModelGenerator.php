<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\ModelPropType\ConstructResolveInterface;
use Bonn\Maker\ModelPropType\IntegerType;
use Bonn\Maker\ModelPropType\ManagerAwareInterface;
use Bonn\Maker\ModelPropType\NamespaceModifyableInterface;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\ModelPropType\StringType;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ModelGenerator extends AbstractGenerator implements ModelGeneratorInterface
{
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
            ->setRequired('model_dir')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $fullClassName = $options['class'];
        $fullInterfaceClassName = $options['class'] . 'Interface';
        $props = $options['props'];
        $namespace = NameResolver::resolveNamespace($fullClassName);

        $onlyClassName = NameResolver::resolveOnlyClassName($fullClassName);
        $onlyInterfaceClassName = $onlyClassName . 'Interface';

        // generate new
        $classNamespace = new PhpNamespace($namespace);
        $interfaceNamespace = new PhpNamespace($namespace);

        // generate exists
        if (class_exists($fullClassName)) {
            $modelClass = ClassType::from($fullClassName);
            $interfaceClass = ClassType::from($fullClassName . 'Interface');

            $classNamespace->add($modelClass);
            $interfaceNamespace->add($interfaceClass);

            if (!$modelClass->hasMethod('__construct')) {
                $this->createConstructor($modelClass);
            }

            $constructor = $modelClass->getMethod('__construct');

            // get old method body
            $reflectionClass = new \ReflectionClass($fullClassName);
            $classLines = file($reflectionClass->getFileName());

            foreach ($modelClass->getMethods() as $method) {
                $reflectionMethod = $reflectionClass->getMethod($method->getName());

                $body = '';
                for ($i = $reflectionMethod->getStartLine() + 1 ;$i < $reflectionMethod->getEndLine() - 1; $i++) {
                    $body .= $classLines[$i];
                }

                if (empty(trim($body))) {
                    $modelClass->removeMethod($method->getName());
                }

                $method->setBody($body);
            }

            // implement to extends
            $interfaceClass->setExtends($interfaceClass->getImplements());
            $interfaceClass->setImplements([]);

            // visibility public
            foreach ($interfaceClass->getMethods() as $method) {
               $method->setVisibility(ClassType::VISIBILITY_PUBLIC);
            }

            // remove method from traits
            /** @var ClassType $trait */
            $traitMethodNames = [];
            $traitPropNames = [];
            foreach ($modelClass->getTraits() as $trait) {
                foreach ($trait->getMethods() as $method) {
                    $traitMethodNames[] = $method->getName();
                }

                foreach ($trait->getProperties() as $property) {
                    $traitPropNames[] = $property->getName();
                }
            }

            foreach ($modelClass->getMethods() as $method) {
                if (!in_array($method->getName(), $traitMethodNames)) {
                    continue;
                }

                $modelClass->removeMethod($method->getName());
            }
            foreach ($modelClass->getProperties() as $property) {
                if (!in_array($property->getName(), $traitPropNames)) {
                    continue;
                }

                $modelClass->removeProperty($property->getName());
            }

        } else {
            $modelClass = $classNamespace->addClass($onlyClassName);
            $modelClass->addImplement($fullInterfaceClassName);
            $interfaceClass = $interfaceNamespace->addInterface($onlyInterfaceClassName);

            $constructor = $this->createConstructor($modelClass);

            // Create Id
            $idPropType = new IntegerType('id');
            $idPropType->addProperty($modelClass);
            $idPropType->addGetter($modelClass);
            $interfaceNamespace->addUse('Sylius\\Component\\Resource\\Model\\ResourceInterface');
            $interfaceClass->addExtend('Sylius\\Component\\Resource\\Model\\ResourceInterface');
        }

        // Extension
        if ($options['with_timestamp_able']) {
            $classNamespace->addUse('Sylius\\Component\\Resource\\Model\\TimestampableTrait');
            $modelClass->addTrait('Sylius\\Component\\Resource\\Model\\TimestampableTrait');
            $interfaceNamespace->addUse('Sylius\\Component\\Resource\\Model\\TimestampableInterface');
            $interfaceClass->addExtend('Sylius\\Component\\Resource\\Model\\TimestampableInterface');
        }
        if ($options['with_code']) {
            $codePropType = new StringType('code');
            $codePropType->addProperty($modelClass);
            $codePropType->addGetter($modelClass);
            $codePropType->addSetter($modelClass);
            $interfaceNamespace->addUse('Sylius\\Component\\Resource\\Model\\CodeAwareInterface');
            $interfaceClass->addExtend('Sylius\\Component\\Resource\\Model\\CodeAwareInterface');
        }

        if (!empty($props)) {
            /** @var PropTypeInterface $prop */
            foreach ($props as $prop) {
                // use for advance props eg. translation
                if ($prop instanceof ManagerAwareInterface) {
                    $prop->setManager($this->manager);
                    $prop->setOption($options);
                }

                $prop->addProperty($modelClass);
                $prop->addGetter($modelClass);
                $prop->addSetter($modelClass);

                $prop->addGetter($interfaceClass);
                $prop->addSetter($interfaceClass);

                if ($prop instanceof ConstructResolveInterface) {
                    $prop->resolveConstruct($constructor);
                }

                if ($prop instanceof NamespaceModifyableInterface) {
                    $prop->modify($classNamespace, $interfaceNamespace);
                }
            }

            foreach ($modelClass->getMethods() as $method) {
                $method->setComment("\n{@inheritdoc}\n");
            }
            foreach ($interfaceClass->getMethods() as $method) {
                $method->setBody(null);
            }
        }

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $options['model_dir'] . "/$onlyClassName.php"));
        $this->manager->persist(new Code(PhpDoctypeCode::render($interfaceNamespace->__toString()), $options['model_dir'] . "/$onlyInterfaceClassName.php"));
    }

    private function createConstructor(ClassType $modelClass)
    {
        return $modelClass
            ->addMethod('__construct')
            ->setVisibility('public');
    }
}
