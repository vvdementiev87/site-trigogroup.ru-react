<?php

namespace devavi\leveltwo\Blog\Repositories\PostsRepository;

use \PDO;
use \PDOStatement;
use Psr\Log\LoggerInterface;
use devavi\leveltwo\Blog\Post;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Blog\Exceptions\PostNotFoundException;
use devavi\leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function save(Post $post): void
    {

        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text, category, date, text_short) 
            VALUES (:uuid, :author_uuid, :title, :text, :category, :date, :text_short)'

        );
        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => (string)$post->user()->uuid(),
            ':title' => $post->title(),
            ':text' => $post->text(),
            ':category' => $post->category(),
            ':date' => $post->date(),
            ':text_short' => $post->textShort()
        ]);

        $this->logger->info("Post created successfully: {$post->uuid()}");
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    public function getAll()
    {
        /* $statement = $this->connection->prepare(
            'SELECT COUNT(*) FROM posts'
        );
        $statement->execute();

        $size = $statement->fetch(\PDO::FETCH_ASSOC);

        $this->logger->warning("size $size[0]"); */

        $statement = $this->connection->prepare(
            'SELECT * FROM posts'
        );
        $statement->execute();
        $size = $statement->rowCount();
        $this->logger->warning("size $size");

        for ($i = 1; $i <= $size; $i++) {
            $result[] = $this->getPost($statement, "all");
        };

        return $result;
    }

    private function getPost(PDOStatement $statement, string $errorString): Post
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            $message = "Cannot find post: $errorString";
            $this->logger->warning($message);
            throw new PostNotFoundException($message);
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text'],
            $result['category'],
            $result['date'],
            $result['text_short']
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE posts.uuid=:uuid;'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);
    }

    public function getByCategory(string $category)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE category = :category'
        );
        $statement->execute([
            ':category' => (string)$category,
        ]);
        $size = $statement->rowCount();
        $this->logger->warning("size $size");

        for ($i = 1; $i <= $size; $i++) {
            $result[] = $this->getPost($statement, "category:" . $category);
        };

        return $result;
    }
}
