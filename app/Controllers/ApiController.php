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
        
        // Connect to database
        try {
            $config = $this->config['database'];
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
            $this->db = new \PDO($dsn, $config['username'], $config['password']);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            // Fallback to static data if database not available
            error_log("Database connection failed: " . $e->getMessage());
            $this->db = null;
        }
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
                        p.status,
                        p.image,
                        CONCAT(p.city, ', ', p.country) as location
                    FROM projects p
                    WHERE p.status IN ('approved', 'funding', 'completed')
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
                        'title' => $p['title'],
                        'location' => $p['location'],
                        'country' => $p['country'],
                        'city' => $p['city'],
                        'sector' => $p['sector'],
                        'target' => $target,
                        'raised' => $raised,
                        'roi' => (int)$p['roi'],
                        'progress' => $progress,
                        'status' => $this->translateStatus($p['status']),
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
     * Traduire le statut de la base de données vers le frontend
     */
    private function translateStatus($status)
    {
        $translations = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'funding' => 'En cours',
            'completed' => 'Finalisé',
            'rejected' => 'Rejeté'
        ];
        
        return $translations[$status] ?? $status;
    }

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register()
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
        $required = ['name', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                echo json_encode([
                    'success' => false,
                    'message' => "Le champ $field est requis"
                ]);
                exit;
            }
        }

        // Validation email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Email invalide']);
            exit;
        }

        // Validation mot de passe
        if (strlen($data['password']) < 8) {
            echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
            exit;
        }

        try {
            // Vérifier si l'email existe déjà
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
                exit;
            }

            // Hasher le mot de passe
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insérer l'utilisateur
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, role, status, phone, country, city, created_at)
                VALUES (?, ?, ?, 'investor', 'active', ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['name'],
                $data['email'],
                $passwordHash,
                $data['phone'] ?? '',
                $data['country'] ?? '',
                $data['city'] ?? ''
            ]);

            $userId = $this->db->lastInsertId();

            // Connecter automatiquement l'utilisateur
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['user_role'] = 'investor';

            echo json_encode([
                'success' => true,
                'message' => 'Inscription réussie ! Vous êtes maintenant connecté.',
                'user' => [
                    'id' => $userId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' => 'investor'
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
        if (empty($data['email']) || empty($data['password'])) {
            echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
            exit;
        }

        try {
            // Rechercher l'utilisateur
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
                exit;
            }

            // Vérifier le mot de passe
            if (!password_verify($data['password'], $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
                exit;
            }

            // Vérifier le statut
            if ($user['status'] !== 'active') {
                echo json_encode(['success' => false, 'message' => 'Compte non activé']);
                exit;
            }

            // Créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

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
            
            // Insert into database
            $stmt = $this->db->prepare("
                INSERT INTO projects (
                    title, promoter, city, country, sector, 
                    description, funding_sought, funding_raised, 
                    expected_roi, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            
            $stmt->execute([
                $data['name'],
                $data['owner'],
                $city,
                $country,
                $data['sector'],
                $data['description'] ?? '',
                (int)$data['target'],
                0,
                (int)($data['roi'] ?? 20)
            ]);
            
            $projectId = $this->db->lastInsertId();
            
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
        
        $isAuthenticated = isset($_SESSION['user_id']);
        $isInvestor = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'investor';
        
        echo json_encode([
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
        ]);
        exit;
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
            // Get investor stats
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(DISTINCT inv.project_id) as projects,
                    SUM(inv.amount) as total_invested,
                    AVG(p.expected_roi) as roi_average,
                    SUM(inv.amount * p.expected_roi / 100) as gains
                FROM investments inv
                JOIN projects p ON inv.project_id = p.id
                WHERE inv.investor_id = ?
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Get individual investments
            $stmt = $this->db->prepare("
                SELECT 
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
                    'project' => $inv['project'],
                    'amount' => (int)$inv['amount'],
                    'progress' => $progress,
                    'roi' => (int)$inv['roi'],
                    'status' => $this->translateStatus($inv['status'])
                ];
            }
            
            $data = [
                'stats' => [
                    'projects' => (int)($stats['projects'] ?? 0),
                    'total_invested' => (int)($stats['total_invested'] ?? 0),
                    'roi_average' => round((float)($stats['roi_average'] ?? 0), 1),
                    'gains' => (int)($stats['gains'] ?? 0)
                ],
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
}
