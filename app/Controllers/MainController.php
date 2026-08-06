<?php

namespace App\Controllers;

use App\Core\Database;

/**
 * Main Controller - Sert la nouvelle interface moderne
 */
class MainController extends SimpleController
{
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database($this->config['database']);
    }
    /**
     * Afficher la page d'accueil avec la nouvelle interface
     */
    public function index()
    {
        $newsItems = $this->db->fetchAll(
            "SELECT * FROM news WHERE status = 'published' ORDER BY published_at DESC LIMIT 3"
        );
        // Charger la nouvelle interface HTML
        $viewPath = BASE_PATH . '/index.html';
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Erreur: index.html non trouvé";
        }
    }

    /**
     * API: Récupérer les projets (forward vers ApiController)
     */
    public function apiProjects()
    {
        $api = new ApiController();
        $api->getProjects();
    }

    /**
     * API: Soumettre un projet (forward vers ApiController)
     */
    public function apiSubmitProject()
    {
        $api = new ApiController();
        $api->submitProject();
    }

    /**
     * API: Soumettre le contact (forward vers ApiController)
     */
    public function apiSubmitContact()
    {
        $api = new ApiController();
        $api->submitContact();
    }

    /**
     * API: Vérifier l'auth (forward vers ApiController)
     */
    public function apiCheckAuth()
    {
        $api = new ApiController();
        $api->checkAuth();
    }

    /**
     * API: Données investisseur (forward vers ApiController)
     */
    public function apiInvestorData()
    {
        $api = new ApiController();
        $api->getInvestorData();
    }

    /**
     * API: Inscription (forward vers ApiController)
     */
    public function apiRegister()
    {
        $api = new ApiController();
        $api->register();
    }

    /**
     * API: Connexion (forward vers ApiController)
     */
    public function apiLogin()
    {
        $api = new ApiController();
        $api->login();
    }

    /**
     * API: Déconnexion (forward vers ApiController)
     */
    public function apiLogout()
    {
        $api = new ApiController();
        $api->logout();
    }

    /**
     * API: Approuver/Rejeter projet (forward vers ApiController)
     */
    public function apiApproveProject()
    {
        $api = new ApiController();
        $api->approveProject();
    }
}
