<?php

namespace devavi\leveltwo\Http\Actions\Comments;

use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\SuccessfulResponse;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Blog\Exceptions\CommentNotFoundException;
use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Blog\Exceptions\PostNotFoundException;
use devavi\leveltwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use devavi\leveltwo\Http\Auth\TokenAuthenticationInterface;
use devavi\leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;



class FindCommentByPostUuid implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
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
            $postUuid = $request->jsonBodyField('post_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comments = $this->commentsRepository->getByPostUuid(new UUID($postUuid));
        } catch (CommentNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $result = [];
        foreach ($comments as $comment) {
            array_push($result, [
                'uuid' => (string)$comment->uuid(),
                'comment' => [
                    "postUuid" => $comment->post()->uuid(),
                    "author" => $comment->user()->uuid(),
                    "text" => $comment->text(),
                    "date" => $comment->date()
                ]
            ]);
        }

        return new SuccessfulResponse([
            'category' => [
                'comments' => $result
            ]
        ]);
    }
}
