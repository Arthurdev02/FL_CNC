<?php

namespace App\Controller;

use App\Model\Rental;
use App\Repository\RentalRepository;
use App\Middleware\AuthMiddleware;

class RentalController
{
    private $repository;

    public function __construct()
    {
        $this->repository = new RentalRepository();
    }

    public function create()
    {
        AuthMiddleware::checkAuth(); // Vérifie si l'utilisateur est connecté

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rentalData = [
                'id_accommodation' => $_POST['id_accommodation'],
                'id_customer' => $_SESSION['user_id'],
                'date_from' => $_POST['date_from'],
                'date_to' => $_POST['date_to']
            ];

            $this->repository->create($rentalData);
            header('Location: /rentals');
            exit;
        }

        // Si la méthode est GET, afficher la page de réservation
        require __DIR__ . '/../../views/rental/create.phtml';
    }
}
