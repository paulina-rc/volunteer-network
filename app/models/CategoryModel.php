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

    /**
     * Same catalog, plus how many opportunities in each category a volunteer
     * could actually enroll in right now. Feeds the category grid on the home
     * page. Categories with no open opportunities are still returned, with 0.
     */
    public function getAllWithCounts(): array
    {
        $statement = $this->connection->query(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM opportunities o
                      WHERE o.category_id = c.id
                        AND o.status = 'active'
                        AND o.activity_date >= CURDATE()) AS opportunity_count
               FROM categories c
              ORDER BY c.id"
        );

        return $statement->fetchAll();
    }
}
