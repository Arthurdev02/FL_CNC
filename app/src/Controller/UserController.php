<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Middleware\AuthMiddleware;

class UserController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    // Afficher le formulaire d'inscription
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $data = [
                'email' => $_POST['email'],
                'password' => $passwordHash,
                'lastname' => $_POST['lastname'],
                'firstname' => $_POST['firstname'],
                'phone_number' => $_POST['phone_number'],
                'id_role' => 2 // L'id_role pour un utilisateur normal (par exemple 2 pour un utilisateur classique)
            ];

            // Créer l'utilisateur dans la base de données
            $this->repository->create($data);
            header('Location: /login');
            exit;
        }

        require __DIR__ . '/../../views/user/register.phtml'; // Afficher le formulaire d'inscription
    }

    // Afficher le formulaire de connexion
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Récupérer l'utilisateur de la base de données par email
            $user = $this->repository->findByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                // L'utilisateur existe et les identifiants sont corrects
                session_start();
                $_SESSION['user_id'] = $user->getId();
                header('Location: /');
                exit;
            } else {
                // Erreur d'authentification
                $error = "Identifiants incorrects.";
            }
        }

        require __DIR__ . '/../../views/user/login.phtml'; // Afficher le formulaire de connexion
    }

    // Déconnexion de l'utilisateur
    public function logout()
    {
        session_start();
        session_destroy(); // Détruire la session pour déconnecter l'utilisateur
        header('Location: /login'); // Rediriger vers la page de connexion
        exit;
    }

    // Afficher le profil de l'utilisateur (par exemple, modification de profil)
    public function profile()
    {
        // Vérifier que l'utilisateur est connecté
        AuthMiddleware::checkAuth();

        $userId = $_SESSION['user_id'];
        $user = $this->repository->findById($userId);

        require __DIR__ . '/../../views/user/profile.phtml'; // Afficher la page de profil
    }

    // Modifier les informations du profil
    public function updateProfile()
    {
        // Vérifier que l'utilisateur est connecté
        AuthMiddleware::checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $data = [
                'email' => $_POST['email'],
                'lastname' => $_POST['lastname'],
                'firstname' => $_POST['firstname'],
                'phone_number' => $_POST['phone_number']
            ];

            // Mettre à jour les informations de l'utilisateur dans la base de données
            $this->repository->update($userId, $data);
            header('Location: /user/profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->repository->findById($userId);
        require __DIR__ . '/../../views/user/edit-profile.phtml'; // Afficher le formulaire d'édition du profil
    }
}
