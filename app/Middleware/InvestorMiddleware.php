<?php

namespace App\Middleware;

use App\Core\Request;

class InvestorMiddleware
{
    public function handle(Request $request)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'investor') {
            header('Location: /investor/kyc');
            exit;
        }

        // Check if investor is approved
        if ($_SESSION['investor_status'] !== 'approved') {
            header('Location: /investor/kyc');
            exit;
        }

        return true;
    }
}
