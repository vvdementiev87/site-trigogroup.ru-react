<?php

require_once 'User.php';

class UserProvider
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function registerUser(User $user, string $plainPassword)
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO tbl_user (username,password,first_name,last_name,role) VALUES (:username, :password, :first_name, :last_name, :role)'
        );

        return $statement->execute([
            'username' => $user->getUsername(),
            'first_name' => $user->getFirst_name(),
            'last_name' => $user->getLast_name(),
            'role' => $user->getRole(),
            'password' => password_hash($plainPassword, PASSWORD_DEFAULT)
        ]);
    }

    public function getByUsernameAndPassword(string $username, string $password)
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM tbl_user WHERE username = :username LIMIT 1'
        );
        $statement->execute([
            'username' => $username
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $result['password'] ?? null)) {
            $user = new User($username);
            $user->setFirst_name($result['first_name'] ?? 'empty');
            $user->setLast_name($result['last_name'] ?? 'empty');
            $user->setRole($result['role']);
            $user->setUserId($result['user_id']);            
            return $user;
        }

        return null;

    }
}