<?php

class OpportunityModel
{
    /**
     * Shared projection for every read in this model.
     *
     * Besides the opportunity row it exposes the category and organization the
     * views need for the card header, plus two counters computed per row:
     * taken_slots (accepted + completed enrollments, the ones that consume a
     * slot under RN05) and total_enrollments (every enrollment, whatever its
     * status). Callers append their own WHERE / ORDER BY.
     */
    private const SELECT_BASE = "
        SELECT o.*, c.name AS category_name, c.color_hex, c.icon,
               org.id AS org_id, org.name AS organization_name,
               org.location AS org_location, org.founded_year,
               (SELECT COUNT(*) FROM enrollments e WHERE e.opportunity_id = o.id
                  AND e.status IN ('accepted','completed')) AS taken_slots,
               (SELECT COUNT(*) FROM enrollments e WHERE e.opportunity_id = o.id)
                  AS total_enrollments
        FROM opportunities o
        JOIN organizations org ON org.id = o.organization_id
        JOIN categories c ON c.id = o.category_id
    ";

    /** Statuses accepted by the opportunities.status enum. */
    private const VALID_STATUSES = ['active', 'closed', 'draft'];

    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->connection->prepare(self::SELECT_BASE . ' WHERE o.id = :id');
        $statement->execute(['id' => $id]);
        $opportunity = $statement->fetch();

