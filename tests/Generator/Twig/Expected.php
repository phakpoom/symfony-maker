<?php

declare(strict_types=1);

namespace Test\Generator\Twig;

class DummyExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            //new \Twig_Filter('name', [$this, 'method']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            //new \Twig_Function('name', [$this, 'method']),
        ];
    }
}
