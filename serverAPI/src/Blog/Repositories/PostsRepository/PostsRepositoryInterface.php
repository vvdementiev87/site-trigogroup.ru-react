<?php

namespace devavi\leveltwo\Blog\Repositories\PostsRepository;

use devavi\leveltwo\Blog\Post;
use devavi\leveltwo\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function delete(UUID $uuid): void;
}