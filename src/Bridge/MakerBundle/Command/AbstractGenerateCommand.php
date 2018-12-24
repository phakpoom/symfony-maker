<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle\Command;

use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\StyleInterface;

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
}
