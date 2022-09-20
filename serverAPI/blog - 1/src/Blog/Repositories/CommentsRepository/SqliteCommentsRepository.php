<?php

namespace devavi\leveltwo\Blog\Repositories\CommentsRepository;

use \PDO;
use \PDOStatement;
use Psr\Log\LoggerInterface;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Blog\Comment;
use devavi\leveltwo\Blog\Exceptions\CommentNotFoundException;
use devavi\leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use devavi\leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }


    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text) 
            VALUES (:uuid, :post_uuid, :author_uuid, :text)'

        );

        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':post_uuid' => (string)$comment->post()->uuid(),
            ':author_uuid' => (string)$comment->user()->uuid(),
            ':text' => $comment->text(),
        ]);

        $this->logger->info("Comment created successfully: {$comment->uuid()}");
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = ?'
        );

        $statement->execute([(string)$uuid]);

        return $this->getComment($statement, $uuid);
    }

    private function getComment(PDOStatement $statement, string $errorString): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            $message = "Cannot find comment: $errorString";
            $this->logger->warning($message);
            throw new CommentNotFoundException($message);
        }

        $postRepository = new SqlitePostsRepository($this->connection, $this->logger);
        $post = $postRepository->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post->user(),
            $post,
            $result['text']
        );
    }
}
