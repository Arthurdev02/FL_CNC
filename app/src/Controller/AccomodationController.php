<?php

class AccommodationModel
{

    private $pdo;

    // Constructeur : récupération de l'objet PDO
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Fonction pour ajouter une nouvelle annonce
    public function addAccommodation($data)
    {
        // Préparation de la requête SQL pour insérer une nouvelle annonce
        $stmt = $this->pdo->prepare("
            INSERT INTO accommodations (price, surface, description, capacity, id_owner, id_type, id_address)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        // Exécution de la requête avec les données passées
        $stmt->execute([
            $data['price'],
            $data['surface'],
            $data['description'],
            $data['capacity'],
            $data['id_owner'],
            $data['id_type'],
            $data['id_address']
        ]);

        // Retourner l'ID de la dernière annonce insérée
        return $this->pdo->lastInsertId();
    }

    // Fonction pour obtenir toutes les annonces d'un utilisateur
    public function getUserAccommodations($userId)
    {
        // Récupérer toutes les annonces d'un propriétaire (annonceur)
        $stmt = $this->pdo->prepare("SELECT * FROM accommodations WHERE id_owner = ?");
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }
}
