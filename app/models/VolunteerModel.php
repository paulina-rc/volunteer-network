<?php

class VolunteerModel
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    public function createVolunteer(int $userId, string $fullName): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO volunteers (user_id, full_name) VALUES (:user_id, :full_name)'
        );
        $statement->execute([
            'user_id'   => $userId,
            'full_name' => $fullName,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function findByUserId(int $userId): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM volunteers WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);
        $volunteer = $statement->fetch();

        return $volunteer === false ? null : $volunteer;
    }

    // Step 3 of the development plan:
    // - updateProfile(int $id, array $data): bool
    // - saveInterests(int $volunteerId, array $categoryIds): void
    // - saveSkills(int $volunteerId, array $skillIds): void
}
