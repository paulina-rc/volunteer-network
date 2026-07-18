<?php

class OrganizacionModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    public function crearOrganizacion(int $idUsuario, string $nombre): int
    {
        $consulta = $this->conexion->prepare(
            'INSERT INTO organizaciones (id_usuario, nombre) VALUES (:id_usuario, :nombre)'
        );
        $consulta->execute([
            'id_usuario' => $idUsuario,
            'nombre'     => $nombre,
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    public function buscarPorIdUsuario(int $idUsuario): ?array
    {
        $consulta = $this->conexion->prepare('SELECT * FROM organizaciones WHERE id_usuario = :id_usuario');
        $consulta->execute(['id_usuario' => $idUsuario]);
        $organizacion = $consulta->fetch();

        return $organizacion === false ? null : $organizacion;
    }

    // Paso 3 del plan de desarrollo:
    // - actualizarPerfil(int $id, array $datos): bool
    // - marcarPerfilCompleto(int $id): void   // controla RN02
}
