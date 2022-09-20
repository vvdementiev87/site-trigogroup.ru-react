<?php

namespace devavi\leveltwo\Http\Actions\Auth;

use DateTimeImmutable;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\SuccessfulResponse;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Http\Auth\BearerTokenAuthentication;
use devavi\leveltwo\Blog\Exceptions\AuthTokenNotFoundException;
use devavi\leveltwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;

class LogOut implements ActionInterface
{

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private BearerTokenAuthentication $authentication
    ) {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $token = $this->authentication->getAuthTokenString($request);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $exception) {
            throw new AuthException($exception->getMessage());
        }

        $authToken->setExpiresOn(new DateTimeImmutable("now"));


        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->token()
        ]);
    }
}
