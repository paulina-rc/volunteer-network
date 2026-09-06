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

    public function findById(int $id): ?array
    {
        $statement = $this->connection->prepare(
            'SELECT o.*, c.name AS category_name, c.color_hex, c.icon
               FROM organizations o
               LEFT JOIN categories c ON c.id = o.category_id
              WHERE o.id = :id'
        );
        $statement->execute(['id' => $id]);
        $organization = $statement->fetch();

        return $organization === false ? null : $organization;
    }

    public function findByUserId(int $userId): ?array
    {
        $statement = $this->connection->prepare(
            'SELECT o.*, c.name AS category_name, c.color_hex, c.icon
               FROM organizations o
               LEFT JOIN categories c ON c.id = o.category_id
              WHERE o.user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);
        $organization = $statement->fetch();

        return $organization === false ? null : $organization;
    }

    /**
     * Every organization, for the public directory.
     */
    public function getAll(): array
    {
        $statement = $this->connection->query(
            'SELECT o.*, c.name AS category_name, c.color_hex, c.icon
               FROM organizations o
               LEFT JOIN categories c ON c.id = o.category_id
              ORDER BY o.name'
        );

        return $statement->fetchAll();
    }

    /**
     * Saves the organization profile (RF04) and keeps profile_complete in sync,
     * because that column is what RN02 checks before allowing a publication.
     */
    public function updateProfile(int $id, array $data): bool
    {
        $categoryId = isset($data['category_id']) && (int) $data['category_id'] > 0
            ? (int) $data['category_id']
            : null;
        $foundedYear = isset($data['founded_year']) && (int) $data['founded_year'] > 0
            ? (int) $data['founded_year']
            : null;

        $statement = $this->connection->prepare(
            'UPDATE organizations
                SET name             = :name,
                    category_id      = :category_id,
                    description      = :description,
                    location         = :location,
                    contact          = :contact,
                    founded_year     = :founded_year,
                    profile_complete = :profile_complete
              WHERE id = :id'
        );

        return $statement->execute([
            'name'             => (string) $data['name'],
            'category_id'      => $categoryId,
            'description'      => $data['description'] ?? null,
            'location'         => $data['location'] ?? null,
            'contact'          => $data['contact'] ?? null,
            'founded_year'     => $foundedYear,
            'profile_complete' => $this->isProfileComplete($data) ? 1 : 0,
            'id'               => $id,
        ]);
    }

    /**
     * Numbers shown on the organization profile header.
     *
     * - published  — opportunities it has created, in any status
     * - volunteers — distinct volunteers it accepted (completed ones were
     *                accepted first, so they count too)
     *
     * @return array{published:int,volunteers:int}
     */
    public function getStats(int $organizationId): array
    {
        $statement = $this->connection->prepare(
            "SELECT
               (SELECT COUNT(*) FROM opportunities
                 WHERE organization_id = :organization1) AS published,
               (SELECT COUNT(DISTINCT e.volunteer_id)
                  FROM enrollments e
                  JOIN opportunities o ON o.id = e.opportunity_id
                 WHERE o.organization_id = :organization2
                   AND e.status IN ('accepted','completed')) AS volunteers"
        );
        $statement->execute([
            'organization1' => $organizationId,
            'organization2' => $organizationId,
        ]);
        $stats = $statement->fetch();

        return [
            'published'  => (int) ($stats['published'] ?? 0),
            'volunteers' => (int) ($stats['volunteers'] ?? 0),
        ];
    }

    /**
     * RN02 — an organization may only publish opportunities once its profile
     * carries a description, a location and a contact.
     *
     * Takes the row (or the submitted form data) so it can be checked before
     * saving as well as after loading.
     */
    public function isProfileComplete(array $organization): bool
    {
        foreach (['description', 'location', 'contact'] as $field) {
            if (trim((string) ($organization[$field] ?? '')) === '') {
                return false;
            }
        }

        return true;
    }
}
