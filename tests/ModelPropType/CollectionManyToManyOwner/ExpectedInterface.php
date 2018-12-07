<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return Collection|CommentInterface[]
     */
    public function getComments(): Collection;

    /**
     * @param CommentInterface $comment
     *
     * @return bool
     */
    public function hasComment(CommentInterface $comment): bool;

    /**
     * @param CommentInterface $comment
     *
     * @return void
     */
    public function addComment(CommentInterface $comment): void;

    /**
     * @param CommentInterface $comment
     *
     * @return void
     */
    public function removeComment(CommentInterface $comment): void;
}
