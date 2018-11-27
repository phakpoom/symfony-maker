<?php

namespace Bonn\Maker\Writer;

final class FileWriter implements WriterInterface
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
