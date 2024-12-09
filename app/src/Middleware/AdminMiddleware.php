<?php


namespace App\Middleware;

class AdminMiddleware
{
    public static function checkAdmin()
    {
        if ($_SESSION['role'] != 1) {
            header('Location: /home'); // Redirige si ce n'est pas un owner
            exit;
        }
    }
}
