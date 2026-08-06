<?php

namespace App\Controllers;

use App\Helpers\Security;

class AuthController extends Controller
{
    public function loginForm()
    {
        // Generate CSRF token
        $csrfToken = Security::generateCsrfToken();
        return $this->view('auth/login', ['csrf_token' => $csrfToken]);
    }

    public function login()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();

        // Validate CSRF token
        $csrfToken = $request->getBodyParam('csrf_token');
        if (!Security::validateCsrfToken($csrfToken)) {
            $session->setFlashMessage('error', __('security.csrf_error'));
            return $this->redirect('/login');
        }

        $email = Security::sanitize($request->getBodyParam('email'));
        $password = $request->getBodyParam('password');

        // Validate input
        if (empty($email) || empty($password)) {
            $session->setFlashMessage('error', __('auth.empty_fields'));
            return $this->redirect('/login');
        }

        // Validate email format
        if (!Security::validateEmail($email)) {
            $session->setFlashMessage('error', __('auth.invalid_email'));
            return $this->redirect('/login');
        }

        // Rate limiting
        if (!Security::checkRateLimit($email, 5, 900)) {
            $session->setFlashMessage('error', __('security.too_many_attempts'));
            return $this->redirect('/login');
        }

        // Find user
        $user = $db->fetchOne(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );

        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            $session->setFlashMessage('error', __('auth.invalid_credentials'));
            return $this->redirect('/login');
        }

        // Clear rate limit on successful login
        Security::clearRateLimit($email);

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
        $redirectUrl = $session->get('redirect_after_login', '/');
        $session->remove('redirect_after_login');

        if ($user['role'] === 'investor') {
            $investor = $db->fetchOne(
                "SELECT investor_status, kyc_submitted_at FROM investors WHERE user_id = ?",
                [$user['id']]
            );
            $session->set('investor_status', $investor['investor_status'] ?? 'pending');

            $profileExists = $db->fetchOne(
                "SELECT COUNT(*) as count FROM investor_profiles WHERE user_id = ?",
                [$user['id']]
            );

            if (empty($profileExists['count']) || $profileExists['count'] === 0) {
                $redirectUrl = '/investor/profile';
            } elseif (empty($investor['kyc_submitted_at'])) {
                $redirectUrl = '/investor/kyc';
            } else {
                $redirectUrl = '/investor';
            }
        }

        // Redirect based on role
        if ($user['role'] === 'admin') {
            return $this->redirect('/admin');
        } elseif ($user['role'] === 'investor') {
            return $this->redirect($redirectUrl);
        }

        if ($user['role'] === 'admin') {
            return $this->redirect('/admin');
        } elseif ($user['role'] === 'investor') {
            return $this->redirect('/investor');
        }

        return $this->redirect($redirectUrl);
    }

    public function registerForm()
    {
        // Generate CSRF token
        $csrfToken = Security::generateCsrfToken();
        return $this->view('auth/register', ['csrf_token' => $csrfToken]);
    }

    public function register()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();

        // Validate CSRF token
        $csrfToken = $request->getBodyParam('csrf_token');
        if (!Security::validateCsrfToken($csrfToken)) {
            $session->setFlashMessage('error', __('security.csrf_error'));
            return $this->redirect('/register');
        }

        $email = Security::sanitize($request->getBodyParam('email'));
        $password = $request->getBodyParam('password');
        $passwordConfirm = $request->getBodyParam('password_confirm');
        $firstName = Security::sanitize($request->getBodyParam('first_name'));
        $lastName = Security::sanitize($request->getBodyParam('last_name'));
        $role = Security::sanitize($request->getBodyParam('role', 'promoter'));
        $investorType = Security::sanitize($request->getBodyParam('investor_type', 'individual'));
        $companyName = Security::sanitize($request->getBodyParam('company_name'));
        $representativeName = Security::sanitize($request->getBodyParam('representative_name'));
        $position = Security::sanitize($request->getBodyParam('position'));
        $country = Security::sanitize($request->getBodyParam('country'));
        $city = Security::sanitize($request->getBodyParam('city'));
        $address = Security::sanitize($request->getBodyParam('address'));
        $phone = Security::sanitize($request->getBodyParam('phone'));
        $website = Security::sanitize($request->getBodyParam('website'));

        // Validate input
        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $session->setFlashMessage('error', __('auth.all_fields_required'));
            return $this->redirect('/register');
        }

        if ($role === 'investor' && (empty($companyName) || empty($representativeName) || empty($position) || empty($country) || empty($city) || empty($address) || empty($phone))) {
            $session->setFlashMessage('error', __('auth.investor_required_fields'));
            return $this->redirect('/register');
        }

        // Validate email format
        if (!Security::validateEmail($email)) {
            $session->setFlashMessage('error', __('auth.invalid_email'));
            return $this->redirect('/register');
        }

        if ($password !== $passwordConfirm) {
            $session->setFlashMessage('error', __('auth.passwords_not_match'));
            return $this->redirect('/register');
        }

        if (!Security::validatePasswordStrength($password)) {
            $session->setFlashMessage('error', __('auth.password_weak'));
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
        $hashedPassword = Security::hashPassword($password);
        
        $db->execute(
            "INSERT INTO users (email, password, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, 'pending')",
            [$email, $hashedPassword, $firstName, $lastName, $role]
        );

        $userId = $db->lastInsertId();

        // Create investor record if role is investor
        if ($role === 'investor') {
            $investorCategoryType = $investorType === 'individual' ? 'individual' : 'corporate';
            $db->execute(
                "INSERT INTO investors (user_id, type, investor_type, investor_status, company_name, representative_name, position, country, city, address, phone, website) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?)",
                [$userId, $investorCategoryType, $investorType, $companyName, $representativeName, $position, $country, $city, $address, $phone, $website]
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
