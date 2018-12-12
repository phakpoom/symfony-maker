<?php

declare(strict_types=1);

namespace Bonn\Maker\Cache;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\Options;
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
            'max_keep_versions' => 20
        ])
            ->setRequired('cache_dir')
            ->setNormalizer('max_keep_versions', function (Options $options, $value) {
                if (null === $value) {
                    return 20;
                }
                if (0 === $value) {
                    throw new InvalidArgumentException('max_keep_versions must be positive value or -1');
                }

                if (-1 > $value) {
                    throw new InvalidArgumentException('max_keep_versions must be positive value or -1');
                }

                return $value;
            })
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

        $versions = $this->listVersions($className);
        if (-1 !== $this->options['max_keep_versions'] && count($versions) >= $this->options['max_keep_versions']) {
            $versions = array_slice($versions, -($this->options['max_keep_versions'] - 1), $this->options['max_keep_versions'] - 1);
        }

        $versionsString = '';
        foreach ($versions as $version => $data) {
            $versionsString .= $version . '||' . $data[0] . '||' . $data[1] . "\n";
        }

        $version = uniqid();
        $versionsString .= $version . '||' . $modelDir . '||' . $info . "\n";

        $this->fs->dumpFile($this->getFileLocate($className), $versionsString);

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

    /**
     * {@inheritdoc}
     */
    public function clear(string $className): void
    {
        if (!$this->fs->exists($this->getFileLocate($className))) {
            return;
        }

        $this->fs->remove($this->getFileLocate($className));
    }

    private function getFileLocate(string $className): string
    {
        return $this->options['cache_dir'] . '/' . $className . '.cache';
    }
}
