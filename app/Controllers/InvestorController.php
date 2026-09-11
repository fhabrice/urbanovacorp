<?php

namespace App\Controllers;

use App\Helpers\AdminHelper;

class InvestorController extends Controller
{
    private function ensureInvestorRole()
    {
        $session = $this->getSession();
        if ($session->get('user_role') !== 'investor') {
            $session->setFlashMessage('error', __('auth.must_be_investor'));
            return $this->redirect('/');
        }
    }

    public function index()
    {
        $this->ensureInvestorRole();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        // Get projects the investor has expressed interest in
        $interests = $db->fetchAll("
            SELECT i.*, p.title, p.city, p.country, p.funding_sought, p.roi
            FROM investor_interests i
            JOIN projects p ON i.project_id = p.id
            WHERE i.investor_id = ?
            ORDER BY i.created_at DESC
        ", [$investor['id']]);

        // Get investor's favorite projects
        $favorites = $db->fetchAll("
            SELECT f.*, p.title, p.city, p.country, p.funding_sought, p.roi
            FROM investor_favorites f
            JOIN projects p ON f.project_id = p.id
            WHERE f.investor_id = ?
            ORDER BY f.created_at DESC
        ", [$investor['id']]);

        return $this->view('investor/index', [
            'investor' => $investor,
            'interests' => $interests,
            'favorites' => $favorites,
        ]);
    }

    public function kycForm()
    {
        $this->ensureInvestorRole();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get existing KYC data if any
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        return $this->view('investor/kyc', [
            'investor' => $investor,
        ]);
    }

    public function kycSubmit()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        $type = $request->getBodyParam('type');
        $nationality = $request->getBodyParam('nationality');
        $phone = $request->getBodyParam('phone');
        $address = $request->getBodyParam('address');
        $city = $request->getBodyParam('city');
        $country = $request->getBodyParam('country');
        $investmentCapacity = $request->getBodyParam('investment_capacity');
        $investmentSectors = $request->getBodyParam('investment_sectors');
        $riskProfile = $request->getBodyParam('risk_profile');

        // Validate required declarations
        $declarations = $request->getBodyParam('declarations', []);
        $requiredDeclarations = ['accuracy', 'terms', 'privacy', 'verification'];
        foreach ($requiredDeclarations as $required) {
            if (!in_array($required, $declarations)) {
                $session->setFlashMessage('error', __('investor.declarations_required'));
                return $this->redirect('/investor/kyc');
            }
        }

        // Handle file uploads
        $kycDocuments = [];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
        $uploadPath = PUBLIC_PATH . '/uploads/kyc';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Upload ID document (individual)
        if (isset($_FILES['id_document']) && $_FILES['id_document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['id_document'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowedTypes)) {
                $filename = 'id_' . $userId . '_' . time() . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $filename);
                $kycDocuments['id_document'] = $filename;
            }
        }

        // Upload profile photo (individual, optional)
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowedTypes)) {
                $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $filename);
                $kycDocuments['profile_photo'] = $filename;
            }
        }

        // Upload corporate documents
        $corporateDocs = ['company_registration_doc', 'company_statutes', 'representative_id', 'power_of_attorney'];
        foreach ($corporateDocs as $docType) {
            if (isset($_FILES[$docType]) && $_FILES[$docType]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$docType];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowedTypes)) {
                    $filename = $docType . '_' . $userId . '_' . time() . '.' . $ext;
                    move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $filename);
                    $kycDocuments[$docType] = $filename;
                }
            }
        }

        // Upload additional documents
        foreach (['proof_address', 'financial_docs'] as $docType) {
            if (isset($_FILES[$docType]) && $_FILES[$docType]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$docType];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowedTypes)) {
                    $filename = $docType . '_' . $userId . '_' . time() . '.' . $ext;
                    move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $filename);
                    $kycDocuments[$docType] = $filename;
                }
            }
        }

        // Update or create investor record
        if ($type === 'corporate') {
            $companyName = $request->getBodyParam('company_name');
            $companyRegNumber = $request->getBodyParam('company_registration_number');
            $companyTaxId = $request->getBodyParam('company_tax_id');

            $sql = "
                UPDATE investors SET
                    type = ?,
                    nationality = ?,
                    phone = ?,
                    address = ?,
                    city = ?,
                    country = ?,
                    company_name = ?,
                    company_registration_number = ?,
                    company_tax_id = ?,
                    investment_capacity = ?,
                    investment_sectors = ?,
                    risk_profile = ?,
                    kyc_documents = ?,
                    kyc_submitted_at = NOW(),
                    investor_status = 'pending'
                WHERE user_id = ?
            ";
            
            $db->execute($sql, [
                $type, $nationality, $phone, $address, $city, $country,
                $companyName, $companyRegNumber, $companyTaxId,
                $investmentCapacity, $investmentSectors, $riskProfile,
                json_encode($kycDocuments), $userId
            ]);
        } else {
            $idDocType = $request->getBodyParam('id_document_type');
            $idDocNumber = $request->getBodyParam('id_document_number');
            $idDocExpiry = $request->getBodyParam('id_document_expiry');

            $sql = "
                UPDATE investors SET
                    type = ?,
                    nationality = ?,
                    phone = ?,
                    address = ?,
                    city = ?,
                    country = ?,
                    id_document_type = ?,
                    id_document_number = ?,
                    id_document_expiry = ?,
                    investment_capacity = ?,
                    investment_sectors = ?,
                    risk_profile = ?,
                    kyc_documents = ?,
                    kyc_submitted_at = NOW(),
                    investor_status = 'pending'
                WHERE user_id = ?
            ";
            
            $db->execute($sql, [
                $type, $nationality, $phone, $address, $city, $country,
                $idDocType, $idDocNumber, $idDocExpiry,
                $investmentCapacity, $investmentSectors, $riskProfile,
                json_encode($kycDocuments), $userId
            ]);
        }

        $session->setFlashMessage('success', __('investor.kyc_submitted'));
        return $this->redirect('/investor');
    }

    public function dataRoom($projectId)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        // Get project data
        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ? AND status = 'approved'",
            [$projectId]
        );

        if (!$project) {
            $session->setFlashMessage('error', __('investor.project_not_found'));
            return $this->redirect('/marketplace');
        }

        // §11 — Data Room uniquement pour investisseur vérifié (KYC approuvé) ou avec autorisation explicite
        $accessGranted = false;
        try {
            $accessRow = $db->fetchOne("SELECT id FROM data_room_accesses WHERE project_id=? AND investor_id=? AND status='autorise' LIMIT 1", [$projectId, $investor['id']]);
            if($accessRow) $accessGranted = true;
            if(!$accessGranted){
                $perm = $db->fetchOne("SELECT id FROM data_room_permissions WHERE project_id=? AND investor_id=? LIMIT 1", [$projectId, $investor['id']]);
                if($perm) $accessGranted = true;
            }
        } catch(\Exception $e){}
        // Si non approuvé et pas d'accès explicite, on peut quand même montrer la page mais avec message KYC — on laisse passer, la vue gère le verrou
        // Option stricte : bloquer si investor_status != approved et pas d'accès
        // if(($investor['investor_status'] ?? $investor['kyc_status'] ?? '') !== 'approved' && !$accessGranted){
        //     $session->setFlashMessage('error', 'Votre profil KYC est en cours de validation. Accès Data Room sur demande.');
        //     return $this->redirect('/marketplace/'.$projectId.'#dataroom');
        // }

        // Check if investor has expressed interest
        $interest = $db->fetchOne(
            "SELECT * FROM investor_interests WHERE project_id = ? AND investor_id = ?",
            [$projectId, $investor['id']]
        );

        // Get project documents
        $documents = $db->fetchAll(
            "SELECT * FROM project_documents WHERE project_id = ?",
            [$projectId]
        );

        return $this->view('investor/data-room', [
            'project' => $project,
            'investor' => $investor,
            'interest' => $interest,
            'documents' => $documents,
        ]);
    }

    public function profileForm()
    {
        $this->ensureInvestorRole();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        $profile = $db->fetchOne("SELECT * FROM investor_profiles WHERE user_id = ?", [$userId]);
        $preferences = $db->fetchOne("SELECT * FROM investor_preferences WHERE user_id = ?", [$userId]);

        return $this->view('investor/profile', [
            'profile' => $profile,
            'preferences' => $preferences,
        ]);
    }

    public function profileSubmit()
    {
        $this->ensureInvestorRole();
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        $years = $request->getBodyParam('years_experience');
        $projectsFinanced = $request->getBodyParam('projects_financed');
        $presentation = $request->getBodyParam('presentation');
        $references = $request->getBodyParam('references');
        $investmentMin = $request->getBodyParam('investment_min');
        $investmentMax = $request->getBodyParam('investment_max');
        $horizon = $request->getBodyParam('investment_horizon');
        $expectedRoi = $request->getBodyParam('expected_roi');

        $preferredSectors = $request->getBodyParam('preferred_sectors');
        $preferredCountries = $request->getBodyParam('preferred_countries');
        $investmentTypes = $request->getBodyParam('investment_types');

        if (is_string($preferredCountries)) {
            $preferredCountries = array_filter(array_map('trim', explode(',', $preferredCountries)));
        }

        if (!is_array($preferredSectors)) {
            $preferredSectors = [];
        }

        if (!is_array($investmentTypes)) {
            $investmentTypes = [];
        }

        // upsert profile
        $exists = $db->fetchOne("SELECT id FROM investor_profiles WHERE user_id = ?", [$userId]);
        if ($exists) {
            $db->execute("UPDATE investor_profiles SET years_experience = ?, projects_financed = ?, presentation = ?, references_portfolio = ?, investment_min = ?, investment_max = ?, investment_horizon = ?, expected_roi = ?, updated_at = NOW() WHERE user_id = ?", [
                $years, $projectsFinanced, $presentation, $references, $investmentMin, $investmentMax, $horizon, $expectedRoi, $userId
            ]);
        } else {
            $db->execute("INSERT INTO investor_profiles (user_id, years_experience, projects_financed, presentation, references_portfolio, investment_min, investment_max, investment_horizon, expected_roi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $userId, $years, $projectsFinanced, $presentation, $references, $investmentMin, $investmentMax, $horizon, $expectedRoi
            ]);
        }

        // upsert preferences
        $prefsExist = $db->fetchOne("SELECT id FROM investor_preferences WHERE user_id = ?", [$userId]);
        $ps = !empty($preferredSectors) ? json_encode(array_values($preferredSectors)) : null;
        $pc = !empty($preferredCountries) ? json_encode(array_values($preferredCountries)) : null;
        $it = !empty($investmentTypes) ? json_encode(array_values($investmentTypes)) : null;

        if ($prefsExist) {
            $db->execute("UPDATE investor_preferences SET preferred_sectors = ?, preferred_countries = ?, investment_types = ?, updated_at = NOW() WHERE user_id = ?", [$ps, $pc, $it, $userId]);
        } else {
            $db->execute("INSERT INTO investor_preferences (user_id, preferred_sectors, preferred_countries, investment_types) VALUES (?, ?, ?, ?)", [$userId, $ps, $pc, $it]);
        }

        $session->setFlashMessage('success', __('investor.profile_saved'));
        return $this->redirect('/investor');
    }

    public function expressInterest($projectId)
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            // Visiteur non connecté — stocker projet et demander connexion (§3)
            $session->set('pending_interest_project_id', $projectId);
            $session->set('pending_interest_confirm', 1);
            if ($request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success'=>false, 'need_auth'=>true, 'message'=>'Vous devez disposer d’un compte investisseur Urbanova pour manifester votre intérêt.', 'redirect'=>'/login?interest='.$projectId]);
                exit;
            }
            $session->setFlashMessage('error', 'Vous devez disposer d’un compte investisseur Urbanova pour manifester votre intérêt.');
            return $this->redirect('/login?interest='.$projectId);
        }

        // Vérifier si utilisateur est investisseur, sinon proposer activation (§6)
        $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
        $investor = $db->fetchOne("SELECT * FROM investors WHERE user_id = ?", [$userId]);

        if (!$investor) {
            // Utilisateur connecté mais non investisseur — inviter à activer profil
            $session->set('pending_interest_project_id', $projectId);
            $session->set('pending_interest_confirm', 1);
            if ($request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success'=>false, 'need_activation'=>true, 'message'=>'Pour accéder aux opportunités d’investissement, veuillez compléter votre profil Investisseur.', 'activate_url'=>'/investor/activate?interest='.$projectId]);
                exit;
            }
            $session->setFlashMessage('error', 'Pour accéder aux opportunités d’investissement, veuillez compléter votre profil Investisseur.');
            return $this->redirect('/investor/activate?interest='.$projectId);
        }

        // Récupérer projet — public : approved/published mais aussi tout projet visible marketplace pour démo
        $project = $db->fetchOne("SELECT * FROM projects WHERE id = ?", [$projectId]);
        if (!$project) {
            $session->setFlashMessage('error', __('investor.project_not_found'));
            return $this->redirect('/marketplace');
        }

        // Si investisseur connecté (§7) — enregistrer intérêt
        $investmentAmount = $request->getBodyParam('investment_amount') ?? $request->getBodyParam('amount') ?? $project['funding_sought'] ?? 0;
        $message = $request->getBodyParam('message') ?? 'Manifestation d’intérêt via Marketplace';

        // Vérifier doublon
        $existingInterest = $db->fetchOne("SELECT id FROM investor_interests WHERE project_id = ? AND investor_id = ?", [$projectId, $investor['id']]);

        try {
            if ($existingInterest) {
                $db->execute("UPDATE investor_interests SET investment_amount = ?, message = ?, interest_type = 'inquiry', status = 'pending' WHERE id = ?", [$investmentAmount, $message, $existingInterest['id']]);
            } else {
                $db->execute("INSERT INTO investor_interests (project_id, investor_id, investment_amount, message, interest_type, status) VALUES (?, ?, ?, ?, 'inquiry', 'pending')", [$projectId, $investor['id'], $investmentAmount, $message]);
            }
        } catch (\Exception $e) {
            // Si table n'existe pas, fallback investments
            try { $db->execute("INSERT INTO investor_interests (project_id, investor_id, investment_amount, message, interest_type, status) VALUES (?, ?, ?, ?, 'inquiry', 'pending')", [$projectId, $investor['id'], $investmentAmount, $message]); } catch(\Exception $ee){}
        }

        // §8 — Création automatique d'opportunité CRM
        try {
            $investorName = trim(($user['first_name']??'').' '.($user['last_name']??'')) ?: ($user['name'] ?? $user['email']);
            $fullName = $investorName . ' — ' . ($project['title'] ?? 'Projet #'.$projectId);
            // Vérifier si opportunité déjà existe pour ce couple investisseur/projet non perdue
            $existingOpp = $db->fetchOne("SELECT id FROM opportunities WHERE project_id=? AND (contact_id=? OR responsible_id=?) ORDER BY created_at DESC LIMIT 1", [$projectId, $investor['id'], $userId]);
            if (!$existingOpp) {
                $ref = AdminHelper::generateReference($db, 'OPP');
                $amount = is_numeric($investmentAmount) ? (float)$investmentAmount : (float)($project['funding_sought'] ?? 0);
                $db->execute("INSERT INTO opportunities (reference, name, contact_id, company_id, project_id, type, amount, currency, probability, stage, responsible_id, opened_at, notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [$ref, 'Intérêt - '.$fullName, null, null, $projectId, 'investissement', $amount, 'USD', 20, 'nouveau', $userId, date('Y-m-d'), 'Investisseur: '.$investorName.' | Projet: '.($project['title']??'#'.$projectId).' | Action: Manifestation d’intérêt | Source: Marketplace Urbanova | Statut CRM: Nouveau intérêt | Responsable: Équipe Investissement']);
                // Notification admin équipe investissement
                try { $db->execute("INSERT INTO notifications (user_id, type, title, message, link, priority) SELECT id, 'investment_update', 'Nouvel intérêt Marketplace', ?, ?, 'action_requise' FROM users WHERE role IN ('admin','super_admin') LIMIT 5", ["Intérêt de $investorName pour ".($project['title']??'#'.$projectId), "/admin/crm/opportunities"]); } catch(\Exception $e){}
                // Tâche pour qualification
                try { $taskRef = AdminHelper::generateReference($db,'TASK'); $db->execute("INSERT INTO tasks (reference, title, description, responsible_id, project_id, opportunity_id, priority, due_date, status) VALUES (?,?,?,?,?,?,?,?,?)", [$taskRef, 'Qualifier intérêt - '.$investorName, 'Intérêt Marketplace à qualifier (KYC/Data Room)', $userId, $projectId, null, 'haute', date('Y-m-d', strtotime('+2 days')), 'a_faire']); } catch(\Exception $e){}
            }
            AdminHelper::auditLog($db, $userId, 'creation', 'opportunities', $projectId, null, ['investor'=>$investor['id']], "Opportunité CRM auto-créée depuis intérêt Marketplace projet #$projectId");
        } catch(\Exception $e){ error_log("CRM opportunity creation failed: ".$e->getMessage()); }

        // Nettoyer pending
        $session->remove('pending_interest_project_id');
        $session->remove('pending_interest_confirm');

        if ($request->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success'=>true, 'message'=>'Votre intérêt pour ce projet a bien été enregistré.', 'project_id'=>$projectId, 'next'=>['data_room'=>'/investor/data-room/'.$projectId, 'rdv'=>'/investor/messages', 'contact'=>'/contact', 'follow'=>'/investor/favorites/'.$projectId.'/add']]);
            exit;
        }

        $session->setFlashMessage('success', 'Votre intérêt pour ce projet a bien été enregistré.');
        // Retour avec proposition Data Room etc. (§7)
        return $this->redirect('/marketplace/'.$projectId.'?interest_recorded=1');
    }

    public function activate()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');
        if (!$userId) {
            $interest = $request->getParam('interest') ?? $request->getParam('project_id');
            return $this->redirect('/login'.($interest ? '?interest='.$interest : ''));
        }
        $user = $db->fetchOne("SELECT * FROM users WHERE id=?", [$userId]);
        if (!$user) return $this->redirect('/');

        // Si déjà investisseur, rediriger
        $existing = $db->fetchOne("SELECT id FROM investors WHERE user_id=?", [$userId]);
        if ($existing) {
            $session->set('user_role','investor');
            $_SESSION['user_role']='investor';
            $interest = $request->getParam('interest') ?? $session->get('pending_interest_project_id');
            if ($interest) return $this->redirect('/marketplace/'.$interest.'?confirm_interest=1');
            return $this->redirect('/investor');
        }

        if ($request->isPost()) {
            // Activer le profil investisseur (§6) — uniquement sur POST explicite
            $interest = $request->getBodyParam('interest') ?? $request->getParam('interest') ?? $session->get('pending_interest_project_id');
            $investorType = $request->getBodyParam('investor_type') ?? 'individual';
            $type = $investorType==='individual' ? 'individual' : 'corporate';
            $phone = $request->getBodyParam('phone');
            $country = $request->getBodyParam('country');
            $city = $request->getBodyParam('city');
            $address = $request->getBodyParam('address');
            try {
                $db->execute("INSERT INTO investors (user_id, type, investor_type, investor_status, kyc_status, phone, country, city, address) VALUES (?, ?, ?, 'pending', 'non_commence', ?, ?, ?, ?)", [$userId, $type, $investorType, $phone, $country, $city, $address]);
            } catch(\Exception $e){
                try { $db->execute("INSERT INTO investors (user_id, type, investor_type, investor_status, phone, country, city, address) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?)", [$userId, $type, $investorType, $phone, $country, $city, $address]); } catch(\Exception $e2){
                    try { $db->execute("INSERT INTO investors (user_id, type, investor_status) VALUES (?, ?, 'pending')", [$userId, $type]); } catch(\Exception $ee){}
                }
            }
            // Mettre à jour rôle utilisateur — ajouter statut Investisseur sans supprimer ancien rôle ? On garde investor comme rôle principal pour accès
            try { $db->execute("UPDATE users SET role='investor' WHERE id=?", [$userId]); } catch(\Exception $e){}
            $session->set('user_role','investor');
            $_SESSION['user_role']='investor';
            $session->set('investor_status','pending');
            $_SESSION['investor_status']='pending';
            AdminHelper::auditLog($db, $userId, 'activation', 'investors', $userId, ['role'=>$user['role']], ['role'=>'investor'], "Activation profil investisseur pour user #$userId");
            $session->setFlashMessage('success','Votre profil investisseur a été activé. Vous pouvez maintenant manifester votre intérêt.');
            if ($interest) {
                $session->set('pending_interest_project_id', $interest);
                $session->set('pending_interest_confirm', 1);
                return $this->redirect('/marketplace/'.$interest.'?confirm_interest=1');
            }
            return $this->redirect('/investor/kyc');
        }

        // Afficher page d'activation
        $interest = $request->getParam('interest') ?? $session->get('pending_interest_project_id');
        $project = null;
        if ($interest) {
            try { $project = $db->fetchOne("SELECT * FROM projects WHERE id=?", [$interest]); } catch(\Exception $e){}
        }
        return $this->view('investor/activate', ['interest'=>$interest, 'project'=>$project, 'user'=>$user]);
    }

    public function addFavorite($projectId)
    {
        $this->ensureInvestorRole();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        // Check if project exists
        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ? AND status = 'approved'",
            [$projectId]
        );

        if (!$project) {
            $session->setFlashMessage('error', __('investor.project_not_found'));
            return $this->redirect('/marketplace');
        }

        // Check if already favorited
        $existing = $db->fetchOne(
            "SELECT id FROM investor_favorites WHERE investor_id = ? AND project_id = ?",
            [$investor['id'], $projectId]
        );

        if ($existing) {
            $session->setFlashMessage('info', __('investor.already_favorited'));
            return $this->redirect('/marketplace');
        }

        // Add to favorites
        $db->execute(
            "INSERT INTO investor_favorites (investor_id, project_id) VALUES (?, ?)",
            [$investor['id'], $projectId]
        );

        $session->setFlashMessage('success', __('investor.added_to_favorites'));
        return $this->redirect('/marketplace');
    }

    public function removeFavorite($projectId)
    {
        $this->ensureInvestorRole();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        // Remove from favorites
        $db->execute(
            "DELETE FROM investor_favorites WHERE investor_id = ? AND project_id = ?",
            [$investor['id'], $projectId]
        );

        $session->setFlashMessage('success', __('investor.removed_from_favorites'));
        return $this->redirect('/investor');
    }

    public function messages()
    {
        $this->ensureInvestorRole();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        // Get investor's messages
        $messages = $db->fetchAll("
            SELECT m.*, u.first_name as admin_first_name, u.last_name as admin_last_name
            FROM investor_messages m
            LEFT JOIN users u ON m.admin_replied_by = u.id
            WHERE m.investor_id = ?
            ORDER BY m.created_at DESC
        ", [$investor['id']]);

        return $this->view('investor/messages', [
            'investor' => $investor,
            'messages' => $messages,
        ]);
    }

    public function sendMessage()
    {
        $this->ensureInvestorRole();
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        $subject = trim($request->getBodyParam('subject'));
        $message = trim($request->getBodyParam('message'));

        if (empty($subject) || empty($message)) {
            $session->setFlashMessage('error', __('investor.message_required_fields'));
            return $this->redirect('/investor/messages');
        }

        // Insert message
        $db->execute(
            "INSERT INTO investor_messages (investor_id, subject, message) VALUES (?, ?, ?)",
            [$investor['id'], $subject, $message]
        );

        $session->setFlashMessage('success', __('investor.message_sent'));
        return $this->redirect('/investor/messages');
    }
}
