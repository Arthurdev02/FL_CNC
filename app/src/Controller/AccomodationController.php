<?php

namespace App\Controller;

use App\Repository\AccommodationRepository;
use App\Repository\RentalRepository;
use App\Middleware\AuthMiddleware;
use PDO;

class AccommodationController
{
    private $db;
    private $rentalRepository;

    public function __construct()
    {
        // Connexion à la base de données
        $this->db = new PDO('mysql:host=localhost;dbname=fl_cnc', 'root', ''); // à adapter avec tes paramètres
        $this->rentalRepository = new RentalRepository();
    }

    // Afficher tous les logements (sans findAll())
    public function index()
    {
        $stmt = $this->db->prepare("SELECT * FROM accommodations");
        $stmt->execute();
        $accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/accommodation/list.phtml';
    }

    // Afficher les détails d'un logement spécifique (sans findById())
    public function show($id)
    {
        // Récupérer le logement par son ID
        $stmt = $this->db->prepare("SELECT * FROM accommodations WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $accommodation = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification si le logement existe
        if (!$accommodation) {
            header('Location: /404');
            exit;
        }

        // Afficher les réservations pour ce logement (si l'utilisateur est un Owner)
        $reservations = [];
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            // Vérifier si l'utilisateur est un Owner du logement
            $ownerStmt = $this->db->prepare("SELECT * FROM accommodations WHERE id = :accommodation_id AND id_owner = :user_id");
            $ownerStmt->bindParam(':accommodation_id', $id, PDO::PARAM_INT);
            $ownerStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $ownerStmt->execute();

            if ($ownerStmt->fetch(PDO::FETCH_ASSOC)) {
                // Si c'est un owner, récupérer les réservations
                $reservationsStmt = $this->db->prepare("SELECT * FROM rentals r JOIN users u ON r.id_customer = u.id WHERE r.id_accommodation = :accommodation_id");
                $reservationsStmt->bindParam(':accommodation_id', $id, PDO::PARAM_INT);
                $reservationsStmt->execute();
                $reservations = $reservationsStmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        require __DIR__ . '/../../views/accommodation/details.phtml';
    }

    // Créer un logement (uniquement pour les Owners)
    public function create()
    {
        AuthMiddleware::checkOwner();  // Vérifie si l'utilisateur est un Owner

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $price = $_POST['price'];
            $surface = $_POST['surface'];
            $description = $_POST['description'];
            $capacity = $_POST['capacity'];
            $id_owner = $_SESSION['user_id']; // L'owner est l'utilisateur connecté

            // Créer un logement
            $stmt = $this->db->prepare("INSERT INTO accommodations (price, surface, description, capacity, id_owner) 
                                        VALUES (:price, :surface, :description, :capacity, :id_owner)");

            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':surface', $surface);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':capacity', $capacity);
            $stmt->bindParam(':id_owner', $id_owner);

            $stmt->execute();

            header('Location: /accommodations');
            exit;
        }

        require __DIR__ . '/../../views/accommodation/create.phtml';
    }

    // Mettre à jour un logement (uniquement pour les Owners)
    public function edit($id)
    {
        AuthMiddleware::checkOwner();  // Vérifie si l'utilisateur est un Owner

        // Vérifier que l'utilisateur est bien l'owner du logement
        $stmt = $this->db->prepare("SELECT * FROM accommodations WHERE id = :id AND id_owner = :user_id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $accommodation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$accommodation) {
            header('Location: /404');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $price = $_POST['price'];
            $surface = $_POST['surface'];
            $description = $_POST['description'];
            $capacity = $_POST['capacity'];

            // Mettre à jour le logement
            $updateStmt = $this->db->prepare("UPDATE accommodations SET price = :price, surface = :surface, 
                                              description = :description, capacity = :capacity WHERE id = :id");

            $updateStmt->bindParam(':price', $price);
            $updateStmt->bindParam(':surface', $surface);
            $updateStmt->bindParam(':description', $description);
            $updateStmt->bindParam(':capacity', $capacity);
            $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);

            $updateStmt->execute();

            header('Location: /accommodation/' . $id);
            exit;
        }

        require __DIR__ . '/../../views/accommodation/edit.phtml';
    }

    // Supprimer un logement (uniquement pour les Owners)
    public function delete($id)
    {
        AuthMiddleware::checkOwner();  // Vérifie si l'utilisateur est un Owner

        // Vérifier que l'utilisateur est bien l'owner du logement
        $stmt = $this->db->prepare("SELECT * FROM accommodations WHERE id = :id AND id_owner = :user_id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $accommodation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$accommodation) {
            header('Location: /404');
            exit;
        }

        // Supprimer le logement
        $deleteStmt = $this->db->prepare("DELETE FROM accommodations WHERE id = :id");
        $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteStmt->execute();

        header('Location: /accommodations');
        exit;
    }

    // Créer une réservation pour un logement
    public function createReservation()
    {
        AuthMiddleware::checkUser();  // Vérifie que l'utilisateur est un User

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_accommodation = $_POST['id_accommodation'];
            $date_from = $_POST['date_from'];
            $date_to = $_POST['date_to'];
            $user_id = $_SESSION['user_id'];

            // Vérifier la disponibilité du logement
            $stmt = $this->db->prepare("SELECT * FROM rentals WHERE id_accommodation = :id_accommodation AND 
                                        ((date_from BETWEEN :date_from AND :date_to) OR (date_to BETWEEN :date_from AND :date_to))");
            $stmt->bindParam(':id_accommodation', $id_accommodation, PDO::PARAM_INT);
            $stmt->bindParam(':date_from', $date_from);
            $stmt->bindParam(':date_to', $date_to);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                // Si le logement est disponible
                $this->rentalRepository->create($id_accommodation, $user_id, $date_from, $date_to);
                header('Location: /rentals');
                exit;
            } else {
                echo "Ce logement n'est pas disponible pour ces dates.";
            }
        }
    }
}
