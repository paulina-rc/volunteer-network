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
        $categories = (new CategoryModel())->getAll();
        require __DIR__ . '/../views/opportunity_list.php';
    }

    public function view(): void
    {
        require __DIR__ . '/../views/opportunity_detail.php';
    }

    public function publish(): void
    {
        $categories = (new CategoryModel())->getAll();
        require __DIR__ . '/../views/opportunity_publish.php';
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
