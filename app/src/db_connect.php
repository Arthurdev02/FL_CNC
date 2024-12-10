<?php
// Charger les variables d'environnement depuis le fichier .env
// Cela permet de centraliser les paramètres de connexion à la base de données
require_once __DIR__ . '/../../vendor/autoload.php'; // Autoloader de Composer
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // Définition du chemin vers .env
$dotenv->load(); // Chargement des variables d'environnement

// Récupérer les paramètres de connexion depuis les variables d'environnement
$host = $_ENV['DB_HOST'];       // Adresse du serveur de base de données
$dbname = $_ENV['DB_NAME'];     // Nom de la base de données
$user = $_ENV['DB_USER'];       // Nom d'utilisateur
$password = $_ENV['DB_PASSWORD']; // Mot de passe

try {
    // Création d'une connexion à la base de données via PDO
    // DSN : Data Source Name, inclut l'hôte, le nom de la base et le charset
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);

    // Configurer PDO pour :
    // 1. Lancer des exceptions en cas d'erreur (pour pouvoir les gérer proprement)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // 2. Récupérer les résultats sous forme de tableau associatif par défaut
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Si la connexion réussit, $pdo contient l'objet de connexion prêt à l'emploi
} catch (PDOException $e) {
    // En cas d'erreur lors de la connexion à la base, le script s'arrête avec un message d'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
