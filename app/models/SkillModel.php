<?php

class SkillModel
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    /**
     * The full skills catalog, for the profile and publish forms.
     */
    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM skills ORDER BY name');

        return $statement->fetchAll();
    }
}
