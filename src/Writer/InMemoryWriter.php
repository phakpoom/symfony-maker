<?php

declare(strict_types=1);

namespace Bonn\Maker\Writer;

class InMemoryWriter implements WriterInterface
{
    public $files = [];

    /**
     * {@inheritdoc}
     */
    public function write(string $content, string $locate): void
    {
        $this->files[$locate] = $content;
    }
}
