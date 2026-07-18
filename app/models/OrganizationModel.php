<?php

class OrganizationModel
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    public function createOrganization(int $userId, string $name): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO organizations (user_id, name) VALUES (:user_id, :name)'
        );
        $statement->execute([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function findByUserId(int $userId): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM organizations WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);
        $organization = $statement->fetch();

        return $organization === false ? null : $organization;
    }

    // Step 3 of the development plan:
    // - updateProfile(int $id, array $data): bool
    // - markProfileComplete(int $id): void   // controls RN02
}
