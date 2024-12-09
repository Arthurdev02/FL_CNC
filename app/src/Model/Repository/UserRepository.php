<?php

namespace App\Model\Repository;

use Symplefony\Model\Repository;

use App\Model\Entity\User;

class UserRepository extends Repository
{
    protected function getTableName(): string { return 'users'; }

    /* Crud: Create */
    public function create( User $user ): ?User
    {
        $query = sprintf(
            'INSERT INTO `%s` 
                (`password`,`email`,`firstname`,`lastname`,`phone_number`,`address_id`) 
                VALUES (:password,:email,:firstname,:lastname,:phone_number,:address_id)',
            $this->getTableName()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return null;
        }

        $success = $sth->execute([
            'password' => $user->getPassword(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'phone_number' => $user->getPhoneNumber(),
            'address_id' => $user->getAddressId()
        ]);

        // Si echec de l'insertion
        if( ! $success ) {
            return null;
        }

        // Ajout de l'id de l'item créé en base de données
        $user->setId( $this->pdo->lastInsertId() );

        return $user;
    }

    /* cRud: Read tous les items */
    public function getAll(): array
    {
        return $this->readAll( User::class );
    }

    /* cRud: Read un item par son id */
    public function getById( int $id ): ?User
    {
        return $this->readById( User::class, $id );
    }

    /* crUd: Update */
    public function update( User $user ): ?User
    {
        $query = sprintf(
            'UPDATE `%s` 
                SET
                    `password`=:password,
                    `email`=:email,
                    `firstname`=:firstname,
                    `lastname`=:lastname,
                    `phone_number`=:phone_number
                WHERE id=:id',
            $this->getTableName()
        );

        $sth = $this->pdo->prepare( $query );

        // Si la préparation échoue
        if( ! $sth ) {
            return null;
        }

        $success = $sth->execute([
            'password' => $user->getPassword(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'phone_number' => $user->getPhoneNumber(),
            'id' => $user->getId()
        ]);

        // Si echec de la mise à jour
        if( ! $success ) {
            return null;
        }

        return $user;
    }

    /* cruD: Delete */
    public function deleteOne(int $id): bool
    {
        // On récupère l'user pour pouvoir connaîre son address_id 
        $user = $this->getById( $id );

        if( is_null( $user ) ) {
            return false;
        }

        $addressRepo = RepoManager::getRM()->getAddressRepo();
        // On récupère l'addresse
        $address = $addressRepo->getById( $user->getAddressId() );

        if( is_null( $address ) ) {
            return false;
        }

        // On supprime l'user
        $success = parent::deleteOne( $id );

        // Si cela a fonctionné, on supprimé l'addresse liée
        if( $success ) {
            $success = $addressRepo->deleteOne( $address->getId());
        }

        return $success;
    }
}