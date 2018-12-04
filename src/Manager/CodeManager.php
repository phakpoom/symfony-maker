<?php

declare(strict_types=1);

namespace Bonn\Maker\Manager;

use Bonn\Maker\Model\Code;
use Bonn\Maker\Writer\WriterInterface;

final class CodeManager implements CodeManagerInterface
{
    /** @var array|Code[] */
    private $codes = [];

    /** @var WriterInterface */
    private $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodes(): array
    {
        return $this->codes;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Code $code): void
    {
        $this->codes[$code->getOutputPath()] = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function detach(Code $code): void
    {
        unset($this->codes[$code->getOutputPath()]);
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        foreach ($this->codes as $code) {
            if (true === $code->getExtra()['dump_only']) {
                echo $code->getContent();

                continue;
            }

            $this->writer->write($code->getContent(), $code->getOutputPath());
        }

        $this->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->codes = [];
    }
}
