<?php

class OportunidadModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    // Paso 4/5 del plan de desarrollo:
    // - crear(array $datos): int                     // RF05, RN03
    // - buscarPorId(int $id): ?array
    // - buscarConFiltros(array $filtros): array       // RF06
    // - editar(int $id, array $datos): bool           // RN09
    // - cerrar(int $id): bool                         // RN05, RN09
    // - decrementarCupo(int $id): bool                // RN05
}
