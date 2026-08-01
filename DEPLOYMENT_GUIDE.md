# URBANOVA SOLUTIONS - Guide de Déploiement

## 📋 Structure du projet

### Fichiers essentiels (à déployer)
```
urbanovacorp/
├── index.html              # Interface principale (Single Page Application)
├── api.php                 # API direct (évite les problèmes de routage)
├── index.php               # Routage de base
├── .htaccess               # Configuration Apache
├── .env.example            # Exemple de configuration
├── config/
│   └── config.php          # Configuration de la base de données
└── app/
    ├── Controllers/
    │   └── ApiController.php     # API endpoints
    └── Core/
        └── Database.php          # Connexion à la base de données
```

## 🚀 Instructions de déploiement

### 1. Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Apache avec mod_rewrite activé
- Accès à phpMyAdmin ou ligne de commande MySQL

### 2. Configuration de la base de données

```sql
-- Créer la base de données
CREATE DATABASE urbanova_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Créer l'utilisateur (optionnel)
CREATE USER 'urbanova_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON urbanova_db.* TO 'urbanova_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Importer le schéma de la base de données

```sql
-- Table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('investor', 'admin') DEFAULT 'investor',
    status ENUM('active', 'inactive') DEFAULT 'active',
    phone VARCHAR(50),
    country VARCHAR(100),
    city VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table projects
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    promoter VARCHAR(255) NOT NULL,
    promoter_id INT,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    sector VARCHAR(100) NOT NULL,
    description TEXT,
    funding_sought DECIMAL(15,2) NOT NULL,
    funding_raised DECIMAL(15,2) DEFAULT 0,
    expected_roi DECIMAL(5,2),
    status ENUM('pending', 'approved', 'rejected', 'active', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (promoter_id) REFERENCES users(id)
);

-- Table investments
CREATE TABLE investments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    investor_id INT NOT NULL,
    project_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (investor_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

-- Table contacts
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4. Configuration de l'application

Copiez `.env.example` vers `.env` et configurez:

```php
<?php
return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'urbanova_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'name' => 'URBANOVA SOLUTIONS',
        'url' => 'https://votre-domaine.com',
        'env' => 'production'
    ],
    'security' => [
        'admin_code' => '1234567'  // Changez ce code!
    ]
];
```

### 5. Déploiement des fichiers

1. **Uploader les fichiers essentiels** sur votre serveur:
   - Utilisez FTP, SFTP ou git
   - Assurez-vous que les permissions sont correctes (755 pour dossiers, 644 pour fichiers)

2. **Vérifier la configuration Apache**:
   - Assurez-vous que mod_rewrite est activé
   - Vérifiez que AllowOverride est activé dans VirtualHost

3. **Configurer le VirtualHost** (Apache):
```apache
<VirtualHost *:80>
    ServerName votre-domaine.com
    DocumentRoot /var/www/urbanovacorp
    
    <Directory /var/www/urbanovacorp>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/urbanovacorp_error.log
    CustomLog ${APACHE_LOG_DIR}/urbanovacorp_access.log combined
</VirtualHost>
```

### 6. Sécurité

**IMPORTANT**: Avant de mettre en production:

1. **Changez le code admin** dans le fichier JavaScript (index.html)
2. **Désactivez l'affichage des erreurs PHP** en production:
```php
error_reporting(0);
ini_set('display_errors', 0);
```

3. **Configurez HTTPS** avec un certificat SSL (Let's Encrypt recommandé)

4. **Protégez le dossier config**:
```apache
<Directory /var/www/urbanovacorp/config>
    Require all denied
</Directory>
```

5. **Activez les en-têtes de sécurité** dans .htaccess:
```apache
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

### 7. Test de déploiement

1. **Testez l'accès au site**: https://votre-domaine.com
2. **Testez l'inscription**: Créez un compte test
3. **Testez la connexion**: Connectez-vous avec le compte créé
4. **Testez l'admin**: Code d'accès: 1234567
5. **Testez la soumission de projet**: Soumettez un projet
6. **Testez l'investissement**: Approuvez le projet et investissez

## 🔧 Maintenance

### Sauvegardes automatiques
Configurez des sauvegardes automatiques de la base de données:

```bash
# Via cron (tous les jours à 3h du matin)
0 3 * * * mysqldump -u root -p urbanova_db > /backups/urbanova_$(date +\%Y\%m\%d).sql
```

### Surveillance
- Surveillez les logs d'erreurs Apache
- Surveillez l'utilisation du disque
- Surveillez les connexions à la base de données

## 📞 Support

Pour toute question technique, contactez l'équipe de développement.

## 📝 Notes importantes

- Le système utilise des sessions PHP pour l'authentification
- Les mots de passe sont hashés avec bcrypt (cost=12)
- L'API directe (api.php) contourne les problèmes de routage
- L'interface est une Single Page Application (SPA)
- Le code admin par défaut est 1234567 (changez-le!)
