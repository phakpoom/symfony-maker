<?php

declare(strict_types=1);

namespace Bonn\Maker\ModelPropType;

use Bonn\Maker\Manager\CodeManagerInterface;

interface ManagerAwareInterface
{
    public function setManager(CodeManagerInterface $manager): void;

    public function getManager(): CodeManagerInterface;

    public function setOption(array $options): void;

    public function getOption(): array;
}
