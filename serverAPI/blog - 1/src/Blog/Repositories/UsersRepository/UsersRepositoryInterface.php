<?php

namespace devavi\leveltwo\Blog\Repositories\UsersRepository;

use devavi\leveltwo\Blog\User;
use devavi\leveltwo\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}