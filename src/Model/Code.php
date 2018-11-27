<?php

declare(strict_types=1);

namespace Bonn\Maker\Model;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Code
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string|null
     */
    private $outputPath;

    /**
     * @var array
     */
    private $extra;

    public function __construct(string $content, string $outputPath, array $extra = [])
    {
        $this->content = $content;
        $this->outputPath = $outputPath;
        $this->extra = (new OptionsResolver())->setDefaults([
            'dump_only' => false
        ])->resolve($extra);
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getOutputPath(): ?string
    {
        return $this->outputPath;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
