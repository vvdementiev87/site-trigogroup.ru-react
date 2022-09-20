<?php

namespace devavi\leveltwo\Http\Auth;

use DateTimeImmutable;
use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Blog\Exceptions\AuthTokenNotFoundException;
use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use devavi\leveltwo\Blog\User;
use devavi\leveltwo\Http\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{

    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository,
        // Репозиторий пользователей
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    /**
     * @throws AuthException
     */
    public function getAuthTokenString(Request $request): string
    {
        // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }
        // Отрезаем префикс Bearer
        return mb_substr($header, strlen(self::HEADER_PREFIX));
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        $token = $this->getAuthTokenString($request);
        // Ищем токен в репозитории
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }
        // Проверяем срок годности токена
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }
        // Получаем UUID пользователя из токена
        $userUuid = $authToken->userUuid();
        // Ищем и возвращаем пользователя
        return $this->usersRepository->get($userUuid);
    }
}
