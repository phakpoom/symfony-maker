<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface
{
    /**
     * @return Collection<int, CommentInterface>
     */
    public function getComments(): Collection;

    public function hasComment(CommentInterface $comment): bool;

    public function addComment(CommentInterface $comment): void;

    public function removeComment(CommentInterface $comment): void;

    /**
     * @return Collection<int, PostInterface>
     */
    public function getPosts(): Collection;

    public function hasPost(PostInterface $post): bool;

    public function addPost(PostInterface $post): void;

    public function removePost(PostInterface $post): void;
}
