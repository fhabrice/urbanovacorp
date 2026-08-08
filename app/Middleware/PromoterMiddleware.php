<?php

namespace App\Middleware;

class PromoterMiddleware
{
    public function handle($request, $next)
    {
        $session = $request->getSession();
        
        if (!$session->get('user_id')) {
            $session->setFlashMessage('error', __('auth.must_be_logged_in'));
            return header('Location: /login');
            exit;
        }
        
        $role = $session->get('user_role');
        
        if ($role !== 'promoter' && $role !== 'admin') {
            $session->setFlashMessage('error', __('auth.must_be_promoter'));
            return header('Location: /');
            exit;
        }
        
        return $next($request);
    }
}
