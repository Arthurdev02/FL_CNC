<?php

namespace App\Model\Repository;

use App\Model\Entity\Car;
use Symplefony\Model\Repository;

class CarRepository extends Repository
{
    protected function getTableName(): string { return 'cars'; }

    /* Crud: Create */
    public function create( Car $car ): ?Car
    {
        $query = sprintf(
            'INSERT INTO `%s` 
                (`label`,`seats`,`energy`,`plate_number`,`price_day`,`price_distance`,`image`) 
                VALUES (:label,:seats,:energy,:plate_number,:price_day,:price_distance,:image)',
            $this->getTableName()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return null;
        }

        $success = $sth->execute([
            'label' => $car->getLabel(),
            'seats' => $car->getSeats(),
            'energy' => $car->getEnergy(),
            'plate_number' => $car->getPlateNumber(),
            'price_day' => $car->getPriceDay(),
            'price_distance' => $car->getPriceDistance(),
            'image' => $car->getImage()
        ]);

        // Si echec de l'insertion
        if( ! $success ) {
            return null;
        }

        // Ajout de l'id de l'item créé en base de données
        $car->setId( $this->pdo->lastInsertId() );

        return $car;
    }

    /* cRud: Read tous les items */
    public function getAll(): array
    {
        return $this->readAll( Car::class );
    }

    /* cRud: Read un item par son id */
    public function getById( int $id ): ?Car
    {
        return $this->readById( Car::class, $id );
    }

    /* crUd: Update */
    public function update( Car $car ): ?Car
    {
        $query = sprintf(
            'UPDATE `%s` 
                SET
                    `label`=:label,
                    `seats`=:seats,
                    `energy`=:energy,
                    `plate_number`=:plate_number,
                    `price_day`=:price_day,
                    `price_distance`=:price_distance,
                    `image`=:image
                WHERE id=:id',
            $this->getTableName()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return null;
        }

        $success = $sth->execute([
            'label' => $car->getLabel(),
            'seats' => $car->getSeats(),
            'energy' => $car->getEnergy(),
            'plate_number' => $car->getPlateNumber(),
            'price_day' => $car->getPriceDay(),
            'price_distance' => $car->getPriceDistance(),
            'image' => $car->getImage(),
            'id' => $car->getId()
        ]);

        // Si echec de la mise à jour
        if( ! $success ) {
            return null;
        }

        return $car;
    }

    /* cruD: Delete */
    public function deleteOne(int $id): bool
    {
        // On supprime d'abord toutes les liaisons avec les catégories
        $success = RepoManager::getRM()->getCategoryRepo()->detachAllForCar( $id );

        // Si cela a fonctionné on invoke la méthode deleteOne parente
        if( $success) {
            $success = parent::deleteOne( $id );
        }

        return $success;
    }
}