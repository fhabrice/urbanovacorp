<?php

namespace App\Controllers;

class ProjectController extends Controller
{
    public function index()
    {
        $db = $this->getDb();
        $session = $this->getSession();

        // Get filter parameters
        $country = $this->getRequest()->getParam('country');
        $city = $this->getRequest()->getParam('city');
        $sector = $this->getRequest()->getParam('sector');
        $type = $this->getRequest()->getParam('type');

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

        $sql .= " ORDER BY created_at DESC";

        $projects = $db->fetchAll($sql, $params);

        return $this->view('projects/index', [
            'projects' => $projects,
            'filters' => [
                'country' => $country,
                'city' => $city,
                'sector' => $sector,
                'type' => $type,
            ],
        ]);
    }

    public function show($id)
    {
        $db = $this->getDb();

        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ?",
            [$id]
        );

        if (!$project) {
            $session = $this->getSession();
            $session->setFlashMessage('error', __('project.not_found'));
            return $this->redirect('/projects');
        }

        // Get project promoter info
        $promoter = $db->fetchOne(
            "SELECT u.first_name, u.last_name, u.email FROM users u WHERE u.id = ?",
            [$project['user_id']]
        );

        return $this->view('projects/show', [
            'project' => $project,
            'promoter' => $promoter,
        ]);
    }

    public function submitForm()
    {
        return $this->view('projects/submit');
    }

    public function submit()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get form data
        $title = $request->getBodyParam('title');
        $description = $request->getBodyParam('description');
        $type = $request->getBodyParam('type');
        $sector = $request->getBodyParam('sector');
        $country = $request->getBodyParam('country');
        $city = $request->getBodyParam('city');
        $address = $request->getBodyParam('address');
        $totalCost = $request->getBodyParam('total_cost');
        $equityContribution = $request->getBodyParam('equity_contribution');
        $fundingSought = $request->getBodyParam('funding_sought');
        $roi = $request->getBodyParam('roi');
        $tri = $request->getBodyParam('tri');
        $paybackPeriod = $request->getBodyParam('payback_period');
        $projectDuration = $request->getBodyParam('project_duration');
        $housingUnits = $request->getBodyParam('housing_units') ?: 0;
        $jobsCreated = $request->getBodyParam('jobs_created') ?: 0;

        // Validate required fields
        if (empty($title) || empty($description) || empty($type) || empty($country) || 
            empty($city) || empty($totalCost) || empty($fundingSought)) {
            $session->setFlashMessage('error', __('project.required_fields'));
            return $this->redirect('/projects/submit');
        }

        // Handle file uploads
        $uploadPath = PUBLIC_PATH . '/uploads/projects';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $uploadedFiles = [];

        // Upload project image
        if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['project_image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowedTypes)) {
                $filename = 'project_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $filename);
                $uploadedFiles['image'] = $filename;
            }
        }

        // Upload documents
        $documentFields = [
            'business_plan' => 'business_plan_path',
            'pitch_deck' => 'pitch_deck_path',
            'financial_model' => 'financial_model_path',
            'feasibility_study' => 'feasibility_study_path',
            'land_title' => 'land_title_path',
            'plans' => 'plans_path',
            'permits' => 'permits_path',
        ];

        foreach ($documentFields as $field => $dbField) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$field];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowedTypes)) {
                    $filename = $field . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $filename);
                    $uploadedFiles[$dbField] = $filename;
                }
            }
        }

        // Insert project
        $sql = "
            INSERT INTO projects (
                user_id, title, description, type, sector, country, city, address,
                total_cost, equity_contribution, funding_sought, roi, tri, payback_period,
                project_duration, housing_units, jobs_created, status, submitted_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted', NOW())
        ";

        $db->execute($sql, [
            $userId, $title, $description, $type, $sector, $country, $city, $address,
            $totalCost, $equityContribution, $fundingSought, $roi, $tri, $paybackPeriod,
            $projectDuration, $housingUnits, $jobsCreated
        ]);

        $projectId = $db->lastInsertId();

        // Update uploaded files
        if (!empty($uploadedFiles)) {
            $updateFields = [];
            $updateParams = [];

            foreach ($uploadedFiles as $field => $filename) {
                $updateFields[] = "$field = ?";
                $updateParams[] = $filename;
            }

            if (!empty($updateFields)) {
                $updateParams[] = $projectId;
                $updateSql = "UPDATE projects SET " . implode(', ', $updateFields) . " WHERE id = ?";
                $db->execute($updateSql, $updateParams);
            }
        }

        $session->setFlashMessage('success', __('project.submitted_success'));
        return $this->redirect('/projects/' . $projectId);
    }
}
