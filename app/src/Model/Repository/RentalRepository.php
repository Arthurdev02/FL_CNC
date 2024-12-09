<?php

namespace App\Repository;

use App\Database;

class RentalRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    public function findByUser($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM rentals r
                                JOIN accommodations a ON r.id_accommodation = a.id
                                WHERE r.id_customer = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function findByAccommodation($accommodation_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM rentals r
                                JOIN users u ON r.id_customer = u.id
                                WHERE r.id_accommodation = :accommodation_id");
        $stmt->bindParam(':accommodation_id', $accommodation_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Créer une réservation
    public function create($id_accommodation, $id_customer, $date_from, $date_to)
    {
        $stmt = $this->db->prepare("INSERT INTO rentals (id_accommodation, id_customer, date_from, date_to) VALUES (:id_accommodation, :id_customer, :date_from, :date_to)");
        $stmt->bindParam(':id_accommodation', $id_accommodation);
        $stmt->bindParam(':id_customer', $id_customer);
        $stmt->bindParam(':date_from', $date_from);
        $stmt->bindParam(':date_to', $date_to);

        $stmt->execute();
    }
}
