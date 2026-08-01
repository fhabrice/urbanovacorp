<?php

namespace App\Middleware;

use App\Core\Request;

class AuthMiddleware
{
    public function handle(Request $request)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $request->getUri();
            header('Location: /login');
            exit;
        }
        return true;
    }
}
