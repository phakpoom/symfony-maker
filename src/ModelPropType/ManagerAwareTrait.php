<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;

trait ManagerAwareTrait
{
    /** @var CodeManagerInterface|null */
    protected $manager;

    /** @var array */
    protected $options = [];

    /**
     * {@inheritdoc}
     */
    public function setManager(CodeManagerInterface $manager): void
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getManager(): CodeManagerInterface
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption(array $options): void
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(): array
    {
        return $this->options;
    }
}
