<?php

namespace devavi\leveltwo\Http\Actions\Likes;

use devavi\leveltwo\Blog\Like;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\SuccessfulResponse;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Blog\Exceptions\LikeAlreadyExists;
use devavi\leveltwo\Blog\Exceptions\PostNotFoundException;
use devavi\leveltwo\Http\Auth\TokenAuthenticationInterface;
use devavi\leveltwo\Blog\Exceptions\InvalidArgumentException;
use devavi\leveltwo\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;

class CreatePostLike implements ActionInterface
{
    public   function __construct(
        private LikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
    }


    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $postUuid = $request->JsonBodyField('post_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForPostExists($postUuid, $author->uuid());
        } catch (LikeAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newLikeUuid = UUID::random();

        $like = new Like(
            uuid: $newLikeUuid,
            post_uuid: new UUID($postUuid),
            user_uuid: $author->uuid(),

        );

        $this->likesRepository->save($like);

        return new SuccessFulResponse(
            ['uuid' => (string)$newLikeUuid]
        );
    }
}
