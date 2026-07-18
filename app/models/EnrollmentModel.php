<?php

class EnrollmentModel
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    // Step 6 of the development plan:
    // - create(int $volunteerId, int $opportunityId): int   // RF07, RN04, CU05
    // - listByOpportunity(int $opportunityId): array          // RF08, CU06
    // - listByVolunteer(int $volunteerId): array               // RF11
    // - updateStatus(int $id, string $status): bool            // RN06
}
