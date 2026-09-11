<?php

namespace App\Controllers;

use App\Helpers\Security;

class AuthController extends Controller
{
    public function loginForm()
    {
        $csrfToken = Security::generateCsrfToken();
        $request = $this->getRequest();
        $session = $this->getSession();
        // Préserver le projet d'intérêt (§5 — ne pas perdre le projet)
        $interest = $request->getParam('interest') ?? $request->getParam('project_id') ?? $request->getParam('project');
        if ($interest) {
            $session->set('pending_interest_project_id', $interest);
        }
        // Si déjà stocké, récupérer pour la vue
        $pending = $session->get('pending_interest_project_id');
        return $this->view('auth/login', ['csrf_token' => $csrfToken, 'interest' => $interest ?? $pending, 'pending_interest' => $pending]);
    }

    public function login()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();

        $csrfToken = $request->getBodyParam('csrf_token');
        if (!Security::validateCsrfToken($csrfToken)) {
            $session->setFlashMessage('error', __('security.csrf_error'));
            return $this->redirect('/login');
        }

        // Récupérer l'intérêt potentiel depuis le formulaire caché ou session
        $interestFromBody = $request->getBodyParam('interest') ?? $request->getBodyParam('pending_interest');
        if ($interestFromBody) $session->set('pending_interest_project_id', $interestFromBody);

        $email = Security::sanitize($request->getBodyParam('email'));
        $password = $request->getBodyParam('password');

        if (empty($email) || empty($password)) {
            $session->setFlashMessage('error', __('auth.empty_fields'));
            return $this->redirect($interestFromBody ? '/login?interest='.$interestFromBody : '/login');
        }

        if (!Security::validateEmail($email)) {
            $session->setFlashMessage('error', __('auth.invalid_email'));
            return $this->redirect('/login');
        }

        if (!Security::checkRateLimit($email, 5, 900)) {
            $session->setFlashMessage('error', __('security.too_many_attempts'));
            return $this->redirect('/login');
        }

