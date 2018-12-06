<?php

declare(strict_types=1);

namespace Bonn\Maker\Model;

class Code
{
    /** @var string */
    private $content;

    /** @var string|null */
    private $outputPath;

    /** @var array */
    private $extra;

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

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setExtra(string $key, $value): void
    {
        $this->extra[$key] = $value;
    }
}
