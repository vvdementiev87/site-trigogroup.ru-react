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



class FindByCategory implements ActionInterface
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
            $category = $request->query('categoty');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $posts = $this->postsRepository->getByCategory($category);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $result = [];
        foreach ($posts as $post) {
            array_push($result, [
                'uuid' => (string)$post->uuid(),
                'post' => [
                    "author" => $post->user()->username(),
                    "title" => $post->title(),
                    "text" => $post->text(),
                    "category" => $post->category(),
                    "date" => $post->date(),
                    "textShort" => $post->textShort(),
                    "imgDir" => $post->imgDir(),
                ]
            ]);
        }

        return new SuccessfulResponse([
            'category' => [
                'category' => $category,
                'posts' => $result
            ]
        ]);
    }
}
