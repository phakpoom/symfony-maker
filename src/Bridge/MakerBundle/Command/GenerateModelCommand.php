<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Cache\ModelGeneratedCacheInterface;
use Bonn\Maker\Converter\PropTypeConverterInterface;
use Bonn\Maker\Generator\DoctrineGeneratorInterface;
use Bonn\Maker\Generator\ModelGeneratorInterface;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\Utils\NameResolver;
use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class GenerateModelCommand extends AbstractGenerateCommand
{
    public const SUPPORT_OPS = [
        'make',
        'dump',
        'rollback',
    ];

    /** @var ModelGeneratorInterface */
    private $generator;

    /** @var DoctrineGeneratorInterface */
    private $doctrineGenerator;

    /** @var PropTypeConverterInterface */
    private $converter;

    /** @var ModelGeneratedCacheInterface */
    private $cache;

    /** @var array */
    private $infos = [];

    /** @var array */
    private $props = [];

    public function __construct(
        ModelGeneratorInterface $generator,
        DoctrineGeneratorInterface $doctrineGenerator,
        PropTypeConverterInterface $converter,
        ModelGeneratedCacheInterface $cache
    ) {
        $this->converter = $converter;
        $this->doctrineGenerator = $doctrineGenerator;
        $this->generator = $generator;
        $this->cache = $cache;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:model:maker')
            ->setDescription('Generate model')
            ->addArgument('op', InputArgument::OPTIONAL, 'mode can be ' . implode('|', self::SUPPORT_OPS), 'make')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $op = $input->getArgument('op');

        if (!in_array($input->getArgument('op'), self::SUPPORT_OPS)) {
            throw new InvalidArgumentException(
                sprintf('Operation must be one of %s, got %s', implode('|', self::SUPPORT_OPS), $op));
        }

        $helper = $this->getHelper('question');

        if ('dump' !== $op) {
            // Ask Class name
            $question = new Question('Please enter the class name (eg. MyClass): ');
            $question->setValidator($this->notEmptyValidate())->setMaxAttempts(5);
            $classNameInput = $helper->ask($input, $output, $question);
        } else {
            $classNameInput = 'Dummy';
        }

        if ([] === $returnFromOp = $this->handleOp($op, $classNameInput, $input, $output, $helper)) {
            return;
        }

        [$modelDir, $info] = $returnFromOp;

        // resolve full class name with namespace
        $className = str_replace($this->configs['project_source_dir'], '', $modelDir . '/' . $this->configs['model_dir_name'] . '/' . $classNameInput);
        $className = $this->configs['namespace_prefix'] . '\\' . $className;
        $className = str_replace('/', '\\', $className);
        $className = preg_replace('/\\\+/', '\\', $className);
        $className = ltrim($className, '\\');

        $this->generator->generate([
            'class' => $className,
            'props' => $this->converter->convertMultiple($info),
            'model_dir' => $modelDir . '/' . $this->configs['model_dir_name'],
        ]);
        $this->doctrineGenerator->generate([
            'class' => $className,
            'props' => $this->converter->convertMultiple($info),
            'doctrine_mapping_dir' => $modelDir . '/' . $this->configs['doctrine_mapping_dir_name'],
        ]);

        if ('dump' === $op) {
            foreach ($this->manager->getCodes() as $code) {
                $code->setExtra('dump_only', true);
            }
        }

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();

        if ('rollback' !== $op) {
            $this->cache->appendVersion(NameResolver::resolveOnlyClassName($className), $info, $modelDir);
        }

        // reset
        $this->infos = [];
    }

    protected function handleOp(string $op, string $classNameInput, InputInterface $input, OutputInterface $output, HelperInterface $helper): array
    {
        if ($op === 'rollback') {
            $allVersions = $this->cache->listVersions(NameResolver::resolveOnlyClassName($classNameInput));

            if (empty($allVersions)) {
                $output->writeln('<info>No versions for class ' . $classNameInput . '</info>');

                return [];
            }

            $question = new ChoiceQuestion(
                "Please select your version:$classNameInput",
                array_keys($allVersions)
            );

            $versionSelected = $helper->ask($input, $output, $question);

            return $allVersions[$versionSelected];
        }
        if ('make' === $op) {
            // Ask Bundle
            $choices = [];

            $finder = new Finder();
            $dirs = [];

            $iterator = $finder->directories()->in($this->configs['bundle_root_dir'])->depth('== 0')->getIterator();
            foreach ($iterator as $dir) {
                $dirs[] = $dir->getRealPath();
            }
            asort($dirs);
            foreach ($dirs as $dir) {
                $choices[] = $dir;
            }

            if (1 < count($choices)) {
                $question = new ChoiceQuestion('Please select your folder', $choices);
                $modelDir = $helper->ask($input, $output, $question);
            } else {
                $modelDir = $choices[0];
            }

            while (true !== $this->askForProperty($input, $output)) {
            }
            $info = $this->converter->combineInfos($this->infos);

            return [$modelDir, $info];
        }
        if ('dump' === $op) {
            while (true !== $this->askForProperty($input, $output)) {
            }
            $info = $this->converter->combineInfos($this->infos);

            return ['', $info];
        }

        return [];
    }

    private function askForProperty(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the property name: ');
        $question->setValidator($this->propertyNameValidate())->setMaxAttempts(5);
        $propertyName = $helper->ask($input, $output, $question);

        if (empty($propertyName)) {
            return true;
        }

        $this->props[] = $propertyName;
        $supportedTypes = $this->converter->getSupportedType();
        $question = new ChoiceQuestion(
            "Please select your property:$propertyName type (default string) ",
            $supportedTypeChoices = array_map(function ($propTypeClass) {
                /** @var PropTypeInterface $propTypeClass */
                return $propTypeClass::getTypeName();
            }, $supportedTypes),
            0
        );
        $propertyType = $helper->ask($input, $output, $question);

        $oReflectionClass = new \ReflectionClass($supportedTypes[array_search($propertyType, $supportedTypeChoices)]);
        $typeDocblock = $oReflectionClass->getDocComment();

        $askForValue = true;
        $valueRequired = false;
        $valueQuestion = 'Enter value (enter for skip): ';
        $defaultValue = '';
        if (!empty($typeDocblock)) {
            $docblock = DocBlockFactory::createInstance()->create($typeDocblock);
            if (!empty($docblock->getTagsByName('commandValueSkip'))) {
                $askForValue = false;
            } else {
                if (!empty($docblock->getTagsByName('commandValueRequired'))) {
                    $valueRequired = true;
                }

                if (!empty($docblock->getTagsByName('commandValueDescription')[0])) {
                    $valueQuestion = $docblock->getTagsByName('commandValueDescription')[0] . ': ';
                }
            }
        }

        if ($askForValue) {
            $question = new Question($valueQuestion);
            if (true === $valueRequired) {
                $question->setValidator($this->notEmptyValidate())->setMaxAttempts(5);
            }

            $defaultValue = $helper->ask($input, $output, $question);
        }

        $this->infos[] = $this->converter->buildInfoString($propertyName, $propertyType, $defaultValue);

        return false;
    }

    private function notEmptyValidate()
    {
        return function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException(
                    'Value cannot be empty'
                );
            }

            return $answer;
        };
    }

    private function propertyNameValidate()
    {
        return function ($answer) {
            if (empty($answer)) {
                return $answer;
            }
            if (in_array($answer, $this->props)) {
                throw new \RuntimeException(
                    "$answer is already added"
                );
            }

            return $answer;
        };
    }

    /**
     * Easy testing
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * Easy testing
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }
}
