-- Demo Data for URBANOVA SOLUTIONS
-- OPTIONAL: Run this script to populate the database with demonstration data.
-- In phpMyAdmin, select your database (ex: wqmetrvw_urbanova) before importing.

-- Demo users and roles
INSERT INTO users (first_name, last_name, email, password, role, status, email_verified, created_at, updated_at) VALUES
('Jean', 'Dupont', 'jean.dupont@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'investor', 'active', TRUE, NOW(), NOW()),
('Marie', 'Kouassi', 'marie.kouassi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'investor', 'active', TRUE, NOW(), NOW()),
('Claire', 'Ngoma', 'claire.ngoma@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'promoter', 'active', TRUE, NOW(), NOW()),
('Admin', 'Urbanova', 'admin@urbanova.cd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', TRUE, NOW(), NOW());

-- Demo investor profiles
INSERT INTO investors (user_id, type, investor_status, nationality, phone, address, city, country, investment_capacity, investment_sectors, risk_profile, kyc_documents, kyc_submitted_at, created_at, updated_at) VALUES
(1, 'individual', 'approved', 'Congo', '+243810000001', 'Avenue Kimbangu, Kinshasa', 'Kinshasa', 'RDC', 850000, 'Immobilier, Infrastructures', 'moderate', JSON_ARRAY('id_document'), NOW(), NOW(), NOW()),
(2, 'individual', 'approved', 'Congo', '+243820000002', 'Boulevard du 30 Juin, Kinshasa', 'Kinshasa', 'RDC', 600000, 'Logement social, Energie', 'conservative', JSON_ARRAY('id_document', 'proof_address'), NOW(), NOW(), NOW());

-- Demo projects
INSERT INTO projects (user_id, title, description, type, sector, country, city, address, total_cost, equity_contribution, funding_sought, funding_mobilized, roi, tri, payback_period, project_duration, housing_units, jobs_created, status, image, created_at, updated_at) VALUES
(3, 'Résidence Horizon', 'Construction de 40 logements haut de gamme avec espaces verts et services intégrés.', 'residential', 'Résidentiel', 'RDC', 'Goma', 'Quartier Panorama, Goma', 1200000, 400000, 800000, 250000, 24, 18, 36, 24, 40, 120, 'approved', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80', NOW(), NOW()),
(3, 'Urban Business Park', 'Complexe de bureaux modernes et espaces commerciaux en plein centre-ville.', 'commercial', 'Bureaux', 'RDC', 'Kinshasa', 'Avenue des Huileries, Kinshasa', 7500000, 1500000, 6000000, 2100000, 28, 20, 48, 60, 0, 180, 'approved', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80', NOW(), NOW()),
(3, 'Eco City Villas', 'Villas écologiques avec panneaux solaires, récupération d''eau et jardin partagé.', 'residential', 'Résidentiel', 'Rwanda', 'Kigali', 'Zone Verte, Kigali', 1800000, 300000, 1500000, 330000, 22, 16, 42, 18, 30, 95, 'approved', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80', NOW(), NOW());

-- Demo project documents
INSERT INTO project_documents (project_id, document_type, title, file_path, file_size, mime_type, is_confidential, uploaded_by, created_at) VALUES
(1, 'business_plan', 'Business Plan Résidence Horizon', 'business_plan_rh.pdf', 240000, 'application/pdf', TRUE, 3, NOW()),
(1, 'financial_model', 'Modèle financier Résidence Horizon', 'financial_model_rh.xlsx', 320000, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', TRUE, 3, NOW()),
(2, 'business_plan', 'Business Plan Urban Business Park', 'business_plan_ubp.pdf', 280000, 'application/pdf', TRUE, 3, NOW()),
(3, 'due_diligence', 'Étude de faisabilité Eco City Villas', 'due_diligence_ecv.pdf', 210000, 'application/pdf', TRUE, 3, NOW());

-- Demo investor interests
INSERT INTO investor_interests (project_id, investor_id, interest_type, investment_amount, message, nda_signed, nda_signed_at, status, created_at, updated_at) VALUES
(1, 1, 'inquiry', 250000, 'Je souhaite financer une partie de la résidence et participer au suivi du projet.', TRUE, NOW(), 'accepted', NOW(), NOW()),
(2, 1, 'inquiry', 400000, 'Intéressé par le business park pour diversification de portefeuille.', FALSE, NULL, 'pending', NOW(), NOW()),
(1, 2, 'inquiry', 150000, 'Projet attractif pour le secteur résidentiel à Goma.', TRUE, NOW(), 'accepted', NOW(), NOW());

-- Demo investment records
INSERT INTO investments (investor_id, project_id, amount, status, investment_date, expected_return, actual_return, notes, created_at) VALUES
(1, 1, 250000, 'confirmed', NOW(), 60000, NULL, 'Investissement initial confirmé pour Résidence Horizon.', NOW()),
(2, 1, 150000, 'confirmed', NOW(), 36000, NULL, 'Participation au financement de la phase 1.', NOW());

-- Demo contact messages
INSERT INTO contacts (name, email, phone, subject, message, created_at) VALUES
('Pierre Mukendi', 'pierre.mukendi@example.com', '+243900000000', 'Demande d''informations', 'Je souhaite obtenir des détails financiers sur Résidence Horizon.', NOW()),
('Sarah Ndaye', 'sarah.ndaye@example.com', '+243810000000', 'Investissement', 'Je suis intéressée par le projet Eco City Villas.', NOW());
