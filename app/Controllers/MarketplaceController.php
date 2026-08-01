<?php

namespace App\Controllers;

class MarketplaceController extends Controller
{
    public function index()
    {
        $db = $this->getDb();

        // Get filter parameters
        $country = $this->getRequest()->getParam('country');
        $city = $this->getRequest()->getParam('city');
        $sector = $this->getRequest()->getParam('sector');
        $type = $this->getRequest()->getParam('type');
        $minFunding = $this->getRequest()->getParam('min_funding');
        $maxFunding = $this->getRequest()->getParam('max_funding');
        $minRoi = $this->getRequest()->getParam('min_roi');

        // Build query
        $sql = "SELECT * FROM projects WHERE status = 'approved'";
        $params = [];

        if ($country) {
            $sql .= " AND country = ?";
            $params[] = $country;
        }

        if ($city) {
            $sql .= " AND city = ?";
            $params[] = $city;
        }

        if ($sector) {
            $sql .= " AND sector = ?";
            $params[] = $sector;
        }

        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }

        if ($minFunding) {
            $sql .= " AND funding_sought >= ?";
            $params[] = $minFunding;
        }

        if ($maxFunding) {
            $sql .= " AND funding_sought <= ?";
            $params[] = $maxFunding;
        }

        if ($minRoi) {
            $sql .= " AND roi >= ?";
            $params[] = $minRoi;
        }

        $sql .= " ORDER BY created_at DESC";

        $projects = $db->fetchAll($sql, $params);

        // Get unique values for filters
        $countries = $db->fetchAll("SELECT DISTINCT country FROM projects WHERE status = 'approved' ORDER BY country");
        $cities = $db->fetchAll("SELECT DISTINCT city FROM projects WHERE status = 'approved' ORDER BY city");
        $sectors = $db->fetchAll("SELECT DISTINCT sector FROM projects WHERE status = 'approved' ORDER BY sector");

        return $this->view('marketplace/index', [
            'projects' => $projects,
            'filters' => [
                'country' => $country,
                'city' => $city,
                'sector' => $sector,
                'type' => $type,
                'min_funding' => $minFunding,
                'max_funding' => $maxFunding,
                'min_roi' => $minRoi,
            ],
            'filterOptions' => [
                'countries' => $countries,
                'cities' => $cities,
                'sectors' => $sectors,
            ],
        ]);
    }

    public function show($id)
    {
        $db = $this->getDb();

        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ? AND status = 'approved'",
            [$id]
        );

        if (!$project) {
            $session = $this->getSession();
            $session->setFlashMessage('error', __('project.not_found'));
            return $this->redirect('/marketplace');
        }

        // Get project promoter info
        $promoter = $db->fetchOne(
            "SELECT u.first_name, u.last_name, u.email FROM users u WHERE u.id = ?",
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
}
