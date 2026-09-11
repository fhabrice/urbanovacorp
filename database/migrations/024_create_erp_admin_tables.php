<?php

/**
 * Migration: Create ERP Admin tables for Urbanova
 * Covers CRM, Projects, Investors, Fundraising, Investments, Commissions, DataRoom, Requests, etc.
 */

return [
    'up' => function($db) {

        // Helper to check column exists
        $columnExists = function($table, $column) use ($db) {
            try {
                $res = $db->fetchAll("SHOW COLUMNS FROM `$table` LIKE ?", [$column]);
                return !empty($res);
            } catch (Exception $e) {
                return false;
            }
        };

        // 1. LEADS (Prospects) - Spec Section 7-10
        $db->execute("
            CREATE TABLE IF NOT EXISTS leads (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                first_name VARCHAR(100),
                last_name VARCHAR(100) NOT NULL,
                company_name VARCHAR(255),
                company_id INT NULL,
                phone VARCHAR(50),
                email VARCHAR(255),
                type ENUM('investisseur','porteur_projet','client','proprietaire','partenaire','institution','prestataire') NOT NULL DEFAULT 'investisseur',
                source ENUM('site_web','linkedin','whatsapp','facebook','instagram','email','evenement','recommandation','appel','prospection','partenaire') NOT NULL DEFAULT 'site_web',
                project_id INT NULL,
                amount_potential DECIMAL(15,2) NULL,
                currency VARCHAR(3) DEFAULT 'USD',
                status ENUM('nouveau','a_qualifier','qualifie','contacte','rendez_vous','opportunite','converti','perdu','suspendu') NOT NULL DEFAULT 'nouveau',
                country VARCHAR(100),
                city VARCHAR(100),
                function_title VARCHAR(100),
                sector VARCHAR(100),
                assigned_to INT NULL,
                comment TEXT,
                next_action_date DATE NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_reference (reference),
                INDEX idx_status (status),
                INDEX idx_assigned_to (assigned_to),
                INDEX idx_project_id (project_id),
                INDEX idx_email (email),
                INDEX idx_phone (phone)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 2. COMPANIES (Entreprises) - Spec 12
        $db->execute("
            CREATE TABLE IF NOT EXISTS companies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                name VARCHAR(255) NOT NULL,
                legal_form VARCHAR(100),
                rccm VARCHAR(100) UNIQUE,
                nif VARCHAR(100) UNIQUE,
                id_national VARCHAR(100) UNIQUE,
                sector VARCHAR(100),
                address TEXT,
                city VARCHAR(100),
                country VARCHAR(100),
                website VARCHAR(255),
                phone VARCHAR(50),
                email VARCHAR(255),
                responsible_id INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_name (name),
                INDEX idx_rccm (rccm),
                INDEX idx_nif (nif),
                INDEX idx_sector (sector)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 3. CRM CONTACTS - Spec 11 (separate from general contacts)
        $db->execute("
            CREATE TABLE IF NOT EXISTS crm_contacts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(255),
                phone VARCHAR(50),
                whatsapp VARCHAR(50),
                function_title VARCHAR(100),
                company_id INT NULL,
                country VARCHAR(100),
                city VARCHAR(100),
                type ENUM('investisseur','porteur_projet','client','proprietaire','partenaire','institution','prestataire') DEFAULT 'client',
                responsible_id INT NULL,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_email (email),
                INDEX idx_company_id (company_id),
                INDEX idx_responsible (responsible_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 4. OPPORTUNITIES - Spec 13-16
        $db->execute("
            CREATE TABLE IF NOT EXISTS opportunities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                name VARCHAR(255) NOT NULL,
                contact_id INT NULL,
                company_id INT NULL,
                project_id INT NULL,
                type ENUM('investissement','levee_fonds','accompagnement','conseil','immobilier','partenariat','vente','autre') NOT NULL DEFAULT 'investissement',
                amount DECIMAL(15,2) NOT NULL DEFAULT 0,
                currency VARCHAR(3) DEFAULT 'USD',
                probability TINYINT UNSIGNED DEFAULT 50,
                stage ENUM('nouveau','qualifie','contact_etabli','rendez_vous','proposition','due_diligence','negociation','engagement','gagne','perdu','suspendu') NOT NULL DEFAULT 'nouveau',
                responsible_id INT NULL,
                opened_at DATE,
                expected_close DATE,
                next_action DATE,
                notes TEXT,
                weighted_value DECIMAL(15,2) GENERATED ALWAYS AS (amount * probability / 100) STORED,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_reference (reference),
                INDEX idx_stage (stage),
                INDEX idx_responsible (responsible_id),
                INDEX idx_project_id (project_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 5. ACTIVITIES CRM - Spec 17
        $db->execute("
            CREATE TABLE IF NOT EXISTS activities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type ENUM('appel','email','whatsapp','reunion','visite','note','presentation','proposition','negociation','due_diligence') NOT NULL,
                contact_id INT NULL,
                opportunity_id INT NULL,
                project_id INT NULL,
                responsible_id INT NULL,
                activity_date DATE NOT NULL,
                activity_time TIME,
                summary TEXT,
                result TEXT,
                next_action TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_type (type),
                INDEX idx_contact (contact_id),
                INDEX idx_opportunity (opportunity_id),
                INDEX idx_date (activity_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 6. TASKS & RELANCES - Spec 18
        $db->execute("
            CREATE TABLE IF NOT EXISTS tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                responsible_id INT NULL,
                contact_id INT NULL,
                project_id INT NULL,
                opportunity_id INT NULL,
                priority ENUM('faible','normale','haute','urgente') NOT NULL DEFAULT 'normale',
                due_date DATE,
                due_time TIME,
                status ENUM('a_faire','en_cours','terminee','annulee') NOT NULL DEFAULT 'a_faire',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_responsible (responsible_id),
                INDEX idx_status (status),
                INDEX idx_priority (priority),
                INDEX idx_due_date (due_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 7. INVESTORS ENHANCEMENT - Ensure extended fields (Spec 27-30)
        // Add columns to investors if not exists
        $investorCols = [
            "ticket_minimum DECIMAL(15,2) NULL" => "ticket_minimum",
            "ticket_maximum DECIMAL(15,2) NULL" => "ticket_maximum",
            "sectors TEXT NULL" => "sectors",
            "kyc_status ENUM('non_commence','incomplet','en_verification','valide','rejete','expire') DEFAULT 'non_commence'" => "kyc_status",
            "geographies TEXT NULL" => "geographies",
            "risk_level ENUM('faible','modere','eleve') NULL" => "risk_level",
            "investment_horizon VARCHAR(50) NULL" => "investment_horizon",
            "deleted_at TIMESTAMP NULL" => "deleted_at"
        ];
        foreach ($investorCols as $def => $col) {
            if (!$columnExists('investors', $col)) {
                try { $db->execute("ALTER TABLE investors ADD COLUMN $def"); } catch (Exception $e) {}
            }
        }

        // 8. PROJECTS ENHANCEMENT - Spec 21-26
        $projectCols = [
            "reference VARCHAR(20) UNIQUE NULL" => "reference",
            "sub_sector VARCHAR(100) NULL" => "sub_sector",
            "promoter_id INT NULL" => "promoter_id",
            "company_id INT NULL" => "company_id",
            "start_date DATE NULL" => "start_date",
            "currency VARCHAR(3) DEFAULT 'USD'" => "currency",
            "funding_type ENUM('equity','dette','subvention','convertible','joint_venture','partenariat','mixte') NULL" => "funding_type",
            "amount_raised DECIMAL(15,2) DEFAULT 0" => "amount_raised",
            "validation_status VARCHAR(50) NULL" => "validation_status",
            "deleted_at TIMESTAMP NULL" => "deleted_at",
            "rccm VARCHAR(100) NULL" => "rccm",
            "nif VARCHAR(100) NULL" => "nif"
        ];
        foreach ($projectCols as $def => $col) {
            if (!$columnExists('projects', $col)) {
                try { $db->execute("ALTER TABLE projects ADD COLUMN $def"); } catch (Exception $e) {}
            }
        }
        // Try to expand status enum for projects to spec 22
        try {
            $db->execute("ALTER TABLE projects MODIFY COLUMN status ENUM('draft','submitted','under_review','info_requested','in_analysis','approved','rejected','published','fundraising_active','funded','suspended','closed','pending','brouillon','soumis','en_verification','informations_demandees','en_analyse','approuve','rejete','publie','levee_active','finance','cloture') NOT NULL DEFAULT 'draft'");
        } catch (Exception $e) {}

        // 9. FUNDRAISING CAMPAIGNS - Enhance funding_campaigns per Spec 31-33
        $campCols = [
            "reference VARCHAR(20) UNIQUE NULL" => "reference",
            "objective DECIMAL(15,2) NULL" => "objective",
            "amount_raised DECIMAL(15,2) DEFAULT 0" => "amount_raised",
            "currency VARCHAR(3) DEFAULT 'USD'" => "currency",
            "ticket_min DECIMAL(15,2) NULL" => "ticket_min",
            "ticket_max DECIMAL(15,2) NULL" => "ticket_max",
            "description TEXT NULL" => "description",
            "conditions TEXT NULL" => "conditions",
            "progress DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN objective>0 THEN amount_raised/objective*100 ELSE 0 END) STORED" => "progress",
            "deleted_at TIMESTAMP NULL" => "deleted_at"
        ];
        // Check if funding_campaigns exists, otherwise create
        try {
            $db->fetchOne("SELECT 1 FROM funding_campaigns LIMIT 1");
        } catch (Exception $e) {
            $db->execute("
                CREATE TABLE IF NOT EXISTS funding_campaigns (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    reference VARCHAR(20) UNIQUE NULL,
                    project_id INT NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    objective DECIMAL(15,2) NOT NULL,
                    amount_raised DECIMAL(15,2) DEFAULT 0,
                    currency VARCHAR(3) DEFAULT 'USD',
                    ticket_min DECIMAL(15,2) NULL,
                    ticket_max DECIMAL(15,2) NULL,
                    start_date DATE,
                    end_date DATE,
                    description TEXT,
                    conditions TEXT,
                    status ENUM('preparation','validation','active','suspendue','objectif_atteint','cloturee','annulee') DEFAULT 'preparation',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    deleted_at TIMESTAMP NULL,
                    INDEX idx_project_id (project_id),
                    INDEX idx_status (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
        foreach ($campCols as $def => $col) {
            if (!$columnExists('funding_campaigns', $col)) {
                try { $db->execute("ALTER TABLE funding_campaigns ADD COLUMN $def"); } catch (Exception $e) {}
            }
        }

        // 10. INVESTMENTS - Spec 34-37 (comprehensive)
        $db->execute("
            CREATE TABLE IF NOT EXISTS investments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                investor_id INT NOT NULL,
                project_id INT NOT NULL,
                campaign_id INT NULL,
                amount DECIMAL(15,2) NOT NULL,
                currency VARCHAR(3) DEFAULT 'USD',
                engagement_date DATE,
                payment_date DATE,
                investment_type ENUM('equity','dette','convertible','joint_venture','autre') DEFAULT 'equity',
                payment_mode VARCHAR(50),
                status ENUM('interet','intention','engagement','due_diligence','accord','en_attente_paiement','decaissement','confirme','annule') NOT NULL DEFAULT 'interet',
                commission_rate DECIMAL(5,2) DEFAULT 3.00,
                commission_amount DECIMAL(15,2) DEFAULT 0,
                responsible_id INT NULL,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_reference (reference),
                INDEX idx_investor (investor_id),
                INDEX idx_project (project_id),
                INDEX idx_campaign (campaign_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Ensure investments table has reference etc if it already existed via investment_schema.sql
        $invCols = [
            "reference VARCHAR(20) UNIQUE NULL" => "reference",
            "campaign_id INT NULL" => "campaign_id",
            "currency VARCHAR(3) DEFAULT 'USD'" => "currency",
            "engagement_date DATE NULL" => "engagement_date",
            "payment_date DATE NULL" => "payment_date",
            "investment_type ENUM('equity','dette','convertible','joint_venture','autre') DEFAULT 'equity'" => "investment_type",
            "payment_mode VARCHAR(50) NULL" => "payment_mode",
            "commission_rate DECIMAL(5,2) DEFAULT 3.00" => "commission_rate",
            "commission_amount DECIMAL(15,2) DEFAULT 0" => "commission_amount",
            "responsible_id INT NULL" => "responsible_id",
            "deleted_at TIMESTAMP NULL" => "deleted_at"
        ];
        foreach ($invCols as $def => $col) {
            if (!$columnExists('investments', $col)) {
                try { $db->execute("ALTER TABLE investments ADD COLUMN $def"); } catch (Exception $e) {}
            }
        }

        // 11. COMMISSIONS - Spec 38-39
        $db->execute("
            CREATE TABLE IF NOT EXISTS commissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                investment_id INT NULL,
                project_id INT NOT NULL,
                client_id INT NULL,
                base_amount DECIMAL(15,2) NOT NULL,
                rate DECIMAL(5,2) NOT NULL,
                commission_amount DECIMAL(15,2) NOT NULL,
                invoiced DECIMAL(15,2) DEFAULT 0,
                paid DECIMAL(15,2) DEFAULT 0,
                balance DECIMAL(15,2) GENERATED ALWAYS AS (commission_amount - paid) STORED,
                status ENUM('a_facturer','facturee','partiellement_payee','payee','annulee') NOT NULL DEFAULT 'a_facturer',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_investment (investment_id),
                INDEX idx_project (project_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 12. DATA ROOMS - Spec 40-45
        $db->execute("
            CREATE TABLE IF NOT EXISTS data_rooms (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                project_id INT NOT NULL,
                promoter_id INT NULL,
                status ENUM('brouillon','en_attente','validee','active','suspendue','archivee') NOT NULL DEFAULT 'brouillon',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                UNIQUE KEY unique_project (project_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->execute("
            CREATE TABLE IF NOT EXISTS data_room_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                data_room_id INT NOT NULL,
                project_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                category ENUM('juridique','corporate','financier','commercial','technique','fiscal','rh','contrats','licences','etudes','autres') NOT NULL DEFAULT 'autres',
                file_path VARCHAR(500) NOT NULL,
                file_size INT,
                mime_type VARCHAR(100),
                version VARCHAR(20) DEFAULT '1.0',
                uploaded_by INT NOT NULL,
                confidentiality ENUM('public','confidentiel','strict') DEFAULT 'confidentiel',
                download_allowed BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_data_room (data_room_id),
                INDEX idx_project (project_id),
                INDEX idx_category (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->execute("
            CREATE TABLE IF NOT EXISTS data_room_accesses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                data_room_id INT NOT NULL,
                project_id INT NOT NULL,
                investor_id INT NOT NULL,
                status ENUM('demande','en_verification','informations_requises','autorise','refuse','suspendu','expire') NOT NULL DEFAULT 'demande',
                nda_required BOOLEAN DEFAULT FALSE,
                nda_signed BOOLEAN DEFAULT FALSE,
                requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                decided_at TIMESTAMP NULL,
                decided_by INT NULL,
                expires_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_access (data_room_id, investor_id),
                INDEX idx_status (status),
                INDEX idx_investor (investor_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Extend data_room_audit_log if exists, ensure it logs required fields (Spec 45)
        // Already created in 019, but ensure columns

        // 13. REQUESTS (Demandes) - Spec 46-49
        $db->execute("
            CREATE TABLE IF NOT EXISTS requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                requester_id INT NULL,
                requester_name VARCHAR(255),
                requester_email VARCHAR(255),
                type ENUM('investissement','financement','accompagnement','conseil','partenariat','data_room','immobilier','visite','autre') NOT NULL DEFAULT 'autre',
                subject VARCHAR(255) NOT NULL,
                project_id INT NULL,
                description TEXT,
                priority ENUM('faible','normale','haute','urgente') DEFAULT 'normale',
                status ENUM('nouveau','assigne','en_traitement','informations_requises','traite','rejete','cloture') NOT NULL DEFAULT 'nouveau',
                assigned_to INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_reference (reference),
                INDEX idx_type (type),
                INDEX idx_status (status),
                INDEX idx_assigned (assigned_to)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 14. APPOINTMENTS (Visites / Réservations unifiée) - Spec 50
        $db->execute("
            CREATE TABLE IF NOT EXISTS appointments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                user_id INT NULL,
                project_id INT NULL,
                property_id INT NULL,
                agent_id INT NULL,
                appointment_date DATE NOT NULL,
                appointment_time TIME NOT NULL,
                subject VARCHAR(255),
                status ENUM('demandee','confirmee','effectuee','reportee','annulee','absence') NOT NULL DEFAULT 'demandee',
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_project (project_id),
                INDEX idx_user (user_id),
                INDEX idx_status (status),
                INDEX idx_date (appointment_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 15. NOTIFICATIONS - Enhance to support levels (Spec 51-52)
        $notifCols = [
            "priority ENUM('information','action_requise','important','urgent') DEFAULT 'information'" => "priority",
            "category VARCHAR(50) NULL" => "category",
            "is_read BOOLEAN DEFAULT FALSE" => "is_read",
            "read_at TIMESTAMP NULL" => "read_at"
        ];
        // Ensure notifications table exists first
        try { $db->fetchOne("SELECT 1 FROM notifications LIMIT 1"); } catch (Exception $e) {
            $db->execute("
                CREATE TABLE IF NOT EXISTS notifications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    type VARCHAR(50) NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    message TEXT NOT NULL,
                    link VARCHAR(500) NULL,
                    priority ENUM('information','action_requise','important','urgent') DEFAULT 'information',
                    is_read BOOLEAN DEFAULT FALSE,
                    read_at TIMESTAMP NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user (user_id),
                    INDEX idx_is_read (is_read)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
        foreach ($notifCols as $def => $col) {
            if (!$columnExists('notifications', $col)) {
                try { $db->execute("ALTER TABLE notifications ADD COLUMN $def"); } catch (Exception $e) {}
            }
        }
        // Try to expand type enum
        try {
            $db->execute("ALTER TABLE notifications MODIFY COLUMN type VARCHAR(50) NOT NULL");
        } catch (Exception $e) {}

        // 16. AUDIT LOG - Spec 64
        $db->execute("
            CREATE TABLE IF NOT EXISTS audit_logs (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                user_name VARCHAR(255),
                action VARCHAR(50) NOT NULL,
                module VARCHAR(50) NOT NULL,
                entity_type VARCHAR(50),
                entity_id INT,
                old_value JSON,
                new_value JSON,
                description TEXT,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_module (module),
                INDEX idx_action (action),
                INDEX idx_entity (entity_type, entity_id),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 17. ROLES & PERMISSIONS - Enhance for matrix Spec 62-63
        // Ensure roles table exists for internal admin users
        $db->execute("
            CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) UNIQUE NOT NULL,
                display_name VARCHAR(100) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        // Seed roles if empty
        $roleCount = $db->fetchOne("SELECT COUNT(*) as c FROM roles")['c'] ?? 0;
        if ($roleCount == 0) {
            $roles = [
                ['super_admin','Super Admin','Accès total'],
                ['direction','Direction','Vue stratégique'],
                ['admin_projets','Admin Projets','Gestion projets'],
                ['admin_investissement','Admin Investissement','Gestion investissements'],
                ['commercial','Commercial / CRM','Gestion CRM'],
                ['finance','Finance','Gestion commissions et finances'],
                ['content_manager','Content Manager','Gestion contenus'],
                ['support','Support','Support utilisateurs']
            ];
            foreach ($roles as $r) {
                $db->execute("INSERT INTO roles (name, display_name, description) VALUES (?,?,?)", $r);
            }
        }

        // 18. DOCUMENTS - Generic document management Spec 77-78
        $db->execute("
            CREATE TABLE IF NOT EXISTS documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference VARCHAR(20) UNIQUE NOT NULL,
                name VARCHAR(255) NOT NULL,
                category VARCHAR(50),
                file_path VARCHAR(500) NOT NULL,
                file_size INT,
                mime_type VARCHAR(100),
                version VARCHAR(20) DEFAULT '1.0',
                owner_id INT NULL,
                owner_type VARCHAR(50),
                uploader_id INT NOT NULL,
                access_level ENUM('public','prive','confidentiel') DEFAULT 'prive',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                INDEX idx_category (category),
                INDEX idx_owner (owner_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 19. Add sequence counters for identifiants automatiques Spec 67
        $db->execute("
            CREATE TABLE IF NOT EXISTS sequences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                prefix VARCHAR(20) UNIQUE NOT NULL,
                last_number INT NOT NULL DEFAULT 0,
                year INT NOT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 20. USERS enhancement for admin
        $userExtras = [
            "phone VARCHAR(50) NULL" => "phone",
            "country VARCHAR(100) NULL" => "country",
            "city VARCHAR(100) NULL" => "city",
            "deleted_at TIMESTAMP NULL" => "deleted_at",
            "last_login_at TIMESTAMP NULL" => "last_login_at",
            "role VARCHAR(50) DEFAULT 'investor'" => "role_old_check"
        ];
        // Ensure users role enum includes new roles
        try {
            $db->execute("ALTER TABLE users MODIFY COLUMN role ENUM('admin','super_admin','direction','admin_projets','admin_investissement','commercial','finance','content_manager','support','promoter','investor','client','entrepreneur','entreprise','proprietaire','partenaire') NOT NULL DEFAULT 'investor'");
        } catch (Exception $e) {}
        foreach (["phone","country","city","deleted_at","last_login_at"] as $col) {
            if (!$columnExists('users', $col)) {
                $type = $col=='deleted_at' || $col=='last_login_at' ? 'TIMESTAMP NULL' : 'VARCHAR(100) NULL';
                if ($col=='phone') $type='VARCHAR(50) NULL';
                try { $db->execute("ALTER TABLE users ADD COLUMN $col $type"); } catch (Exception $e) {}
            }
        }

        // 21. Add fake data for demo if tables empty (to populate dashboard)
        // Insert sample leads if empty
        $leadCount = $db->fetchOne("SELECT COUNT(*) as c FROM leads")['c'] ?? 0;
        if ($leadCount == 0) {
            $samples = [
                ['LEAD-2026-0001','Amadou','Diallo','Groupe Diallo','+243810000111','amadou@diallo.cd','investisseur','linkedin',1,250000,'nouveau','RDC','Kinshasa'],
                ['LEAD-2026-0002','Sophie','Martin','Martin Invest','+243820000222','sophie@martin.cd','porteur_projet','site_web',2,500000,'a_qualifier','RDC','Lubumbashi'],
                ['LEAD-2026-0003','Jean','Kabila','Kabila SARL','+243830000333','jean@kabila.cd','partenaire','recommandation',null,100000,'contacte','RDC','Goma'],
            ];
            foreach ($samples as $s) {
                try {
                    $db->execute("INSERT INTO leads (reference, first_name, last_name, company_name, phone, email, type, source, project_id, amount_potential, status, country, city) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", $s);
                } catch (Exception $e) {}
            }
        }

        $oppCount = $db->fetchOne("SELECT COUNT(*) as c FROM opportunities")['c'] ?? 0;
        if ($oppCount == 0) {
            $opps = [
                ['OPP-2026-0001','Investissement Résidence Horizon',1,1,1,'investissement',250000,'USD',60,'proposition',1],
                ['OPP-2026-0002','Levée Fonds Urban Business Park',2,1,2,'levee_fonds',600000,'USD',40,'negociation',1],
                ['OPP-2026-0003','Partenariat Eco City',1,1,3,'partenariat',150000,'USD',80,'engagement',1],
            ];
            foreach ($opps as $o) {
                try {
                    $db->execute("INSERT INTO opportunities (reference, name, contact_id, company_id, project_id, type, amount, currency, probability, stage, responsible_id, opened_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,CURDATE())", $o);
                } catch (Exception $e) {}
            }
        }

        // Sample tasks
        $taskCount = $db->fetchOne("SELECT COUNT(*) as c FROM tasks")['c'] ?? 0;
        if ($taskCount == 0) {
            $tasks = [
                ['TASK-2026-0001','Relance lead Amadou','Appeler pour qualification',1,1,1,1,'haute', date('Y-m-d', strtotime('+2 days')),'a_faire'],
                ['TASK-2026-0002','Vérification KYC Sophie','Vérifier documents KYC',1,null,null,2,'urgente', date('Y-m-d'),'en_cours'],
                ['TASK-2026-0003','Préparer proposition','Envoyer proposition commerciale',1,1,1,1,'normale', date('Y-m-d', strtotime('+5 days')),'a_faire'],
            ];
            foreach ($tasks as $t) {
                try {
                    $db->execute("INSERT INTO tasks (reference, title, description, responsible_id, contact_id, project_id, opportunity_id, priority, due_date, status) VALUES (?,?,?,?,?,?,?,?,?,?)", $t);
                } catch (Exception $e) {}
            }
        }

        // Sample companies
        $compCount = $db->fetchOne("SELECT COUNT(*) as c FROM companies")['c'] ?? 0;
        if ($compCount == 0) {
            try {
                $db->execute("INSERT INTO companies (reference, name, legal_form, rccm, nif, sector, city, country, phone, email) VALUES ('COMP-2026-0001','Groupe Diallo SARL','SARL','RCCM-001','NIF-001','Immobilier','Kinshasa','RDC','+243810000111','contact@diallo.cd')");
                $db->execute("INSERT INTO companies (reference, name, legal_form, rccm, nif, sector, city, country, phone, email) VALUES ('COMP-2026-0002','Urban Invest SA','SA','RCCM-002','NIF-002','Finance','Lubumbashi','RDC','+243820000222','contact@urbaninvest.cd')");
            } catch (Exception $e) {}
        }
    },
    'down' => function($db) {
        $tables = ['audit_logs','sequences','documents','roles','appointments','requests','data_room_accesses','data_room_documents','data_rooms','commissions','investments','funding_campaigns','tasks','activities','opportunities','crm_contacts','companies','leads'];
        foreach ($tables as $t) {
            $db->execute("DROP TABLE IF EXISTS $t");
        }
    }
];
