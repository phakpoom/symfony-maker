<?php

declare(strict_types=1);

namespace Bonn\Maker\Writer;

final class EchoWriter implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(string $content, string $locate): void
    {
        echo '=========' . $locate . '==========' . "\n";
        echo $content . "\n";
        echo '=========' . $locate . '==========' . "\n";
    }
}
