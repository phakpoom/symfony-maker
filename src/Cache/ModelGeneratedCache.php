<?php

declare(strict_types=1);

namespace Bonn\Maker\Cache;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ModelGeneratedCache implements ModelGeneratedCacheInterface
{
    /** @var Filesystem */
    private $fs;

    /** @var array */
    private $options;

    public function __construct(array $options)
    {
        $this->fs = new Filesystem();
        $this->options = (new OptionsResolver())->setDefaults([
            'max_keep_versions' => 20,
        ])
            ->setRequired('cache_dir')
            ->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    public function appendVersion(string $className, string $info, string $modelDir): string
    {
        if (!$this->fs->exists($this->options['cache_dir'])) {
            $this->fs->mkdir($this->options['cache_dir']);
        }

        $version = (new \DateTime())->format('YmdHis');
        $this->fs->appendToFile($this->getFileLocate($className), $version . '||' . $modelDir . '||' . $info . "\n");

        return $version;
    }

    /**
     * {@inheritdoc}
     */
    public function listVersions(string $className): array
    {
        if (!$this->fs->exists($this->getFileLocate($className))) {
            return [];
        }

        $content = file_get_contents($this->getFileLocate($className));

        $lists = [];
        foreach (explode("\n", trim($content, "\n")) as $line) {
            [$version, $modelDir, $info] = explode('||', $line);

            $lists[$version] = [$modelDir, $info];
        }

        return $lists;
    }

    private function getFileLocate(string $className): string
    {
        return $this->options['cache_dir'] . '/' . $className . '.cache';
    }
}
