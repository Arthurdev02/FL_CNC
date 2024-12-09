<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Model\User;
use App\Middleware\AuthMiddleware;

class AuthController
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // Page de connexion
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Vérifier l'utilisateur
            $user = $this->userRepository->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Lors de la connexion de l'utilisateur
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['role'] = $user->getRole(); // 1 pour Owner, 2 pour User
                // Rediriger selon le rôle
                if ($user['id_role'] == 1) {
                    header('Location: /owner'); // Accès à la page owner
                } else {
                    header('Location: /home'); // Accès à la page utilisateur
                }
                exit;
            } else {
                echo "Identifiants incorrects";
            }
        }

        require __DIR__ . '/../../views/auth/login.phtml';
    }



    // Page d'inscription
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation des données d'inscription
            $email = $_POST['email'];
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password_confirm'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $phone_number = $_POST['phone_number'];

            // Vérification si l'email existe déjà
            $existingUser = $this->userRepository->findByEmail($email);

            if ($existingUser) {
                $error = "L'email est déjà utilisé.";
            } elseif ($password !== $passwordConfirm) {
                $error = "Les mots de passe ne correspondent pas.";
            } else {
                // Hash du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Création du nouvel utilisateur
                $userData = [
                    'email' => $email,
                    'password' => $hashedPassword,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'phone_number' => $phone_number,
                    'id_role' => 2 // Par défaut, un utilisateur est un client (id_role=2)
                ];

                $userId = $this->userRepository->create($userData);

                // Connexion automatique après l'inscription
                session_start();
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 2;

                // Rediriger vers la page d'accueil ou dashboard
                header('Location: /');
                exit;
            }
        }

        // Affichage de la page d'inscription
        require __DIR__ . '/../../views/auth/register.phtml';
    }

    // Déconnexion
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        // Rediriger vers la page d'accueil après la déconnexion
        header('Location: /');
        exit;
    }
}
