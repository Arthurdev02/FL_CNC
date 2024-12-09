<?php

namespace App\Model;

class User
{
    private $id;
    private $email;
    private $password;
    private $lastname;
    private $firstname;
    private $phone_number;
    private $id_role; // Nouveau champ pour le rôle

    // Getters and setters here...

    public function isOwner()
    {
        return $this->id_role === 1;
    }

    public function isUser()
    {
        return $this->id_role === 2;
    }
}
