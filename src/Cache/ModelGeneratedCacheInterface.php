<?php

namespace Bonn\Maker\Cache;

interface ModelGeneratedCacheInterface
{
    /**
     * @param string $className
     * @param string $info
     * @param string $modelDir
     *
     * @return string
     */
    public function appendVersion(string $className, string $info, string $modelDir): string;

    /**
     * @param string $className
     *
     * @return array contains key = version and value = info
     */
    public function listVersions(string $className): array;
}
