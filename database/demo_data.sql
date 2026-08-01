-- Demo Data for URBANOVA SOLUTIONS
-- OPTIONAL: Run this script to populate the database with demonstration data
-- ONLY use this for testing/development purposes

USE urbanova_db;

-- Insert demo projects
INSERT INTO projects (title, promoter, city, country, sector, description, funding_sought, funding_raised, expected_roi, status, image, created_at) VALUES
('Résidence Horizon', 'SARL Horizon', 'Goma', 'RDC', 'Résidentiel', 'Projet de construction de 40 logements haut de gamme avec infrastructure moderne', 800000, 250000, 24, 'funding', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80', NOW()),
('Urban Business Park', 'Urban Corp', 'Kinshasa', 'RDC', 'Bureaux', 'Complexe de bureaux et espaces commerciaux au cœur de Kinshasa', 6000000, 2100000, 28, 'funding', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=600&q=80', NOW()),
('Eco City Villas', 'Green Builders', 'Kigali', 'Rwanda', 'Résidentiel', 'Villas écologiques avec système solaire et récupération d''eau', 1500000, 330000, 22, 'approved', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80', NOW()),
('Commercial Hub Goma', 'Goma Investment', 'Goma', 'RDC', 'Commercial', 'Centre commercial moderne avec 50 boutiques et espace de restauration', 2000000, 500000, 25, 'funding', 'https://images.unsplash.com/photo-1554469384-e58fac16e23a?auto=format&fit=crop&w=600&q=80', NOW()),
('Résidence Kivu Green', 'Kivu Properties', 'Goma', 'RDC', 'Résidentiel', 'Logements écologiques avec vue sur le lac Kivu', 1200000, 450000, 20, 'completed', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80', NOW());

-- Insert demo users (investors)
-- Password for all demo users: password
INSERT INTO users (name, email, password, role, status, created_at) VALUES
('Jean Dupont', 'jean.dupont@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'investor', 'active', NOW()),
('Marie Kouassi', 'marie.kouassi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'investor', 'active', NOW()),
('Admin URBANOVA', 'admin@urbanova.cd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW());

-- Insert demo investments
INSERT INTO investments (investor_id, project_id, amount, status, created_at) VALUES
(1, 1, 300000, 'confirmed', NOW()),
(1, 2, 700000, 'confirmed', NOW()),
(1, 3, 300000, 'confirmed', NOW()),
(2, 1, 200000, 'confirmed', NOW()),
(2, 4, 250000, 'confirmed', NOW());

-- Insert demo contact messages
INSERT INTO contacts (name, email, phone, subject, message, created_at) VALUES
('Pierre Mukendi', 'pierre.mukendi@example.com', '+243900000000', 'Demande d''informations', 'Je souhaite avoir plus d''informations sur vos projets à Goma', NOW()),
('Sarah Ndaye', 'sarah.ndaye@example.com', '+243800000000', 'Investissement', 'Je suis intéressée par le projet Eco City Villas', NOW());
