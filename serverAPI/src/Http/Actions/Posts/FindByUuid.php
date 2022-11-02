<?php

namespace devavi\leveltwo\Http\Actions\Posts;

use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\SuccessfulResponse;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Blog\Exceptions\PostNotFoundException;
use devavi\leveltwo\Http\Auth\TokenAuthenticationInterface;
use devavi\leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;



class FindByUuid implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
    }



    public function handle(Request $request): Response
    {
        try {
            $this->authentication->user($request);
        } catch (AuthException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $postUuid = $request->query('uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => $post->uuid(),
            'post' => [
                "author" => $post->user()->username(),
                "title" => $post->title(),
                "text" => $post->text(),
                "category" => $post->category(),
                "date" => $post->date(),
                "textShort" => $post->textShort(),
                "imgDir" => $post->imgDir(),
            ],
        ]);
    }
}
