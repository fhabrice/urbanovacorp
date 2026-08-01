<?php

namespace App\Controllers;

class AdminController extends Controller
{
    public function index()
    {
        $db = $this->getDb();

        // Get statistics
        $stats = [
            'total_projects' => $db->fetchOne("SELECT COUNT(*) as count FROM projects")['count'],
            'pending_projects' => $db->fetchOne("SELECT COUNT(*) as count FROM projects WHERE status = 'submitted'")['count'],
            'approved_projects' => $db->fetchOne("SELECT COUNT(*) as count FROM projects WHERE status = 'approved'")['count'],
            'total_investors' => $db->fetchOne("SELECT COUNT(*) as count FROM investors")['count'],
            'pending_investors' => $db->fetchOne("SELECT COUNT(*) as count FROM investors WHERE investor_status = 'pending'")['count'],
            'approved_investors' => $db->fetchOne("SELECT COUNT(*) as count FROM investors WHERE investor_status = 'approved'")['count'],
            'total_funding_sought' => $db->fetchOne("SELECT SUM(funding_sought) as total FROM projects WHERE status = 'approved'")['total'] ?? 0,
            'total_funding_mobilized' => $db->fetchOne("SELECT SUM(funding_mobilized) as total FROM projects WHERE status = 'approved'")['total'] ?? 0,
        ];

        // Get recent projects
        $recentProjects = $db->fetchAll("
            SELECT p.*, u.first_name, u.last_name 
            FROM projects p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC 
            LIMIT 5
        ");

        // Get recent investors
        $recentInvestors = $db->fetchAll("
            SELECT i.*, u.first_name, u.last_name, u.email 
            FROM investors i 
            JOIN users u ON i.user_id = u.id 
            ORDER BY i.created_at DESC 
            LIMIT 5
        ");

        return $this->view('admin/index', [
            'stats' => $stats,
            'recentProjects' => $recentProjects,
            'recentInvestors' => $recentInvestors,
        ]);
    }

    public function projects()
    {
        $db = $this->getDb();

        $projects = $db->fetchAll("
            SELECT p.*, u.first_name, u.last_name, u.email 
            FROM projects p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC
        ");

        return $this->view('admin/projects', [
            'projects' => $projects,
        ]);
    }

    public function approveProject($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ?",
            [$id]
        );

        if (!$project) {
            $session->setFlashMessage('error', __('admin.project_not_found'));
            return $this->redirect('/admin/projects');
        }

        $db->execute(
            "UPDATE projects SET status = 'approved', reviewed_at = NOW(), reviewed_by = ? WHERE id = ?",
            [$userId, $id]
        );

        $session->setFlashMessage('success', __('admin.project_approved'));
        return $this->redirect('/admin/projects');
    }

    public function rejectProject($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ?",
            [$id]
        );

        if (!$project) {
            $session->setFlashMessage('error', __('admin.project_not_found'));
            return $this->redirect('/admin/projects');
        }

        $db->execute(
            "UPDATE projects SET status = 'rejected', reviewed_at = NOW(), reviewed_by = ?, rejection_reason = 'Rejected by administrator' WHERE id = ?",
            [$userId, $id]
        );

        $session->setFlashMessage('success', __('admin.project_rejected'));
        return $this->redirect('/admin/projects');
    }

    public function investors()
    {
        $db = $this->getDb();

        $investors = $db->fetchAll("
            SELECT i.*, u.first_name, u.last_name, u.email 
            FROM investors i 
            JOIN users u ON i.user_id = u.id 
            ORDER BY i.created_at DESC
        ");

        return $this->view('admin/investors', [
            'investors' => $investors,
        ]);
    }

    public function approveInvestor($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE id = ?",
            [$id]
        );

        if (!$investor) {
            $session->setFlashMessage('error', __('admin.investor_not_found'));
            return $this->redirect('/admin/investors');
        }

        $db->execute(
            "UPDATE investors SET investor_status = 'approved', kyc_reviewed_at = NOW(), kyc_reviewed_by = ? WHERE id = ?",
            [$userId, $id]
        );

        // Update user status
        $db->execute(
            "UPDATE users SET status = 'active' WHERE id = ?",
            [$investor['user_id']]
        );

        $session->setFlashMessage('success', __('admin.investor_approved'));
        return $this->redirect('/admin/investors');
    }

    public function statistics()
    {
        $db = $this->getDb();

        // Detailed statistics
        $stats = [
            'projects_by_status' => $db->fetchAll("
                SELECT status, COUNT(*) as count 
                FROM projects 
                GROUP BY status
            "),
            'projects_by_type' => $db->fetchAll("
                SELECT type, COUNT(*) as count 
                FROM projects 
                WHERE status = 'approved'
                GROUP BY type
            "),
            'projects_by_country' => $db->fetchAll("
                SELECT country, COUNT(*) as count 
                FROM projects 
                WHERE status = 'approved'
                GROUP BY country
            "),
            'investors_by_status' => $db->fetchAll("
                SELECT investor_status, COUNT(*) as count 
                FROM investors 
                GROUP BY investor_status
            "),
            'investors_by_type' => $db->fetchAll("
                SELECT type, COUNT(*) as count 
                FROM investors 
                GROUP BY type
            "),
            'funding_trends' => $db->fetchAll("
                SELECT DATE(created_at) as date, SUM(funding_sought) as total 
                FROM projects 
                WHERE status = 'approved' 
                GROUP BY DATE(created_at) 
                ORDER BY date DESC 
                LIMIT 30
            "),
        ];

        return $this->view('admin/statistics', [
            'stats' => $stats,
        ]);
    }
}
