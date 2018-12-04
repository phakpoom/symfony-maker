<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $deletedAt
     *
     * @return void
     */
    public function setDeletedAt(?\DateTime $deletedAt): void;
}
