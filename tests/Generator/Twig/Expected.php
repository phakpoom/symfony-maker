<?php

declare(strict_types=1);

namespace Test\Generator\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DummyExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            //new TwigFilter('name', [$this, 'method']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            //new TwigFunction('name', [$this, 'method']),
        ];
    }
}
