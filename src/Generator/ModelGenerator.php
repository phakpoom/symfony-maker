<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\ModelPropType\ConstructResolveInterface;
use Bonn\Maker\ModelPropType\IntegerType;
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

final class ModelGenerator implements ModelGeneratorInterface
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
                'with_timestamp_able' => true,
                'with_code' => true,
                'with_toggle' => true,
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
    public function generate($options = [])
    {
        $optionResolver = new OptionsResolver();
        $this->configurationOptions($optionResolver);
        $options = $optionResolver->resolve($options);

        $fullClassName = $options['class'];
        $fullInterfaceClassName = $options['class'] . 'Interface';
        $props = $options['props'];
        $namespace = NameResolver::resolveNamespace($fullClassName);

        $classNamespace = new PhpNamespace($namespace);
        $interfaceNamespace = new PhpNamespace($namespace);

        $onlyClassName = NameResolver::resolveOnlyClassName($fullClassName);
        $onlyInterfaceClassName = $onlyClassName . 'Interface';

        $modelClass = $classNamespace->addClass($onlyClassName);
        $interfaceClass = $interfaceNamespace->addInterface($onlyInterfaceClassName);

        $constructor = $this->createConstructor($modelClass);

        // Create Id
        $idPropType = new IntegerType('id');
        $idPropType->addProperty($modelClass);
        $idPropType->addGetter($modelClass);
        $interfaceNamespace->addUse('Sylius\\Component\\Resource\\Model\\ResourceInterface');
        $interfaceClass->addExtend('Sylius\\Component\\Resource\\Model\\ResourceInterface');

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
        if ($options['with_toggle']) {
            $classNamespace->addUse('Sylius\\Component\\Resource\\Model\\ToggleableTrait');
            $modelClass->addTrait('Sylius\\Component\\Resource\\Model\\ToggleableTrait');
            $interfaceNamespace->addUse('Sylius\\Component\\Resource\\Model\\ToggleableInterface');
            $interfaceClass->addExtend('Sylius\\Component\\Resource\\Model\\ToggleableInterface');
        }

        if (!empty($props)) {
            /** @var PropTypeInterface $prop */
            foreach ($props as $prop) {
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

        $modelClass->addImplement($fullInterfaceClassName);

        $this->codeManager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $options['model_dir'] . "/$onlyClassName.php"));
        $this->codeManager->persist(new Code(PhpDoctypeCode::render($interfaceNamespace->__toString()), $options['model_dir'] . "/$onlyInterfaceClassName.php"));
    }

    private function createConstructor(ClassType $modelClass)
    {
        return $modelClass
            ->addMethod('__construct')
            ->setVisibility('public');
    }

    private function createIdProp(ClassType $modelClass, ClassType $interfaceClass)
    {
        return $modelClass
            ->addMethod('__construct')
            ->setVisibility('public');
    }
}
