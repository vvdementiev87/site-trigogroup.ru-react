<?php

namespace devavi\leveltwo\Blog\Repositories\CommentsRepository;

use devavi\leveltwo\Blog\Comment;
use devavi\leveltwo\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
}