<?php

declare(strict_types=1);

namespace Bonn\Maker\Writer;

class FileWriter implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(string $content, string $locate): void
    {
        // create folder
        $explodedPath = explode('/', $locate);
        array_pop($explodedPath);
        @mkdir(implode('/', $explodedPath));

        // create file
        file_put_contents($locate, $content);
    }
}
