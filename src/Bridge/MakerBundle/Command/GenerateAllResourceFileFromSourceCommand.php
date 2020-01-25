<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Bridge\MakerBundle\Service\TranslationResolverInterface;
use Bonn\Maker\Bridge\MakerBundle\Service\TwigTemplateResolverInterface;
use Bonn\Maker\Generator\SimpleFileGenerator;
use Bonn\Maker\Model\Code;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;

class GenerateAllResourceFileFromSourceCommand extends AbstractGenerateCommand
{
    /** @var SimpleFileGenerator */
    private $generator;

    /** @var ContainerInterface */
    private $container;

    /** @var TwigTemplateResolverInterface */
    private $twigTemplateResolver;

    /** @var TranslationResolverInterface */
    private $translationResolver;

    public function __construct(
        SimpleFileGenerator $generator,
        TwigTemplateResolverInterface $twigTemplateResolver,
        TranslationResolverInterface $translationResolver,
        ContainerInterface $container
    ) {
        $this->generator = $generator;
        $this->container = $container;
        $this->twigTemplateResolver = $twigTemplateResolver;
        $this->translationResolver = $translationResolver;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('bonn:file:maker')
            ->setDescription('Generate all files from source')
            ->addArgument('file', InputArgument::REQUIRED, 'file name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectDir = $this->container->getParameter('kernel.project_dir');

        $file = realpath($input->getArgument('file'));
        if ($this->endsWith($file, '.yaml') || $this->endsWith($file, '.yml')) {
            $data = Yaml::parseFile($file);
            array_walk_recursive($data, function (&$v, $k) {
                if ('resource' !== $k) {
                    return;
                }

                if (!empty($v)) {
                    $v = Yaml::parse($v) ?: '';
                }
            });
        }

        if (!isset($data)) {
            throw new \InvalidArgumentException('No Support File.');
        }

        $this->resolveTwig($data);
        $this->resolveTranslation($data);

        $this->writeCreatedFiles($this->manager, new SymfonyStyle($input, $output));

        $this->manager->flush();
    }

    private function resolveTwig(array $data): void
    {
        $projectDir = $this->container->getParameter('kernel.project_dir');
        foreach ($this->twigTemplateResolver->resolve($data) as $file) {
            $dir = $projectDir . '/templates';
            $fileLocated = $dir . '/' . $file;
            if ('@' === $file[0]) {
                $explodeFilePath = explode('/', $file);
                $bundleName = $explodeFilePath[0] . 'Bundle';

                $explodeFilePath[0] = $this->container->get('kernel')->locateResource($bundleName . '/Resources/views');
                $fileLocated = implode('/', $explodeFilePath);
            }

            if (file_exists($fileLocated)) {
                continue;
            }

            $this->generator->generate([
                'content' => '',
                'path' => $fileLocated,
            ]);
        }
    }

    private function resolveTranslation(array $data): void
    {
        if ($this->container->has('translator')) {
            $translationDir = $this->container->getParameter('kernel.project_dir') . '/' . $this->configs['translations_dir'];
            $missingTrans = [];
            foreach ($this->translationResolver->resolve($data) as $tran) {
                // has trans
                if ($tran !== $this->container->get('translator')->trans($tran)) {
                    continue;
                }

                // no dot
                if (1 === count(explode('.', $tran))) {
                    continue;
                }

                $missingTrans[] = $tran;
            }

            $propAccessor = PropertyAccess::createPropertyAccessor();
            $messageFiles = (new Finder())->in($translationDir)->name('messages.*');

            /** @var \SplFileInfo $messageFile */
            foreach ($messageFiles as $messageFile) {
                $arr = (array) Yaml::parseFile($messageFile->getRealPath());
                foreach ($missingTrans as $tran) {
                    $tran = implode('', array_map(function ($v) {
                        return '[' . $v . ']';
                    }, explode('.', $tran)));

                    if (null === $propAccessor->getValue($arr, $tran)) {
                        $propAccessor->setValue($arr, $tran, '');
                    }
                }

                $this->manager->persist(new Code(Yaml::dump($arr, 100), $messageFile->getRealPath()));
            }
        }
    }

    protected function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length === 0) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}
