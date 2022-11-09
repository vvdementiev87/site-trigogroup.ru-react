<?php

namespace devavi\leveltwo\Blog\Repositories\CommentsRepository;

use DateTimeImmutable;
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
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text, date) 
            VALUES (:uuid, :post_uuid, :author_uuid, :text, :date)'

        );

        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':post_uuid' => (string)$comment->post()->uuid(),
            ':author_uuid' => (string)$comment->user()->uuid(),
            ':text' => $comment->text(),
            ':date' => $comment->date()->getTimestamp()
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
        $date = (new DateTimeImmutable)->setTimestamp(+$result['date']);

        return new Comment(
            new UUID($result['uuid']),
            $post->user(),
            $post,
            $result['text'],
            $date
        );
    }
    public function getByPostUuid(UUID $uuid)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE post_uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        $size = $statement->rowCount();
        $this->logger->warning("size $size");

        for ($i = 1; $i <= $size; $i++) {
            $result[] = $this->getComment($statement, "uuid:" . (string)$uuid);
        };

        return $result;
    }
}
