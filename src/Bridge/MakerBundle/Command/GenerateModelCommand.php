<?php

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Cache\ModelGeneratedCacheInterface;
use Bonn\Maker\Converter\PropTypeConverterInterface;
use Bonn\Maker\Generator\DoctrineGeneratorInterface;
use Bonn\Maker\Generator\ModelGeneratorInterface;
use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class GenerateModelCommand extends Command
{
    /** @var ModelGeneratorInterface  */
    private $generator;

    /** @var DoctrineGeneratorInterface  */
    private $doctrineGenerator;

    /** @var PropTypeConverterInterface  */
    private $converter;

    /** @var CodeManagerInterface  */
    private $manager;

    /** @var ModelGeneratedCacheInterface  */
    private $cache;

    /** @var array  */
    private $configs = [];

    /** @var array  */
    private $infos = [];

    /** @var array  */
    private $props = [];

    public function __construct(
        ModelGeneratorInterface $generator,
        DoctrineGeneratorInterface $doctrineGenerator,
        CodeManagerInterface $manager,
        PropTypeConverterInterface $converter,
        ModelGeneratedCacheInterface $cache,
        array $configs = []
    )
    {
        $this->converter = $converter;
        $this->doctrineGenerator = $doctrineGenerator;
        $this->manager = $manager;
        $this->generator = $generator;
        $this->cache = $cache;
        $this->configs = $configs;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:model:maker')
            ->setDescription('Generate model')
            ->addArgument('op', InputArgument::OPTIONAL, 'mode can be make|rollback', 'make')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        // Ask Class name
        $question = new Question('Please enter the class name (eg. MyClass): ');
        $question->setValidator($this->notEmptyValidate())->setMaxAttempts(5);
        $classNameInput = $helper->ask($input, $output, $question);

        if ($isRollback = $input->getArgument('op') === 'rollback') {
            $allVersions = $this->cache->listVersions(NameResolver::resolveOnlyClassName($className));

            if (empty($allVersions)) {
                $output->writeln('<info>No versions for class ' . $className . '</info>');

                return;
            }

            $question = new ChoiceQuestion(
                "Please select your version:$className",
                array_keys($allVersions)
            );

            $versionSelected = $helper->ask($input, $output, $question);
            [$modelDir, $info] = $allVersions[$versionSelected];
        } else {
            $finder = new Finder();
            // Ask Bundle
            $choices = [];
            foreach ($finder->directories()->in($this->configs['bundle_root_dir'])->depth('== 0') as $dir) {
                $choices[] = $dir->getRealPath();
            }

            $question = new ChoiceQuestion("Please select your folder", $choices);
            $modelDir = $helper->ask($input, $output, $question);

            while (true !== $this->askForProperty($input, $output)) {}
            $info = $this->converter->combineInfos($this->infos);
        }

        // resolve full class name with namespace
        $className = str_replace($this->configs['project_source_dir'], "", $modelDir . '/' . $this->configs['model_dir_name'] . '/' . $classNameInput);
        $className = $this->configs['namespace_prefix'] . '\\' . $className;
        $className = str_replace('/', '\\', str_replace('\\\\', '\\', $className));
        $this->generator->generate([
            'class' => $className,
            'props' => $this->converter->convertMultiple($info),
            'model_dir' => $modelDir . '/' . $this->configs['model_dir_name']
        ]);
        $this->doctrineGenerator->generate([
            'class' => $className,
            'props' => $this->converter->convertMultiple($info),
            'doctrine_mapping_dir' => $modelDir . '/' . $this->configs['doctrine_mapping_dir_name']
        ]);

        $createdFiles = array_map(function(Code $code) {
            return $code->getOutputPath();
        }, $this->manager->getCodes());

        $this->manager->flush();

        // Print success
        $io = new SymfonyStyle($input, $output);
        foreach ($createdFiles as $createdFile) {
            $io->success($createdFile);
        }

        if (!$isRollback) {
            $this->cache->appendVersion(NameResolver::resolveOnlyClassName($className), $info, $modelDir);
        }
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
        $question = new ChoiceQuestion(
            "Please select your property:$propertyName type (default string) ",
            array_map(function ($propTypeClass) {
                /** @var PropTypeInterface $propTypeClass */
                return $propTypeClass::getTypeName();
            }, $this->converter->getSupportedType()),
            0
        );

        $propertyType = $helper->ask($input, $output, $question);
        $question = new Question('Enter default value (enter for skip)');
        $defaultValue = $helper->ask($input, $output, $question);

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
}
