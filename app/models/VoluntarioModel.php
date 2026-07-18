<?php

class VoluntarioModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    // Paso 2/3 del plan de desarrollo:
    // - crearVoluntario(int $idUsuario, string $nombreCompleto): int
    // - buscarPorIdUsuario(int $idUsuario): ?array
    // - actualizarPerfil(int $id, array $datos): bool
    // - guardarIntereses(int $idVoluntario, array $idsCategorias): void
    // - guardarHabilidades(int $idVoluntario, array $idsHabilidades): void
}
