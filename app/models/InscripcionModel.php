<?php

class InscripcionModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    // Paso 6 del plan de desarrollo:
    // - crear(int $idVoluntario, int $idOportunidad): int   // RF07, RN04, CU05
    // - listarPorOportunidad(int $idOportunidad): array      // RF08, CU06
    // - listarPorVoluntario(int $idVoluntario): array        // RF11
    // - actualizarEstado(int $id, string $estado): bool      // RN06
}
