<?php

namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $db = $this->getDb();

        // Get statistics
        $stats = [
            'projects_completed' => $db->fetchOne("SELECT COUNT(*) as count FROM projects WHERE status = 'completed'")['count'] ?? 0,
            'total_investments' => $db->fetchOne("SELECT SUM(funding_sought) as total FROM projects WHERE status = 'approved'")['total'] ?? 0,
            'housing_units' => $db->fetchOne("SELECT SUM(housing_units) as total FROM projects WHERE status = 'completed'")['total'] ?? 0,
            'jobs_created' => $db->fetchOne("SELECT SUM(jobs_created) as total FROM projects WHERE status = 'completed'")['total'] ?? 0,
        ];

        // Get featured projects
        $featuredProjects = $db->fetchAll("
            SELECT * FROM projects
            WHERE status = 'approved'
            ORDER BY created_at DESC
            LIMIT 3
        ");

        return $this->view('home/index', [
            'stats' => $stats,
            'featuredProjects' => $featuredProjects,
        ]);
    }
}
