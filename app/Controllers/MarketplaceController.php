<?php

namespace App\Controllers;

class MarketplaceController extends Controller
{
    public function index()
    {
        try {
            $db = $this->getDb();

            // Simple query to get ALL projects first (no status filter for debugging)
            $sql = "SELECT * FROM projects ORDER BY created_at DESC";
            $projects = $db->fetchAll($sql);

            // Get unique values for filters
            $countries = $db->fetchAll("SELECT DISTINCT country FROM projects ORDER BY country");
            $cities = $db->fetchAll("SELECT DISTINCT city FROM projects ORDER BY city");
            $sectors = $db->fetchAll("SELECT DISTINCT sector FROM projects ORDER BY sector");

            return $this->view('marketplace/index', [
                'projects' => $projects ?? [],
                'filters' => [
                    'country' => '',
                    'city' => '',
                    'sector' => '',
                    'type' => '',
                    'min_funding' => '',
                    'max_funding' => '',
                    'min_roi' => '',
                ],
                'filterOptions' => [
                    'countries' => $countries ?? [],
                    'cities' => $cities ?? [],
                    'sectors' => $sectors ?? [],
                ],
            ]);
        } catch (Exception $e) {
            error_log("Marketplace error: " . $e->getMessage());
            
            // Show error message to user
            echo "<div style='padding: 2rem; background: #fee; border: 1px solid #fcc; margin: 2rem;'>";
            echo "<h2>Erreur de connexion à la base de données</h2>";
            echo "<p>Message: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>Veuillez vérifier les paramètres de connexion dans config/config.php</p>";
            echo "</div>";
            exit;
            
            return $this->view('marketplace/index', [
                'projects' => [],
                'filters' => [
                    'country' => '',
                    'city' => '',
                    'sector' => '',
                    'type' => '',
                    'min_funding' => '',
                    'max_funding' => '',
                    'min_roi' => '',
                ],
                'filterOptions' => [
                    'countries' => [],
                    'cities' => [],
                    'sectors' => [],
                ],
            ]);
        }
    }

    public function show($id)
    {
        $db = $this->getDb();

        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ? AND status IN ('approved', 'published')",
            [$id]
        );

        if (!$project) {
            $session = $this->getSession();
            $session->setFlashMessage('error', __('project.not_found'));
            return $this->redirect('/marketplace');
        }

        // Get project promoter info
        $promoter = $db->fetchOne(
            "SELECT u.*, COALESCE(CONCAT(u.first_name, ' ', u.last_name), u.name) as full_name FROM users u WHERE u.id = ?",
            [$project['user_id']]
        );

        // Calculate funding progress
        $fundingProgress = 0;
        if ($project['funding_sought'] > 0) {
            $fundingProgress = ($project['funding_mobilized'] / $project['funding_sought']) * 100;
        }

        return $this->view('marketplace/show', [
            'project' => $project,
            'promoter' => $promoter,
            'fundingProgress' => $fundingProgress,
        ]);
    }

    public function debug()
    {
        echo "<h1>Marketplace Debug</h1>";
        
        // Test database connection
        echo "<h2>Database Connection Test</h2>";
        try {
            $config = require APP_PATH . '/config/config.php';
            echo "<p>Database: " . $config['database']['database'] . "</p>";
            echo "<p>Host: " . $config['database']['host'] . "</p>";
            echo "<p>User: " . $config['database']['username'] . "</p>";
            
            $db = $this->getDb();
            $test = $db->fetchOne("SELECT 1 as test");
            echo "<p>✓ Database connection: OK</p>";
        } catch (Exception $e) {
            echo "<p>✗ Database connection: FAILED - " . $e->getMessage() . "</p>";
            exit;
        }
        
        // Check projects table
        echo "<h2>Projects Table Check</h2>";
        try {
            $totalProjects = $db->fetchOne("SELECT COUNT(*) as count FROM projects");
            echo "<p>Total projects: " . $totalProjects['count'] . "</p>";
            
            $approvedProjects = $db->fetchOne("SELECT COUNT(*) as count FROM projects WHERE status IN ('approved', 'published', 'funded', 'completed')");
            echo "<p>Approved/Published projects: " . $approvedProjects['count'] . "</p>";
            
            $allStatuses = $db->fetchAll("SELECT DISTINCT status, COUNT(*) as count FROM projects GROUP BY status");
            echo "<h3>Projects by status:</h3><ul>";
            foreach ($allStatuses as $status) {
                echo "<li>" . $status['status'] . ": " . $status['count'] . "</li>";
            }
            echo "</ul>";
            
            // Show sample projects
            $sampleProjects = $db->fetchAll("SELECT id, title, status, country, city FROM projects LIMIT 5");
            echo "<h3>Sample projects:</h3><ul>";
            foreach ($sampleProjects as $project) {
                echo "<li>ID: " . $project['id'] . " | " . htmlspecialchars($project['title']) . " | Status: " . $project['status'] . " | " . $project['city'] . ", " . $project['country'] . "</li>";
            }
            echo "</ul>";
            
        } catch (Exception $e) {
            echo "<p>✗ Query failed: " . $e->getMessage() . "</p>";
        }
        
        exit;
    }
}
