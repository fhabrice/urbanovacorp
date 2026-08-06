<?php

namespace App\Controllers;

class ProjectController extends Controller
{
    public function index()
    {
        $db = $this->getDb();
        $request = $this->getRequest();

        // Get filter parameters
        $country = $request->getParam('country');
        $city = $request->getParam('city');
        $sector = $request->getParam('sector');
        $type = $request->getParam('type');

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
            "SELECT u.*, COALESCE(CONCAT(u.first_name, ' ', u.last_name), u.name) as full_name FROM users u WHERE u.id = ?",
            [$project['user_id']]
        );

        return $this->view('projects/show', [
            'project' => $project,
            'promoter' => $promoter,
        ]);
    }

    public function submitForm()
    {
        $request = $this->getRequest();
        $step = (int) ($request->getParam('step') ?: 1);
        if ($step < 1 || $step > 4) $step = 1;

        $data = $_SESSION['project_submission'] ?? [];

        return $this->view('projects/submit-step' . $step, [
            'data' => $data,
            'step' => $step,
        ]);
    }

    public function submit()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        $step = (int) ($request->getBodyParam('step') ?: 1);
        $action = $request->getBodyParam('action') ?: 'next';

        if (!isset($_SESSION['project_submission'])) {
            $_SESSION['project_submission'] = [];
        }

        // Merge posted non-file fields into session
        $posted = $request->getBody();
        foreach ($posted as $k => $v) {
            if (in_array($k, ['step', 'action'])) continue;
            $_SESSION['project_submission'][$k] = $v;
        }

        // If files were uploaded (step 3), save them now into uploads dir and keep filenames in session
        if (!empty($_FILES)) {
            $uploadPath = PUBLIC_PATH . '/uploads/projects';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

            $allowed = ['pdf','doc','docx','jpg','jpeg','png','webp','mp4','mov'];
            foreach ($_FILES as $field => $fileInfo) {
                if (is_array($fileInfo['name'])) {
                    // multiple files
                    $saved = [];
                    for ($i=0;$i<count($fileInfo['name']);$i++) {
                        if ($fileInfo['error'][$i] !== UPLOAD_ERR_OK) continue;
                        $ext = strtolower(pathinfo($fileInfo['name'][$i], PATHINFO_EXTENSION));
                        if (!in_array($ext, $allowed)) continue;
                        $filename = $field . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                        move_uploaded_file($fileInfo['tmp_name'][$i], $uploadPath . '/' . $filename);
                        $saved[] = $filename;
                    }
                    if (!empty($saved)) {
                        $_SESSION['project_submission']['uploaded_files'][$field] = $saved;
                    }
                } else {
                    if ($fileInfo['error'] !== UPLOAD_ERR_OK) continue;
                    $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed)) continue;
                    $filename = $field . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                    move_uploaded_file($fileInfo['tmp_name'], $uploadPath . '/' . $filename);
                    $_SESSION['project_submission']['uploaded_files'][$field] = $filename;
                }
            }
        }

        // Navigation actions
        if ($action === 'next' && $step < 4) {
            $next = $step + 1;
            return $this->redirect('/projects/submit?step=' . $next);
        }

        if ($action === 'prev' && $step > 1) {
            $prev = $step - 1;
            return $this->redirect('/projects/submit?step=' . $prev);
        }

        // Final submit: validate and persist
        $data = $_SESSION['project_submission'];

        $required = ['promoter_name','promoter_type','contact_name','contact_phone','contact_email','project_name','project_type','operation_type','country','city','description','project_status','total_cost','funding_sought'];
        $errors = [];
        foreach ($required as $r) {
            if (empty($data[$r])) {
                $errors[$r] = 'Ce champ est requis.';
            }
        }

        // Email validation
        if (!empty($data['contact_email']) && !filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['contact_email'] = 'Adresse e-mail invalide.';
        }

        // Phone minimal validation (digits, +, spaces)
        if (!empty($data['contact_phone']) && !preg_match('/^[0-9+\s\-()]{6,20}$/', $data['contact_phone'])) {
            $errors['contact_phone'] = 'Numéro de téléphone invalide.';
        }

        // Numeric checks
        if (!empty($data['total_cost']) && !is_numeric($data['total_cost'])) {
            $errors['total_cost'] = 'Valeur numérique attendue.';
        }
        if (!empty($data['funding_sought']) && !is_numeric($data['funding_sought'])) {
            $errors['funding_sought'] = 'Valeur numérique attendue.';
        }

        if (!empty($errors)) {
            $_SESSION['project_submission_errors'] = $errors;
            return $this->redirect('/projects/submit?step=1');
        }

        // generate reference and slug
        $reference = 'URB-' . strtoupper(substr(md5(uniqid((string)time(), true)), 0, 8));
        $slug = preg_replace('~[^a-z0-9]+~', '-', strtolower($data['project_name']));
        $slug = trim($slug, '-');

        // prepare documents JSON
        $uploadedFiles = $data['uploaded_files'] ?? [];
        $documentsJson = !empty($uploadedFiles) ? json_encode($uploadedFiles) : null;

        // insert with correct project document columns
        $imagePath = null;
        if (!empty($uploadedFiles['photos'])) {
            if (is_array($uploadedFiles['photos'])) {
                $imagePath = $uploadedFiles['photos'][0] ?? null;
            } else {
                $imagePath = $uploadedFiles['photos'];
            }
        }

        $sql = "INSERT INTO projects (
            user_id, reference, title, slug, promoter, promoter_name, promoter_id,
            contact_name, contact_phone, contact_email,
            type, operation_type, country, province, city, commune, gps,
            description, project_status, estimated_delivery_date,
            total_cost, amount_invested, funding_sought, currency,
            units_for_sale, units_for_rent, sale_price, rental_price,
            business_plan_path, pitch_deck_path, financial_model_path, funding_raised, expected_roi, duration_months,
            image, status, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted', NOW(), NOW())";

        $params = [
            $userId,
            $reference,
            $data['project_name'],
            $slug,
            $data['promoter_name'] ?? null,
            $data['promoter_name'] ?? null,
            $userId,
            $data['contact_name'] ?? null,
            $data['contact_phone'] ?? null,
            $data['contact_email'] ?? null,
            $data['project_type'] ?? null,
            $data['operation_type'] ?? null,
            $data['country'] ?? null,
            $data['province'] ?? null,
            $data['city'] ?? null,
            $data['commune'] ?? null,
            $data['gps'] ?? null,
            $data['description'] ?? null,
            $data['project_status'] ?? null,
            !empty($data['estimated_delivery_date']) ? $data['estimated_delivery_date'] : null,
            $data['total_cost'] ?? 0,
            $data['amount_invested'] ?? 0,
            $data['funding_sought'] ?? 0,
            $data['currency'] ?? null,
            $data['units_for_sale'] ?? null,
            $data['units_for_rent'] ?? null,
            $data['sale_price'] ?? null,
            $data['rental_price'] ?? null,
            $uploadedFiles['business_plan'] ?? null,
            $uploadedFiles['pitch_deck'] ?? null,
            $uploadedFiles['financial_model'] ?? null,
            $data['funding_raised'] ?? 0,
            $data['roi'] ?? null,
            is_numeric($data['project_duration']) ? (int)$data['project_duration'] : null,
            $imagePath
        ];

        $db->execute($sql, $params);
        $projectId = $db->lastInsertId();

        // clear session
        unset($_SESSION['project_submission']);
        unset($_SESSION['project_submission_errors']);

        $session->setFlashMessage('success', __('project.submitted_success'));
        return $this->redirect('/projects/' . $projectId);
    }
}
