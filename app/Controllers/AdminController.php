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

    public function news()
    {
        $db = $this->getDb();

        $news = $db->fetchAll(
            "SELECT n.*, u.first_name, u.last_name FROM news n LEFT JOIN users u ON n.author_id = u.id ORDER BY n.created_at DESC"
        );

        return $this->view('admin/news', [
            'news' => $news,
        ]);
    }

    public function createNews()
    {
        return $this->view('admin/news-form', [
            'newsItem' => null,
            'action' => '/admin/news/create',
            'method' => 'POST',
        ]);
    }

    public function storeNews()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        if (!$request->isPost()) {
            return $this->redirect('/admin/news');
        }

        $title = trim($request->getBodyParam('title'));
        $excerpt = trim($request->getBodyParam('excerpt'));
        $content = trim($request->getBodyParam('content'));
        $category = trim($request->getBodyParam('category'));
        $status = trim($request->getBodyParam('status')) ?: 'draft';

        if (empty($title) || empty($category)) {
            $session->setFlashMessage('error', __('admin.news_required_fields'));
            return $this->redirect('/admin/news/create');
        }

        $slug = $this->slugify($title);
        $image = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadPath = PUBLIC_PATH . '/uploads/news';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $imageFile = $_FILES['image'];
            $extension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($extension, $allowed)) {
                $filename = 'news_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                $target = $uploadPath . '/' . $filename;
                if (move_uploaded_file($imageFile['tmp_name'], $target)) {
                    $image = '/uploads/news/' . $filename;
                }
            }
        }

        $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;

        $db->execute(
            "INSERT INTO news (title, slug, excerpt, content, category, image, author_id, status, published_at, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [$title, $slug, $excerpt, $content, $category, $image, $userId, $status, $publishedAt]
        );

        $session->setFlashMessage('success', __('admin.news_created'));
        return $this->redirect('/admin/news');
    }

    public function editNews($id)
    {
        $db = $this->getDb();

        $newsItem = $db->fetchOne(
            "SELECT * FROM news WHERE id = ?",
            [$id]
        );

        if (!$newsItem) {
            $session = $this->getSession();
            $session->setFlashMessage('error', __('admin.news_not_found'));
            return $this->redirect('/admin/news');
        }

        return $this->view('admin/news-form', [
            'newsItem' => $newsItem,
            'action' => '/admin/news/' . $id . '/edit',
            'method' => 'POST',
        ]);
    }

    public function updateNews($id)
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();

        if (!$request->isPost()) {
            return $this->redirect('/admin/news');
        }

        $newsItem = $db->fetchOne("SELECT * FROM news WHERE id = ?", [$id]);
        if (!$newsItem) {
            $session->setFlashMessage('error', __('admin.news_not_found'));
            return $this->redirect('/admin/news');
        }

        $title = trim($request->getBodyParam('title'));
        $excerpt = trim($request->getBodyParam('excerpt'));
        $content = trim($request->getBodyParam('content'));
        $category = trim($request->getBodyParam('category'));
        $status = trim($request->getBodyParam('status')) ?: 'draft';

        if (empty($title) || empty($category)) {
            $session->setFlashMessage('error', __('admin.news_required_fields'));
            return $this->redirect('/admin/news/' . $id . '/edit');
        }

        $slug = $this->slugify($title);
        $image = $newsItem['image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadPath = PUBLIC_PATH . '/uploads/news';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $imageFile = $_FILES['image'];
            $extension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($extension, $allowed)) {
                $filename = 'news_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                $target = $uploadPath . '/' . $filename;
                if (move_uploaded_file($imageFile['tmp_name'], $target)) {
                    $image = '/uploads/news/' . $filename;
                }
            }
        }

        $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;

        $db->execute(
            "UPDATE news SET title = ?, slug = ?, excerpt = ?, content = ?, category = ?, image = ?, status = ?, published_at = ?, updated_at = NOW() WHERE id = ?",
            [$title, $slug, $excerpt, $content, $category, $image, $status, $publishedAt, $id]
        );

        $session->setFlashMessage('success', __('admin.news_updated'));
        return $this->redirect('/admin/news');
    }

    public function deleteNews($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();

        $newsItem = $db->fetchOne("SELECT * FROM news WHERE id = ?", [$id]);
        if (!$newsItem) {
            $session->setFlashMessage('error', __('admin.news_not_found'));
            return $this->redirect('/admin/news');
        }

        $db->execute("DELETE FROM news WHERE id = ?", [$id]);
        $session->setFlashMessage('success', __('admin.news_deleted'));
        return $this->redirect('/admin/news');
    }

    public function deleteProject($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();

        $project = $db->fetchOne("SELECT * FROM projects WHERE id = ?", [$id]);
        if (!$project) {
            $session->setFlashMessage('error', __('admin.project_not_found'));
            return $this->redirect('/admin/projects');
        }

        $db->execute("DELETE FROM projects WHERE id = ?", [$id]);
        $session->setFlashMessage('success', __('admin.project_deleted'));
        return $this->redirect('/admin/projects');
    }

    private function slugify($text)
    {
        $text = preg_replace('~[\\p{Cntrl}]~u', '', $text);
        $text = preg_replace('~[^ -]+~u', '', $text);
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text)) {
            return 'item-' . time();
        }
        return $text . '-' . time();
    }
}
