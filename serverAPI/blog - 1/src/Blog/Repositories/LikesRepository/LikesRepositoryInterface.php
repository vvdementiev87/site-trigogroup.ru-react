<?php

namespace devavi\leveltwo\Blog\Repositories\LikesRepository;

use devavi\leveltwo\Blog\Like;
use devavi\leveltwo\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $uuid): array;
    public function checkUserLikeForPostExists(UUID $postUuid, UUID $userUuid): void;
}
