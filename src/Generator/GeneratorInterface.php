<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface GeneratorInterface
{
    public function configurationOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function generate($options = []);
}
