<?php

namespace App\Model\Repository;

use Symplefony\Model\Repository;

use App\Model\Entity\Address;

class AddressRepository extends Repository
{
    protected function getTableName(): string { return 'addresses'; }

    /* Crud: Create */
    public function create( Address $address ): ?Address
    {
        $query = sprintf(
            'INSERT INTO `%s` 
                (`city`,`country`) 
                VALUES (:city,:country)',
            $this->getTableName()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return null;
        }

        $success = $sth->execute([
            'city' => $address->getCity(),
            'country' => $address->getCountry()
        ]);

        // Si echec de l'insertion
        if( ! $success ) {
            return null;
        }

        // Ajout de l'id de l'item créé en base de données
        $address->setId( $this->pdo->lastInsertId() );

        return $address;
    }

    /* cRud: Read tous les items */
    public function getAll(): array
    {
        return $this->readAll( Address::class );
    }

    /* cRud: Read un item par son id */
    public function getById( int $id ): ?Address
    {
        return $this->readById( Address::class, $id );
    }

    /* crUd: Update */
    public function update( Address $address ): ?Address
    {
        $query = sprintf(
            'UPDATE `%s` 
                SET
                    `city`=:city,
                    `country`=:country
                WHERE id=:id',
            $this->getTableName()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return null;
        }

        $success = $sth->execute([
            'city' => $address->getCity(),
            'country' => $address->getCountry(),
            'id' => $address->getId()
        ]);

        // Si echec de la mise à jour
        if( ! $success ) {
            return null;
        }

        return $address;
    }
}