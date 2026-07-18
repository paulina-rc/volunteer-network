<?php

class SkillModel
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    // Step 3 of the development plan:
    // - getAll(): array
    // - createIfNotExists(string $name): int
}
