<?php

namespace App\Middleware;

use App\Core\Request;

class AdminMiddleware
{
    public function handle(Request $request)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }
        return true;
    }
}
