<?php

namespace devavi\leveltwo\Http\Actions\Users;

use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use devavi\leveltwo\Blog\Exceptions\UserNotFoundException;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\SuccessfulResponse;

class FindByUsername implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }



    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }


        try {
            // Пытаемся найти пользователя в репозитории
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            // Если пользователь не найден -
            // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }


        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $user->username(),
            'first_name' => $user->name()->first(),
            'last_name' => $user->name()->last(),
            'email' => $user->email(),
        ]);
    }
}
