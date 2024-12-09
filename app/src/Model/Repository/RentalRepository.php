<?php

namespace App\Repository;

use App\Model\Rental;
use PDO;

class RentalRepository
{
    public function create($data)
    {
        $query = "INSERT INTO rentals (id_accommodation, id_customer, date_from, date_to) 
                  VALUES (:id_accommodation, :id_customer, :date_from, :date_to)";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute($data);
    }

    public function findByAccommodation($id_accommodation)
    {
        $query = "SELECT * FROM rentals WHERE id_accommodation = :id_accommodation";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute(['id_accommodation' => $id_accommodation]);

        return $stmt->fetchAll();
    }
}
