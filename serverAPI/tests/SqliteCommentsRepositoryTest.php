<?php

namespace devavi\leveltwo;

use devavi\leveltwo\Blog\Post;
use devavi\leveltwo\Blog\User;
use devavi\leveltwo\Blog\UUID;
use devavi\leveltwo\Blog\Comment;
use devavi\leveltwo\Person\Name;
use devavi\leveltwo\Blog\Exceptions\CommentNotFoundException;
use devavi\leveltwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use devavi\leveltwo\UnitTests\DummyLogger;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionMock, new DummyLogger);
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Cannot find comment: 59f095ce-8b43-487b-973c-d30aa00185f0');

        $repository->get(new UUID('59f095ce-8b43-487b-973c-d30aa00185f0'));
    }

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174001',
                ':post_uuid' => '123e4567-e89b-12d3-a456-426614174002',
                ':text' => 'Text',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger);

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174001'),
            new Name('first_name', 'last_name'),
            'name',
            '123'
        );
        $post = new Post(
            new UUID('123e4567-e89b-12d3-a456-426614174002'),
            $user,
            'Title',
            'Text'
        );

        $repository->save(
            new Comment(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                $post,
                'Text'
            )
        );
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStubComment = $this->createStub(PDOStatement::class);
        $statementStubPost = $this->createStub(PDOStatement::class);
        $statementStubUser = $this->createStub(PDOStatement::class);

        $statementStubComment
            ->method('fetch')
            ->willReturn([
                'uuid' => '9dba7ab0-93be-4ff4-9699-165320c97696',
                'author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                'post_uuid' => '9dba7ab0-93be-4ff4-9699-165320c97694',
                'text' => 'Text',
            ]);
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
        $connectionMock->method('prepare')->willReturn($statementStubComment, $statementStubPost, $statementStubUser);

        $commentRepository = new SqliteCommentsRepository($connectionMock, new DummyLogger);
        $comment = $commentRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97696'));
        $this->assertSame('9dba7ab0-93be-4ff4-9699-165320c97696', (string)$comment->uuid());
    }
}
