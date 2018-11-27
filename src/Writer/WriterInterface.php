<?php

namespace Bonn\Maker\Writer;

interface WriterInterface
{
    /**
     * @param string $content
     * @param string $locate
     */
    public function write(string $content, string $locate): void;
}
