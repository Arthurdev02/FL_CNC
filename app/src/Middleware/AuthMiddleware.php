<?php

namespace App\Middleware;

use App\Model\User;
use App\Repository\UserRepository;

class AuthMiddleware
{
    public static function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function checkOwner()
    {
        self::checkAuth();  // Vérifier si l'utilisateur est connecté

        $userRepo = new UserRepository();
        $user = $userRepo->findById($_SESSION['user_id']);

        if (!$user->isOwner()) {
            header('Location: /'); // Rediriger si ce n'est pas un Owner
            exit;
        }
    }

    public static function checkUser()
    {
        self::checkAuth();  // Vérifier si l'utilisateur est connecté

        $userRepo = new UserRepository();
        $user = $userRepo->findById($_SESSION['user_id']);

        if (!$user->isUser()) {
            header('Location: /'); // Rediriger si ce n'est pas un User
            exit;
        }
    }
}
