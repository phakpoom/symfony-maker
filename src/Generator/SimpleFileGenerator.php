<?php

declare(strict_types=1);

namespace Bonn\Maker\Generator;

use Bonn\Maker\Model\Code;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimpleFileGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configurationOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined('content')
            ->setDefined('path')
            ->setAllowedTypes('content', ['string'])
            ->setAllowedTypes('path', ['string']);
    }

    /**
     * {@inheritdoc}
     */
    protected function generateWithResolvedOptions(array $options)
    {
        $this->manager->persist(new Code($options['content'], $options['path']));
    }
}