        return $opportunity === false ? null : $opportunity;
    }

    /**
     * Most recently published opportunities that are still open and upcoming.
     * Feeds the "Oportunidades destacadas" block on the home page.
     */
    public function getFeatured(int $limit = 3): array
    {
        $statement = $this->connection->prepare(
            self::SELECT_BASE .
            " WHERE o.status = 'active' AND o.activity_date >= CURDATE()
              ORDER BY o.created_at DESC
              LIMIT :limit"
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Every opportunity of one organization, including drafts and closed ones,
     * for its own profile screen. Active first, then drafts, then closed.
     */
    public function getByOrganization(int $organizationId): array
    {
        $statement = $this->connection->prepare(
            self::SELECT_BASE .
            " WHERE o.organization_id = :organization_id
              ORDER BY FIELD(o.status,'active','draft','closed'), o.activity_date"
        );
        $statement->execute(['organization_id' => $organizationId]);

        return $statement->fetchAll();
    }

    /**
     * Number of opportunities a volunteer could actually enroll in right now.
     * Uses the same conditions as searchWithFilters() so the headline count and
     * the result list can never disagree.
     */
    public function countActive(): int
    {
        $statement = $this->connection->query(
            "SELECT COUNT(*) FROM opportunities
             WHERE status = 'active' AND activity_date >= CURDATE()"
        );

        return (int) $statement->fetchColumn();
    }

    /**
     * Opportunity search with optional filters (RF06).
     *
     * Accepted keys:
     * - text       string, matched against title, description and organization name
     * - categories int[], category ids
     * - location   string, matched against the opportunity location
     * - date       'week' | 'month' | 'quarter'
     * - sort       'recent' (default) | 'soonest' | 'slots'
     *
     * Only open, upcoming opportunities are ever returned.
     */
    public function searchWithFilters(array $filters): array
    {
        $conditions = ["o.status = 'active'", 'o.activity_date >= CURDATE()'];
        $params = [];

        $text = trim((string) ($filters['text'] ?? ''));
        if ($text !== '') {
            // PDO with ATTR_EMULATE_PREPARES = false does not allow reusing the
            // same named placeholder, so the three LIKE checks get one each.
            $conditions[] = '(o.title LIKE :text1 OR o.description LIKE :text2 OR org.name LIKE :text3)';
            $params['text1'] = '%' . $text . '%';
            $params['text2'] = '%' . $text . '%';
            $params['text3'] = '%' . $text . '%';
        }

        $categoryIds = array_values(array_filter(
            array_map('intval', (array) ($filters['categories'] ?? [])),
            static fn (int $categoryId): bool => $categoryId > 0
        ));
        if ($categoryIds !== []) {
            // One generated placeholder per id — the values never touch the SQL.
            $placeholders = [];
            foreach ($categoryIds as $index => $categoryId) {
                $placeholder = 'category' . $index;
                $placeholders[] = ':' . $placeholder;
                $params[$placeholder] = $categoryId;
            }
            $conditions[] = 'o.category_id IN (' . implode(', ', $placeholders) . ')';
        }

        $location = trim((string) ($filters['location'] ?? ''));
        if ($location !== '') {
            $conditions[] = 'o.location LIKE :location';
            $params['location'] = '%' . $location . '%';
        }

        // Fixed intervals picked from a whitelist, never built from user input.
        $dateRanges = [
            'week'    => 'INTERVAL 7 DAY',
            'month'   => 'INTERVAL 1 MONTH',
            'quarter' => 'INTERVAL 3 MONTH',
        ];
        $dateRange = (string) ($filters['date'] ?? '');
        if (isset($dateRanges[$dateRange])) {
            $conditions[] = 'o.activity_date <= DATE_ADD(CURDATE(), ' . $dateRanges[$dateRange] . ')';
        }

        $sortOptions = [
            'recent'  => 'o.created_at DESC',
            'soonest' => 'o.activity_date ASC',
            'slots'   => 'o.available_slots DESC',
        ];
        $sort = (string) ($filters['sort'] ?? 'recent');
        $orderBy = $sortOptions[$sort] ?? $sortOptions['recent'];

        $statement = $this->connection->prepare(
            self::SELECT_BASE .
            ' WHERE ' . implode(' AND ', $conditions) .
            ' ORDER BY ' . $orderBy
        );
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * Publishes a new opportunity and its required skills (RF05, RN03).
     * available_slots starts equal to total_slots — nobody is enrolled yet.
     *
     * @return int the new opportunity id
     */
    public function create(array $data, array $skillIds): int
    {
        $ownTransaction = !$this->connection->inTransaction();
        if ($ownTransaction) {
            $this->connection->beginTransaction();
        }

        try {
            $totalSlots = (int) ($data['total_slots'] ?? MAX_SLOTS_DEFAULT);

            $statement = $this->connection->prepare(
                'INSERT INTO opportunities
                   (organization_id, category_id, title, description, requirements,
                    location, activity_date, `time`, total_slots, available_slots, status)
                 VALUES
                   (:organization_id, :category_id, :title, :description, :requirements,
                    :location, :activity_date, :time, :total_slots, :available_slots, :status)'
            );
            $statement->execute([
                'organization_id' => (int) $data['organization_id'],
                'category_id'     => (int) $data['category_id'],
                'title'           => (string) $data['title'],
                'description'     => $data['description'] ?? null,
                'requirements'    => $data['requirements'] ?? null,
                'location'        => $data['location'] ?? null,
                'activity_date'   => (string) $data['activity_date'],
                'time'            => $data['time'] ?? null,
                'total_slots'     => $totalSlots,
                // Same value, but the placeholder cannot be reused.
                'available_slots' => $totalSlots,
                'status'          => $this->normalizeStatus($data['status'] ?? 'active'),
            ]);

            $opportunityId = (int) $this->connection->lastInsertId();
            $this->saveSkills($opportunityId, $skillIds);

            if ($ownTransaction) {
                $this->connection->commit();
            }

            return $opportunityId;
        } catch (PDOException $exception) {
            if ($ownTransaction) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }

    /**
     * Edits an existing opportunity and replaces its required skills (RN09).
     * Recomputes available_slots afterwards, so changing total_slots cannot
     * leave the counter out of sync.
     */
    public function update(int $id, array $data, array $skillIds): bool
    {
        $ownTransaction = !$this->connection->inTransaction();
        if ($ownTransaction) {
            $this->connection->beginTransaction();
        }

        try {
            $statement = $this->connection->prepare(
                'UPDATE opportunities
                    SET category_id   = :category_id,
                        title         = :title,
                        description   = :description,
                        requirements  = :requirements,
                        location      = :location,
                        activity_date = :activity_date,
                        `time`        = :time,
                        total_slots   = :total_slots,
                        status        = :status
                  WHERE id = :id'
            );
            $updated = $statement->execute([
                'category_id'   => (int) $data['category_id'],
                'title'         => (string) $data['title'],
                'description'   => $data['description'] ?? null,
                'requirements'  => $data['requirements'] ?? null,
                'location'      => $data['location'] ?? null,
                'activity_date' => (string) $data['activity_date'],
                'time'          => $data['time'] ?? null,
                'total_slots'   => (int) ($data['total_slots'] ?? MAX_SLOTS_DEFAULT),
                'status'        => $this->normalizeStatus($data['status'] ?? 'active'),
                'id'            => $id,
            ]);

            $this->saveSkills($id, $skillIds);
            $this->syncAvailableSlots($id);

            if ($ownTransaction) {
                $this->connection->commit();
            }

            return $updated;
        } catch (PDOException $exception) {
            if ($ownTransaction) {
                $this->connection->rollBack();
            }
            throw $exception;
        }
    }

    /**
     * Manually changes the status of an opportunity (RN09).
     */
    public function setStatus(int $id, string $status): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE opportunities SET status = :status WHERE id = :id'
        );

        return $statement->execute([
            'status' => $this->normalizeStatus($status),
            'id'     => $id,
        ]);
    }

    /**
     * Required skills of an opportunity (RN03).
     */
    public function getSkills(int $opportunityId): array
    {
        $statement = $this->connection->prepare(
            'SELECT s.id, s.name
               FROM opportunity_skill os
               JOIN skills s ON s.id = os.skill_id
              WHERE os.opportunity_id = :opportunity_id
              ORDER BY s.name'
        );
        $statement->execute(['opportunity_id' => $opportunityId]);

        return $statement->fetchAll();
    }

    /**
     * Replaces the required skills of an opportunity: deletes the current rows
     * and inserts the given ones. Duplicates in the input are ignored.
     */
    public function saveSkills(int $opportunityId, array $skillIds): void
    {
        $delete = $this->connection->prepare(
            'DELETE FROM opportunity_skill WHERE opportunity_id = :opportunity_id'
        );
        $delete->execute(['opportunity_id' => $opportunityId]);

        $uniqueIds = array_unique(array_filter(
            array_map('intval', $skillIds),
            static fn (int $skillId): bool => $skillId > 0
        ));

        if ($uniqueIds === []) {
            return;
        }

        $insert = $this->connection->prepare(
            'INSERT INTO opportunity_skill (opportunity_id, skill_id)
             VALUES (:opportunity_id, :skill_id)'
        );
        foreach ($uniqueIds as $skillId) {
            $insert->execute([
                'opportunity_id' => $opportunityId,
                'skill_id'       => $skillId,
            ]);
        }
    }

    /**
     * Recomputes available_slots from the enrollments instead of adding or
     * subtracting one at a time, so the counter cannot drift, and applies the
     * automatic close/reopen rule (RN05).
     *
     * Drafts are never touched.
     *
     * @return bool true when the opportunity is now full
     */
    public function syncAvailableSlots(int $id): bool
    {
        $recount = $this->connection->prepare(
            "UPDATE opportunities o
                SET o.available_slots = GREATEST(0, o.total_slots - (
                      SELECT COUNT(*) FROM enrollments e
                       WHERE e.opportunity_id = o.id
                         AND e.status IN ('accepted','completed')))
              WHERE o.id = :id"
        );
        $recount->execute(['id' => $id]);

        $statement = $this->connection->prepare(
            'SELECT available_slots, status, activity_date >= CURDATE() AS is_upcoming
               FROM opportunities WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $opportunity = $statement->fetch();

        if ($opportunity === false) {
            return false;
        }

        $availableSlots = (int) $opportunity['available_slots'];
        $status = $opportunity['status'];
        $isFull = $availableSlots === 0;

        if ($isFull && $status === 'active') {
            // RN05: the last slot was taken, close it automatically.
            $this->setStatus($id, 'closed');
        } elseif (!$isFull && $status === 'closed' && (int) $opportunity['is_upcoming'] === 1) {
            // A slot opened up again (an acceptance was undone) and the date has
            // not passed, so the opportunity goes back on the board.
            $this->setStatus($id, 'active');
        }

        return $isFull;
    }

    /**
     * Recommended opportunities for a volunteer (RF10, RN08).
     *
     * Priority is skills > interests > location, which is exactly the ORDER BY.
     * Only open, upcoming opportunities with free slots that the volunteer has
     * not already enrolled in are considered.
     *
     * SELECT_BASE is used as a derived table: the match counters below reference
     * the joined columns, so they cannot sit in the same SELECT as the JOINs.
     */
    public function getRecommendationsFor(int $volunteerId, string $location, int $limit = 3): array
    {
        // Only the canton (the part before the comma) is compared, so
        // "Ciudad Quesada, Alajuela" still matches "Ciudad Quesada".
        $canton = trim(explode(',', $location)[0]);
        // An empty location must match nothing. '%%' would match every row.
        $cityPattern = $canton === '' ? '~~no-location~~' : '%' . $canton . '%';

        $sql = "
            SELECT t.*,
              (SELECT COUNT(*) FROM opportunity_skill os
                 JOIN volunteer_skill vs ON vs.skill_id = os.skill_id
                  AND vs.volunteer_id = :vol1
               WHERE os.opportunity_id = t.id) AS skill_matches,
              (SELECT COUNT(*) FROM volunteer_interest vi
               WHERE vi.volunteer_id = :vol2 AND vi.category_id = t.category_id) AS interest_match,
              (CASE WHEN t.location LIKE :city THEN 1 ELSE 0 END) AS location_match
            FROM ( " . self::SELECT_BASE . "
                   WHERE o.status = 'active' AND o.activity_date >= CURDATE()
                     AND o.available_slots > 0
                     AND o.id NOT IN (SELECT opportunity_id FROM enrollments
                                      WHERE volunteer_id = :vol3) ) AS t
            HAVING skill_matches > 0 OR interest_match > 0 OR location_match > 0
            ORDER BY skill_matches DESC, interest_match DESC, location_match DESC,
                     t.activity_date ASC
            LIMIT :limit
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(':vol1', $volunteerId, PDO::PARAM_INT);
        $statement->bindValue(':vol2', $volunteerId, PDO::PARAM_INT);
        $statement->bindValue(':vol3', $volunteerId, PDO::PARAM_INT);
        $statement->bindValue(':city', $cityPattern, PDO::PARAM_STR);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Keeps the status enum safe from unexpected input.
     */
    private function normalizeStatus(string $status): string
    {
        return in_array($status, self::VALID_STATUSES, true) ? $status : 'active';
    }
}
