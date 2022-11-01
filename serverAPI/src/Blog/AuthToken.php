<?php

namespace devavi\leveltwo\Blog;

use DateTimeImmutable;

class AuthToken
{
    public function __construct(
        // Строка токена
        private string            $token,
        // UUID пользователя
        private UUID              $userUuid,
        // Срок годности
        private int $expiresOn
    ) {
    }

    public function setExpiresOn(int $expiresOn): void
    {
        $this->expiresOn = $expiresOn;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function userUuid(): UUID
    {
        return $this->userUuid;
    }

    public function expiresOn(): int
    {
        return $this->expiresOn;
    }
}
