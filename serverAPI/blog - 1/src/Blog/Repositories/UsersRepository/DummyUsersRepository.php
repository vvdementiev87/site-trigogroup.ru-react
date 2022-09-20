<?php

namespace devavi\leveltwo\Blog\Repositories\UsersRepository;

use devavi\leveltwo\Blog\Exceptions\UserNotFoundException;
use devavi\leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use devavi\leveltwo\Blog\User;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Person\Name;

class DummyUsersRepository implements UsersRepositoryInterface
{

    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    public function getByUsername(string $username): User
    {
        return new User(UUID::random(), new Name("first", "last"), "user123", "123");
    }
}
