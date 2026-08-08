<?php

namespace App\Controllers;

/**
 * API Controller - Gère les requêtes AJAX pour la nouvelle interface
 */
class ApiController extends SimpleController
{
    private $db;

    public function __construct()
    {
        parent::__construct();
        
        // Connect to database with robust fallback for host & local environments
        $config = $this->config['database'] ?? [];
        $candidates = [
            [
                'host' => $config['host'] ?? 'localhost',
                'db'   => $config['database'] ?? 'wqmetrvw_urbanova',
                'user' => $config['username'] ?? 'wqmetrvw_urbanova',
                'pass' => $config['password'] ?? 'Goma@2019'
            ],
            [
                'host' => 'localhost',
                'db'   => 'wqmetrvw_urbanova',
                'user' => 'wqmetrvw_urbanova',
                'pass' => 'Goma@2019'
            ],
            [
                'host' => 'localhost',
                'db'   => 'urbanova_db',
                'user' => 'root',
                'pass' => ''
            ],
            [
                'host' => '127.0.0.1',
                'db'   => 'urbanova_db',
                'user' => 'root',
                'pass' => ''
            ]
        ];

        $this->db = null;
        foreach ($candidates as $c) {
            try {
                $dsn = "mysql:host={$c['host']};dbname={$c['db']};charset=utf8mb4";
                $pdo = new \PDO($dsn, $c['user'], $c['pass'], [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ]);
                $this->db = $pdo;
                break;
            } catch (\PDOException $e) {
                error_log("DB candidate failed ({$c['host']}/{$c['db']}): " . $e->getMessage());
            }
        }
    }

    /**
     * Traduit un statut en français
     */
    private function translateStatus($status)
    {
        $translations = [
            'draft'           => 'Brouillon',
            'submitted'       => 'Soumis',
            'under_review'    => 'En analyse',
            'additional_info' => 'Infos complémentaires',
            'approved'        => 'Approuvé',
            'rejected'        => 'Rejeté',
            'published'       => 'Publié',
            'suspended'       => 'Suspendu',
            'sold'            => 'Vendu',
            'rented'          => 'Loué',
            'archived'        => 'Archivé',
            'pending'         => 'En attente',
            'active'          => 'Actif',
            'funding'         => 'En financement',
            'completed'       => 'Terminé',
        ];
        return $translations[$status] ?? $status;
    }

