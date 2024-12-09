<?php

namespace App\Controller;

use App\Model\Repository\UserRepository;

class AuthController
{
    private $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->repository->findByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                session_start();
                $_SESSION['user_id'] = $user->getId();
                header('Location: /');
                exit;
            } else {
                $error = "Identifiants incorrects.";
            }
        }

        require __DIR__ . '/../../views/user/login.phtml';
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }
}
