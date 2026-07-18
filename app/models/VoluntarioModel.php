<?php

class VoluntarioModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    public function crearVoluntario(int $idUsuario, string $nombreCompleto): int
    {
        $consulta = $this->conexion->prepare(
            'INSERT INTO voluntarios (id_usuario, nombre_completo) VALUES (:id_usuario, :nombre_completo)'
        );
        $consulta->execute([
            'id_usuario'      => $idUsuario,
            'nombre_completo' => $nombreCompleto,
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    public function buscarPorIdUsuario(int $idUsuario): ?array
    {
        $consulta = $this->conexion->prepare('SELECT * FROM voluntarios WHERE id_usuario = :id_usuario');
        $consulta->execute(['id_usuario' => $idUsuario]);
        $voluntario = $consulta->fetch();

        return $voluntario === false ? null : $voluntario;
    }

    // Paso 3 del plan de desarrollo:
    // - actualizarPerfil(int $id, array $datos): bool
    // - guardarIntereses(int $idVoluntario, array $idsCategorias): void
    // - guardarHabilidades(int $idVoluntario, array $idsHabilidades): void
}
