-- urbanova_schema_sqlite.sql
-- SQLite-compatible schema for Urbanova (development/test)

PRAGMA foreign_keys = ON;

-- Users
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'user', -- values: admin, editor, user
  created_at DATETIME DEFAULT (datetime('now'))
);

-- Departments
CREATE TABLE departments (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  description TEXT,
  created_at DATETIME DEFAULT (datetime('now'))
);

-- Services
CREATE TABLE services (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  description TEXT,
  created_at DATETIME DEFAULT (datetime('now'))
);

-- Projects
CREATE TABLE projects (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  excerpt TEXT,
  description TEXT,
  department_id INTEGER REFERENCES departments(id) ON DELETE SET NULL,
  status TEXT DEFAULT 'draft', -- draft, published, archived
  start_date DATE,
  end_date DATE,
  location TEXT,
  featured_image TEXT,
  created_at DATETIME DEFAULT (datetime('now')),
  updated_at DATETIME
);

-- Team members
CREATE TABLE team_members (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  role TEXT,
  bio TEXT,
  photo TEXT,
  email TEXT,
  phone TEXT,
  created_at DATETIME DEFAULT (datetime('now'))
);

-- Blog posts
CREATE TABLE blog_posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  excerpt TEXT,
  content TEXT,
  author_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
  status TEXT DEFAULT 'draft',
  published_at DATETIME,
  featured_image TEXT,
  created_at DATETIME DEFAULT (datetime('now'))
);

-- Contacts
CREATE TABLE contacts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT,
  phone TEXT,
  message TEXT NOT NULL,
  status TEXT DEFAULT 'new', -- new, contacted, closed
  created_at DATETIME DEFAULT (datetime('now'))
);

-- Images
CREATE TABLE images (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  model_type TEXT NOT NULL,
  model_id INTEGER NOT NULL,
  path TEXT NOT NULL,
  alt TEXT,
  sort_order INTEGER DEFAULT 0
);

-- Settings
CREATE TABLE settings (
  key TEXT PRIMARY KEY,
  value TEXT
);

-- Sample inserts (optional)
INSERT INTO users (name,email,password_hash,role) VALUES ('Admin','admin@example.com','<replace_hash>','admin');
