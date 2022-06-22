<?php

declare(strict_types=1);

namespace Bonn\Maker\Manager;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Writer\WriterInterface;

final class CodeManager implements CodeManagerInterface
{
    /** @var array|Code[] */
    private array $codes = [];

    private WriterInterface $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function getCodes(): array
    {
        return $this->codes;
    }

    public function persist(Code $code): void
    {
        $this->codes[$code->getOutputPath()] = $code;
    }

    public function detach(Code $code): void
    {
        unset($this->codes[$code->getOutputPath()]);
    }

    public function flush(): void
    {
        foreach ($this->codes as $code) {
            if ($code->getExtra()['dump_only'] ?? false) {
                echo '>>>>' . $code->getOutputPath() . '<<<<' . "\n";
                echo $code->getContent();
                echo "\n";

                continue;
            }

            $this->writer->write($code->getContent(), $code->getOutputPath());
        }

        $this->clear();
    }

    public function clear(): void
    {
        $this->codes = [];
    }
}
