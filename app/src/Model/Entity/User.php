<?php

namespace App\Model\Entity;

use App\Model\Repository\RepoManager;
use Symplefony\Model\Entity;

class User extends Entity
{
    protected string $password;
    public function getPassword(): string { return $this->password; }
    public function setPassword( int $value ): self
    {
        $this->id = $value;
        return $this; // Permet de "chaîner" les appels aux setters: $toto->setId(2)->setName('toto'), etc.
    }
   
    protected string $email;
    public function getEmail(): string { return $this->email; }
    public function setEmail( int $value ): self
    {
        $this->email = $value;
        return $this;
    }

    protected string $firstname;
    public function getFirstname(): string { return $this->firstname; }
    public function setFirstname( int $value ): self
    {
        $this->firstname = $value;
        return $this;
    }

    protected string $lastname;
    public function getLastname(): string { return $this->lastname; }
    public function setLastname( int $value ): self
    {
        $this->lastname = $value;
        return $this;
    }

    protected string $phone_number;
    public function getPhoneNumber(): string { return $this->phone_number; }
    public function setPhoneNumber( int $value ): self
    {
        $this->phone_number = $value;
        return $this;
    }

    protected string $address_id;
    public function getAddressId(): string { return $this->address_id; }
    public function setAddressId( int $value ): self
    {
        $this->address_id = $value;
        return $this;
    }

    // Liaisons
    protected Address $address;
    public function getAddress(): Address
    {
        if( ! isset( $this->address ) ) {
            $this->address = RepoManager::getRM()->getAddressRepo()->getById( $this->address_id );
        }

        return $this->address;
    }
    
    public function setAddress( Address $address ): self
    {
        $this->address = $address;
        return $this;
    }
}