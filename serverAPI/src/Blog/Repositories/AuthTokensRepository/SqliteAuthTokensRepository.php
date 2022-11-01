<?php

namespace devavi\leveltwo\Blog\Repositories\AuthTokensRepository;

use DateTimeImmutable;
use devavi\leveltwo\Blog\AuthToken;
use devavi\leveltwo\Blog\Exceptions\AuthTokenNotFoundException;
use devavi\leveltwo\Blog\Exceptions\AuthTokensRepositoryException;
use devavi\leveltwo\Blog\UUID;
use Exception;
use PDO;
use PDOException;

class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {
    }

    /**
     * @throws AuthTokensRepositoryException
     */
    public function save(AuthToken $authToken): void
    {
        $query = <<<SQL
    INSERT INTO tokens (
        token,
        user_uuid,
        expires_on
    ) VALUES (
        :token,
        :user_uuid,
        :expires_on
    )
    ON DUPLICATE KEY UPDATE expires_on = :expires_on
SQL;

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => $authToken->token(),
                ':user_uuid' => (string)$authToken->userUuid(),
                ':expires_on' => $authToken->expiresOn()->getTimestamp(),
            ]);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws AuthTokensRepositoryException
     * @throws AuthTokenNotFoundException
     */
    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);

            $result = $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
        if (false === $result) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }



        $date = (new DateTimeImmutable)->setTimestamp(+$result['expires_on']);


        if (false === $date) {
            throw new AuthTokenNotFoundException("Cannot convert date");
        }

        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                $date

            );
        } catch (\Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
