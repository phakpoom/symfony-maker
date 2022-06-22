<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Customer implements CustomerInterface
{
    protected ?int $id;

    /** @var Collection<int, DummyInterface> */
    protected Collection $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->abc = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function hasGroup(DummyInterface $group): bool
    {
        return $this->groups->contains($group);
    }

    public function addGroup(DummyInterface $group): void
    {
        if (!$this->hasGroup($group)) {
            $this->groups->add($group);
            //$group->setXXX($this);
        }
    }

    public function removeGroup(DummyInterface $group): void
    {
        if ($this->hasGroup($group)) {
            $this->groups->removeElement($group);
            //$group->setXXX(null);
        }
    }
}
