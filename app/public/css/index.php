<?php

require_once __DIR__ . '/../src/db_connect.php'; 
require_once __DIR__ . '/../src/Model/AccommodationModel.php'; 
require_once __DIR__ . '/../src/Controller/AccommodationController.php'; 
// Démarrer la session (si ce n'est pas déjà fait, pour la gestion des utilisateurs connectés)
session_start();

// Contrôleur principal pour les annonces
$controller = new AccommodationController();

// Vérifier si la requête est pour la création d'une annonce
if ($_SERVER['REQUEST_URI'] === '/create-accommodation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->createAccommodation();
} elseif ($_SERVER['REQUEST_URI'] === '/create-accommodation' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->createForm();
} elseif ($_SERVER['REQUEST_URI'] === '/user/accommodations') {
    $controller->listUserAccommodations();
} else {
    echo "Page non trouvée."; 
}
