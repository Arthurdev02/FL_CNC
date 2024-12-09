<?php

namespace App\Model\Repository;

use App\Model\Entity\Category;
use Symplefony\Model\Repository;

class CategoryRepository extends Repository
{
    protected function getTableName(): string { return 'categories'; }
    private function getMappingCar(): string { return 'car_category'; }

    /* Crud: Create */
    public function create( Category $category ): ?Category
    {
        $query = sprintf(
            'INSERT INTO `%s` 
                (`label`) 
                VALUES (:label)',
            $this->getTableName()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return null;
        }

        $success = $sth->execute([
            'label' => $category->getLabel()
        ]);

        // Si echec de l'insertion
        if( ! $success ) {
            return null;
        }

        // Ajout de l'id de l'item créé en base de données
        $category->setId( $this->pdo->lastInsertId() );

        return $category;
    }

    /* cRud: Read tous les items */
    public function getAll(): array
    {
        return $this->readAll( Category::class );
    }

    /* cRud: Read un item par son id */
    public function getById( int $id ): ?Category
    {
        return $this->readById( Category::class, $id );
    }

    /* cRud: Read avec liaison de tous les items reliés à une voiture donnée */
    public function getAllForCar( int $id ): array
    {
        $query = sprintf(
            'SELECT c.* FROM `%1$s` as c 
                JOIN `%2$s` as cc ON cc.category_id = c.id
                WHERE cc.car_id=:id',
            $this->getTableName(),
            $this->getMappingCar()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return [];
        }

        $success = $sth->execute([
            'id' => $id
        ]);

        // Si echec de l'insertion
        if( ! $success ) {
            return [];
        }

        $categories = [];

        while( $category_data = $sth->fetch() ) {
            $categories[] = new Category( $category_data );
        }

        return $categories;
    }

    /* crUd: Update */
    public function update( Category $category ): ?Category
    {
        $query = sprintf(
            'UPDATE `%s` 
                SET `label`=:label
                WHERE id=:id',
            $this->getTableName()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return null;
        }

        $success = $sth->execute([
            'label' => $category->getLabel(),
            'id' => $category->getId()
        ]);

        // Si echec de la mise à jour
        if( ! $success ) {
            return null;
        }

        return $category;
    }

    /* Delete toutes les liaisons de catégories d'une voiture donnée */
    public function detachAllForCar( int $id ): bool
    {
        $query = sprintf(
            'DELETE FROM `%s` WHERE car_id=:id',
            $this->getMappingCar()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return false;
        }

        $success = $sth->execute([ 'id' => $id ]);

        return $success;
    }

    /* Insére les liaisons de catégories demandée pour d'une voiture donnée */
    public function attachForCar( array $ids_categories, int $car_id ): bool
    {
        $query_values = [];
        foreach( $ids_categories as $category_id ) {
            $query_values[] = sprintf( '( %s,%s )', $category_id, $car_id );
        }

        $query = sprintf(
            'INSERT INTO `%s` 
                (`category_id`, `car_id`) 
                VALUES %s',
            $this->getMappingCar(),
            implode( ',', $query_values )
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return false;
        }

        return $sth->execute();
    }
}