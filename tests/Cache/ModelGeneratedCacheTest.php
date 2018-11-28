<?php

namespace Test\Cache;

use Bonn\Maker\Cache\ModelGeneratedCache;

final class ModelGeneratedCacheTest
{
    /**
     * {@inheritdoc}
     */
    public function testAppendNewVersion(string $className, string $info, string $modelDir): string
    {
        $cache = new ModelGeneratedCache([]);
    }
}
