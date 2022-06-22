<?php

declare(strict_types=1);

namespace Bonn\Maker\Model;

class Code
{
    private string $content;

    private string $outputPath;

    private array $extra;

    public function __construct(string $content, string $outputPath, array $extra = [])
    {
        $this->content = $content;
        $this->outputPath = $outputPath;
        $this->extra = $extra;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getOutputPath(): ?string
    {
        return $this->outputPath;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public function setExtra(string $key, $value): void
    {
        $this->extra[$key] = $value;
    }
}
