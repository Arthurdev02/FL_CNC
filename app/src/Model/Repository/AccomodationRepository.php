<?php

namespace App\Model\Repository;

use PDO;
use App\Model\Entity\Accommodation;

class AccommodationRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=your_database', 'root', '');
    }

    // Récupère tous les logements
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM accommodations');
        return $stmt->fetchAll(PDO::FETCH_CLASS, Accommodation::class);
    }

    // Récupère un logement par son ID
    public function findById(int $id): ?Accommodation
    {
        $stmt = $this->pdo->prepare('SELECT * FROM accommodations WHERE id = ?');
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Accommodation::class);
        return $stmt->fetch() ?: null;
    }

    // Ajoute un logement
    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO accommodations (price, surface, description, capacity, id_owner, id_type, id_address)
            VALUES (:price, :surface, :description, :capacity, :id_owner, :id_type, :id_address)
        ');
        $stmt->execute($data);
    }
}
