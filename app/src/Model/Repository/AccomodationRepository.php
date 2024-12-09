<?php

namespace App\Repository;

use App\Database;
use App\Model\Accommodation;

class AccommodationRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Vérification de la disponibilité d'un logement
    public function isAvailable($id_accommodation, $date_from, $date_to)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM rentals 
            WHERE id_accommodation = :id_accommodation 
            AND ((date_from <= :date_to AND date_to >= :date_from))"
        );

        $stmt->bindParam(':id_accommodation', $id_accommodation);
        $stmt->bindParam(':date_from', $date_from);
        $stmt->bindParam(':date_to', $date_to);

        $stmt->execute();

        // Si aucune réservation n'a été trouvée, le logement est disponible
        return $stmt->rowCount() == 0;
    }
}
