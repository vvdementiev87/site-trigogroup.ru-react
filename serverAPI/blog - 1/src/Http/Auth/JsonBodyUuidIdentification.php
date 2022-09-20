<?php

namespace devavi\leveltwo\Http\Auth;

use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Blog\Exceptions\InvalidArgumentException;
use devavi\leveltwo\Blog\Exceptions\UserNotFoundException;
use devavi\leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use devavi\leveltwo\Blog\User;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Http\Request;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            // Получаем UUID пользователя из JSON-тела запроса;
            // ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            // Если невозможно получить UUID из запроса -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
            // Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким UUID не найден -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}
