<?php

declare(strict_types=1);

namespace Bonn\Maker\Writer;

interface WriterInterface
{
    public function write(string $content, string $locate): void;
}
