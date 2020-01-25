<?php

declare(strict_types=1);

namespace Bonn\Maker\Bridge\MakerBundle;

use Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Compiler\GeneratorCommandPass;
use Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Compiler\GeneratorServicePass;
use Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Compiler\PropTypePass;
use Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BonnMakerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new Extension();
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new PropTypePass());
        $container->addCompilerPass(new GeneratorCommandPass());
        $container->addCompilerPass(new GeneratorServicePass());
    }
}
