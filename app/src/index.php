<?php

use App\Controller\AccommodationController;
use App\Controller\AuthController;
use App\Controller\PageController;

require_once __DIR__ . '/../vendor/autoload.php';

// Récupération de l'URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Définition des routes
switch ($uri) {
    case '/':
        $controller = new PageController();
        $controller->home();
        break;

    case '/accommodations':
        $controller = new AccommodationController();
        $controller->list();
        break;

    case '/accommodations/details':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo "ID requis.";
            exit;
        }
        $controller = new AccommodationController();
        $controller->details((int)$_GET['id']);
        break;

    case '/accommodations/create':
        $controller = new AccommodationController();
        $controller->create();
        break;

    case '/login':
        $controller = new AuthController();
        $controller->login();
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/../src/views/_errors/404.phtml';
        break;
}
