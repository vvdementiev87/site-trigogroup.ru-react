<?php

namespace devavi\leveltwo\Http\Actions\Posts;

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

class ShowPosts implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        // Внедряем контракт логгера
        private LoggerInterface $logger


    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        $posts = $this->postsRepository->getAll();
        $this->logger->info("Post shown");
        $result=[];
        foreach ($posts as $post) {
            array_push($result, [
            'uuid' => (string)$post->uuid(),
        'post' => ["author"=>$post->user()->username(),
        "title"=>$post->title(),
        "text"=>$post->text(),
        "category"=>$post->category(),
        "date"=>$post->date(),
        ]]);}
        

        return new SuccessfulResponse([
            'posts' => $result,
        ]);
    }
}
