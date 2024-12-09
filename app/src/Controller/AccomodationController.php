<?php

namespace App\Controller;

use App\Model\Repository\AccommodationRepository;

class AccommodationController
{
    private $repository;

    public function __construct()
    {
        $this->repository = new AccommodationRepository();
    }

    // Affiche la liste des logements
    public function list()
    {
        $accommodations = $this->repository->findAll();
        require __DIR__ . '/../../views/accommodation/list.phtml';
    }

    // Affiche les détails d’un logement
    public function details(int $id)
    {
        $accommodation = $this->repository->findById($id);
        if (!$accommodation) {
            http_response_code(404);
            require __DIR__ . '/../../views/_errors/404.phtml';
            return;
        }
        require __DIR__ . '/../../views/accommodation/details.phtml';
    }

    // Formulaire d'ajout d'un logement
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'price' => $_POST['price'],
                'surface' => $_POST['surface'],
                'description' => $_POST['description'],
                'capacity' => $_POST['capacity'],
                'id_owner' => $_POST['id_owner'],
                'id_type' => $_POST['id_type'],
                'id_address' => $_POST['id_address']
            ];
            $this->repository->create($data);
            header('Location: /accommodations');
            exit;
        }
        require __DIR__ . '/../../views/accommodation/create.phtml';
    }
}
