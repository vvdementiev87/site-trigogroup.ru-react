<?php

namespace devavi\leveltwo\Http\Actions\Auth;

use DateTimeImmutable;
use devavi\leveltwo\Blog\AuthToken;
use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Http\Auth\PasswordAuthenticationInterface;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Генерируем токен
        $authToken = new AuthToken(
            // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->uuid(),
            // Срок годности - 1 день
            (new DateTimeImmutable("now"))->modify('+1 day')
        );
        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->token(),
            'expiresOn' => $authToken->expiresOn(),
            'uuid' => (string)$user->uuid(),
            'username' => $user->username()
        ]);
    }
}
