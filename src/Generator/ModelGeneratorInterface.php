<?php

namespace Bonn\Maker\Generator;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ModelGeneratorInterface
{
    /**
     * @param OptionsResolver $resolver
     * @return mixed
     */
    public function configurationOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function generate($options = []);
}
