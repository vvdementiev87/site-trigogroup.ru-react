<?php

namespace devavi\leveltwo;

use devavi\leveltwo\Blog\Post;
use devavi\leveltwo\Blog\Exceptions\PostNotFoundException;
use devavi\leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use devavi\leveltwo\Blog\User;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Person\Name;
use devavi\leveltwo\UnitTests\DummyLogger;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock, new DummyLogger());
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot find post: 59f095ce-8b43-487b-973c-d30aa00185f0');

        $repository->get(new UUID('59f095ce-8b43-487b-973c-d30aa00185f0'));
    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':title' => 'Title',
                ':text' => 'Text',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new Name('first_name', 'last_name'),
            'name',
            '123'
        );

        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Title',
                'Text'
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStubPost = $this->createStub(PDOStatement::class);
        $statementStubUser = $this->createStub(PDOStatement::class);
        $statementStubPost
            ->method('fetch')
            ->willReturn([
                'uuid' => '9dba7ab0-93be-4ff4-9699-165320c97694',
                'author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                'title' => 'Title',
                'text' => 'Text',
            ]);
        $statementStubUser
            ->method('fetch')
            ->willReturn([
                'uuid' => '123e4567-e89b-12d3-a456-426614174000',
                'username' => 'ivan123',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
                'password' => '123'
            ]);
        $connectionMock->method('prepare')->willReturn($statementStubPost, $statementStubUser);

        $postRepository = new SqlitePostsRepository($connectionMock, new DummyLogger());
        $post = $postRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));
        $this->assertSame('9dba7ab0-93be-4ff4-9699-165320c97694', (string)$post->uuid());
    }
}
