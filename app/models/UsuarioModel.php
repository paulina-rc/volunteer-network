<?php

class UsuarioModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    public function crearUsuario(string $correo, string $contrasenaHash, string $rol): int
    {
        $consulta = $this->conexion->prepare(
            'INSERT INTO usuarios (correo, contrasena_hash, rol) VALUES (:correo, :contrasena_hash, :rol)'
        );
        $consulta->execute([
            'correo'          => $correo,
            'contrasena_hash' => $contrasenaHash,
            'rol'             => $rol,
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $consulta = $this->conexion->prepare('SELECT * FROM usuarios WHERE correo = :correo');
        $consulta->execute(['correo' => $correo]);
        $usuario = $consulta->fetch();

        return $usuario === false ? null : $usuario;
    }

    public function verificarCredenciales(string $correo, string $contrasena): ?array
    {
        $usuario = $this->buscarPorCorreo($correo);

        if ($usuario === null || !$usuario['esta_activo']) {
            return null;
        }

        if (!password_verify($contrasena, $usuario['contrasena_hash'])) {
            return null;
        }

        return $usuario;
    }
}
