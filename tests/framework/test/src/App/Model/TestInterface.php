<?php

declare(strict_types=1);

namespace App\App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Net;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface TestInterface extends ResourceInterface, TimestampableInterface, CodeAwareInterface, ToggleableInterface
{
    /**
     * @return Collection|Net[]
     */
    public function getTests(): Collection;

    /**
     * @param Net $test
     *
     * @return bool
     */
    public function hasTest(Net $test): bool;

    /**
     * @param Net $test
     */
    public function addTest(Net $test);

    /**
     * @param Net $test
     */
    public function removeTest(Net $test);
}
