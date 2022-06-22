<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

class Dummy implements DummyInterface
{
    use TimestampableTrait;

    protected ?int $id;
    protected ?string $displayName;
    protected ?int $age;

    /** @var Collection<int, ResourceInterface> */
    protected Collection $groups;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): void
    {
        $this->age = $age;
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function hasGroup(ResourceInterface $group): bool
    {
        return $this->groups->contains($group);
    }

    public function addGroup(ResourceInterface $group): void
    {
        if (!$this->hasGroup($group)) {
            $this->groups->add($group);
            //$group->setXXX($this);
        }
    }

    public function removeGroup(ResourceInterface $group): void
    {
        if ($this->hasGroup($group)) {
            $this->groups->removeElement($group);
            //$group->setXXX(null);
        }
    }
}