        $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);

        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            $session->setFlashMessage('error', __('auth.invalid_credentials'));
            return $this->redirect($interestFromBody ? '/login?interest='.$interestFromBody : '/login');
        }

        Security::clearRateLimit($email);

        if ($user['status'] === 'suspended') {
            $session->setFlashMessage('error', __('auth.account_suspended'));
            return $this->redirect('/login');
        }

        $session->set('user_id', $user['id']);
        $session->set('user_email', $user['email']);
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: ($user['name'] ?? '');
        $session->set('user_name', $name);
        $session->set('user_role', $user['role']);
        try { $db->execute("UPDATE users SET last_login_at=NOW() WHERE id=?", [$user['id']]); } catch(\Exception $e){}

        if ($user['role'] === 'investor') {
            $investor = $db->fetchOne("SELECT investor_status, kyc_submitted_at FROM investors WHERE user_id = ?", [$user['id']]);
            $session->set('investor_status', $investor['investor_status'] ?? 'pending');
        }

        // §5 — Retour automatique vers le Projet A après connexion
        $pendingInterest = $session->get('pending_interest_project_id');
        // Aussi vérifier redirect_after_login
        $redirectUrl = $session->get('redirect_after_login', '/');
        $session->remove('redirect_after_login');

        if ($pendingInterest) {
            // Garder en session pour afficher "Confirmer mon intérêt" sur la fiche
            $session->set('pending_interest_confirm', 1);
            // Rediriger vers le projet concerné
            return $this->redirect('/marketplace/'.$pendingInterest.'?confirm_interest=1');
        }

        if ($user['role'] === 'investor') {
            $investor = $db->fetchOne("SELECT investor_status, kyc_submitted_at FROM investors WHERE user_id = ?", [$user['id']]);
            $session->set('investor_status', $investor['investor_status'] ?? 'pending');
            $profileExists = 0;
            try { $profileExists = $db->fetchOne("SELECT COUNT(*) as count FROM investors WHERE user_id = ?", [$user['id']])['count'] ?? 0; } catch(\Exception $e){}
            // Si pas de KYC, rediriger vers KYC, sauf si on a un pending interest (déjà géré)
            if (empty($investor['kyc_submitted_at']) && !$pendingInterest) {
                $redirectUrl = '/investor/kyc';
            } else if (!$pendingInterest) {
                $redirectUrl = '/investor';
            }
        }

        if ($user['role'] === 'admin' || $user['role'] === 'super_admin') {
            return $this->redirect('/admin');
        } elseif ($user['role'] === 'investor' && !$pendingInterest) {
            return $this->redirect($redirectUrl);
        }

        return $this->redirect($redirectUrl);
    }

    public function registerForm()
    {
        $csrfToken = Security::generateCsrfToken();
        $request = $this->getRequest();
        $session = $this->getSession();
        // Pré-sélection rôle INVESTISSEUR si demandé (§4)
        $roleParam = $request->getParam('role') ?? $request->getParam('type');
        $interest = $request->getParam('interest') ?? $request->getParam('project_id') ?? $request->getParam('project');
        if ($interest) $session->set('pending_interest_project_id', $interest);
        if ($roleParam === 'investor' || $roleParam === 'investisseur') $roleParam = 'investor';
        $pending = $session->get('pending_interest_project_id');
        return $this->view('auth/register', ['csrf_token' => $csrfToken, 'preselected_role' => $roleParam ?? $pending ? 'investor' : null, 'interest' => $interest ?? $pending, 'pending_interest' => $pending]);
    }

    public function register()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();

        $csrfToken = $request->getBodyParam('csrf_token');
        if (!Security::validateCsrfToken($csrfToken)) {
            $session->setFlashMessage('error', __('security.csrf_error'));
            return $this->redirect('/register');
        }

        $interestFromBody = $request->getBodyParam('interest') ?? $request->getBodyParam('pending_interest');
        if ($interestFromBody) $session->set('pending_interest_project_id', $interestFromBody);

        $email = Security::sanitize($request->getBodyParam('email'));
        $password = $request->getBodyParam('password');
        $passwordConfirm = $request->getBodyParam('password_confirm');
        $firstName = Security::sanitize($request->getBodyParam('first_name'));
        $lastName = Security::sanitize($request->getBodyParam('last_name'));
        $role = Security::sanitize($request->getBodyParam('role', 'promoter'));
        // Si interest pending et pas de rôle, forcer investor (§4)
        if (!empty($interestFromBody) && empty($role)) $role = 'investor';
        $investorType = Security::sanitize($request->getBodyParam('investor_type', 'individual'));
        $companyName = Security::sanitize($request->getBodyParam('company_name'));
        $representativeName = Security::sanitize($request->getBodyParam('representative_name'));
        $position = Security::sanitize($request->getBodyParam('position'));
        $country = Security::sanitize($request->getBodyParam('country'));
        $city = Security::sanitize($request->getBodyParam('city'));
        $address = Security::sanitize($request->getBodyParam('address'));
        $phone = Security::sanitize($request->getBodyParam('phone'));
        $website = Security::sanitize($request->getBodyParam('website'));

        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $session->setFlashMessage('error', __('auth.all_fields_required'));
            return $this->redirect($interestFromBody ? '/register?interest='.$interestFromBody.'&role=investor' : '/register');
        }

        // Pour investisseur, exiger champs minimum (§4) : nom, prénom, email, téléphone, pays, ville, mot de passe, type
        if ($role === 'investor' && (empty($phone) || empty($country) || empty($city))) {
            $session->setFlashMessage('error', __('auth.investor_required_fields'));
            return $this->redirect($interestFromBody ? '/register?interest='.$interestFromBody.'&role=investor' : '/register');
        }

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

        $existingUser = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existingUser) {
            $session->setFlashMessage('error', __('auth.email_exists'));
            return $this->redirect('/register');
        }

        $hashedPassword = Security::hashPassword($password);

        $hasNameColumns = false;
        try { $hasNameColumns = $db->fetchOne("SHOW COLUMNS FROM users LIKE 'first_name'") && $db->fetchOne("SHOW COLUMNS FROM users LIKE 'last_name'"); } catch(\Exception $e){}
        if ($hasNameColumns) {
            $columns = ['email', 'password', 'first_name', 'last_name', 'role', 'status'];
            $values = [$email, $hashedPassword, $firstName, $lastName, $role, 'pending'];
        } else {
            $columns = ['email', 'password', 'name', 'role', 'status'];
            $values = [$email, $hashedPassword, trim($firstName . ' ' . $lastName), $role, 'pending'];
        }

        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $db->execute("INSERT INTO users (" . implode(', ', $columns) . ") VALUES ($placeholders)", $values);
        $userId = $db->lastInsertId();

        if ($role === 'investor') {
            $investorCategoryType = $investorType === 'individual' ? 'individual' : 'corporate';
            try {
                $db->execute("INSERT INTO investors (user_id, type, investor_type, investor_status, company_name, country, city, address, phone) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?)", [$userId, $investorCategoryType, $investorType, $companyName, $country, $city, $address, $phone]);
            } catch(\Exception $e){
                // Fallback simple
                try { $db->execute("INSERT INTO investors (user_id, type, investor_status) VALUES (?, ?, 'pending')", [$userId, $investorCategoryType]); } catch(\Exception $ee){}
            }
        }

        $session->setFlashMessage('success', __('auth.registration_success'));
        // Conserver l'intérêt pour la connexion suivante (§5)
        if ($interestFromBody) {
            $session->set('pending_interest_project_id', $interestFromBody);
            return $this->redirect('/login?interest='.$interestFromBody);
        }
        return $this->redirect('/login');
    }

    public function logout()
    {
        $session = $this->getSession();
        // Conserver pending interest ? Non, on nettoie tout sauf éventuellement intérêt pour UX, mais logout nettoie tout
        $pending = $session->get('pending_interest_project_id');
        $session->destroy();
        // Ne pas perdre le projet si on était en parcours ? On peut le restaurer en session temporaire via cookie, mais simplifions
        if ($pending) {
            // Recréer session courte
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['pending_interest_project_id'] = $pending;
        }
        return $this->redirect('/');
    }
}
