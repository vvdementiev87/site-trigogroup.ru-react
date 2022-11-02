<?php

namespace devavi\leveltwo\Http\Actions\Posts;

use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use devavi\leveltwo\Blog\Post;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\SuccessfulResponse;
use devavi\leveltwo\Http\Actions\ActionInterface;
use devavi\leveltwo\Blog\Exceptions\AuthException;
use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Http\Auth\TokenAuthenticationInterface;
use devavi\leveltwo\Blog\Exceptions\InvalidArgumentException;
use devavi\leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        // Внедряем контракт логгера
        private LoggerInterface $logger,
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
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }


        $newPostUuid = UUID::random();

        $format = 'Y-m-d H:i:s';
        $date = DateTimeImmutable::createFromFormat($format, $request->jsonBodyField('date'));

        if (false === $date) {
            throw new ErrorResponse("Cannot convert date to fomat: $format");
        }

        try {
            $post = new Post(
                $newPostUuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
                $request->jsonBodyField('textShort'),
                $request->jsonBodyField('category'),
                $date,
                $request->jsonBodyField('imgDir')
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postsRepository->save($post);
        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse(
            [
                'uuid' => (string)$newPostUuid,
                'post' => [
                    "author" => $post->user()->username(),
                    "title" => $post->title(),
                    "text" => $post->text(),
                    "category" => $post->category(),
                    "date" => $post->date(),
                    "textShort" => $post->textShort(),
                    "imgDir" => $post->imgDir(),
                ]
            ]
        );
    }
}
