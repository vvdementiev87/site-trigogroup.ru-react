<?php

namespace devavi\leveltwo\Blog\Command;

use devavi\leveltwo\Blog\Exceptions\ArgumentsException;
use devavi\leveltwo\Blog\Exceptions\CommandException;
use devavi\leveltwo\Blog\Exceptions\InvalidArgumentException;
use devavi\leveltwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use devavi\leveltwo\Blog\Comment;
use devavi\leveltwo\Blog\UUID;

//php cli.php comment post_uuid=59f095ce-8b43-487b-973c-d30aa00185f0 author_uuid=76a49ea6-7883-4475-8f0f-74e18f6ea524 text=SeLaVi

class CreateCommentCommand
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Arguments $arguments): void
    {        
        $this->commentsRepository->save(new Comment(
            UUID::random(),
            UUID::uuidFromString($arguments->get('post_uuid')),
            UUID::uuidFromString($arguments->get('author_uuid')),
            $arguments->get('text')
        ));
    }
}