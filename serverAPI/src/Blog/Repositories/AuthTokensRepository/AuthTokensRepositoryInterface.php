<?php

namespace devavi\leveltwo\Blog\Repositories\AuthTokensRepository;

use devavi\leveltwo\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    // Метод сохранения токена
    public function save(AuthToken $authToken): void;
    // Метод получения токена
    public function get(string $token): AuthToken;
}
