<?php

namespace App\Controllers;

class AuthController extends Controller
{
    public function loginForm()
    {
        return $this->view('auth/login');
    }

    public function login()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();

        $email = $request->getBodyParam('email');
        $password = $request->getBodyParam('password');

        // Validate input
        if (empty($email) || empty($password)) {
            $session->setFlashMessage('error', __('auth.empty_fields'));
            return $this->redirect('/login');
        }

        // Find user
        $user = $db->fetchOne(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            $session->setFlashMessage('error', __('auth.invalid_credentials'));
            return $this->redirect('/login');
        }

        // Check account status
        if ($user['status'] === 'suspended') {
            $session->setFlashMessage('error', __('auth.account_suspended'));
            return $this->redirect('/login');
        }

        // Set session
        $session->set('user_id', $user['id']);
        $session->set('user_email', $user['email']);
        $session->set('user_name', $user['first_name'] . ' ' . $user['last_name']);
        $session->set('user_role', $user['role']);

        // Set investor status if applicable
        if ($user['role'] === 'investor') {
            $investor = $db->fetchOne(
                "SELECT investor_status FROM investors WHERE user_id = ?",
                [$user['id']]
            );
            $session->set('investor_status', $investor['investor_status'] ?? 'pending');
        }

        // Redirect based on role
        $redirectUrl = $session->get('redirect_after_login', '/');
        $session->remove('redirect_after_login');

        if ($user['role'] === 'admin') {
            return $this->redirect('/admin');
        } elseif ($user['role'] === 'investor') {
            return $this->redirect('/investor');
        }

        return $this->redirect($redirectUrl);
    }

    public function registerForm()
    {
        return $this->view('auth/register');
    }

    public function register()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();

        $email = $request->getBodyParam('email');
        $password = $request->getBodyParam('password');
        $passwordConfirm = $request->getBodyParam('password_confirm');
        $firstName = $request->getBodyParam('first_name');
        $lastName = $request->getBodyParam('last_name');
        $role = $request->getBodyParam('role', 'promoter');

        // Validate input
        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $session->setFlashMessage('error', __('auth.all_fields_required'));
            return $this->redirect('/register');
        }

        if ($password !== $passwordConfirm) {
            $session->setFlashMessage('error', __('auth.passwords_not_match'));
            return $this->redirect('/register');
        }

        if (strlen($password) < 8) {
            $session->setFlashMessage('error', __('auth.password_too_short'));
            return $this->redirect('/register');
        }

        // Check if email already exists
        $existingUser = $db->fetchOne(
            "SELECT id FROM users WHERE email = ?",
            [$email]
        );

        if ($existingUser) {
            $session->setFlashMessage('error', __('auth.email_exists'));
            return $this->redirect('/register');
        }

        // Create user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $db->execute(
            "INSERT INTO users (email, password, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, 'pending')",
            [$email, $hashedPassword, $firstName, $lastName, $role]
        );

        $userId = $db->lastInsertId();

        // Create investor record if role is investor
        if ($role === 'investor') {
            $db->execute(
                "INSERT INTO investors (user_id, type, investor_status) VALUES (?, 'individual', 'pending')",
                [$userId]
            );
        }

        $session->setFlashMessage('success', __('auth.registration_success'));
        return $this->redirect('/login');
    }

    public function logout()
    {
        $session = $this->getSession();
        $session->destroy();
        return $this->redirect('/');
    }
}
