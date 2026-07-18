<?php

class UserModel
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    public function createUser(string $email, string $passwordHash, string $role): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (email, password_hash, role) VALUES (:email, :password_hash, :role)'
        );
        $statement->execute([
            'email'         => $email,
            'password_hash' => $passwordHash,
            'role'          => $role,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM users WHERE email = :email');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public function verifyCredentials(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if ($user === null || !$user['is_active']) {
            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        return $user;
    }
}
