<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerInterface extends ResourceInterface
{
    /**
     * @return Collection<int, DummyInterface>
     */
    public function getGroups(): Collection;

    public function hasGroup(DummyInterface $group): bool;

    public function addGroup(DummyInterface $group): void;

    public function removeGroup(DummyInterface $group): void;
}
