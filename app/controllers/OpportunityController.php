<?php
require_once __DIR__ . '/../helpers/view.php';

class OpportunityController
{
    public function home(): void
    {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAll();
        require __DIR__ . '/../views/home.php';
    }

    public function search(): void
    {
        renderPending('Buscar oportunidades', 'Se implementa en el Paso 5 del plan de desarrollo (RF06).');
    }

    public function view(): void
    {
        renderPending('Detalle de oportunidad', 'Se implementa en los Pasos 4-5 del plan de desarrollo.');
    }

    public function publish(): void
    {
        renderPending('Publicar oportunidad', 'Se implementa en el Paso 4 del plan de desarrollo (RF05, RN02, RN03).');
    }

    public function edit(): void
    {
        renderPending('Editar oportunidad', 'Se implementa en el Paso 4 del plan de desarrollo (RN09).');
    }

    public function close(): void
    {
        renderPending('Cerrar oportunidad', 'Se implementa en el Paso 4 del plan de desarrollo (RN05, RN09).');
    }
}
