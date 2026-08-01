-- urbanova_schema.sql
-- MySQL schema for Urbanova (utf8mb4)

CREATE DATABASE IF NOT EXISTS urbanova CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE urbanova;

-- Users (admin/editor)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Departments
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Services
CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Projects
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(250) NOT NULL,
  slug VARCHAR(250) NOT NULL UNIQUE,
  excerpt VARCHAR(500),
  description LONGTEXT,
  department_id INT NULL,
  status ENUM('draft','published','archived') DEFAULT 'draft',
  start_date DATE NULL,
  end_date DATE NULL,
  location VARCHAR(255) NULL,
  featured_image VARCHAR(512) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team members
CREATE TABLE team_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  role VARCHAR(150),
  bio TEXT,
  photo VARCHAR(512),
  email VARCHAR(200),
  phone VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Blog posts
CREATE TABLE blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(250) NOT NULL,
  slug VARCHAR(250) NOT NULL UNIQUE,
  excerpt VARCHAR(500),
  content LONGTEXT,
  author_id INT NULL,
  status ENUM('draft','published') DEFAULT 'draft',
  published_at DATETIME NULL,
  featured_image VARCHAR(512) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact messages / leads
CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(200),
  phone VARCHAR(50),
  message TEXT NOT NULL,
  status ENUM('new','contacted','closed') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Generic images table to attach images to projects, posts, team etc.
CREATE TABLE images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  model_type VARCHAR(100) NOT NULL,
  model_id INT NOT NULL,
  path VARCHAR(512) NOT NULL,
  alt VARCHAR(255),
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Simple key/value settings
CREATE TABLE settings (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes for common lookups
CREATE INDEX idx_projects_department ON projects(department_id);
CREATE INDEX idx_blog_status ON blog_posts(status);
CREATE INDEX idx_contacts_status ON contacts(status);

-- Sample initial data (adjust paths/emails as needed)
INSERT INTO users (name,email,password_hash,role) VALUES
('Admin','admin@example.com','<remplacez_par_hash>', 'admin');

INSERT INTO departments (name,slug,description) VALUES
('Urbanova Construction','urbanova-construction','Travaux de construction et maintenance'),
('Urbanova Clean City','urbanova-clean-city','Assainissement et gestion des déchets'),
('Urbanova Facility','urbanova-facility','Maintenance et facility management');

INSERT INTO services (title,slug,description) VALUES
('Construction & Maintenance','construction-maintenance','Conception, construction et maintenance d\'infrastructures'),
('Assainissement & Déchets','assainissement-dechets','Collecte, traitement et valorisation des déchets');

INSERT INTO team_members (name,role,bio,photo,email) VALUES
('Jean Mupemba','Chef de Chantier','Ingénieur civil spécialisé en génie civil et QSE','assets/images/team1.jpg','jean@urbanova.cd'),
('Aïsha Kitenge','Responsable Environnement','Spécialiste en assainissement et gestion des déchets','assets/images/team2.jpg','aisha@urbanova.cd');

INSERT INTO projects (title,slug,excerpt,description,department_id,status,location,featured_image) VALUES
('Réhabilitation route Kasai','rehabilitation-route-kasai','Réhabilitation d\'une section de 12km','Détails du projet... ',1,'published','Province du Kasai','assets/images/slide1.jpg'),
('Collecte & Valorisation déchets Kinshasa','collecte-valorisation-kinshasa','Programme pilote de collecte','Détails du projet... ',2,'published','Kinshasa','assets/images/slide2.jpg');

INSERT INTO blog_posts (title,slug,excerpt,content,author_id,status,published_at,featured_image) VALUES
('Urbanisme durable en RDC','urbanisme-durable-rdc','Résumé de la stratégie...','Contenu complet...',1,'published',NOW(),'assets/images/slide3.jpg');

-- Example settings
INSERT INTO settings (`key`,`value`) VALUES
('site_name','URBANOVA SOLUTIONS'),
('contact_email','contact@urbanova.cd');

-- End of schema