    /**
     * Récupérer tous les projets pour la marketplace
     */
    public function getProjects()
    {
        header('Content-Type: application/json');
        
        if ($this->db) {
            try {
                $stmt = $this->db->query("
                    SELECT 
                        p.id,
                        p.title,
                        p.city,
                        p.country,
                        p.sector,
                        p.funding_sought as target,
                        p.funding_raised as raised,
                        p.expected_roi as roi,
                        p.validation_status as status,
                        p.project_type,
                        p.operation_type,
                        p.coordinates_lat,
                        p.coordinates_lng,
                        p.promoter,
                        p.description,
                        p.image,
                        CONCAT(p.city, ', ', p.country) as location
                    FROM projects p
                    WHERE p.validation_status IS NOT NULL
                    ORDER BY p.created_at DESC
                ");
                
                $projects = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                // Format data for frontend
                $formattedProjects = [];
                foreach ($projects as $p) {
                    $target = (int)$p['target'];
                    $raised = (int)$p['raised'];
                    $progress = $target > 0 ? round(($raised / $target) * 100) : 0;
                    
                    $formattedProjects[] = [
                        'id' => 'proj-' . $p['id'],
                        'numeric_id' => (int)$p['id'],
                        'title' => $p['title'],
                        'location' => $p['location'],
                        'country' => $p['country'],
                        'city' => $p['city'],
                        'sector' => $p['sector'],
                        'target' => $target,
                        'raised' => $raised,
                        'roi' => (int)$p['roi'],
                        'progress' => $progress,
                        'raw_status' => $p['status'],
                        'promoter' => $p['promoter'] ?? 'Non spécifié',
                        'funding_sought' => $target,
                        'expected_roi' => (int)$p['roi'],
                        'description' => $p['description'] ?? '',
                        'status' => $this->translateStatus($p['status']),
                        'project_type' => $p['project_type'] ?? 'residential',
                        'operation_type' => $p['operation_type'] ?? 'sale',
                        'coordinates' => [
                            'lat' => $p['coordinates_lat'] ?? null,
                            'lng' => $p['coordinates_lng'] ?? null
                        ],
                        'image' => $p['image'] ?: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80'
                    ];
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $formattedProjects
                ]);
                exit;
            } catch (\PDOException $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur de base de données: ' . $e->getMessage()
                ]);
                exit;
            }
        }
        
        // No database connection
        echo json_encode([
            'success' => false,
            'message' => 'Base de données non disponible'
        ]);
        exit;
    }

    /**
     * Récupérer les projets du porteur connecté
     */
    public function getPromoterProjects()
    {
        header('Content-Type: application/json');
        
        if ($this->db) {
            try {
                $promoterId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Use 1 for test
                
                $stmt = $this->db->prepare("
                    SELECT p.*, 
                           (SELECT COUNT(*) FROM reservations r WHERE r.project_id = p.id) as reservation_count,
                           (SELECT COUNT(*) FROM visit_requests v WHERE v.project_id = p.id) as visit_count
                    FROM projects p 
                    WHERE p.promoter_id = ? 
                    ORDER BY p.created_at DESC
                ");
                $stmt->execute([$promoterId]);
                $projects = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $projects
                ]);
                exit;
            } catch (\PDOException $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur de base de données: ' . $e->getMessage()
                ]);
                exit;
            }
        }
        
        // No database connection
        echo json_encode([
            'success' => false,
            'message' => 'Base de données non disponible'
        ]);
    }

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register()
    {
        header('Content-Type: application/json');
        
        error_log('Register API called');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('Register API: Method not allowed');
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            error_log('Register API: Database not available');
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        error_log('Register API data: ' . json_encode($data));

        // Validation
        $required = ['name', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                error_log("Register API: Missing field $field");
                echo json_encode([
                    'success' => false,
                    'message' => "Le champ $field est requis"
                ]);
                exit;
            }
        }

        // Validation email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            error_log('Register API: Invalid email');
            echo json_encode(['success' => false, 'message' => 'Email invalide']);
            exit;
        }

        // Validation mot de passe
        if (strlen($data['password']) < 8) {
            error_log('Register API: Password too short');
            echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
            exit;
        }

        try {
            // Vérifier si l'email existe déjà
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            
            if ($stmt->fetch()) {
                error_log('Register API: Email already exists');
                echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
                exit;
            }

            // Hasher le mot de passe avec options sécurisées
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]);
            error_log('Register API: Password hashed successfully');

            $name = trim($data['name'] ?? '');
            if (empty($name) && (!empty($data['first_name']) || !empty($data['last_name']))) {
                $name = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            }
            
            $nameParts = explode(' ', $name, 2);
            $firstName = $data['first_name'] ?? ($nameParts[0] ?? '');
            $lastName = $data['last_name'] ?? ($nameParts[1] ?? '');
            $role = in_array($data['role'] ?? '', ['admin', 'investor', 'promoter', 'project_owner']) ? $data['role'] : 'investor';
            $investorType = $data['investor_type'] ?? 'individual';

            // Insérer l'utilisateur avec toutes les colonnes compatibles
            $stmt = $this->db->prepare("
                INSERT INTO users (name, first_name, last_name, email, password, role, status, phone, country, city, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?, ?, NOW())
            ");
            
            $result = $stmt->execute([
                $name,
                $firstName,
                $lastName,
                $data['email'],
                $passwordHash,
                $role,
                $data['phone'] ?? '',
                $data['country'] ?? '',
                $data['city'] ?? ''
            ]);
            
            if (!$result) {
                error_log('Register API: Insert failed');
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion dans la base de données']);
                exit;
            }
            
            $userId = $this->db->lastInsertId();
            error_log('Register API: User created with ID ' . $userId);

            // Si c'est un investisseur, insérer dans la table investors
            if ($role === 'investor' || $role === 'promoter') {
                try {
                    $invStmt = $this->db->prepare("
                        INSERT INTO investors (
                            user_id, type, investor_type, investor_status, 
                            company_name, country, city, address, phone
                        ) VALUES (
                            ?, 'individual', ?, 'approved', ?, 
                            ?, ?, ?, ?
                        )
                    ");
                    $invStmt->execute([
                        $userId,
                        $investorType,
                        $data['company_name'] ?? '',
                        $data['country'] ?? '',
                        $data['city'] ?? '',
                        $data['address'] ?? '',
                        $data['phone'] ?? ''
                    ]);
                } catch (\PDOException $invEx) {
                    error_log('Register API: Failed to insert into investors table: ' . $invEx->getMessage());
                }
            }

            // Connecter automatiquement l'utilisateur
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['user_role'] = $role;
            
            error_log('Register API: Session created');

            echo json_encode([
                'success' => true,
                'message' => 'Inscription réussie ! Vous êtes maintenant connecté.',
                'user' => [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $data['email'],
                    'role' => $role
                ]
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login()
    {
        header('Content-Type: application/json');
        
        error_log('Login API called');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('Login API: Method not allowed');
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            error_log('Login API: Database not available');
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        error_log('Login API data: ' . json_encode($data));

        // Validation
        if (empty($data['email']) || empty($data['password'])) {
            error_log('Login API: Missing email or password');
            echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
            exit;
        }

        try {
            // Rechercher l'utilisateur
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            error_log('Login API: User found: ' . ($user ? 'yes' : 'no'));

            if (!$user) {
                error_log('Login API: User not found for email: ' . $data['email']);
                echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
                exit;
            }

            // Vérifier le mot de passe
            $passwordVerified = password_verify($data['password'], $user['password']);
            error_log('Login API: Password verified: ' . ($passwordVerified ? 'yes' : 'no'));
            
            if (!$passwordVerified) {
                error_log('Login API: Password incorrect for user: ' . $data['email']);
                echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
                exit;
            }

            // Vérifier le statut
            if ($user['status'] !== 'active') {
                error_log('Login API: User status not active: ' . $user['status']);
                echo json_encode(['success' => false, 'message' => 'Compte non activé']);
                exit;
            }

            // Créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            error_log('Login API: Session created for user ID: ' . $user['id']);

            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie !',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        header('Content-Type: application/json');
        
        // Détruire la session
        session_unset();
        session_destroy();

        echo json_encode([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
        exit;
    }

    /**
     * Soumettre un nouveau projet
     */
    public function submitProject()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        // Récupérer les données POST
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        // Validation basique
        $required = ['name', 'owner', 'location', 'sector', 'target'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                echo json_encode([
                    'success' => false,
                    'message' => "Le champ $field est requis"
                ]);
                exit;
            }
        }

        try {
            // Parse location
            $locationParts = explode(',', $data['location']);
            $city = trim($locationParts[0]);
            $country = isset($locationParts[1]) ? trim($locationParts[1]) : 'RDC';
            
            // Get promoter_id from session if user is logged in
            $promoterId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            
            if (!$promoterId) {
                echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour soumettre un projet.']);
                exit;
            }

            $slugBase = preg_replace('/[^a-z0-9]+/i', '-', mb_strtolower(trim($data['name'])));
            $slugBase = trim($slugBase, '-');
            if (empty($slugBase)) {
                $slugBase = 'projet-' . time();
            }
            $slug = $slugBase;
            $counter = 1;
            while (true) {
                $checkStmt = $this->db->prepare('SELECT COUNT(*) FROM projects WHERE slug = ?');
                $checkStmt->execute([$slug]);
                if ($checkStmt->fetchColumn() == 0) {
                    break;
                }
                $slug = $slugBase . '-' . $counter++;
            }

            // Insert into database with pending status and submitted validation status
            $stmt = $this->db->prepare("
                INSERT INTO projects (
                    user_id, title, slug, promoter, promoter_id, city, country, sector,
                    description, funding_sought, funding_raised, expected_roi, status, validation_status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'submitted', NOW())
            ");
            
            $stmt->execute([
                $promoterId,
                $data['name'],
                $slug,
                $data['owner'],
                $promoterId,
                $city,
                $country,
                $data['sector'],
                $data['description'] ?? '',
                (float)$data['target'],
                0,
                (float)($data['roi'] ?? 20)
            ]);
            
            $projectId = $this->db->lastInsertId();

            $uploadPath = __DIR__ . '/../../uploads/projects';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $uploadedDocumentPaths = [];
            $documentsData = [];

            if (isset($_FILES['business_plan']) && $_FILES['business_plan']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['business_plan'];
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('business_plan_' . $projectId . '_', true) . '.' . $extension;
                $destination = $uploadPath . '/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $uploadedDocumentPaths['business_plan'] = '/uploads/projects/' . $filename;
                    $documentsData['business_plan'] = $uploadedDocumentPaths['business_plan'];
                }
            }

            if (isset($_FILES['pitch_deck']) && $_FILES['pitch_deck']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['pitch_deck'];
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('pitch_deck_' . $projectId . '_', true) . '.' . $extension;
                $destination = $uploadPath . '/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $documentsData['pitch_deck'] = '/uploads/projects/' . $filename;
                }
            }

            if (isset($_FILES['financial_model']) && $_FILES['financial_model']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['financial_model'];
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('financial_model_' . $projectId . '_', true) . '.' . $extension;
                $destination = $uploadPath . '/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $uploadedDocumentPaths['financial_model'] = '/uploads/projects/' . $filename;
                    $documentsData['financial_model'] = $uploadedDocumentPaths['financial_model'];
                }
            }

            $updateColumns = [];
            $updateParams = [];

            if (isset($uploadedDocumentPaths['business_plan'])) {
                $updateColumns[] = 'business_plan = ?';
                $updateParams[] = $uploadedDocumentPaths['business_plan'];
            }
            if (isset($uploadedDocumentPaths['financial_model'])) {
                $updateColumns[] = 'financial_model = ?';
                $updateParams[] = $uploadedDocumentPaths['financial_model'];
            }
            if (!empty($documentsData)) {
                $updateColumns[] = 'documents = ?';
                $updateParams[] = json_encode($documentsData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            if (!empty($updateColumns)) {
                $updateSql = 'UPDATE projects SET ' . implode(', ', $updateColumns) . ' WHERE id = ?';
                $updateParams[] = $projectId;
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute($updateParams);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet soumis avec succès',
                'data' => [
                    'id' => $projectId,
                    'title' => $data['name'],
                    'status' => 'En attente'
                ]
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Soumettre le formulaire de contact
     */
    public function submitContact()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        // Validation
        $required = ['name', 'email', 'message'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                echo json_encode([
                    'success' => false,
                    'message' => "Le champ $field est requis"
                ]);
                exit;
            }
        }

        try {
            // Insert into contacts table
            $stmt = $this->db->prepare("
                INSERT INTO contacts (
                    name, email, phone, subject, message, created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'] ?? '',
                $data['subject'] ?? '',
                $data['message']
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Message envoyé avec succès'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Vérifier l'authentification de l'investisseur
     */
    public function checkAuth()
    {
        header('Content-Type: application/json');
        
        // Debug session
        error_log('Session data in checkAuth: ' . json_encode($_SESSION));
        
        $isAuthenticated = isset($_SESSION['user_id']);
        $isInvestor = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'investor';
        
        $response = [
            'success' => true,
            'authenticated' => $isAuthenticated,
            'is_investor' => $isInvestor,
            'user' => $isAuthenticated ? [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? 'Utilisateur',
                'email' => $_SESSION['user_email'] ?? '',
                'role' => $_SESSION['user_role'] ?? '',
                'initials' => substr($_SESSION['user_name'] ?? 'JD', 0, 2)
            ] : null
        ];
        
        error_log('Auth check response: ' . json_encode($response));
        
        echo json_encode($response);
        exit;
    }

    /**
     * Approuver ou rejeter un projet (Admin)
     */
    public function approveProject()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        if (empty($data['id']) || empty($data['action'])) {
            echo json_encode(['success' => false, 'message' => 'ID et action requis']);
            exit;
        }

        try {
            // Mapping des actions vers les statuts
            $statusMap = [
                'approve' => 'approved',
                'reject' => 'rejected',
                'publish' => 'published',
                'suspend' => 'suspended',
                'archive' => 'archived',
                'request_info' => 'additional_info'
            ];
            
            $status = $statusMap[$data['action']] ?? $data['action'];
            
            $stmt = $this->db->prepare("UPDATE projects SET validation_status = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
            $reviewerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $stmt->execute([$status, $reviewerId, $data['id']]);
            
            // Ajouter les notes si fournies
            if (!empty($data['notes'])) {
                $stmt = $this->db->prepare("UPDATE projects SET review_notes = ? WHERE id = ?");
                $stmt->execute([$data['notes'], $data['id']]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => $this->translateStatus($status)
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Approve an investor (from admin dashboard)
     */
    public function approveInvestorUser()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) $data = $_POST;

        if (empty($data['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'user_id requis']);
            exit;
        }

        $userId = (int)$data['user_id'];
        $reviewerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        try {
            // Update investor record if it exists
            $stmt = $this->db->prepare("UPDATE investors SET investor_status = 'approved', kyc_reviewed_at = NOW(), kyc_reviewed_by = ? WHERE user_id = ?");
            $stmt->execute([$reviewerId, $userId]);

            // Activate user account
            $stmt2 = $this->db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
            $stmt2->execute([$userId]);

            echo json_encode(['success' => true, 'message' => 'Investisseur approuvé avec succès']);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur DB: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Reject an investor (from admin dashboard)
     */
    public function rejectInvestorUser()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) $data = $_POST;

        if (empty($data['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'user_id requis']);
            exit;
        }

        $userId = (int)$data['user_id'];
        $reviewerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        try {
            // Update investor record if it exists
            $stmt = $this->db->prepare("UPDATE investors SET investor_status = 'rejected', kyc_reviewed_at = NOW(), kyc_reviewed_by = ?, rejection_reason = 'Rejected by administrator' WHERE user_id = ?");
            $stmt->execute([$reviewerId, $userId]);

            // Suspend user account
            $stmt2 = $this->db->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
            $stmt2->execute([$userId]);

            echo json_encode(['success' => true, 'message' => 'Investisseur rejeté']);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur DB: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Récupérer les données de l'investisseur connecté
     */
    public function getInvestorData()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $userId = $_SESSION['user_id'];

        try {
            // Get investor stats with COALESCE to handle NULL values for new investors
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(COUNT(DISTINCT inv.project_id), 0) as projects,
                    COALESCE(SUM(inv.amount), 0) as total_invested,
                    COALESCE(AVG(p.expected_roi), 0) as roi_average,
                    COALESCE(SUM(inv.amount * p.expected_roi / 100), 0) as gains
                FROM investments inv
                JOIN projects p ON inv.project_id = p.id
                WHERE inv.investor_id = ?
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Get individual investments
            $stmt = $this->db->prepare("
                SELECT 
                    inv.id,
                    inv.project_id,
                    p.title as project,
                    inv.amount,
                    p.funding_raised as raised,
                    p.funding_sought as target,
                    p.expected_roi as roi,
                    p.status
                FROM investments inv
                JOIN projects p ON inv.project_id = p.id
                WHERE inv.investor_id = ?
                ORDER BY inv.created_at DESC
                LIMIT 10
            ");
            $stmt->execute([$userId]);
            $investments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Format investments
            $formattedInvestments = [];
            foreach ($investments as $inv) {
                $target = (int)$inv['target'];
                $raised = (int)$inv['raised'];
                $progress = $target > 0 ? round(($raised / $target) * 100) : 0;
                
                $formattedInvestments[] = [
                    'id' => $inv['id'] ?? 0,
                    'project_id' => $inv['project_id'] ?? 0,
                    'project_name' => $inv['project'],
                    'amount' => (int)$inv['amount'],
                    'progress' => $progress,
                    'roi' => (int)$inv['roi'],
                    'status' => $this->translateStatus($inv['status'])
                ];
            }
            
            $data = [
                'project_count' => (int)($stats['projects'] ?? 0),
                'total_invested' => (int)($stats['total_invested'] ?? 0),
                'avg_roi' => round((float)($stats['roi_average'] ?? 0), 1),
                'total_gains' => (int)($stats['gains'] ?? 0),
                'investments' => $formattedInvestments
            ];
            
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Invest in a project
     */
    public function investInProject()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        if (empty($data['project_id']) || empty($data['amount'])) {
            echo json_encode(['success' => false, 'message' => 'ID du projet et montant requis']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $projectId = $data['project_id'];
        $amount = (int)$data['amount'];

        try {
            // Check if project exists and is approved
            $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = ? AND status = 'approved'");
            $stmt->execute([$projectId]);
            $project = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$project) {
                echo json_encode(['success' => false, 'message' => 'Projet non trouvé ou non approuvé']);
                exit;
            }

            // Check if already invested
            $stmt = $this->db->prepare("SELECT * FROM investments WHERE investor_id = ? AND project_id = ?");
            $stmt->execute([$userId, $projectId]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Vous avez déjà investi dans ce projet']);
                exit;
            }

            // Insert investment
            $stmt = $this->db->prepare("
                INSERT INTO investments (investor_id, project_id, amount, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            
            $stmt->execute([$userId, $projectId, $amount]);
            
            // Update project funding
            $stmt = $this->db->prepare("UPDATE projects SET funding_raised = funding_raised + ? WHERE id = ?");
            $stmt->execute([$amount, $projectId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Investissement réussi !'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur de base de données: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Upload project image
     */
    public function uploadProjectImage()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!isset($_FILES['image'])) {
            echo json_encode(['success' => false, 'message' => 'Aucune image fournie']);
            exit;
        }

        $image = $_FILES['image'];
        $projectId = $_POST['project_id'] ?? null;

        // Validation
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($image['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
            exit;
        }

        if ($image['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 5MB)']);
            exit;
        }

        try {
            // Generate unique filename
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $filename = uniqid('project_', true) . '.' . $extension;
            $uploadPath = __DIR__ . '/../../uploads/projects/' . $filename;

            // Move file
            if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
                $imageUrl = '/uploads/projects/' . $filename;
                
                // If project ID provided, update database
                if ($projectId && $this->db) {
                    $stmt = $this->db->prepare("UPDATE projects SET image = ? WHERE id = ?");
                    $stmt->execute([$imageUrl, $projectId]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Image uploadée avec succès',
                    'image_url' => $imageUrl
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload']);
            }
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Request a visit
     */
    public function requestVisit()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        $required = ['project_id', 'name', 'email', 'phone', 'preferred_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                echo json_encode(['success' => false, 'message' => "Le champ $field est requis"]);
                exit;
            }
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO visit_requests (
                    project_id, name, email, phone, preferred_date, 
                    message, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            
            $stmt->execute([
                $data['project_id'],
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['preferred_date'],
                $data['message'] ?? ''
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Demande de visite envoyée avec succès'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Make a reservation
     */
    public function makeReservation()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        $required = ['project_id', 'name', 'email', 'phone'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                echo json_encode(['success' => false, 'message' => "Le champ $field est requis"]);
                exit;
            }
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO reservations (
                    project_id, name, email, phone, 
                    property_type, budget, message, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            
            $stmt->execute([
                $data['project_id'],
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['property_type'] ?? 'purchase',
                $data['budget'] ?? 0,
                $data['message'] ?? ''
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Réservation effectuée avec succès'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Update project coordinates (GPS)
     */
    public function updateProjectCoordinates()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        if (empty($data['project_id']) || empty($data['lat']) || empty($data['lng'])) {
            echo json_encode(['success' => false, 'message' => 'project_id, lat et lng requis']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE projects 
                SET coordinates_lat = ?, coordinates_lng = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([$data['lat'], $data['lng'], $data['project_id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Coordonnées mises à jour avec succès'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Create a new project
     */
    public function createProject()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        try {
            $locationParts = explode(',', $data['location']);
            $city = trim($locationParts[0]);
            $country = isset($locationParts[1]) ? trim($locationParts[1]) : 'RDC';
            
            $promoterId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            
            $stmt = $this->db->prepare("
                INSERT INTO projects (
                    title, promoter, promoter_id, city, country, sector, 
                    description, funding_sought, funding_raised, 
                    expected_roi, validation_status, project_type, operation_type,
                    coordinates_lat, coordinates_lng, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted', ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['name'],
                isset($_SESSION['name']) ? $_SESSION['name'] : 'Porteur',
                $promoterId,
                $city,
                $country,
                $data['sector'],
                $data['description'] ?? '',
                $data['target'],
                0,
                $data['roi'] ?? 0,
                $data['project_type'] ?? 'residential',
                $data['operation_type'] ?? 'sale',
                $data['coordinates_lat'] ?? null,
                $data['coordinates_lng'] ?? null
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet créé avec succès',
                'project_id' => $this->db->lastInsertId()
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Update an existing project
     */
    public function updateProject()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        if (empty($data['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID du projet requis']);
            exit;
        }

        try {
            $locationParts = explode(',', $data['location']);
            $city = trim($locationParts[0]);
            $country = isset($locationParts[1]) ? trim($locationParts[1]) : 'RDC';
            
            $stmt = $this->db->prepare("
                UPDATE projects SET
                    title = ?,
                    city = ?,
                    country = ?,
                    sector = ?,
                    description = ?,
                    funding_sought = ?,
                    expected_roi = ?,
                    project_type = ?,
                    operation_type = ?,
                    coordinates_lat = ?,
                    coordinates_lng = ?,
                    validation_status = 'submitted'
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['name'],
                $city,
                $country,
                $data['sector'],
                $data['description'] ?? '',
                $data['target'],
                $data['roi'] ?? 0,
                $data['project_type'] ?? 'residential',
                $data['operation_type'] ?? 'sale',
                $data['coordinates_lat'] ?? null,
                $data['coordinates_lng'] ?? null,
                $data['id']
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet mis à jour avec succès'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Delete a project
     */
    public function deleteProject()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $data = $_POST;
        }

        if (empty($data['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID du projet requis']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$data['id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet supprimé avec succès'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Get project details
     */
    public function getProjectDetails()
    {
        header('Content-Type: application/json');
        
        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $projectId = $_GET['id'] ?? null;
        
        if (empty($projectId)) {
            echo json_encode(['success' => false, 'message' => 'ID du projet requis']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.id,
                    p.title,
                    p.city,
                    p.country,
                    p.sector,
                    p.funding_sought as target,
                    p.funding_raised as raised,
                    p.expected_roi as roi,
                    p.validation_status as status,
                    p.project_type,
                    p.operation_type,
                    p.coordinates_lat,
                    p.coordinates_lng,
                    p.description,
                    CONCAT(p.city, ', ', p.country) as location
                FROM projects p
                WHERE p.id = ?
            ");
            
            $stmt->execute([$projectId]);
            $project = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($project) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $project['id'],
                        'title' => $project['title'],
                        'location' => $project['location'],
                        'country' => $project['country'],
                        'city' => $project['city'],
                        'sector' => $project['sector'],
                        'target' => (int)$project['target'],
                        'raised' => (int)$project['raised'],
                        'roi' => (int)$project['roi'],
                        'status' => $this->translateStatus($project['status']),
                        'project_type' => $project['project_type'] ?? 'residential',
                        'operation_type' => $project['operation_type'] ?? 'sale',
                        'coordinates' => [
                            'lat' => $project['coordinates_lat'] ?? null,
                            'lng' => $project['coordinates_lng'] ?? null
                        ],
                        'description' => $project['description']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Projet non trouvé']);
            }
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Récupérer tous les utilisateurs (pour l'administration)
     */
    public function getUsers()
    {
        header('Content-Type: application/json');

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        try {
            $stmt = $this->db->query("
                SELECT u.id, u.name, u.email, u.role, u.phone, u.created_at, u.status,
                       i.investor_status, i.id as investor_record_id
                FROM users u
                LEFT JOIN investors i ON i.user_id = u.id
                ORDER BY u.created_at DESC
            ");
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Modifier le statut d'un utilisateur (activer/bloquer)
     */
    public function updateUserStatus()
    {
        header('Content-Type: application/json');

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $userId = $data['user_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$userId || !$status) {
            echo json_encode(['success' => false, 'message' => 'ID utilisateur et statut requis']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$status, $userId]);

            echo json_encode([
                'success' => true,
                'message' => 'Statut de l\'utilisateur mis à jour'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser()
    {
        header('Content-Type: application/json');

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $userId = $data['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'ID utilisateur requis']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Récupérer tous les investissements pour le Dashboard Admin
     */
    public function getAllInvestments()
    {
        header('Content-Type: application/json');

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        try {
            $stmt = $this->db->query("
                SELECT 
                    inv.id,
                    inv.amount,
                    inv.created_at,
                    inv.status,
                    p.title as project_title,
                    p.id as project_id,
                    u.name as investor_name,
                    u.email as investor_email
                FROM investments inv
                LEFT JOIN projects p ON inv.project_id = p.id
                LEFT JOIN users u ON inv.investor_id = u.id
                ORDER BY inv.created_at DESC
            ");
            $investments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Somme totale investie
            $totalStmt = $this->db->query("SELECT COALESCE(SUM(amount), 0) as total FROM investments");
            $totalAmount = (int)($totalStmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

            echo json_encode([
                'success' => true,
                'total_amount' => $totalAmount,
                'data' => $investments
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Mettre à jour le statut d'un investissement (ex: confirmé / en attente / annulé)
     */
    public function updateInvestmentStatus()
    {
        header('Content-Type: application/json');

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $invId = $data['investment_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$invId || !$status) {
            echo json_encode(['success' => false, 'message' => 'ID investissement et statut requis']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE investments SET status = ? WHERE id = ?");
            $stmt->execute([$status, $invId]);

            echo json_encode([
                'success' => true,
                'message' => 'Statut de l\'investissement mis à jour'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Supprimer un investissement
     */
    public function deleteInvestment()
    {
        header('Content-Type: application/json');

        if (!$this->db) {
            echo json_encode(['success' => false, 'message' => 'Base de données non disponible']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $invId = $data['investment_id'] ?? null;

        if (!$invId) {
            echo json_encode(['success' => false, 'message' => 'ID investissement requis']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM investments WHERE id = ?");
            $stmt->execute([$invId]);

            echo json_encode([
                'success' => true,
                'message' => 'Investissement supprimé avec succès'
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
            exit;
        }
    }
}
