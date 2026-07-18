<?php
require_once __DIR__ . '/../helpers/vista.php';

class OportunidadController
{
    public function home(): void
    {
        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->obtenerTodas();
        require __DIR__ . '/../views/home.php';
    }

    public function buscar(): void
    {
        renderPendiente('Buscar oportunidades', 'Se implementa en el Paso 5 del plan de desarrollo (RF06).');
    }

    public function ver(): void
    {
        renderPendiente('Detalle de oportunidad', 'Se implementa en los Pasos 4-5 del plan de desarrollo.');
    }

    public function publicar(): void
    {
        renderPendiente('Publicar oportunidad', 'Se implementa en el Paso 4 del plan de desarrollo (RF05, RN02, RN03).');
    }

    public function editar(): void
    {
        renderPendiente('Editar oportunidad', 'Se implementa en el Paso 4 del plan de desarrollo (RN09).');
    }

    public function cerrar(): void
    {
        renderPendiente('Cerrar oportunidad', 'Se implementa en el Paso 4 del plan de desarrollo (RN05, RN09).');
    }
}
