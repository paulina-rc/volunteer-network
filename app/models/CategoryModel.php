<?php

class CategoryModel
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM categories ORDER BY id');
        return $statement->fetchAll();
    }
}
