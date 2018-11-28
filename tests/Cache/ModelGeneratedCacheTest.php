<?php

namespace Test\Cache;

use Bonn\Maker\Cache\ModelGeneratedCache;
use PHPUnit\Framework\TestCase;

final class ModelGeneratedCacheTest extends TestCase
{
    const CACHE_DIR = __DIR__  . '/_cache';

    private function _clearCache()
    {
        array_map('unlink', glob(self::CACHE_DIR . '/*'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateClassWithoutCacheDir()
    {
        new ModelGeneratedCache([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateClassWithInvalidLimit0()
    {
        new ModelGeneratedCache([
            'cache_dir' => self::CACHE_DIR,
            'max_keep_versions' => 0
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateClassWithInvalidLimitMinus()
    {
        new ModelGeneratedCache([
            'cache_dir' => self::CACHE_DIR,
            'max_keep_versions' => -2
        ]);
    }

    public function testAppendNewVersion()
    {
        $this->_clearCache();
        $cacheFile = self::CACHE_DIR . '/Test.cache';

        $cache = new ModelGeneratedCache([
            'cache_dir' => self::CACHE_DIR
        ]);

        $this->assertFalse(\file_exists($cacheFile));

        $randomValue = uniqid();
        $cache->appendVersion('Test', 'ok:string:' . $randomValue, __DIR__);
        $this->assertTrue(\file_exists($cacheFile));
        $content = file_get_contents($cacheFile);
        $this->assertContains('ok:string:' . $randomValue, $content);
        $this->assertCount(1, $cache->listVersions('Test'));

        $randomValue1 = uniqid();
        $cache->appendVersion('Test', 'ok:string:' . $randomValue1, __DIR__ . '/Model');
        $this->assertCount(2, $versions = $cache->listVersions('Test'));
        $lastVersion = end($versions);
        [$modelDir, $infos] = $lastVersion;
        $this->assertEquals('ok:string:' . $randomValue1, $infos);
        $this->assertEquals(__DIR__ . '/Model', $modelDir);
    }

    public function testAppendNewVersionOverLimitConfig()
    {
        $this->_clearCache();
        $cacheFile = self::CACHE_DIR . '/Test.cache';

        $cache = new ModelGeneratedCache([
            'cache_dir' => self::CACHE_DIR,
            'max_keep_versions' => 5
        ]);

        $this->assertFalse(\file_exists($cacheFile));

        for ($i = 1; $i <= 10; $i++) {
            $randomValue = $i;
            $cache->appendVersion('Test', 'ok:string:' . $randomValue, __DIR__);
        }

        $this->assertCount(5, $versions = $cache->listVersions('Test'));
        [$modelDir, $infos] = end($versions);
        $this->assertEquals('ok:string:10', $infos);
    }

    public function testAppendNewVersionUnLimitConfig()
    {
        $this->_clearCache();
        $cacheFile = self::CACHE_DIR . '/Test.cache';

        $cache = new ModelGeneratedCache([
            'cache_dir' => self::CACHE_DIR,
            'max_keep_versions' => -1
        ]);

        $this->assertFalse(\file_exists($cacheFile));

        for ($i = 1; $i <= 10; $i++) {
            $randomValue = $i;
            $cache->appendVersion('Test', 'ok:string:' . $randomValue, __DIR__);
        }

        $this->assertCount(10, $versions = $cache->listVersions('Test'));
        [$modelDir, $infos] = end($versions);
        $this->assertEquals('ok:string:10', $infos);
    }
}
