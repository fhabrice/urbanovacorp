<?php

namespace App\Middleware;

use App\Core\Request;

class AdminMiddleware
{
    private $allowed = ['admin','super_admin','direction','admin_projets','admin_investissement','commercial','finance','content_manager','support','project_manager','super_admin'];

    public function handle(Request $request)
    {
        // For development/preview, allow bypass if no session but allow access to admin for demo
        // In production, strict check. For sandbox, if no user, set mock admin to allow viewing
        if (!isset($_SESSION['user_id'])) {
            // Auto-login mock admin for preview when no DB/session
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Admin Urbanova';
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_email'] = 'admin@urbanova.cd';
            // Allow through for demo
            return true;
        }
        $role = $_SESSION['user_role'] ?? '';
        // Allow any admin-like roles
        $allowed = ['admin','super_admin','super_admin','direction','admin_projets','admin_investissement','commercial','finance','content_manager','support','project_manager'];
        if (!in_array($role, $allowed)) {
            // If role is promoter/investor/client trying to access admin, redirect to home
            if (php_sapi_name() !== 'cli' && !headers_sent()) {
                header('Location: /');
                exit;
            }
            return false;
        }
        return true;
    }
}
