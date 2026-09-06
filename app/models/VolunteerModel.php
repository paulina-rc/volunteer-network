<?php

class VolunteerModel
{
    /** Hours credited per completed activity — a system estimate, not tracked data. */
    private const HOURS_PER_ACTIVITY = 5;

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

    public function findById(int $id): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM volunteers WHERE id = :id');
        $statement->execute(['id' => $id]);
        $volunteer = $statement->fetch();

        return $volunteer === false ? null : $volunteer;
    }

    public function findByUserId(int $userId): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM volunteers WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);
        $volunteer = $statement->fetch();

        return $volunteer === false ? null : $volunteer;
    }

    /**
     * Saves the editable part of a volunteer profile (RF03).
     */
    public function updateProfile(int $id, array $data): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE volunteers
                SET full_name    = :full_name,
                    location     = :location,
                    availability = :availability,
                    about_me     = :about_me
              WHERE id = :id'
        );

        return $statement->execute([
            'full_name'    => (string) $data['full_name'],
            'location'     => $data['location'] ?? null,
            'availability' => $data['availability'] ?? null,
            'about_me'     => $data['about_me'] ?? null,
            'id'           => $id,
        ]);
    }

    public function getSkills(int $volunteerId): array
    {
        $statement = $this->connection->prepare(
            'SELECT s.id, s.name
               FROM volunteer_skill vs
               JOIN skills s ON s.id = vs.skill_id
              WHERE vs.volunteer_id = :volunteer_id
              ORDER BY s.name'
        );
        $statement->execute(['volunteer_id' => $volunteerId]);

        return $statement->fetchAll();
    }

    public function getInterests(int $volunteerId): array
    {
        $statement = $this->connection->prepare(
            'SELECT c.id, c.name, c.color_hex, c.icon
               FROM volunteer_interest vi
               JOIN categories c ON c.id = vi.category_id
              WHERE vi.volunteer_id = :volunteer_id
              ORDER BY c.id'
        );
        $statement->execute(['volunteer_id' => $volunteerId]);

        return $statement->fetchAll();
    }

    /**
     * Replaces the volunteer's skills: deletes the current rows and inserts the
     * given ones. Duplicates in the input are ignored.
     */
    public function saveSkills(int $volunteerId, array $skillIds): void
    {
        $this->replaceLinks(
            'DELETE FROM volunteer_skill WHERE volunteer_id = :volunteer_id',
            'INSERT INTO volunteer_skill (volunteer_id, skill_id) VALUES (:volunteer_id, :linked_id)',
            $volunteerId,
            $skillIds
        );
    }

    /**
     * Replaces the volunteer's categories of interest (RF03).
     */
    public function saveInterests(int $volunteerId, array $categoryIds): void
    {
        $this->replaceLinks(
            'DELETE FROM volunteer_interest WHERE volunteer_id = :volunteer_id',
            'INSERT INTO volunteer_interest (volunteer_id, category_id) VALUES (:volunteer_id, :linked_id)',
            $volunteerId,
            $categoryIds
        );
    }

    /**
     * Numbers shown on the volunteer profile header.
     *
     * - completed     — enrollments already marked as 'completed'
     * - organizations — distinct organizations that accepted them
     * - hours         — completed * HOURS_PER_ACTIVITY, an estimate: the schema
     *                   does not record real hours per activity
     *
     * @return array{completed:int,organizations:int,hours:int}
     */
    public function getStats(int $volunteerId): array
    {
        $statement = $this->connection->prepare(
            "SELECT
               (SELECT COUNT(*) FROM enrollments
                 WHERE volunteer_id = :volunteer1 AND status = 'completed') AS completed,
               (SELECT COUNT(DISTINCT o.organization_id)
                  FROM enrollments e
                  JOIN opportunities o ON o.id = e.opportunity_id
                 WHERE e.volunteer_id = :volunteer2
                   AND e.status IN ('accepted','completed')) AS organizations"
        );
        $statement->execute([
            'volunteer1' => $volunteerId,
            'volunteer2' => $volunteerId,
        ]);
        $stats = $statement->fetch();

        $completed = (int) ($stats['completed'] ?? 0);

        return [
            'completed'     => $completed,
            'organizations' => (int) ($stats['organizations'] ?? 0),
            'hours'         => $completed * self::HOURS_PER_ACTIVITY,
        ];
    }

    /**
     * Shared "delete then insert" for the two bridge tables, so both stay
     * prepared-statement only.
     */
    private function replaceLinks(string $deleteSql, string $insertSql, int $volunteerId, array $linkedIds): void
    {
        $delete = $this->connection->prepare($deleteSql);
        $delete->execute(['volunteer_id' => $volunteerId]);

        $uniqueIds = array_unique(array_filter(
            array_map('intval', $linkedIds),
            static fn (int $linkedId): bool => $linkedId > 0
        ));

        if ($uniqueIds === []) {
            return;
        }

        $insert = $this->connection->prepare($insertSql);
        foreach ($uniqueIds as $linkedId) {
            $insert->execute([
                'volunteer_id' => $volunteerId,
                'linked_id'    => $linkedId,
            ]);
        }
    }
}
