<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class CommonServiceGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'all_service_file_path' => null,
                'entry_service_file_path' => null,
                'config_dir' => null,
            ])
            ->setRequired('name')
            ->setRequired('namespace')
            ->setRequired('class_dir');
    }
}
