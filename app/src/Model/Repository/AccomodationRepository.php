<?php

namespace App\Repository;

use App\Model\Accommodation;
use PDO;

class AccommodationRepository
{

    private static $connection;

    // Méthode pour obtenir la connexion à la base de données (Singleton)
    public static function getConnection()
    {
        if (self::$connection === null) {
            self::$connection = new PDO(
                'mysql:host=localhost;dbname=your_database',
                'username',
                'password',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$connection;
    }

    // Fonction pour récupérer tous les logements
    public function findAll()
    {
        $query = "SELECT * FROM accommodations";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Fonction pour récupérer un logement par ID
    public function findById($id)
    {
        $query = "SELECT * FROM accommodations WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    // Fonction pour ajouter un nouveau logement
    public function create($data)
    {
        $query = "INSERT INTO accommodations (price, description, capacity, city, type, id_address) 
                  VALUES (:price, :description, :capacity, :city, :type, :id_address)";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute($data);
    }

    // Fonction pour mettre à jour un logement
    public function update($data)
    {
        $query = "UPDATE accommodations SET price = :price, description = :description, 
                  capacity = :capacity, city = :city, type = :type, id_address = :id_address 
                  WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute($data);
    }

    // Fonction pour supprimer un logement
    public function delete($id)
    {
        $query = "DELETE FROM accommodations WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute(['id' => $id]);
    }

    // Fonction pour effectuer une recherche avec des filtres
    public function search($filters)
    {
        $query = "SELECT * FROM accommodations WHERE 1";

        if ($filters['city']) {
            $query .= " AND city LIKE :city";
        }
        if ($filters['type']) {
            $query .= " AND type LIKE :type";
        }
        if ($filters['price_max']) {
            $query .= " AND price <= :price_max";
        }

        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute($filters);

        return $stmt->fetchAll();
    }
}
