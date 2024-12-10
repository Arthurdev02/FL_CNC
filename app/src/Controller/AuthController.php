<?php

class AuthController
{
    /** Affiche le formulaire de connexion*/
    public function login()
    {
        echo "Affichage du formulaire de connexion";
    }

    /** Affiche le formulaire d'inscription*/
    public function register()
    {
        echo "Affichage du formulaire d'inscription";
    }

    /** Gère les données envoyées lors de la connexion*/
    public function handleLogin($email, $password)
    {
        global $pdo;
        // Vérifier si l'utilisateur existe dans la base de données
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Connexion réussie
            echo "Connexion réussie. Bienvenue " . $user['firstname'] . "!";
        } else {
            // Connexion échouée
            echo "Email ou mot de passe incorrect.";
        }
    }

    /** Gère les données envoyées lors de l'inscription*/
    public function handleRegister($data)
    {
        global $pdo; // Utilisation de la connexion PDO définie dans db_connect.php

        // Hash du mot de passe pour des raisons de sécurité
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insertion dans la base de données
        $stmt = $pdo->prepare("INSERT INTO users (email, password, firstname, lastname, phone_number, id_role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['email'],
            $hashedPassword,
            $data['firstname'],
            $data['lastname'],
            $data['phone_number'],
            $data['id_role'] // 1 pour "Annonceur", 2 pour "Utilisateur standard"
        ]);

        echo "Inscription réussie ! Vous pouvez maintenant vous connecter.";
    }
}
