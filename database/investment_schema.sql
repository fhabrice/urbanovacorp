-- Investment Platform Schema for URBANOVA SOLUTIONS
-- MySQL schema for the investment and project management platform

CREATE DATABASE IF NOT EXISTS urbanova_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE urbanova_db;

-- Users table (admins, investors, project owners)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'investor', 'project_owner') NOT NULL DEFAULT 'investor',
  status ENUM('active', 'pending', 'suspended') NOT NULL DEFAULT 'pending',
  phone VARCHAR(50),
  country VARCHAR(100),
  city VARCHAR(100),
  kyc_status ENUM('not_submitted', 'submitted', 'approved', 'rejected') DEFAULT 'not_submitted',
  kyc_documents TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Projects table
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  promoter VARCHAR(255) NOT NULL,
  promoter_id INT NULL,
  city VARCHAR(100) NOT NULL,
  country VARCHAR(100) NOT NULL,
  sector ENUM('Résidentiel', 'Commercial', 'Bureaux', 'Industriel', 'Infrastructure') NOT NULL,
  description TEXT,
  business_plan TEXT,
  financial_model TEXT,
  funding_sought DECIMAL(15,2) NOT NULL,
  funding_raised DECIMAL(15,2) DEFAULT 0,
  expected_roi DECIMAL(5,2) NOT NULL,
  duration_months INT,
  status ENUM('pending', 'approved', 'funding', 'completed', 'rejected') NOT NULL DEFAULT 'pending',
  image VARCHAR(512),
  documents TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (promoter_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Investments table
CREATE TABLE investments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  investor_id INT NOT NULL,
  project_id INT NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  investment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expected_return DECIMAL(15,2),
  actual_return DECIMAL(15,2),
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (investor_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contacts table
CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  subject VARCHAR(255),
  message TEXT NOT NULL,
  status ENUM('new', 'contacted', 'closed') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Documents table (for projects and KYC)
CREATE TABLE documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  project_id INT NULL,
  document_type ENUM('business_plan', 'financial_model', 'kyc_identity', 'kyc_address', 'other') NOT NULL,
  file_path VARCHAR(512) NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_size INT,
  mime_type VARCHAR(100),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- News/Articles table
CREATE TABLE news (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt VARCHAR(500),
  content TEXT,
  category ENUM('entreprise', 'projets', 'marché', 'partenariats') NOT NULL,
  image VARCHAR(512),
  author_id INT NULL,
  status ENUM('draft', 'published') DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes for performance
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_projects_sector ON projects(sector);
CREATE INDEX idx_investments_investor ON investments(investor_id);
CREATE INDEX idx_investments_project ON investments(project_id);
CREATE INDEX idx_contacts_status ON contacts(status);
CREATE INDEX idx_news_status ON news(status);
CREATE INDEX idx_news_category ON news(category);
