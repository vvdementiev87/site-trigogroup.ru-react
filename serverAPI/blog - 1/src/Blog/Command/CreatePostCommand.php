<?php

namespace devavi\leveltwo\Blog\Command;

use devavi\leveltwo\Blog\Exceptions\ArgumentsException;
use devavi\leveltwo\Blog\Exceptions\CommandException;
use devavi\leveltwo\Blog\Exceptions\InvalidArgumentException;
use devavi\leveltwo\Blog\Exceptions\UserNotFoundException;
use devavi\leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use devavi\leveltwo\Blog\Post;
use devavi\leveltwo\Blog\UUID;

//php cli.php post username=Den title=Viva text=SeLaVi

class CreatePostCommand
{


    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
           $user = $this->usersRepository->getByUsername($username);
        } else {
            throw new UserNotFoundException("User not found: $username");
        }
        
        $this->postsRepository->save(new Post(
            UUID::random(),
            $user,
            $arguments->get('title'),
            $arguments->get('text')
        ));
    }
    private function userExists(string $username): bool
    {
        try {       
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}