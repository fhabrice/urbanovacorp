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
     * Vérifie si une colonne existe (compatibilité schémas anciens / nouveaux)
     */
    private function hasColumn($table, $column)
    {
        static $cache = [];
        $key = $table . '.' . $column;
        if (array_key_exists($key, $cache)) return $cache[$key];
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
            );
            $stmt->execute([$table, $column]);
            $cache[$key] = (int)$stmt->fetchColumn() > 0;
        } catch (\Throwable $e) {
            $cache[$key] = false;
        }
        return $cache[$key];
    }

    /**
     * Message d'erreur actionnable quand la base n'a pas encore été mise à jour
     */
    private function dbUpgradeHint(\PDOException $e)
    {
        $msg = $e->getMessage();
        if (stripos($msg, '42S02') !== false || stripos($msg, '42S22') !== false) {
            return 'Base de données à mettre à jour : ouvrez https://votre-site/upgrade.php (mot de passe admin) ou importez database/upgrade_schema.sql dans phpMyAdmin. Détail : ' . $msg;
        }
        return 'Erreur de base de données: ' . $msg;
    }

    /**
     * Récupérer tous les projets pour la marketplace
     */
    public function getProjects()
    {
        header('Content-Type: application/json');
        
        if ($this->db) {
            try {
                // Sélection adaptative : fonctionne aussi si la base n'a pas encore
                // été mise à jour (colonne validation_status absente -> status)
                $f = ['p.id', 'p.title', 'p.city', 'p.country', 'p.sector',
                      'p.funding_sought as target', 'p.funding_raised as raised',
                      'p.expected_roi as roi', 'p.promoter', 'p.description', 'p.image'];
                if ($this->hasColumn('projects', 'validation_status')) {
                    $f[] = 'p.validation_status as status';
                    $where = 'p.validation_status IS NOT NULL';
                } else {
                    $f[] = 'p.status as status';
                    $where = 'p.status IS NOT NULL';
                }
                foreach (['project_type', 'operation_type', 'coordinates_lat', 'coordinates_lng',
                          'video_url', 'virtual_tour_url', 'google_maps_embed', 'brochure_path',
                          'availability', 'price', 'is_featured'] as $col) {
                    if ($this->hasColumn('projects', $col)) $f[] = 'p.' . $col;
                }
                $f[] = "CONCAT(p.city, ', ', p.country) as location";
                $stmt = $this->db->query('SELECT ' . implode(', ', $f) . " FROM projects p WHERE " . $where . " ORDER BY p.created_at DESC");
                
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
                        'image' => $p['image'] ?: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
                        'video_url' => $p['video_url'] ?? null,
                        'virtual_tour_url' => $p['virtual_tour_url'] ?? null,
                        'google_maps_embed' => $p['google_maps_embed'] ?? null,
                        'brochure_path' => $p['brochure_path'] ?? null,
                        'availability' => $p['availability'] ?? 'available',
                        'price' => $p['price'] ?? null,
                        'is_featured' => (int)($p['is_featured'] ?? 0)
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
                    'message' => $this->dbUpgradeHint($e)
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
            $role = in_array($data['role'] ?? '', ['investor', 'promoter', 'client', 'project_owner']) ? $data['role'] : 'investor';
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
                    // KYC : le compte investisseur est validé par Urbanova (statut pending)
                    $invStmt = $this->db->prepare("
                        INSERT INTO investors (
                            user_id, type, investor_type, investor_status, 
                            company_name, country, city, address, phone
                        ) VALUES (
                            ?, 'individual', ?, 'pending', ?, 
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
                    $this->notifyAdmins('kyc_status', 'Nouvel investisseur en attente de validation KYC',
                        'Le compte de ' . $data['email'] . ' attend la validation KYC par Urbanova.');
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id'])) $this->jsonOut(['success' => false, 'message' => 'ID du projet requis']);

        try {
            $id = $data['id'];
            $fields = [];
            $params = [];

            $map = [
                'name'          => 'title',
                'title'         => 'title',
                'city'          => 'city',
                'country'       => 'country',
                'sector'        => 'sector',
                'description'   => 'description',
                'target'        => 'funding_sought',
                'funding_sought'=> 'funding_sought',
                'price'         => 'price',
                'roi'           => 'expected_roi',
                'expected_roi'  => 'expected_roi',
                'project_type'  => 'project_type',
                'operation_type'=> 'operation_type',
                'video_url'     => 'video_url',
                'virtual_tour_url' => 'virtual_tour_url',
                'google_maps_embed' => 'google_maps_embed',
                'brochure_path' => 'brochure_path',
                'availability'  => 'availability',
                'image'         => 'image',
                'coordinates_lat' => 'coordinates_lat',
                'coordinates_lng' => 'coordinates_lng',
            ];

            foreach ($map as $inputKey => $column) {
                if (array_key_exists($inputKey, $data) && $data[$inputKey] !== '') {
                    $fields[] = "$column = ?";
                    $params[] = $data[$inputKey];
                }
            }

            // Le porteur peut modifier tant que le projet n'est pas validé
            $status = null;
            if (in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'project_manager'], true) === false) {
                $status = "submitted";
            } else {
                $status = $data['validation_status'] ?? null;
            }
            if (!empty($data['validation_status'])) {
                $fields[] = 'validation_status = ?';
                $params[] = $data['validation_status'];
            }

            if (empty($fields)) {
                $this->jsonOut(['success' => false, 'message' => 'Aucun champ à mettre à jour']);
            }

            $params[] = $id;
            $sql = "UPDATE projects SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $this->jsonOut(['success' => true, 'message' => 'Projet mis à jour avec succès']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
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
    // ==================================================================
    // ============ MODULES CAHIER DES CHARGES - HELPERS ================
    // ==================================================================

    /**
     * Réponse JSON normalisée
     */
    private function jsonOut($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Vérifie que l'utilisateur connecté possède un des rôles requis
     */
    private function requireRole(array $roles, $message = 'Accès non autorisé')
    {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', $roles, true)) {
            $this->jsonOut(['success' => false, 'message' => $message], 403);
        }
    }

    /**
     * Vérifie une permission RBAC (tables permissions / role_permissions)
     * avec repli sur une table de rôles par défaut si les tables n'existent pas.
     */
    private function hasPermission($permission)
    {
        $role = $_SESSION['user_role'] ?? null;
        if (!$role) return false;
        if ($role === 'super_admin') return true;

        $fallback = [
            'admin' => ['manage_users', 'manage_projects', 'validate_projects', 'manage_investors', 'manage_campaigns', 'view_analytics'],
            'project_manager' => ['manage_projects', 'validate_projects', 'view_analytics'],
            'promoter' => ['manage_own_projects'],
            'investor' => ['view_projects', 'submit_offers', 'access_dataroom'],
            'client' => ['view_marketplace', 'request_visits', 'make_reservations'],
        ];

        if ($this->db) {
            try {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*)
                    FROM role_permissions rp
                    JOIN permissions p ON p.id = rp.permission_id
                    WHERE rp.role = ? AND p.name = ?
                ");
                $stmt->execute([$role, $permission]);
                if ((int)$stmt->fetchColumn() > 0) return true;
            } catch (\PDOException $e) {
                // Tables absentes -> repli
            }
        }

        return in_array($permission, $fallback[$role] ?? [], true);
    }

    /**
     * Crée une notification pour un utilisateur
     */
    private function notify($userId, $type, $title, $message, $link = null)
    {
        if (!$this->db || !$userId) return;
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$userId, $type, $title, $message, $link]);
        } catch (\PDOException $e) {
            // Notification non bloquante
        }
    }

    /**
     * Notifie tous les administrateurs / gestionnaires
     */
    private function notifyAdmins($type, $title, $message, $link = null)
    {
        if (!$this->db) return;
        try {
            $stmt = $this->db->query(
                "SELECT id FROM users WHERE role IN ('super_admin', 'admin', 'project_manager')"
            );
            foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $adminId) {
                $this->notify($adminId, $type, $title, $message, $link);
            }
        } catch (\PDOException $e) {
            // Non bloquant
        }
    }

    /**
     * Enregistre une entrée dans le journal d'audit de la Data Room
     */
    private function logDataRoomAccess($projectId, $investorId, $action, $details = null, $documentId = null)
    {
        if (!$this->db || !$projectId || !$investorId) return;
        try {
            $stmt = $this->db->prepare("
                INSERT INTO data_room_audit_log
                    (project_id, investor_id, action, document_id, details, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $projectId,
                $investorId,
                $action,
                $documentId,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
        } catch (\PDOException $e) {
            // Non bloquant
        }
    }

    /**
     * Retourne l'id de la fiche investisseur liée à l'utilisateur connecté
     */
    private function getCurrentInvestorId()
    {
        if (!$this->db || empty($_SESSION['user_id'])) return null;
        try {
            $stmt = $this->db->prepare("SELECT id FROM investors WHERE user_id = ? LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $id = $stmt->fetchColumn();
            return $id ? (int)$id : null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Retourne l'id de la fiche investisseur d'un utilisateur
     */
    private function getInvestorIdForUser($userId)
    {
        if (!$this->db || !$userId) return null;
        try {
            $stmt = $this->db->prepare("SELECT id FROM investors WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $id = $stmt->fetchColumn();
            return $id ? (int)$id : null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    // ==================================================================
    // ============ MODULE 1 - MARKETPLACE : DEMANDES D'INFO ============
    // ==================================================================

    /**
     * Visiteur -> demande d'informations sur un projet
     */
    public function submitProjectInquiry()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) {
            $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $required = ['project_id', 'name', 'email', 'message'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->jsonOut(['success' => false, 'message' => "Le champ $field est requis"]);
            }
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO project_inquiries (project_id, name, email, phone, message)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                (int)$data['project_id'],
                trim($data['name']),
                trim($data['email']),
                trim($data['phone'] ?? ''),
                trim($data['message']),
            ]);

            // Notifier les administrateurs
            $this->notifyAdmins('inquiry', 'Nouvelle demande d\'information',
                'Demande de ' . $data['name'] . ' sur le projet #' . $data['project_id'],
                '/'); 

            $this->jsonOut(['success' => true, 'message' => 'Votre demande a bien été envoyée. Urbanova vous répondra rapidement.']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : liste des demandes d'informations
     */
    public function getProjectInquiries()
    {
        if (!$this->db) {
            $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        }
        try {
            $stmt = $this->db->query("
                SELECT i.*, p.title AS project_title
                FROM project_inquiries i
                LEFT JOIN projects p ON p.id = i.project_id
                ORDER BY i.created_at DESC
                LIMIT 200
            ");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $this->jsonOut(['success' => true, 'data' => $rows]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : mettre à jour le statut d'une demande d'information
     */
    public function updateProjectInquiryStatus()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id']) || empty($data['status'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID et statut requis']);
        }
        try {
            $stmt = $this->db->prepare("UPDATE project_inquiries SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $data['id']]);
            $this->jsonOut(['success' => true, 'message' => 'Statut de la demande mis à jour']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    // ==================================================================
    // ============ MODULE 2 - LEVÉE DE FONDS : MANDATS =================
    // ==================================================================

    /**
     * Porteur de projet : soumettre une demande de mandat de levée de fonds
     */
    public function submitFundingRequest()
    {
        $this->requireRole(['promoter', 'admin', 'super_admin', 'project_manager'], 'Vous devez être porteur de projet connecté');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['project_id'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID du projet requis']);
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO funding_requests (project_id, user_id, summary, amount_requested)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                (int)$data['project_id'],
                (int)$_SESSION['user_id'],
                trim($data['summary'] ?? ''),
                $data['amount_requested'] ?? null,
            ]);

            $this->notifyAdmins('mandate', 'Nouvelle demande de mandat',
                'Demande de levée de fonds pour le projet #' . $data['project_id']);

            $this->jsonOut(['success' => true, 'message' => 'Votre demande de mandat a été soumise. Urbanova réalisera une étude préalable.']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Porteur : mes demandes de mandat
     */
    public function getMyFundingRequests()
    {
        $this->requireRole(['promoter', 'admin', 'super_admin', 'project_manager'], 'Accès réservé aux porteurs de projet');
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, p.title AS project_title, p.city, p.country
                FROM funding_requests r
                LEFT JOIN projects p ON p.id = r.project_id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : liste des demandes de mandat
     */
    public function getFundingRequests()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stmt = $this->db->query("
                SELECT r.*, p.title AS project_title, p.city, p.country,
                       u.name AS applicant_name, u.email AS applicant_email
                FROM funding_requests r
                LEFT JOIN projects p ON p.id = r.project_id
                LEFT JOIN users u ON u.id = r.user_id
                ORDER BY r.created_at DESC
                LIMIT 300
            ");
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : étudier une demande de mandat (accepter / compléments / refuser)
     */
    public function reviewFundingRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id']) || empty($data['action'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID et action requis']);
        }

        try {
            $statusMap = [
                'accept' => 'accepted',
                'request_info' => 'requested_info',
                'reject' => 'rejected',
                'close' => 'closed',
                'cancel' => 'cancelled',
            ];
            $status = $statusMap[$data['action']] ?? $data['action'];
            $commission = $data['commission_rate'] ?? null;
            $notes = $data['notes'] ?? null;
            $mandateRef = $data['mandate_reference'] ?? null;

            $stmt = $this->db->prepare("
                UPDATE funding_requests
                SET status = ?, commission_rate = COALESCE(?, commission_rate),
                    admin_notes = COALESCE(?, admin_notes),
                    mandate_reference = COALESCE(?, mandate_reference),
                    mandate_date = CASE WHEN ? = 'accepted' THEN CURDATE() ELSE mandate_date END,
                    decided_by = ?, decided_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $status, $commission, $notes, $mandateRef, $status,
                $_SESSION['user_id'] ?? null, $data['id'],
            ]);

            // Récupérer le projet pour notifier + créer la campagne
            $stmt = $this->db->prepare("SELECT project_id, user_id, amount_requested FROM funding_requests WHERE id = ?");
            $stmt->execute([$data['id']]);
            $req = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($req) {
                // Notification au porteur
                $this->notify((int)$req['user_id'], 'mandate',
                    'Décision sur votre demande de mandat',
                    'Votre demande de levée de fonds (projet #' . $req['project_id'] . ') est : ' . $status,
                    '/promoteur');

                // Création automatique d'une campagne brouillon si accepté
                if ($status === 'accepted' && !empty($req['project_id'])) {
                    try {
                        $check = $this->db->prepare("SELECT COUNT(*) FROM funding_campaigns WHERE project_id = ?");
                        $check->execute([$req['project_id']]);
                        if ((int)$check->fetchColumn() === 0) {
                            $stmtP = $this->db->prepare("SELECT title FROM projects WHERE id = ?");
                            $stmtP->execute([$req['project_id']]);
                            $title = $stmtP->fetchColumn();
                            $stmtC = $this->db->prepare("
                                INSERT INTO funding_campaigns
                                    (project_id, title, description, target_amount, commission_rate, start_date, end_date, status)
                                VALUES (?, ?, ?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 'draft')
                            ");
                            $stmtC->execute([
                                $req['project_id'],
                                'Campagne ' . ($title ?: '#' . $req['project_id']),
                                'Levée de fonds organisée par Urbanova Solutions',
                                $req['amount_requested'] ?: 100000,
                                $commission ?: 3.00,
                            ]);
                        }
                    } catch (\PDOException $e) {
                        // La campagne n'a pas pu être créée automatiquement
                    }
                }
            }

            $this->jsonOut(['success' => true, 'message' => 'Demande de mandat mise à jour']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    // ==================================================================
    // ============ MODULE 2 - LEVÉE DE FONDS : CAMPAGNES ===============
    // ==================================================================

    /**
     * Liste des campagnes (publique pour la marketplace / investisseurs)
     */
    public function getCampaigns()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stmt = $this->db->query("
                SELECT c.*, p.title AS project_title, p.city, p.country, p.image,
                       p.description AS project_description
                FROM funding_campaigns c
                LEFT JOIN projects p ON p.id = c.project_id
                ORDER BY c.created_at DESC
            ");
            $campaigns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Montant mobilisé = somme des offres acceptées
            foreach ($campaigns as &$c) {
                $stmt = $this->db->prepare("
                    SELECT COALESCE(SUM(amount), 0) FROM investment_offers
                    WHERE campaign_id = ? AND status = 'accepted'
                ");
                $stmt->execute([$c['id']]);
                $c['raised'] = (float)$stmt->fetchColumn();
                $c['target'] = (float)$c['target_amount'];
                $c['progress'] = $c['target'] > 0
                    ? round(($c['raised'] / $c['target']) * 100, 1)
                    : 0;
            }

            $this->jsonOut(['success' => true, 'data' => $campaigns]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : créer une campagne de financement
     */
    public function createCampaign()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $required = ['project_id', 'title', 'target_amount', 'start_date', 'end_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) $this->jsonOut(['success' => false, 'message' => "Le champ $field est requis"]);
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO funding_campaigns
                    (project_id, title, description, target_amount, minimum_investment,
                     maximum_investment, commission_rate, start_date, end_date, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                (int)$data['project_id'],
                trim($data['title']),
                trim($data['description'] ?? ''),
                $data['target_amount'],
                $data['minimum_investment'] ?? null,
                $data['maximum_investment'] ?? null,
                $data['commission_rate'] ?? 3.00,
                $data['start_date'],
                $data['end_date'],
                $data['status'] ?? 'draft',
            ]);

            $this->jsonOut(['success' => true, 'message' => 'Campagne créée avec succès', 'id' => (int)$this->db->lastInsertId()]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : mettre à jour une campagne
     */
    public function updateCampaign()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id'])) $this->jsonOut(['success' => false, 'message' => 'ID de campagne requis']);

        try {
            $stmt = $this->db->prepare("
                UPDATE funding_campaigns SET
                    title = COALESCE(?, title),
                    description = COALESCE(?, description),
                    target_amount = COALESCE(?, target_amount),
                    minimum_investment = COALESCE(?, minimum_investment),
                    maximum_investment = COALESCE(?, maximum_investment),
                    commission_rate = COALESCE(?, commission_rate),
                    start_date = COALESCE(?, start_date),
                    end_date = COALESCE(?, end_date),
                    status = COALESCE(?, status)
                WHERE id = ?
            ");
            $stmt->execute([
                $data['title'] ?? null,
                $data['description'] ?? null,
                $data['target_amount'] ?? null,
                $data['minimum_investment'] ?? null,
                $data['maximum_investment'] ?? null,
                $data['commission_rate'] ?? null,
                $data['start_date'] ?? null,
                $data['end_date'] ?? null,
                $data['status'] ?? null,
                $data['id'],
            ]);
            $this->jsonOut(['success' => true, 'message' => 'Campagne mise à jour']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : tableau de bord levée de fonds (pipeline + statistiques)
     */
    public function getFundingDashboard()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stats = [];

            $stmt = $this->db->query("SELECT COUNT(*) FROM funding_campaigns WHERE status = 'active'");
            $stats['active_campaigns'] = (int)$stmt->fetchColumn();

            $stmt = $this->db->query("SELECT COALESCE(SUM(target_amount),0) FROM funding_campaigns WHERE status IN ('active','draft')");
            $stats['total_target'] = (float)$stmt->fetchColumn();

            $stmt = $this->db->query("SELECT COALESCE(SUM(o.amount),0) FROM investment_offers o WHERE o.status='accepted'");
            $stats['total_raised'] = (float)$stmt->fetchColumn();

            $stmt = $this->db->query("SELECT COUNT(DISTINCT investor_id) FROM investment_offers");
            $stats['total_investors'] = (int)$stmt->fetchColumn();

            $stmt = $this->db->query("SELECT COUNT(*) FROM funding_requests");
            $stats['mandate_requests'] = (int)$stmt->fetchColumn();

            $stmt = $this->db->query("SELECT status, COUNT(*) AS c FROM funding_requests GROUP BY status");
            $stats['mandate_by_status'] = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

            $stmt = $this->db->query("SELECT status, COUNT(*) AS c FROM investment_offers GROUP BY status");
            $stats['offers_by_status'] = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

            // Pipeline : campagnes avec progression
            $stmt = $this->db->query("
                SELECT c.*, p.title AS project_title, p.city, p.country,
                       (SELECT COALESCE(SUM(amount),0) FROM investment_offers o
                        WHERE o.campaign_id = c.id AND o.status='accepted') AS raised
                FROM funding_campaigns c
                LEFT JOIN projects p ON p.id = c.project_id
                ORDER BY c.created_at DESC
                LIMIT 100
            ");
            $campaigns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($campaigns as &$c) {
                $c['target'] = (float)$c['target_amount'];
                $c['raised'] = (float)$c['raised'];
                $c['progress'] = $c['target'] > 0 ? round(($c['raised'] / $c['target']) * 100, 1) : 0;
            }
            $stats['campaigns'] = $campaigns;

            $this->jsonOut(['success' => true, 'data' => $stats]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }
    // ==================================================================
    // ============ MODULE 2 - LEVÉE DE FONDS : OFFRES ==================
    // ==================================================================

    /**
     * Investisseur : soumettre une offre d'investissement sur une campagne
     */
    public function submitInvestmentOffer()
    {
        $this->requireRole(['investor', 'admin', 'super_admin'], 'Seuls les investisseurs certifiés peuvent soumettre une offre');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['campaign_id']) || empty($data['amount'])) {
            $this->jsonOut(['success' => false, 'message' => 'Campagne et montant requis']);
        }

        $investorId = $this->getCurrentInvestorId();
        if (!$investorId) {
            $this->jsonOut(['success' => false, 'message' => 'Compte investisseur introuvable. Complétez votre KYC.']);
        }

        try {
            // Vérifier statut investisseur
            $stmt = $this->db->prepare("SELECT investor_status FROM investors WHERE id = ?");
            $stmt->execute([$investorId]);
            $status = $stmt->fetchColumn();
            if ($status !== 'approved') {
                $this->jsonOut(['success' => false, 'message' => 'Votre profil investisseur doit être approuvé (KYC) avant de soumettre une offre.']);
            }

            $stmt = $this->db->prepare("
                INSERT INTO investment_offers (campaign_id, investor_id, amount, message)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                (int)$data['campaign_id'],
                $investorId,
                $data['amount'],
                trim($data['message'] ?? ''),
            ]);

            $this->notifyAdmins('offer', 'Nouvelle offre d\'investissement',
                'Offre de ' . number_format((float)$data['amount'], 0, ',', ' ') . ' $ sur la campagne #' . $data['campaign_id']);

            $this->jsonOut(['success' => true, 'message' => 'Votre offre a été soumise et sera analysée par Urbanova.']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Investisseur : mes offres
     */
    public function getMyInvestmentOffers()
    {
        $this->requireRole(['investor', 'admin', 'super_admin'], 'Accès réservé aux investisseurs');
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $investorId = $this->getCurrentInvestorId();
        if (!$investorId) $this->jsonOut(['success' => true, 'data' => []]);

        try {
            $stmt = $this->db->prepare("
                SELECT o.*, c.title AS campaign_title, p.title AS project_title
                FROM investment_offers o
                LEFT JOIN funding_campaigns c ON c.id = o.campaign_id
                LEFT JOIN projects p ON p.id = c.project_id
                WHERE o.investor_id = ?
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$investorId]);
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : liste des offres d'investissement
     */
    public function getInvestmentOffers()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stmt = $this->db->query("
                SELECT o.*, c.title AS campaign_title, p.title AS project_title,
                       u.name AS investor_name, u.email AS investor_email
                FROM investment_offers o
                LEFT JOIN funding_campaigns c ON c.id = o.campaign_id
                LEFT JOIN projects p ON p.id = c.project_id
                LEFT JOIN investors i ON i.id = o.investor_id
                LEFT JOIN users u ON u.id = i.user_id
                ORDER BY o.created_at DESC
                LIMIT 300
            ");
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : accepter / refuser une offre d'investissement
     */
    public function reviewInvestmentOffer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id']) || empty($data['action'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID et action requis']);
        }

        try {
            $statusMap = ['accept' => 'accepted', 'reject' => 'rejected', 'withdraw' => 'withdrawn'];
            $status = $statusMap[$data['action']] ?? $data['action'];

            $stmt = $this->db->prepare("
                UPDATE investment_offers
                SET status = ?, reviewed_by = ?, reviewed_at = NOW(), rejection_reason = COALESCE(?, rejection_reason)
                WHERE id = ?
            ");
            $stmt->execute([$status, $_SESSION['user_id'] ?? null, $data['reason'] ?? null, $data['id']]);

            // Notifier l'investisseur
            $stmt = $this->db->prepare("
                SELECT o.investor_id, i.user_id, o.amount, c.title
                FROM investment_offers o
                LEFT JOIN investors i ON i.id = o.investor_id
                LEFT JOIN funding_campaigns c ON c.id = o.campaign_id
                WHERE o.id = ?
            ");
            $stmt->execute([$data['id']]);
            $offer = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($offer && $offer['user_id']) {
                $this->notify((int)$offer['user_id'], 'offer',
                    'Offre d\'investissement ' . ($status === 'accepted' ? 'acceptée' : 'refusée'),
                    'Votre offre de ' . number_format((float)$offer['amount'], 0, ',', ' ') . ' $ sur "' . ($offer['title'] ?? '') . '" : ' . $status);
            }

            $this->jsonOut(['success' => true, 'message' => 'Offre mise à jour']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Investisseur : exprimer un intérêt pour un projet en levée de fonds
     */
    public function expressFundingInterest()
    {
        $this->requireRole(['investor', 'admin', 'super_admin'], 'Accès réservé aux investisseurs');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['project_id'])) $this->jsonOut(['success' => false, 'message' => 'Projet requis']);

        $investorId = $this->getCurrentInvestorId();
        if (!$investorId) $this->jsonOut(['success' => false, 'message' => 'Compte investisseur introuvable']);

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM investor_interests WHERE project_id = ? AND investor_id = ?");
            $stmt->execute([$data['project_id'], $investorId]);
            if ((int)$stmt->fetchColumn() > 0) {
                $this->jsonOut(['success' => true, 'message' => 'Intérêt déjà exprimé pour ce projet']);
            }
            $stmt = $this->db->prepare("INSERT INTO investor_interests (project_id, investor_id) VALUES (?, ?)");
            $stmt->execute([$data['project_id'], $investorId]);

            $this->notifyAdmins('investment_update', 'Nouvel intérêt investisseur',
                'Un investisseur s\'intéresse au projet #' . $data['project_id']);

            $this->jsonOut(['success' => true, 'message' => 'Intérêt exprimé avec succès']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    // ==================================================================
    // ============ MODULE 3 - DATA ROOM ================================
    // ==================================================================

    /**
     * Investisseur : demander l'accès à la Data Room d'un projet
     */
    public function requestDataRoomAccess()
    {
        $this->requireRole(['investor', 'admin', 'super_admin'], 'Accès réservé aux investisseurs');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['project_id'])) $this->jsonOut(['success' => false, 'message' => 'Projet requis']);

        $investorId = $this->getCurrentInvestorId();
        if (!$investorId) $this->jsonOut(['success' => false, 'message' => 'Compte investisseur introuvable']);

        $level = in_array($data['permission_level'] ?? '', ['view_only', 'download_allowed', 'full_access', 'temporary'], true)
            ? $data['permission_level'] : 'view_only';

        try {
            // Pas de demande en double en attente
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM data_room_requests
                WHERE project_id = ? AND investor_id = ? AND status IN ('pending', 'approved')
            ");
            $stmt->execute([$data['project_id'], $investorId]);
            if ((int)$stmt->fetchColumn() > 0) {
                $this->jsonOut(['success' => false, 'message' => 'Une demande existe déjà pour ce projet']);
            }

            $stmt = $this->db->prepare("
                INSERT INTO data_room_requests (project_id, investor_id, requested_level, justification)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$data['project_id'], $investorId, $level, trim($data['justification'] ?? '')]);

            $this->notifyAdmins('data_room', 'Demande d\'accès Data Room',
                'Un investisseur demande l\'accès aux documents du projet #' . $data['project_id']);

            $this->jsonOut(['success' => true, 'message' => 'Votre demande d\'accès a été envoyée à Urbanova.']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Investisseur : mes demandes d'accès Data Room
     */
    public function getMyDataRoomRequests()
    {
        $this->requireRole(['investor', 'admin', 'super_admin'], 'Accès réservé aux investisseurs');
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $investorId = $this->getCurrentInvestorId();
        if (!$investorId) $this->jsonOut(['success' => true, 'data' => []]);

        try {
            $stmt = $this->db->prepare("
                SELECT r.*, p.title AS project_title, p.city, p.country
                FROM data_room_requests r
                LEFT JOIN projects p ON p.id = r.project_id
                WHERE r.investor_id = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$investorId]);
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : liste des demandes d'accès Data Room
     */
    public function getDataRoomRequests()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stmt = $this->db->query("
                SELECT r.*, p.title AS project_title,
                       u.name AS investor_name, u.email AS investor_email
                FROM data_room_requests r
                LEFT JOIN projects p ON p.id = r.project_id
                LEFT JOIN investors i ON i.id = r.investor_id
                LEFT JOIN users u ON u.id = i.user_id
                ORDER BY r.created_at DESC
                LIMIT 300
            ");
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : valider / refuser / révoquer une demande d'accès Data Room
     */
    public function reviewDataRoomAccess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id']) || empty($data['action'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID et action requis']);
        }

        try {
            $stmt = $this->db->prepare("
                SELECT r.*, i.user_id AS investor_user_id
                FROM data_room_requests r
                LEFT JOIN investors i ON i.id = r.investor_id
                WHERE r.id = ?
            ");
            $stmt->execute([$data['id']]);
            $req = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$req) $this->jsonOut(['success' => false, 'message' => 'Demande introuvable']);

            $action = $data['action'];

            if ($action === 'approve') {
                $level = in_array($data['permission_level'] ?? '', ['view_only', 'download_allowed', 'full_access', 'temporary'], true)
                    ? $data['permission_level'] : ($req['requested_level'] ?: 'view_only');
                $expiresAt = null;
                if ($level === 'temporary' && !empty($data['duration_days'])) {
                    $expiresAt = date('Y-m-d H:i:s', time() + ((int)$data['duration_days'] * 86400));
                }

                // Créer / mettre à jour la permission
                $stmt = $this->db->prepare("
                    SELECT id FROM data_room_permissions
                    WHERE project_id = ? AND investor_id = ?
                ");
                $stmt->execute([$req['project_id'], $req['investor_id']]);
                $permId = $stmt->fetchColumn();

                if ($permId) {
                    $stmt = $this->db->prepare("
                        UPDATE data_room_permissions
                        SET permission_level = ?, expires_at = ?, status = 'active',
                            granted_by = ?, granted_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$level, $expiresAt, $_SESSION['user_id'] ?? null, $permId]);
                } else {
                    $stmt = $this->db->prepare("
                        INSERT INTO data_room_permissions
                            (project_id, investor_id, permission_level, expires_at, granted_by)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$req['project_id'], $req['investor_id'], $level, $expiresAt, $_SESSION['user_id'] ?? null]);
                    $permId = (int)$this->db->lastInsertId();
                }

                $stmt = $this->db->prepare("
                    UPDATE data_room_requests
                    SET status = 'approved', granted_permission_id = ?, decided_by = ?, decided_at = NOW(),
                        refusal_reason = NULL
                    WHERE id = ?
                ");
                $stmt->execute([$permId, $_SESSION['user_id'] ?? null, $req['id']]);

                $this->logDataRoomAccess($req['project_id'], $req['investor_id'], 'access_granted',
                    'Accès accordé (niveau: ' . $level . ($expiresAt ? ', expire le ' . $expiresAt : '') . ')');

                if ($req['investor_user_id']) {
                    $this->notify((int)$req['investor_user_id'], 'data_room',
                        'Accès Data Room accordé',
                        'Votre accès aux documents du projet #' . $req['project_id'] . ' a été validé.', '/investor');
                }

                $this->jsonOut(['success' => true, 'message' => 'Accès accordé']);
            } elseif ($action === 'refuse') {
                $stmt = $this->db->prepare("
                    UPDATE data_room_requests
                    SET status = 'refused', decided_by = ?, decided_at = NOW(), refusal_reason = ?
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'] ?? null, $data['reason'] ?? null, $req['id']]);

                $this->logDataRoomAccess($req['project_id'], $req['investor_id'], 'access_denied',
                    'Demande refusée' . (!empty($data['reason']) ? ': ' . $data['reason'] : ''));

                if ($req['investor_user_id']) {
                    $this->notify((int)$req['investor_user_id'], 'data_room',
                        'Demande Data Room refusée',
                        'Votre demande d\'accès au projet #' . $req['project_id'] . ' a été refusée.', '/investor');
                }

                $this->jsonOut(['success' => true, 'message' => 'Demande refusée']);
            } elseif ($action === 'revoke') {
                // Retirer la permission liée
                if (!empty($req['granted_permission_id'])) {
                    $stmt = $this->db->prepare("UPDATE data_room_permissions SET status = 'revoked' WHERE id = ?");
                    $stmt->execute([$req['granted_permission_id']]);
                }
                $stmt = $this->db->prepare("
                    UPDATE data_room_requests SET status = 'revoked', decided_by = ?, decided_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id'] ?? null, $req['id']]);

                $this->logDataRoomAccess($req['project_id'], $req['investor_id'], 'access_revoked',
                    'Accès retiré par Urbanova');

                if ($req['investor_user_id']) {
                    $this->notify((int)$req['investor_user_id'], 'data_room',
                        'Accès Data Room retiré',
                        'Votre accès aux documents du projet #' . $req['project_id'] . ' a été retiré.', '/investor');
                }

                $this->jsonOut(['success' => true, 'message' => 'Accès retiré']);
            } else {
                $this->jsonOut(['success' => false, 'message' => 'Action inconnue']);
            }
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Documents d'un projet : publics (non confidentiels) ou si permission Data Room
     */
    public function getProjectDocuments()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $projectId = $_GET['project_id'] ?? null;
        if (!$projectId) $this->jsonOut(['success' => false, 'message' => 'projet requis (paramètre project_id)']);

        try {
            $investorId = $this->getCurrentInvestorId();
            $isAdmin = in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'project_manager'], true);

            // Accès à la Data Room ?
            $hasAccess = false;
            if ($investorId) {
                $stmt = $this->db->prepare("
                    SELECT permission_level FROM data_room_permissions
                    WHERE project_id = ? AND investor_id = ? AND status = 'active'
                      AND (expires_at IS NULL OR expires_at > NOW())
                    LIMIT 1
                ");
                $stmt->execute([$projectId, $investorId]);
                $hasAccess = (bool)$stmt->fetchColumn();
            }

            $stmt = $this->db->prepare("
                SELECT id, project_id, document_type, title, file_path, file_size, mime_type, is_confidential, created_at
                FROM project_documents
                WHERE project_id = ?
                ORDER BY is_confidential ASC, created_at DESC
            ");
            $stmt->execute([$projectId]);
            $docs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $visible = array_filter($docs, function ($d) use ($hasAccess, $isAdmin) {
                return $isAdmin || $hasAccess || !(bool)$d['is_confidential'];
            });

            foreach ($visible as &$d) {
                $d['can_download'] = $isAdmin || !(bool)$d['is_confidential'] || $hasAccess;
            }

            $this->jsonOut(['success' => true, 'has_access' => $hasAccess || $isAdmin, 'data' => array_values($visible)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Journalisation d'une consultation / téléchargement de document
     */
    public function logDocumentAccess()
    {
        $this->requireRole(['investor', 'admin', 'super_admin', 'project_manager'], 'Accès non autorisé');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['document_id']) || empty($data['project_id'])) {
            $this->jsonOut(['success' => false, 'message' => 'Document et projet requis']);
        }
        $action = $data['action'] === 'download' ? 'document_downloaded' : 'document_viewed';
        $investorId = $this->getCurrentInvestorId();
        if (!$investorId && !in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'project_manager'], true)) {
            $this->jsonOut(['success' => false, 'message' => 'Compte investisseur introuvable']);
        }

        $this->logDataRoomAccess($data['project_id'], $investorId ?: 0, $action,
            'Document #' . $data['document_id'], $data['document_id']);
        $this->jsonOut(['success' => true]);
    }

    /**
     * Admin : journal d'audit Data Room
     */
    public function getDataRoomAudit()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $params = [];
            $where = "1=1";
            if (!empty($_GET['project_id'])) {
                $where .= " AND a.project_id = ?";
                $params[] = (int)$_GET['project_id'];
            }
            if (!empty($_GET['action'])) {
                $where .= " AND a.action = ?";
                $params[] = $_GET['action'];
            }

            $stmt = $this->db->prepare("
                SELECT a.*, p.title AS project_title, u.name AS investor_name, u.email AS investor_email
                FROM data_room_audit_log a
                LEFT JOIN projects p ON p.id = a.project_id
                LEFT JOIN investors i ON i.id = a.investor_id
                LEFT JOIN users u ON u.id = i.user_id
                WHERE $where
                ORDER BY a.created_at DESC
                LIMIT 300
            ");
            $stmt->execute($params);
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    // ==================================================================
    // ============ MODULE 1+2 - ADMIN : DEMANDES, VISITES, STATS =======
    // ==================================================================

    /**
     * Administrateur : statistiques globales de la plateforme
     */
    public function getAdminStats()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $stats = [];

        $queries = [
            'total_projects'      => "SELECT COUNT(*) FROM projects",
            'pending_projects'    => "SELECT COUNT(*) FROM projects WHERE validation_status IN ('pending','submitted','under_review')",
            'published_projects'  => "SELECT COUNT(*) FROM projects WHERE validation_status IN ('approved','published')",
            'sold_or_rented'      => "SELECT COUNT(*) FROM projects WHERE validation_status IN ('sold','rented')",
            'total_users'         => "SELECT COUNT(*) FROM users",
            'total_investors'     => "SELECT COUNT(*) FROM investors",
            'pending_kyc'         => "SELECT COUNT(*) FROM investors WHERE investor_status = 'pending'",
            'pending_reservations' => "SELECT COUNT(*) FROM project_reservations WHERE status = 'pending'",
            'pending_visits'      => "SELECT COUNT(*) FROM project_visits WHERE status = 'pending'",
            'pending_queries'     => "SELECT COUNT(*) FROM project_inquiries WHERE status = 'new'",
            'pending_dataroom'    => "SELECT COUNT(*) FROM data_room_requests WHERE status = 'pending'",
            'pending_offers'      => "SELECT COUNT(*) FROM investment_offers WHERE status = 'pending'",
            'pending_mandates'    => "SELECT COUNT(*) FROM funding_requests WHERE status IN ('pending_study','under_review')",
            'active_campaigns'    => "SELECT COUNT(*) FROM funding_campaigns WHERE status = 'active'",
            'total_investments'   => "SELECT COUNT(*) FROM investments",
            'total_investment_amount' => "SELECT COALESCE(SUM(amount), 0) FROM investments",
        ];

        foreach ($queries as $key => $sql) {
            try {
                $stmt = $this->db->query($sql);
                $stats[$key] = $stmt->fetchColumn();
            } catch (\PDOException $e) {
                $stats[$key] = 0;
            }
        }

        $this->jsonOut(['success' => true, 'data' => $stats]);
    }

    /**
     * Admin : liste des réservations
     */
    public function getReservations()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stmt = $this->db->query("
                SELECT r.*, p.title AS project_title, p.city, p.country
                FROM project_reservations r
                LEFT JOIN projects p ON p.id = r.project_id
                ORDER BY r.created_at DESC
                LIMIT 300
            ");
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : changer le statut d'une réservation
     */
    public function updateReservationStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id']) || empty($data['status'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID et statut requis']);
        }
        try {
            $stmt = $this->db->prepare("UPDATE project_reservations SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $data['id']]);
            $this->jsonOut(['success' => true, 'message' => 'Réservation mise à jour']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : liste des demandes de visite
     */
    public function getVisits()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stmt = $this->db->query("
                SELECT v.*, p.title AS project_title, p.city, p.country
                FROM project_visits v
                LEFT JOIN projects p ON p.id = v.project_id
                ORDER BY v.created_at DESC
                LIMIT 300
            ");
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : changer le statut d'une demande de visite
     */
    public function updateVisitStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id']) || empty($data['status'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID et statut requis']);
        }
        try {
            $stmt = $this->db->prepare("UPDATE project_visits SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $data['id']]);
            $this->jsonOut(['success' => true, 'message' => 'Visite mise à jour']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    // ==================================================================
    // ============ NOTIFICATIONS & MESSAGERIE ==========================
    // ==================================================================

    /**
     * Notifications de l'utilisateur connecté
     */
    public function getMyNotifications()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        if (empty($_SESSION['user_id'])) $this->jsonOut(['success' => true, 'data' => []]);
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM notifications
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT 100
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt2 = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
            $stmt2->execute([$_SESSION['user_id']]);
            $this->jsonOut(['success' => true, 'unread' => (int)$stmt2->fetchColumn(), 'data' => $rows]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Marquer les notifications comme lues
     */
    public function markNotificationsRead()
    {
        if (!$this->db || empty($_SESSION['user_id'])) $this->jsonOut(['success' => true]);
        try {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $this->jsonOut(['success' => true]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Messagerie investisseur : messages de l'utilisateur connecté (ou tous pour admin)
     */
    public function getInvestorMessages()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $isAdmin = in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'project_manager'], true);
        try {
            if ($isAdmin) {
                $stmt = $this->db->query("
                    SELECT m.*, u.name AS investor_name, u.email AS investor_email, p.title AS project_title
                    FROM investor_messages m
                    LEFT JOIN investors i ON i.id = m.investor_id
                    LEFT JOIN users u ON u.id = i.user_id
                    LEFT JOIN projects p ON p.id = m.project_id
                    ORDER BY m.created_at DESC
                    LIMIT 300
                ");
            } else {
                $this->requireRole(['investor'], 'Accès réservé aux investisseurs');
                $investorId = $this->getCurrentInvestorId();
                if (!$investorId) $this->jsonOut(['success' => true, 'data' => []]);
                $stmt = $this->db->prepare("
                    SELECT * FROM investor_messages
                    WHERE investor_id = ?
                    ORDER BY created_at DESC
                    LIMIT 300
                ");
                $stmt->execute([$investorId]);
            }
            $this->jsonOut(['success' => true, 'data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Investisseur : envoyer un message à Urbanova
     */
    public function sendInvestorMessage()
    {
        $this->requireRole(['investor', 'admin', 'super_admin'], 'Accès réservé aux investisseurs');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['subject']) || empty($data['message'])) {
            $this->jsonOut(['success' => false, 'message' => 'Sujet et message requis']);
        }

        $investorId = $this->getCurrentInvestorId();
        if (!$investorId) $this->jsonOut(['success' => false, 'message' => 'Compte investisseur introuvable']);

        try {
            $stmt = $this->db->prepare("
                INSERT INTO investor_messages (investor_id, project_id, subject, message)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$investorId, $data['project_id'] ?? null, $data['subject'], $data['message']]);

            $this->notifyAdmins('message', 'Nouveau message investisseur', $data['subject']);
            $this->jsonOut(['success' => true, 'message' => 'Message envoyé à Urbanova']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : répondre à un message investisseur
     */
    public function replyInvestorMessage()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id']) || empty($data['reply'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID et réponse requis']);
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE investor_messages
                SET status = 'replied', admin_reply = ?, admin_replied_at = NOW(), admin_replied_by = ?
                WHERE id = ?
            ");
            $stmt->execute([$data['reply'], $_SESSION['user_id'] ?? null, $data['id']]);

            // Notifier l'investisseur
            $stmt = $this->db->prepare("
                SELECT m.investor_id, i.user_id
                FROM investor_messages m
                LEFT JOIN investors i ON i.id = m.investor_id
                WHERE m.id = ?
            ");
            $stmt->execute([$data['id']]);
            $msg = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($msg && $msg['user_id']) {
                $this->notify((int)$msg['user_id'], 'message',
                    'Réponse d\'Urbanova à votre message', 'Urbanova a répondu à votre message.', '/investor');
            }

            $this->jsonOut(['success' => true, 'message' => 'Réponse enregistrée']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }
    // ==================================================================
    // ============ CMS CONTENUS (Projets phares, Actualités, Site) ======
    // ==================================================================

    /**
     * Projets phares de la page d'accueil
     */
    public function getFeaturedProjects()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            // Adaptatif : fonctionne sur l'ancien schéma (sans is_featured / validation_status)
            $hasValidation = $this->hasColumn('projects', 'validation_status');
            $hasFeatured = $this->hasColumn('projects', 'is_featured');
            $statusCol = $hasValidation ? 'p.validation_status' : 'p.status';
            $f = ['p.id', 'p.title', 'p.slug', 'p.city', 'p.country', 'p.description', 'p.image',
                  'p.funding_sought AS target', 'p.funding_raised AS raised',
                  'p.expected_roi AS roi', $statusCol . ' AS status'];
            foreach (['housing_units', 'price', 'project_type'] as $col) {
                if ($this->hasColumn('projects', $col)) $f[] = 'p.' . $col;
            }
            $f[] = "CONCAT(p.city, ', ', p.country) AS location";
            $statusCond = $hasValidation
                ? $statusCol . " IN ('published', 'approved', 'funding', 'active')"
                : $statusCol . " IN ('approved', 'funding', 'completed')";
            $featuredCond = $hasFeatured ? 'p.is_featured = 1 AND ' : '';
            $stmt = $this->db->query(
                'SELECT ' . implode(', ', $f) . ' FROM projects p WHERE ' . $featuredCond . $statusCond . ' ORDER BY p.updated_at DESC LIMIT 9'
            );
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $out = [];
            foreach ($rows as $p) {
                $out[] = [
                    'id' => $p['id'],
                    'title' => $p['title'],
                    'name' => $p['title'],
                    'slug' => $p['slug'] ?? null,
                    'type' => $p['project_type'] ?? 'residential',
                    'location' => $p['location'],
                    'city' => $p['city'],
                    'country' => $p['country'],
                    'description' => $p['description'] ?? '',
                    'image' => $p['image'] ?: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
                    'target' => (int)($p['target'] ?? 0),
                    'raised' => (int)($p['raised'] ?? 0),
                    'roi' => (int)($p['roi'] ?? 0),
                    'status' => $this->translateStatus($p['status'] ?? ''),
                    'raw_status' => $p['status'] ?? '',
                    'housing_units' => (int)($p['housing_units'] ?? 0),
                    'price' => $p['price'] ?? null,
                    'project_type' => $p['project_type'] ?? 'residential',
                ];
            }
            $this->jsonOut(['success' => true, 'data' => $out]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => $this->dbUpgradeHint($e)]);
        }
    }

    /**
     * Admin : activer / désactiver la mise en avant d'un projet
     */
    public function setFeaturedProject()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id'])) $this->jsonOut(['success' => false, 'message' => 'ID du projet requis']);
        try {
            $featured = !empty($data['featured']) ? 1 : 0;
            $stmt = $this->db->prepare("UPDATE projects SET is_featured = ? WHERE id = ?");
            $stmt->execute([$featured, $data['id']]);
            $this->jsonOut(['success' => true, 'message' => $featured ? 'Projet mis en avant (projet phare)' : 'Projet retiré des projets phares']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualités publiées (public) ou toutes (admin)
     */
    public function getNews()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $isAdmin = in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'project_manager'], true)
            || (
                !empty($_SERVER['HTTP_X_ADMIN_PASSWORD'])
                && hash_equals(
                    (string)($this->config['security']['admin_password'] ?? 'urbanova'),
                    (string)$_SERVER['HTTP_X_ADMIN_PASSWORD']
                )
            );
        try {
            if ($isAdmin && !empty($_GET['all'])) {
                $stmt = $this->db->query("
                    SELECT * FROM news
                    ORDER BY COALESCE(published_at, created_at) DESC
                    LIMIT 200
                ");
            } else {
                $stmt = $this->db->query("
                    SELECT * FROM news
                    WHERE status = 'published' AND deleted_at IS NULL
                    ORDER BY published_at DESC
                    LIMIT 100
                ");
            }
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as &$r) {
                $r['date_display'] = !empty($r['published_at'])
                    ? date('d F Y', strtotime($r['published_at']))
                    : date('d F Y', strtotime($r['created_at']));
            }
            $this->jsonOut(['success' => true, 'data' => $rows]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => $this->dbUpgradeHint($e)]);
        }
    }

    /**
     * Admin : créer une actualité
     */
    public function createNews()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['title'])) $this->jsonOut(['success' => false, 'message' => 'Titre requis']);
        try {
            $slug = $this->uniqueSlug('news', $data['title']);
            $status = $data['status'] === 'published' ? 'published' : 'draft';
            $stmt = $this->db->prepare("
                INSERT INTO news (title, slug, excerpt, content, category, image, author_id, status, published_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $publishedAt = !empty($data['published_at'])
                ? date('Y-m-d H:i:s', strtotime((string)$data['published_at']))
                : null;
            $stmt->execute([
                trim($data['title']),
                $slug,
                trim($data['excerpt'] ?? ''),
                $data['content'] ?? '',
                in_array($data['category'] ?? '', ['entreprise', 'projets', 'marché', 'partenariats'], true) ? $data['category'] : 'entreprise',
                $data['image'] ?? null,
                $_SESSION['user_id'] ?? null,
                $status,
                $status === 'published' ? ($publishedAt ?: date('Y-m-d H:i:s')) : null,
            ]);
            $this->jsonOut(['success' => true, 'message' => 'Actualité créée', 'id' => (int)$this->db->lastInsertId()]);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : modifier une actualité
     */
    public function updateNews()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id']) || empty($data['title'])) {
            $this->jsonOut(['success' => false, 'message' => 'ID et titre requis']);
        }
        try {
            $stmt = $this->db->prepare("
                UPDATE news SET
                    title = ?, excerpt = ?, content = ?, category = ?, image = ?, status = ?,
                    published_at = CASE
                        WHEN ? = 'published' AND ? IS NOT NULL THEN ?
                        WHEN ? = 'published' AND published_at IS NULL THEN NOW()
                        ELSE published_at
                    END
                WHERE id = ?
            ");
            $publishedAt = !empty($data['published_at'])
                ? date('Y-m-d H:i:s', strtotime((string)$data['published_at']))
                : null;
            $stmt->execute([
                trim($data['title']),
                trim($data['excerpt'] ?? ''),
                $data['content'] ?? '',
                in_array($data['category'] ?? '', ['entreprise', 'projets', 'marché', 'partenariats'], true) ? $data['category'] : 'entreprise',
                $data['image'] ?? null,
                $data['status'] === 'published' ? 'published' : 'draft',
                $data['status'] === 'published' ? 'published' : 'draft',
                $publishedAt,
                $data['status'] === 'published' ? 'published' : 'draft',
                $data['id'],
            ]);
            $this->jsonOut(['success' => true, 'message' => 'Actualité mise à jour']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin : supprimer (soft delete) une actualité
     */
    public function deleteNews()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['id'])) $this->jsonOut(['success' => false, 'message' => 'ID requis']);
        try {
            $stmt = $this->db->prepare("UPDATE news SET deleted_at = NOW() WHERE id = ?");
            $stmt->execute([$data['id']]);
            $this->jsonOut(['success' => true, 'message' => 'Actualité supprimée']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Contenu du site (clé -> valeur)
     */
    public function getSiteContent()
    {
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        try {
            $stmt = $this->db->query("SELECT content_key, content_value, category FROM site_content");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $map = [];
            foreach ($rows as $r) $map[$r['content_key']] = $r['content_value'];
            $this->jsonOut(['success' => true, 'data' => $map]);
        } catch (\PDOException $e) {
            // Table absente -> valeurs par défaut
            $this->jsonOut(['success' => true, 'data' => []]);
        }
    }

    /**
     * Admin : enregistrer les contenus du site
     */
    public function updateSiteContent()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonOut(['success' => false, 'message' => 'Méthode non autorisée'], 405);
        }
        if (!$this->db) $this->jsonOut(['success' => false, 'message' => 'Base de données non disponible']);
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        if (empty($data['content']) || !is_array($data['content'])) {
            $this->jsonOut(['success' => false, 'message' => 'Contenus requis']);
        }
        try {
            $stmt = $this->db->prepare("
                INSERT INTO site_content (content_key, content_value, category, updated_by)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), updated_by = VALUES(updated_by)
            ");
            foreach ($data['content'] as $key => $value) {
                $key = preg_replace('/[^a-z0-9_\-]/i', '_', (string)$key);
                if ($key === '') continue;
                $value = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
                $stmt->execute([$key, $value, $data['category'] ?? 'general', $_SESSION['user_id'] ?? null]);
            }
            $this->jsonOut(['success' => true, 'message' => 'Contenus du site enregistrés']);
        } catch (\PDOException $e) {
            $this->jsonOut(['success' => false, 'message' => $this->dbUpgradeHint($e)]);
        }
    }

    /**
     * Génère un slug unique dans une table
     */
    private function uniqueSlug($table, $title)
    {
        $base = trim(preg_replace('/[^a-z0-9]+/i', '-', mb_strtolower($title)), '-');
        if ($base === '') $base = 'article-' . time();
        $slug = $base;
        $i = 1;
        while (true) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM $table WHERE slug = ?");
            $stmt->execute([$slug]);
            if ((int)$stmt->fetchColumn() === 0) break;
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
