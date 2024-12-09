<?php

namespace App\Controller;

use App\Model\Repository\UserRepository;
use App\Model\User;
use App\Middleware\AuthMiddleware;
use PDO;

class AuthController
{
    private $pdo;

    public function __construct()
    {
        // Connexion PDO à la base de données
        $this->pdo = new PDO('mysql:host=localhost;dbname=nom_de_votre_db', 'utilisateur', 'mot_de_passe');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Page de connexion
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Exécution de la requête SQL pour vérifier l'utilisateur
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['id_role']; // Stocke le rôle dans la session

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
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                $error = "L'email est déjà utilisé.";
            } elseif ($password !== $passwordConfirm) {
                $error = "Les mots de passe ne correspondent pas.";
            } else {
                // Hash du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Création du nouvel utilisateur
                $stmt = $this->pdo->prepare('
                    INSERT INTO users (email, password, firstname, lastname, phone_number, id_role)
                    VALUES (:email, :password, :firstname, :lastname, :phone_number, :id_role)
                ');

                // Exécution de la requête pour ajouter l'utilisateur
                $stmt->execute([
                    'email' => $email,
                    'password' => $hashedPassword,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'phone_number' => $phone_number,
                    'id_role' => 2 // Par défaut, un utilisateur est un client (id_role=2)
                ]);

                // Récupérer l'ID du nouvel utilisateur
                $userId = $this->pdo->lastInsertId();

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
