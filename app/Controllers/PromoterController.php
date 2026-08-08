<?php

namespace App\Controllers;

class PromoterController extends Controller
{
    public function index()
    {
        $this->ensurePromoterRole();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get promoter's projects
        $projects = $db->fetchAll("
            SELECT * FROM projects 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ", [$userId]);

        // Get statistics
        $stats = [
            'total_projects' => count($projects),
            'draft' => 0,
            'submitted' => 0,
            'under_review' => 0,
            'approved' => 0,
            'published' => 0,
            'sold' => 0,
        ];

        foreach ($projects as $project) {
            if (isset($stats[$project['status']])) {
                $stats[$project['status']]++;
            }
        }

        // Get visit requests
        $visits = $db->fetchAll("
            SELECT v.*, p.title as project_title
            FROM project_visits v
            JOIN projects p ON v.project_id = p.id
            WHERE p.user_id = ?
            ORDER BY v.preferred_date DESC
            LIMIT 10
        ", [$userId]);

        // Get reservations
        $reservations = $db->fetchAll("
            SELECT r.*, p.title as project_title
            FROM project_reservations r
            JOIN projects p ON r.project_id = p.id
            WHERE p.user_id = ?
            ORDER BY r.created_at DESC
            LIMIT 10
        ", [$userId]);

        return $this->view('promoter/index', [
            'projects' => $projects,
            'stats' => $stats,
            'visits' => $visits,
            'reservations' => $reservations,
        ]);
    }

    private function ensurePromoterRole()
    {
        $session = $this->getSession();
        $role = $session->get('user_role');
        
        if ($role !== 'promoter' && $role !== 'admin') {
            $session->setFlashMessage('error', __('auth.must_be_promoter'));
            return $this->redirect('/');
        }
    }
}
