<?php

namespace App\Controllers;

class InvestorController extends Controller
{
    public function index()
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        // Get projects the investor has expressed interest in
        $interests = $db->fetchAll("
            SELECT i.*, p.title, p.city, p.country, p.funding_sought, p.roi
            FROM investor_interests i
            JOIN projects p ON i.project_id = p.id
            WHERE i.investor_id = ?
            ORDER BY i.created_at DESC
        ", [$investor['id']]);

        return $this->view('investor/index', [
            'investor' => $investor,
            'interests' => $interests,
        ]);
    }

    public function kycForm()
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get existing KYC data if any
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        return $this->view('investor/kyc', [
            'investor' => $investor,
        ]);
    }

    public function kycSubmit()
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        $type = $request->getBodyParam('type');
        $nationality = $request->getBodyParam('nationality');
        $phone = $request->getBodyParam('phone');
        $address = $request->getBodyParam('address');
        $city = $request->getBodyParam('city');
        $country = $request->getBodyParam('country');
        $investmentCapacity = $request->getBodyParam('investment_capacity');
        $investmentSectors = $request->getBodyParam('investment_sectors');
        $riskProfile = $request->getBodyParam('risk_profile');

        // Handle file uploads
        $kycDocuments = [];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
        $uploadPath = PUBLIC_PATH . '/uploads/kyc';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Upload ID document
        if (isset($_FILES['id_document']) && $_FILES['id_document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['id_document'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowedTypes)) {
                $filename = 'id_' . $userId . '_' . time() . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $filename);
                $kycDocuments['id_document'] = $filename;
            }
        }

        // Upload additional documents
        foreach (['proof_address', 'company_docs', 'financial_docs'] as $docType) {
            if (isset($_FILES[$docType]) && $_FILES[$docType]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$docType];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowedTypes)) {
                    $filename = $docType . '_' . $userId . '_' . time() . '.' . $ext;
                    move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $filename);
                    $kycDocuments[$docType] = $filename;
                }
            }
        }

        // Update or create investor record
        if ($type === 'corporate') {
            $companyName = $request->getBodyParam('company_name');
            $companyRegNumber = $request->getBodyParam('company_registration_number');
            $companyTaxId = $request->getBodyParam('company_tax_id');

            $sql = "
                UPDATE investors SET
                    type = ?,
                    nationality = ?,
                    phone = ?,
                    address = ?,
                    city = ?,
                    country = ?,
                    company_name = ?,
                    company_registration_number = ?,
                    company_tax_id = ?,
                    investment_capacity = ?,
                    investment_sectors = ?,
                    risk_profile = ?,
                    kyc_documents = ?,
                    kyc_submitted_at = NOW(),
                    investor_status = 'pending'
                WHERE user_id = ?
            ";
            
            $db->execute($sql, [
                $type, $nationality, $phone, $address, $city, $country,
                $companyName, $companyRegNumber, $companyTaxId,
                $investmentCapacity, $investmentSectors, $riskProfile,
                json_encode($kycDocuments), $userId
            ]);
        } else {
            $idDocType = $request->getBodyParam('id_document_type');
            $idDocNumber = $request->getBodyParam('id_document_number');
            $idDocExpiry = $request->getBodyParam('id_document_expiry');

            $sql = "
                UPDATE investors SET
                    type = ?,
                    nationality = ?,
                    phone = ?,
                    address = ?,
                    city = ?,
                    country = ?,
                    id_document_type = ?,
                    id_document_number = ?,
                    id_document_expiry = ?,
                    investment_capacity = ?,
                    investment_sectors = ?,
                    risk_profile = ?,
                    kyc_documents = ?,
                    kyc_submitted_at = NOW(),
                    investor_status = 'pending'
                WHERE user_id = ?
            ";
            
            $db->execute($sql, [
                $type, $nationality, $phone, $address, $city, $country,
                $idDocType, $idDocNumber, $idDocExpiry,
                $investmentCapacity, $investmentSectors, $riskProfile,
                json_encode($kycDocuments), $userId
            ]);
        }

        $session->setFlashMessage('success', __('investor.kyc_submitted'));
        return $this->redirect('/investor');
    }

    public function dataRoom($projectId)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        // Get project data
        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ? AND status = 'approved'",
            [$projectId]
        );

        if (!$project) {
            $session->setFlashMessage('error', __('investor.project_not_found'));
            return $this->redirect('/marketplace');
        }

        // Check if investor has expressed interest
        $interest = $db->fetchOne(
            "SELECT * FROM investor_interests WHERE project_id = ? AND investor_id = ?",
            [$projectId, $investor['id']]
        );

        // Get project documents
        $documents = $db->fetchAll(
            "SELECT * FROM project_documents WHERE project_id = ?",
            [$projectId]
        );

        return $this->view('investor/data-room', [
            'project' => $project,
            'investor' => $investor,
            'interest' => $interest,
            'documents' => $documents,
        ]);
    }

    public function expressInterest($projectId)
    {
        $request = $this->getRequest();
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');

        // Get investor data
        $investor = $db->fetchOne(
            "SELECT * FROM investors WHERE user_id = ?",
            [$userId]
        );

        // Get project data
        $project = $db->fetchOne(
            "SELECT * FROM projects WHERE id = ? AND status = 'approved'",
            [$projectId]
        );

        if (!$project) {
            $session->setFlashMessage('error', __('investor.project_not_found'));
            return $this->redirect('/marketplace');
        }

        $investmentAmount = $request->getBodyParam('investment_amount');
        $message = $request->getBodyParam('message');

        // Check if interest already exists
        $existingInterest = $db->fetchOne(
            "SELECT id FROM investor_interests WHERE project_id = ? AND investor_id = ?",
            [$projectId, $investor['id']]
        );

        if ($existingInterest) {
            // Update existing interest
            $db->execute(
                "UPDATE investor_interests SET investment_amount = ?, message = ?, interest_type = 'inquiry', status = 'pending' WHERE id = ?",
                [$investmentAmount, $message, $existingInterest['id']]
            );
        } else {
            // Create new interest
            $db->execute(
                "INSERT INTO investor_interests (project_id, investor_id, investment_amount, message, interest_type, status) VALUES (?, ?, ?, ?, 'inquiry', 'pending')",
                [$projectId, $investor['id'], $investmentAmount, $message]
            );
        }

        $session->setFlashMessage('success', __('investor.interest_submitted'));
        return $this->redirect('/investor/data-room/' . $projectId);
    }
}
