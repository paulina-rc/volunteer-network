<?php

class OpportunityModel
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = getConnection();
    }

    // Step 4/5 of the development plan:
    // - create(array $data): int                       // RF05, RN03
    // - findById(int $id): ?array
    // - searchWithFilters(array $filters): array        // RF06
    // - edit(int $id, array $data): bool                // RN09
    // - close(int $id): bool                            // RN05, RN09
    // - decrementSlot(int $id): bool                    // RN05
}
