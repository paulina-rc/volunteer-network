<?php

class CategoriaModel
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = obtenerConexion();
    }

    public function obtenerTodas(): array
    {
        $consulta = $this->conexion->query('SELECT * FROM categorias ORDER BY id');
        return $consulta->fetchAll();
    }
}
