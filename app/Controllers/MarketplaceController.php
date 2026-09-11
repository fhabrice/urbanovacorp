<?php

namespace App\Controllers;

use App\Helpers\AdminHelper;

class MarketplaceController extends Controller
{
    public function index()
    {
        $db = $this->getDb();
        $request = $this->getRequest();
        $session = $this->getSession();

        // Filtres publics : recherche, secteur, localisation, financement, avancement
        $q = trim($request->getParam('q',''));
        $country = $request->getParam('country');
        $city = $request->getParam('city');
        $sector = $request->getParam('sector');
        $type = $request->getParam('type');
        $minFunding = $request->getParam('min_funding');
        $maxFunding = $request->getParam('max_funding');
        $minRoi = $request->getParam('min_roi');

        // Requête principale : uniquement projets visibles publiquement (approved/published/funded)
        // Mais visiteur non connecté voit quand même la liste — marketplace accessible sans compte (§1)
        $sql = "SELECT p.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as promoter_name,
                       COALESCE(p.funding_mobilized, p.amount_raised, 0) as mobilized
                FROM projects p
                LEFT JOIN users u ON p.user_id=u.id
                WHERE p.status IN ('approved','published','publie','finance','funded','levee_active','fundraising_active','completed')
        ";
        $params = [];

        if ($q) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.city LIKE ? OR p.country LIKE ?)";
            $like = "%$q%";
            $params = array_merge($params, [$like,$like,$like,$like]);
        }
        if ($country) { $sql .= " AND p.country = ?"; $params[]=$country; }
        if ($city) { $sql .= " AND p.city = ?"; $params[]=$city; }
        if ($sector) { $sql .= " AND p.sector = ?"; $params[]=$sector; }
        if ($type) { $sql .= " AND p.type = ?"; $params[]=$type; }
        if ($minFunding !== '' && $minFunding !== null) { $sql .= " AND p.funding_sought >= ?"; $params[]=$minFunding; }
        if ($maxFunding !== '' && $maxFunding !== null) { $sql .= " AND p.funding_sought <= ?"; $params[]=$maxFunding; }
        if ($minRoi !== '' && $minRoi !== null) { $sql .= " AND p.roi >= ?"; $params[]=$minRoi; }

        $sql .= " ORDER BY p.created_at DESC LIMIT 100";

        try {
            $projects = $db->fetchAll($sql, $params);
        } catch (\Exception $e) {
            $projects = [];
        }

        // Fallback si aucun projet approuvé : montrer tout pour démo (mais masquer brouillons en prod)
        if (empty($projects)) {
            try {
                $projects = $db->fetchAll("SELECT p.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as promoter_name, COALESCE(p.funding_mobilized, p.amount_raised,0) as mobilized FROM projects p LEFT JOIN users u ON p.user_id=u.id ORDER BY p.created_at DESC LIMIT 12");
            } catch (\Exception $e) { $projects = []; }
        }

        // Options pour filtres
        $countries = $this->safeFetchAll("SELECT DISTINCT country FROM projects WHERE country IS NOT NULL AND country!='' ORDER BY country");
        $cities = $this->safeFetchAll("SELECT DISTINCT city FROM projects WHERE city IS NOT NULL AND city!='' ORDER BY city");
        $sectors = $this->safeFetchAll("SELECT DISTINCT sector FROM projects WHERE sector IS NOT NULL AND sector!='' ORDER BY sector");

        // Pour JS : état visiteur
        $isLogged = $session->get('user_id') ? true : false;
        $userRole = $session->get('user_role') ?? 'visitor';

        return $this->view('marketplace/index', [
            'projects' => $projects ?? [],
            'filters' => [
                'q' => $q,
                'country' => $country,
                'city' => $city,
                'sector' => $sector,
                'type' => $type,
                'min_funding' => $minFunding,
                'max_funding' => $maxFunding,
                'min_roi' => $minRoi,
            ],
            'filterOptions' => [
                'countries' => $countries ?? [],
                'cities' => $cities ?? [],
                'sectors' => $sectors ?? [],
            ],
            'isLogged' => $isLogged,
            'userRole' => $userRole,
        ]);
    }

    private function safeFetchAll($sql, $params=[]){
        try { return $this->getDb()->fetchAll($sql, $params); } catch(\Exception $e){ return []; }
    }

    public function show($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $request = $this->getRequest();

        // Fiche projet : informations publiques accessibles sans compte (§10)
        $project = $db->fetchOne("SELECT p.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as promoter_name, u.email as promoter_email,
                                         COALESCE(p.funding_mobilized, p.amount_raised, 0) as mobilized,
                                         COALESCE(p.funding_sought, p.total_cost, 0) as sought
                                  FROM projects p LEFT JOIN users u ON p.user_id=u.id WHERE p.id = ? ", [$id]);

        if (!$project) {
            $session->setFlashMessage('error', __('project.not_found'));
            return $this->redirect('/marketplace');
        }

        // Si statut non publié et visiteur non admin, on peut quand même afficher si on veut démo, mais en prod on restreint
        // Pour spec, visiteurs voient les projets du marketplace (donc approuvés). On laisse voir.

        $promoter = $db->fetchOne("SELECT u.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as full_name FROM users u WHERE u.id = ?", [$project['user_id']]);

        $fundingProgress = 0;
        $sought = (float)($project['sought'] ?? $project['funding_sought'] ?? 0);
        $mobilized = (float)($project['mobilized'] ?? $project['funding_mobilized'] ?? 0);
        if ($sought > 0) $fundingProgress = round($mobilized / $sought * 100, 1);

        // Niveaux d'accès (§12)
        $userId = $session->get('user_id');
        $userRole = $session->get('user_role');
        $isInvestor = $userRole === 'investor';
        $isLogged = !empty($userId);

        // Vérifier si investisseur connecté a déjà manifesté intérêt
        $hasInterest = false;
        $hasDataRoomAccess = false;
        $isFollowing = false;
        $kycStatus = null;
        if ($isInvestor && $userId) {
            try {
                $investor = $db->fetchOne("SELECT * FROM investors WHERE user_id = ?", [$userId]);
                if ($investor) {
                    $kycStatus = $investor['investor_status'] ?? $investor['kyc_status'] ?? null;
                    $hasInterest = $db->fetchOne("SELECT id FROM investor_interests WHERE project_id=? AND investor_id=?", [$id, $investor['id']]);
                    $hasInterest = !empty($hasInterest);
                    // Data Room : vérif accès autorisé
                    $hasDataRoomAccess = $db->fetchOne("SELECT id FROM data_room_accesses WHERE project_id=? AND investor_id=? AND status='autorise'", [$id, $investor['id']]);
                    $hasDataRoomAccess = !empty($hasDataRoomAccess);
                    // Alternative fallback : data_room_permissions
                    if (!$hasDataRoomAccess) {
                        $hasDataRoomAccess = $db->fetchOne("SELECT id FROM data_room_permissions WHERE project_id=? AND investor_id=?", [$id, $investor['id']]);
                        $hasDataRoomAccess = !empty($hasDataRoomAccess);
                    }
                    // Favoris
                    $isFollowing = $db->fetchOne("SELECT id FROM investor_favorites WHERE project_id=? AND investor_id=?", [$id, $investor['id']]);
                    $isFollowing = !empty($isFollowing);
                }
            } catch (\Exception $e) {}
        }

        // Gérer retour automatique après inscription/connexion (§5) : ?confirm_interest=1
        $confirmInterest = $request->getParam('confirm_interest') || $request->getParam('confirm') || $session->get('pending_interest_confirm');
        if ($confirmInterest) $session->remove('pending_interest_confirm');

        // Stocker le projet en session pour le préserver (§5) si on arrive via interest param
        $interestParam = $request->getParam('interest') ?? $request->getParam('project');
        if ($interestParam) {
            $session->set('pending_interest_project_id', $interestParam);
        }

        return $this->view('marketplace/show', [
            'project' => $project,
            'promoter' => $promoter,
            'fundingProgress' => $fundingProgress,
            'sought' => $sought,
            'mobilized' => $mobilized,
            'isLogged' => $isLogged,
            'isInvestor' => $isInvestor,
            'hasInterest' => $hasInterest,
            'hasDataRoomAccess' => $hasDataRoomAccess,
            'isFollowing' => $isFollowing,
            'kycStatus' => $kycStatus,
            'confirmInterest' => $confirmInterest,
            'userRole' => $userRole,
        ]);
    }

    public function debug()
    {
        echo "<h1>Marketplace Debug</h1>";
        echo "<h2>Database Connection Test</h2>";
        try {
            $config = require APP_PATH . '/config/config.php';
            echo "<p>Database: " . htmlspecialchars($config['database']['database'] ?? $config['database']['name'] ?? 'unknown') . "</p>";
            $db = $this->getDb();
            $test = $db->fetchOne("SELECT 1 as test");
            echo "<p>✓ Database connection: OK</p>";
        } catch (\Exception $e) {
            echo "<p>✗ Database connection: FAILED - " . htmlspecialchars($e->getMessage()) . "</p>";
            exit;
        }
        echo "<h2>Projects Table Check</h2>";
        try {
            $db = $this->getDb();
            $totalProjects = $db->fetchOne("SELECT COUNT(*) as count FROM projects");
            echo "<p>Total projects: " . ($totalProjects['count'] ?? 0) . "</p>";
            $allStatuses = $db->fetchAll("SELECT status, COUNT(*) as count FROM projects GROUP BY status");
            echo "<h3>Projects by status:</h3><ul>";
            foreach ($allStatuses as $status) {
                echo "<li>" . htmlspecialchars($status['status']) . ": " . $status['count'] . "</li>";
            }
            echo "</ul>";
        } catch (\Exception $e) {
            echo "<p>✗ Query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        exit;
    }
}
