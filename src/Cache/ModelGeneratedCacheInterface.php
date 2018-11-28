<?php

declare(strict_types=1);

namespace Bonn\Maker\Cache;

interface ModelGeneratedCacheInterface
{
    public function appendVersion(string $className, string $info, string $modelDir): string;

    /**
     * @return array contains key = version and value = info
     */
    public function listVersions(string $className): array;
}
