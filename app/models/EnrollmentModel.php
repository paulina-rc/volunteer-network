<?php

class EnrollmentModel
{
    /**
     * Shared projection for every read in this model.
     *
     * One enrollment row carries everything both sides of the screen need: the
     * opportunity and its category/organization (volunteer's "Mis inscripciones")
     * and the volunteer's name, location and email (organization's "Gestionar
     * inscripciones"). users is joined twice — uo for the organization's account,
     * uv for the volunteer's.
     */
    private const SELECT_BASE = "
        SELECT e.*,
               o.title, o.activity_date, o.location, o.total_slots, o.available_slots,
               o.status AS opportunity_status, o.organization_id,
               c.name AS category_name, c.color_hex, c.icon,
               org.name AS organization_name,
               uo.email AS organization_email,
               v.full_name AS volunteer_name, v.location AS volunteer_location,
               uv.email AS volunteer_email
        FROM enrollments e
        JOIN opportunities o ON o.id = e.opportunity_id
        JOIN categories c ON c.id = o.category_id
        JOIN organizations org ON org.id = o.organization_id
        JOIN users uo ON uo.id = org.user_id
        JOIN volunteers v ON v.id = e.volunteer_id
        JOIN users uv ON uv.id = v.user_id
    ";

    /**
     * Pending first, because that is what an organization has to act on.
     */
    private const ORDER_BY_STATUS = " ORDER BY FIELD(e.status,'pending','accepted','completed','rejected'), e.enrollment_date DESC";

    /** Statuses accepted by the enrollments.status enum. */
    private const VALID_STATUSES = ['pending', 'accepted', 'rejected', 'completed'];

    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->connection->prepare(self::SELECT_BASE . ' WHERE e.id = :id');
        $statement->execute(['id' => $id]);
        $enrollment = $statement->fetch();

        return $enrollment === false ? null : $enrollment;
    }

    /**
     * Whether this volunteer already applied to this opportunity.
     * Backs the unique (volunteer_id, opportunity_id) key so the controller can
     * show a friendly message instead of letting the insert fail (RN04).
     */
    public function existsFor(int $opportunityId, int $volunteerId): bool
    {
        $statement = $this->connection->prepare(
            'SELECT 1 FROM enrollments
              WHERE opportunity_id = :opportunity_id AND volunteer_id = :volunteer_id
              LIMIT 1'
        );
        $statement->execute([
            'opportunity_id' => $opportunityId,
            'volunteer_id'   => $volunteerId,
        ]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * Enrolls a volunteer in an opportunity (RF07, CU05).
     * Every enrollment starts as 'pending' until the organization decides —
     * RN06 — so the status is never taken from the caller.
     *
     * @return int the new enrollment id
     */
    public function create(int $opportunityId, int $volunteerId): int
    {
        $statement = $this->connection->prepare(
            "INSERT INTO enrollments (opportunity_id, volunteer_id, status)
             VALUES (:opportunity_id, :volunteer_id, 'pending')"
        );
        $statement->execute([
            'opportunity_id' => $opportunityId,
            'volunteer_id'   => $volunteerId,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Every enrollment of one volunteer, for their profile (RF11).
     */
    public function getByVolunteer(int $volunteerId): array
    {
        $statement = $this->connection->prepare(
            self::SELECT_BASE . ' WHERE e.volunteer_id = :volunteer_id' . self::ORDER_BY_STATUS
        );
        $statement->execute(['volunteer_id' => $volunteerId]);

        return $statement->fetchAll();
    }

    /**
     * Every enrollment for one opportunity (RF08, CU06).
     */
    public function getByOpportunity(int $opportunityId): array
    {
        $statement = $this->connection->prepare(
            self::SELECT_BASE . ' WHERE e.opportunity_id = :opportunity_id' . self::ORDER_BY_STATUS
        );
        $statement->execute(['opportunity_id' => $opportunityId]);

        return $statement->fetchAll();
    }

    /**
     * Every enrollment across all opportunities of one organization.
     */
    public function getByOrganization(int $organizationId): array
    {
        $statement = $this->connection->prepare(
            self::SELECT_BASE . ' WHERE o.organization_id = :organization_id' . self::ORDER_BY_STATUS
        );
        $statement->execute(['organization_id' => $organizationId]);

        return $statement->fetchAll();
    }

    /**
     * How many enrollments are waiting for a decision across the whole
     * organization — the badge on its dashboard.
     */
    public function countPending(int $organizationId): int
    {
        $statement = $this->connection->prepare(
            "SELECT COUNT(*)
               FROM enrollments e
               JOIN opportunities o ON o.id = e.opportunity_id
              WHERE o.organization_id = :organization_id AND e.status = 'pending'"
        );
        $statement->execute(['organization_id' => $organizationId]);

        return (int) $statement->fetchColumn();
    }

    /**
     * Accepts, rejects or completes an enrollment (RN06).
     * The caller is responsible for calling
     * OpportunityModel::syncAvailableSlots() afterwards, which applies RN05.
     */
    public function setStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            return false;
        }

        $statement = $this->connection->prepare(
            'UPDATE enrollments SET status = :status WHERE id = :id'
        );

        return $statement->execute([
            'status' => $status,
            'id'     => $id,
        ]);
    }

    /**
     * Counts an already-loaded list by status, for the filter tabs
     * ("Todas (5) · Pendientes (2) · Aceptadas (2)"). Works on the array so the
     * screen does not run a second round of queries.
     *
     * @return array{all:int,pending:int,accepted:int,rejected:int,completed:int}
     */
    public function countByStatus(array $enrollments): array
    {
        $counts = [
            'all'       => count($enrollments),
            'pending'   => 0,
            'accepted'  => 0,
            'rejected'  => 0,
            'completed' => 0,
        ];

        foreach ($enrollments as $enrollment) {
            $status = $enrollment['status'] ?? '';
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
        }

        return $counts;
    }
}
