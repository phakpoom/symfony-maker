<?php

declare(strict_types=1);

namespace Test\Utils;

use Bonn\Maker\Utils\NameResolver;
use PHPUnit\Framework\TestCase;

final class NameResolverTest extends TestCase
{
    public function testResolveClassName()
    {
        $this->assertEquals('TestCase', NameResolver::resolveOnlyClassName(TestCase::class));
        $this->assertEquals('NameResolver', NameResolver::resolveOnlyClassName(NameResolver::class));
        $this->assertEquals('ReflectionClass', NameResolver::resolveOnlyClassName(\ReflectionClass::class));
    }

    public function testResolveNameSpace()
    {
        $this->assertEquals('PHPUnit\Framework', NameResolver::resolveNamespace(TestCase::class));
        $this->assertEquals('Bonn\Maker\Utils', NameResolver::resolveNamespace(NameResolver::class));
        $this->assertEquals('', NameResolver::resolveNamespace(\ReflectionClass::class));
    }

    public function testCamelToUnderScore()
    {
        $this->assertEquals('test_camel_to_under_score', NameResolver::camelToUnderScore('testCamelToUnderScore'));
        $this->assertEquals('post_comments', NameResolver::camelToUnderScore('PostComments'));
        $this->assertEquals('oh_my_god', NameResolver::camelToUnderScore('oh_my_god'));
    }

    public function testResolveToSingular()
    {
        $this->assertEquals('bon', NameResolver::resolveToSingular('bon'));
        $this->assertEquals('comment', NameResolver::resolveToSingular('comments'));
        $this->assertEquals('computer', NameResolver::resolveToSingular('computers'));
    }

    public function testResolveToPlural()
    {
        $this->assertEquals('bons', NameResolver::resolveToPlural('bons'));
        $this->assertEquals('comments', NameResolver::resolveToPlural('comment'));
        $this->assertEquals('computers', NameResolver::resolveToPlural('computer'));
    }
}
