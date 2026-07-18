<?php

class HabilidadModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    // Paso 3 del plan de desarrollo:
    // - obtenerTodas(): array
    // - crearSiNoExiste(string $nombre): int
}
