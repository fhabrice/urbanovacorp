<?php

namespace App\Controllers;

/**
 * API Controller - Gère les requêtes AJAX pour la nouvelle interface
 */
class ApiController extends SimpleController
{
    /**
     * Récupérer tous les projets pour la marketplace
     */
    public function getProjects()
    {
        header('Content-Type: application/json');
        
        // Pour l'instant, retourne des données statiques
        // Plus tard, connectera à la base de données
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

        // Pour l'instant, simuler l'enregistrement
        // Plus tard, connectera à la base de données
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

        // Stocker en session pour simulation
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

        // Pour l'instant, simuler l'envoi
        // Plus tard, enverra un email
        
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

        // Données simulées
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
