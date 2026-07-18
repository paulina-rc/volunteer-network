<?php

class UsuarioModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    // Paso 2 del plan de desarrollo:
    // - crearUsuario(string $correo, string $contrasenaHash, string $rol): int
    // - buscarPorCorreo(string $correo): ?array
    // - verificarCredenciales(string $correo, string $contrasena): ?array
}
