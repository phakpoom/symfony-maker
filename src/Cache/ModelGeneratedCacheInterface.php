<?php

declare(strict_types=1);

namespace Bonn\Maker\Cache;

interface ModelGeneratedCacheInterface
{
    public function appendVersion(string $className, string $info, string $modelDir): string;

    /**
     * @param string $className
     *
     * @return array contains key = version and value = info
     */
    public function listVersions(string $className): array;

    public function clear(string $className): void;
}
