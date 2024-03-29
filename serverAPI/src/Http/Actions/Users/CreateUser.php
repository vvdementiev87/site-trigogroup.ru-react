<?php

namespace devavi\leveltwo\Http\Actions\Users;

use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use devavi\leveltwo\Blog\User;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\SuccessfulResponse;
use devavi\leveltwo\Person\Name;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $newUserUuid = UUID::random();

            $user = User::createFrom(
                $request->jsonBodyField('username'),
                $request->jsonBodyField('email'),
                $request->jsonBodyField('password'),

                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                ),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}
