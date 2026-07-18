<?php

class OrganizacionModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    // Paso 2/3 del plan de desarrollo:
    // - crearOrganizacion(int $idUsuario, string $nombre): int
    // - buscarPorIdUsuario(int $idUsuario): ?array
    // - actualizarPerfil(int $id, array $datos): bool
    // - marcarPerfilCompleto(int $id): void   // controla RN02
}
