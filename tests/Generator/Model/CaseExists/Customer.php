<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Customer implements CustomerInterface
{
    /** @var int|null */
    protected $id;

    /** @var Collection|DummyInterface[] */
    protected $groups;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup(DummyInterface $group): bool
    {
        return $this->groups->contains($group);
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(DummyInterface $group): void
    {
        if (!$this->hasGroup($group)) {
            $this->groups->add($group);
            //$group->setXXX($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup(DummyInterface $group): void
    {
        if ($this->hasGroup($group)) {
            $this->groups->removeElement($group);
            //$group->setXXX(null);
        }
    }
}
