<?php

namespace Bonn\Maker\Writer;

final class EchoWriter implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(string $content, string $locate): void
    {
        echo '=========' . $locate . '==========' . "\n";
        echo $content;
        echo '=========' . $locate . '==========' . "\n";
    }
}
