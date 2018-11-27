<?php

namespace Bonn\Maker\Bridge\MakerBundle;

use Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Compiler\PropTypePass;
use Bonn\Maker\Bridge\MakerBundle\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BonnMakerBundle extends Bundle
{
    public function __construct()
    {
        $this->extension = new Extension();
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new PropTypePass());
    }
}
