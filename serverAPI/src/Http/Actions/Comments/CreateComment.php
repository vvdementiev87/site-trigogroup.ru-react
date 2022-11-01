<?php

namespace devavi\leveltwo\Http\Actions\Comments;

use DateTimeImmutable;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Blog\Comment;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\SuccessfulResponse;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Blog\Exceptions\PostNotFoundException;
use devavi\leveltwo\Blog\Exceptions\UserNotFoundException;
use devavi\leveltwo\Http\Auth\TokenAuthenticationInterface;
use devavi\leveltwo\Blog\Exceptions\InvalidArgumentException;
use devavi\leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
    }
    public function handle(Request $request): Response
    {
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newCommentUuid = UUID::random();
        $date = (new DateTimeImmutable("now"))->getTimestamp();
        $text = $request->jsonBodyField('text');
        try {
            $comment = new Comment(
                $newCommentUuid,
                $author,
                $post,
                $text,
                $date
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->commentsRepository->save($comment);
        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
            'date' => $date,
            'text' => $text,
        ]);
    }
}
