<?php

namespace App\Controller;

use App\Repository\AccommodationRepository;
use App\Repository\RentalRepository;
use App\Middleware\AuthMiddleware;
use App\Model\Rental;

class RentalController
{
    private $accommodationRepository;
    private $rentalRepository;

    public function __construct()
    {
        $this->accommodationRepository = new AccommodationRepository();
        $this->rentalRepository = new RentalRepository();
    }
    public function list()
    {
        AuthMiddleware::checkUser();  // Vérifie que l'utilisateur est un User

        $rentals = $this->rentalRepository->findByUser($_SESSION['user_id']);
        require __DIR__ . '/../../views/rental/list.phtml';
    }

    // Créer une réservation
    public function create()
    {
        AuthMiddleware::checkUser();  // Vérifier que l'utilisateur est un User

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_accommodation = $_POST['id_accommodation'];
            $date_from = $_POST['date_from'];
            $date_to = $_POST['date_to'];
            $user_id = $_SESSION['user_id'];

            // Vérification de la disponibilité
            if ($this->accommodationRepository->isAvailable($id_accommodation, $date_from, $date_to)) {
                $this->rentalRepository->create($id_accommodation, $user_id, $date_from, $date_to);
                header('Location: /rentals');
                exit;
            } else {
                // Si le logement n'est pas disponible
                echo "Ce logement n'est pas disponible pour ces dates.";
            }
        }
    }
}
