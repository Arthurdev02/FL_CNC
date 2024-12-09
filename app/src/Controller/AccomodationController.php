<?php

namespace App\Controller;

use App\Model\Accommodation;
use App\Repository\AccommodationRepository;
use App\Middleware\AuthMiddleware;

class AccommodationController
{
    private $repository;

    public function __construct()
    {
        $this->repository = new AccommodationRepository();
    }

    public function create()
    {
        AuthMiddleware::checkOwner();  // Vérifie que l'utilisateur est un Owner

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accommodationData = [
                'price' => $_POST['price'],
                'surface' => $_POST['surface'],
                'description' => $_POST['description'],
                'capacity' => $_POST['capacity'],
                'id_owner' => $_SESSION['user_id'], // Associer l'owner
                'id_type' => $_POST['id_type'],
                'id_address' => $_POST['id_address']
            ];

            $this->repository->create($accommodationData);
            header('Location: /accommodations');
            exit;
        }

        require __DIR__ . '/../../views/accommodation/create.phtml';
    }

    // Autres méthodes pour gérer les logements...
}
