<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\Utils\NameResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Finder\Finder;

abstract class AbstractGenerateCommand extends Command
{
    /** @var array */
    protected $configs = [];

    /** @var CodeManagerInterface */
    protected $manager = [];

    public function setManager(CodeManagerInterface $manager): void
    {
        $this->manager = $manager;
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }

    protected function writeCreatedFiles(CodeManagerInterface $manager, StyleInterface $io)
    {
        $createdFiles = array_filter($manager->getCodes(), function (Code $code) {
            return true !== @$code->getExtra()['dump_only'];
        });

        $createdFiles = array_map(function (Code $code) {
            return $code->getOutputPath();
        }, $createdFiles);

        if (empty($createdFiles)) {
            return;
        }

        foreach ($createdFiles as $createdFile) {
            $io->success($createdFile);
        }
    }

    protected function guessRootModelDir(string $className): string
    {
        $resourceDir = '';
        if (class_exists($className)) {
            $classDir = (new \ReflectionClass($className))->getFileName();

            $resourceDir = str_replace($this->configs['model_dir_name'], $resourceDir, $classDir);
            $resourceDir = explode('/', $resourceDir);
            $resourceDir = implode('/', array_slice($resourceDir, 0, count($resourceDir) - 1));

            $classDetail = new \ReflectionClass($className);
            if (!in_array('Sylius\\Component\\Resource\\Model\\ResourceInterface', $classDetail->getInterfaceNames())) {
                throw new \InvalidArgumentException(sprintf('Class %s must implement %s', $className, 'Sylius\\Component\\Resource\\Model\\ResourceInterface'));
            }
        }

        return $resourceDir;
    }

    protected function getNamespaceFromClass(string $className, string $postFix): string
    {
        $namespace = NameResolver::resolveNamespace($className);

        return str_replace(str_replace('/', '\\', $this->configs['model_dir_name']), str_replace('/', '\\', $postFix), $namespace);
    }

    protected function askForBundle(HelperInterface $helper, InputInterface $input, OutputInterface $output): string
    {
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

        $ans = $choices[0];
        if (1 < count($choices)) {
            $question = new ChoiceQuestion('Please select your folder', $choices);
            $ans = $helper->ask($input, $output, $question);
        }

        return rtrim(NameResolver::replaceDoubleSlash($ans), '/');
    }

    protected function getFullClassNameFromDir(string $rootDir, string $className)
    {
        // resolve full class name with namespace
        $className = str_replace($this->configs['project_source_dir'], '', $rootDir . '/' . $className);
        $className = $this->configs['namespace_prefix'] . '\\' . $className;
        $className = str_replace('/', '\\', $className);
        $className = preg_replace('/\\\+/', '\\', $className);

        return ltrim($className, '\\');
    }
}
