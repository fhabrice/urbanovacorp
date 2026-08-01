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
                // Fallback to static data on error
            }
        }
        
        // Fallback static data
        $projects = [
            [
                'id' => 'proj-1',
                'title' => 'Résidence Horizon',
                'location' => 'Goma, RDC',
                'country' => 'RDC',
                'city' => 'Goma',
                'sector' => 'Résidentiel',
                'target' => 800000,
                'raised' => 250000,
                'roi' => 24,
                'progress' => 31,
                'status' => 'En cours',
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'id' => 'proj-2',
                'title' => 'Urban Business Park',
                'location' => 'Kinshasa, RDC',
                'country' => 'RDC',
                'city' => 'Kinshasa',
                'sector' => 'Bureaux',
                'target' => 6000000,
                'raised' => 2100000,
                'roi' => 28,
                'progress' => 35,
                'status' => 'En cours',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'id' => 'proj-3',
                'title' => 'Eco City Villas',
                'location' => 'Kigali, Rwanda',
                'country' => 'Rwanda',
                'city' => 'Kigali',
                'sector' => 'Résidentiel',
                'target' => 1500000,
                'raised' => 330000,
                'roi' => 22,
                'progress' => 22,
                'status' => 'Planifié',
                'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'id' => 'proj-4',
                'title' => 'Commercial Hub Goma',
                'location' => 'Goma, RDC',
                'country' => 'RDC',
                'city' => 'Goma',
                'sector' => 'Commercial',
                'target' => 2000000,
                'raised' => 500000,
                'roi' => 25,
                'progress' => 25,
                'status' => 'En cours',
                'image' => 'https://images.unsplash.com/photo-1554469384-e58fac16e23a?auto=format&fit=crop&w=600&q=80'
            ]
        ];

        echo json_encode([
            'success' => true,
            'data' => $projects
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
     * Soumettre un nouveau projet
     */
    public function submitProject()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
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

        if ($this->db) {
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
        
        // Fallback: store in session
        $newProject = [
            'id' => 'proj-' . time(),
            'title' => $data['name'],
            'location' => $data['location'],
            'country' => 'RDC',
            'city' => explode(',', $data['location'])[0],
            'sector' => $data['sector'],
            'target' => (int)$data['target'],
            'raised' => 0,
            'roi' => (int)($data['roi'] ?? 20),
            'progress' => 0,
            'status' => 'En attente',
            'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80'
        ];

        if (!isset($_SESSION['projects'])) {
            $_SESSION['projects'] = [];
        }
        array_unshift($_SESSION['projects'], $newProject);

        echo json_encode([
            'success' => true,
            'message' => 'Projet soumis avec succès',
            'data' => $newProject
        ]);
        exit;
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

        if ($this->db) {
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
        
        // Fallback: just return success
        echo json_encode([
            'success' => true,
            'message' => 'Message envoyé avec succès'
        ]);
        exit;
    }

    /**
     * Vérifier l'authentification de l'investisseur
     */
    public function checkAuth()
    {
        header('Content-Type: application/json');
        
        $isAuthenticated = isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'investor';
        
        echo json_encode([
            'success' => true,
            'authenticated' => $isAuthenticated,
            'user' => $isAuthenticated ? [
                'name' => $_SESSION['user_name'] ?? 'Investisseur',
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

        $userId = $_SESSION['user_id'];

        if ($this->db) {
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
                // Fallback to static data on error
            }
        }
        
        // Fallback static data
        $data = [
            'stats' => [
                'projects' => 5,
                'total_invested' => 2300000,
                'roi_average' => 18.5,
                'gains' => 320000
            ],
            'investments' => [
                [
                    'project' => 'Urban Business Park',
                    'amount' => 1000000,
                    'progress' => 35,
                    'roi' => 28,
                    'status' => 'En cours'
                ],
                [
                    'project' => 'Résidence Horizon',
                    'amount' => 500000,
                    'progress' => 31,
                    'roi' => 24,
                    'status' => 'En cours'
                ],
                [
                    'project' => 'Eco City Villas',
                    'amount' => 300000,
                    'progress' => 22,
                    'roi' => 22,
                    'status' => 'Planifié'
                ]
            ]
        ];

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
        exit;
    }
}
