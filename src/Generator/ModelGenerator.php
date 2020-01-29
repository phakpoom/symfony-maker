<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

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
use Nette\PhpGenerator\Helpers;
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

        // class exists
        if (class_exists($fullClassName)) {
            $this->manager->persist(new Code($this->append($fullClassName, $modelClass, $classNamespace), $options['model_dir'] . "/$onlyClassName.php"));
            $this->manager->persist(new Code($this->append($fullInterfaceClassName, $interfaceClass, $interfaceNamespace), $options['model_dir'] . "/$onlyInterfaceClassName.php"));

            return;
        }

        $this->manager->persist(new Code(PhpDoctypeCode::render($classNamespace->__toString()), $options['model_dir'] . "/$onlyClassName.php"));
        $this->manager->persist(new Code(PhpDoctypeCode::render($interfaceNamespace->__toString()), $options['model_dir'] . "/$onlyInterfaceClassName.php"));
    }

    protected function append(string $fullClassName, ClassType $prototype, PhpNamespace $classNamespace): string
    {
        $isInterface = $prototype->getType() === ClassType::TYPE_INTERFACE;
        $constructBody = '';

        if (!$isInterface) {
            $prototype->removeProperty('id');
            $prototype->removeMethod('getId');
            $constructBody = $prototype->getMethod('__construct')->getBody();
            $prototype->getMethod('__construct')->setBody(null);
        }

        $reflectionClass = new \ReflectionClass($fullClassName);

        $prototypeString = PhpDoctypeCode::render($prototype->__toString());

        $classLines = file($reflectionClass->getFileName());
        $prototypeLines = explode("\n", $prototypeString);

        $foundedLastMethod = null;
        $l = 1;
        while (!$foundedLastMethod) {
            $currentLine = $reflectionClass->getEndLine() - $l;
            if (!isset($classLines[$currentLine])) {
                throw new \LogicException("Class has no method.");
            }

            if ('}' === trim($classLines[$currentLine])) {
                $foundedLastMethod = $reflectionClass->getEndLine() - $l;
            }

            $l++;
        }

        $classLines[$foundedLastMethod - 1] .= "\n{{END_METHOD}}\n";

        if (!$isInterface) {
            $construct = $reflectionClass->getMethod('__construct');
            $line = $construct->getStartLine() - 1;
            if ($construct->getDocComment()) {
                $line = $line - count(explode("\n", $construct->getDocComment())) - 2;
            }
            $classLines[$line] .= "\n{{END_PROP}}\n";

            $constructBody = array_map(function ($v) {
                return Helpers::tabsToSpaces("\t\t" . $v);
            }, explode("\n", trim($constructBody)));
            // add __construct
            $classLines[$construct->getEndLine() - 2] .= implode("\n", $constructBody) . "\n";
        }

        // add use
        $oldUses = [];
        $start = 0;
        $lastUseFoundLine = 0;
        while ($start <= count($classLines)) {
            $found = $this->getLine($classLines, 'use', $start);

            if (-1 === $found) {
                break;
            }

            // resolve only class name
            $use = str_replace('use ', '', $classLines[$found]);
            $use = str_replace(';', '', $use);
            $oldUses[] = trim($use);

            $start = $found + 1;

            $lastUseFoundLine = $found;
        }

        // ignore itself class
        $uses = array_filter($classNamespace->getUses(), function ($class) use ($reflectionClass, $oldUses) {
            return $class !== $reflectionClass->getName() && !in_array($class, $oldUses);
        });

        if (!empty($uses)) {
            // render use
            $classLines[$lastUseFoundLine] .= implode("\n", array_map(function ($v) {
                return 'use ' . $v . ';';
            }, $uses));
            $classLines[$lastUseFoundLine] .= "\n";
        }

        $classString = implode('', $classLines);

        if (!$isInterface) {
            $startLineProp = $this->getLine($prototypeLines, 'class') + 2;
            $endLineProp = $this->getLine($prototypeLines, '    public function __construct') - 4;

            // add props
            $propString = implode("\n", array_slice($prototypeLines, $startLineProp, $endLineProp - $startLineProp));

            $classString = str_replace('{{END_PROP}}', $propString, $classString);

            $startLineMethod = $this->getLine($prototypeLines, '    public function __construct') + 2;
            $endLineMethod = count($prototypeLines) - 2;
        } else {
            $startLineMethod = $this->getLine($prototypeLines, '{') + 1;
            $endLineMethod = count($prototypeLines) - 2;
        }

        // add method
        $methodString = implode("\n", array_slice($prototypeLines, $startLineMethod, $endLineMethod - $startLineMethod));
        $classString = str_replace('{{END_METHOD}}', $methodString, $classString);

        return $classString;
    }

    protected function getLine(array $lines, string $str, int $start = 0 ): int
    {
        foreach ($lines as $lineNumber => $line) {
            if ($lineNumber < $start) {
                continue;
            }

            if (strpos($line, $str) === 0) {
                return $lineNumber;
            }
        }

        return -1;
    }
    protected function getBodyMethod(string $fullClassName, string $method): string
    {
        $reflectionClass = new \ReflectionClass($fullClassName);
        $classLines = file($reflectionClass->getFileName());

        $reflectionMethod = $reflectionClass->getMethod($method);

        $body = '';
        for ($i = $reflectionMethod->getStartLine() + 1; $i < $reflectionMethod->getEndLine() - 1; $i++) {
            $body .= $classLines[$i];
        }

        return $body;
    }

    private function createConstructor(ClassType $modelClass)
    {
        return $modelClass
            ->addMethod('__construct')
            ->setVisibility('public');
    }
}
