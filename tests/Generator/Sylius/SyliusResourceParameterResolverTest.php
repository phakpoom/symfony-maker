<?php

declare(strict_types=1);

namespace Test\Generator\Sylius;

use Bonn\Maker\Generator\Sylius\SyliusResourceServiceNameResolver;
use Bonn\Maker\Tests\AbstractMakerTestCase;

class SyliusResourceServiceNameResolverTest extends AbstractMakerTestCase
{
    public function testWithoutPrefix()
    {
        $resolver = new SyliusResourceServiceNameResolver();

        $this->assertEquals('test.repository.bonn', $resolver->getRepository('Test\\Model\\Bonn'));
        $this->assertEquals('test.controller.bonn', $resolver->getController('Test\\Model\\Bonn'));
        $this->assertEquals('test.factory.bonn', $resolver->getFactory('Test\\Model\\Bonn'));
        $this->assertEquals('test.manager.bonn', $resolver->getManager('Test\\Model\\Bonn'));
        $this->assertEquals('test.model.bonn.class', $resolver->getModelParameter('Test\\Model\\Bonn'));
        $this->assertEquals('bonn', $resolver->getResourceName('Test\\Model\\Bonn'));
        $this->assertEquals('test', $resolver->getPrefix('Test\\Model\\Bonn'));
    }

    public function testWithPrefix()
    {
        $resolver = new SyliusResourceServiceNameResolver('bonn');

        $this->assertEquals('bonn.repository.bonn', $resolver->getRepository('Test\\Model\\Bonn'));
        $this->assertEquals('bonn.controller.bonn', $resolver->getController('Test\\Model\\Bonn'));
        $this->assertEquals('bonn.factory.bonn', $resolver->getFactory('Test\\Model\\Bonn'));
        $this->assertEquals('bonn.manager.bonn', $resolver->getManager('Test\\Model\\Bonn'));
        $this->assertEquals('bonn.model.bonn.class', $resolver->getModelParameter('Test\\Model\\Bonn'));
        $this->assertEquals('bonn', $resolver->getResourceName('Test\\Model\\Bonn'));
        $this->assertEquals('bonn', $resolver->getPrefix('Test\\Model\\Bonn'));
    }
}
