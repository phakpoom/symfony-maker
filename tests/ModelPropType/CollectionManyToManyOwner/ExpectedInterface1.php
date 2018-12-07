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

    /**
     * @return Collection|PostInterface[]
     */
    public function getPosts(): Collection;

    /**
     * @param PostInterface $post
     *
     * @return bool
     */
    public function hasPost(PostInterface $post): bool;

    /**
     * @param PostInterface $post
     *
     * @return void
     */
    public function addPost(PostInterface $post): void;

    /**
     * @param PostInterface $post
     *
     * @return void
     */
    public function removePost(PostInterface $post): void;
}
