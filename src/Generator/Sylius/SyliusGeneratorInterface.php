<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator\Sylius;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface SyliusGeneratorInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configurationOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function generate($options = []);
}
