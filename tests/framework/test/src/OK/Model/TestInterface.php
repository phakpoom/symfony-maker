<?php

declare(strict_types=1);

namespace App\OK\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nette\PhpGenerator\PhpNamespace;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface TestInterface extends ResourceInterface, TimestampableInterface, CodeAwareInterface, ToggleableInterface
{
    /**
     * @return Collection|PhpNamespace[]
     */
    public function getTests(): Collection;

    /**
     * @param PhpNamespace $test
     *
     * @return bool
     */
    public function hasTest(PhpNamespace $test): bool;

    /**
     * @param PhpNamespace $test
     */
    public function addTest(PhpNamespace $test);

    /**
     * @param PhpNamespace $test
     */
    public function removeTest(PhpNamespace $test);
}
