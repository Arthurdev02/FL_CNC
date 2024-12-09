<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function checkUser()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
            header('Location: /login');
            exit;
        }
    }

    public static function checkOwner()
    {
        // Vérifier que l'utilisateur est un Owner
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
            header('Location: /login');
            exit;
        }
    }

    public static function checkAdmin()
    {
        // Vérifier que l'utilisateur est un Admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }
}
