# URBANOVA SOLUTIONS - Plateforme Web Corporate & Investissement Immobilier

Une plateforme web moderne pour le développement urbain durable, la levée de fonds immobilière et la mise en relation entre porteurs de projets et investisseurs certifiés en RD Congo et en Afrique centrale.

## 🚀 Fonctionnalités

### Site Corporate Premium
- Page d'accueil avec statistiques et projets en vedette
- Pages institutionnelles (À propos, Gouvernance, Services)
- Formulaire de contact et demande de devis
- Design responsive et professionnel

### Plateforme de Levée de Fonds
- Soumission de projets immobiliers complets
- Système de validation administrative
- Upload de documents (business plan, plans, autorisations)
- Informations financières détaillées (ROI, TRI, etc.)

### Marketplace Immobilière
- Affichage des projets validés
- Filtres avancés (pays, ville, secteur, type, montant)
- Progression du financement en temps réel
- Interface intuitive pour les investisseurs

### Espace Investisseurs Certifiés
- Vérification KYC/KYB rigoureuse
- Formulaires individuels et corporatifs
- Data Room sécurisée
- Système d'expression d'intérêt
- Contact direct avec les promoteurs

### Administration
- Tableau de bord avec statistiques
- Gestion des projets (validation, rejet)
- Gestion des investisseurs (approbation KYC)
- Rapports et analyses
- Interface de gestion complète

## 🛠️ Technologies

- **Backend**: PHP 8.1+ (Pure PHP, MVC Architecture)
- **Base de données**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Sécurité**: CSRF Protection, Password Hashing, Input Validation, Rate Limiting
- **Langues**: Français/English (Support bilingue)

## 📋 Prérequis

- PHP 8.1 ou supérieur
- MySQL/MariaDB 5.7 ou supérieur
- Apache ou Nginx
- Composer (pour l'autoloading)
- Extensions PHP: PDO, mbstring, json, openssl

## 🔧 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/yourusername/urbanovacorp.git
cd urbanovacorp
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configuration de la base de données

Créez une base de données MySQL:

```sql
CREATE DATABASE urbanova_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configurez les paramètres de connexion dans `config/config.php`:

```php
'database' => [
    'host' => 'localhost',
    'port' => '3306',
    'name' => 'urbanova_db',
    'user' => 'your_username',
    'password' => 'your_password',
],
```

### 4. Exécuter les migrations

```bash
php database/migrate.php
```

Ou manuellement:

```php
<?php
require_once 'database/Migration.php';
require_once 'config/config.php';

$config = require 'config/config.php';
$db = new App\Core\Database($config['database']);
$migration = new Migration($db, 'database/migrations');
$migration->run();
```

### 5. Configuration du serveur web

#### Apache (.htaccess)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

#### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/urbanovacorp/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6. Permissions

```bash
chmod -R 755 public/
chmod -R 777 public/uploads/
chmod -R 777 storage/
```

### 7. Créer un compte administrateur

```sql
INSERT INTO users (email, password, first_name, last_name, role, status) 
VALUES ('admin@urbanova.cd', '$2y$10$your_hashed_password', 'Admin', 'User', 'admin', 'active');
```

## 📁 Structure du projet

```
urbanovacorp/
├── app/
│   ├── Controllers/     # Contrôleurs MVC
│   ├── Core/           # Classes core (Application, Router, etc.)
│   ├── Helpers/        # Fonctions utilitaires
│   ├── Middleware/     # Middleware d'authentification
│   ├── Models/         # Modèles de données
│   ├── Views/          # Templates Blade
│   └── routes/         # Définition des routes
├── config/             # Fichiers de configuration
├── database/           # Migrations et seeds
├── public/             # fichiers publics
│   ├── assets/         # CSS, JS, images
│   └── uploads/        # Fichiers uploadés
└── storage/            # Logs et cache
```

## 🔐 Sécurité

### Caractéristiques de sécurité implémentées:

- **Protection CSRF**: Tokens pour tous les formulaires
- **Hashage de mot de passe**: Utilisation de `password_hash()`
- **Validation des entrées**: Sanitization et validation
- **Rate Limiting**: Protection contre les attaques brute force
- **Validation de fichiers**: Vérification des types MIME
- **Sessions sécurisées**: Configuration HTTPS-only
- **SQL Injection**: Utilisation de PDO prepared statements

### Recommandations de production:

1. **SSL/TLS**: Activer HTTPS sur votre serveur
2. **Variables d'environnement**: Utiliser `.env` pour les secrets
3. **Firewall**: Configurer un pare-feu applicatif
4. **Backups**: Sauvegardes automatiques de la base de données
5. **Monitoring**: Logs et surveillance de sécurité

## 🌍 Support multilingue

La plateforme supporte le français et l'anglais. Les fichiers de traduction sont situés dans:

- `app/Helpers/lang/fr.php` - Français
- `app/Helpers/lang/en.php` - Anglais

Pour ajouter une nouvelle langue:

1. Créez un fichier `app/Helpers/lang/xx.php`
2. Ajoutez la langue dans `config/config.php`
3. Copiez les traductions et adaptez-les

## 📊 Base de données

### Tables principales:

- `users` - Utilisateurs du système
- `investors` - Données KYC/KYB des investisseurs
- `projects` - Projets immobiliers
- `project_documents` - Documents de la Data Room
- `investor_interests` - Intérêts des investisseurs
- `contacts` - Formulaires de contact
- `migrations` - Suivi des migrations

## 🧪 Tests

Pour tester l'application:

```bash
# Tests unitaires (à implémenter)
php vendor/bin/phpunit

# Tests de navigation
# Manuellement via le navigateur
```

## 🚀 Déploiement

### Environnement de production

1. **Variables d'environnement**:
```bash
export APP_ENV=production
export APP_DEBUG=false
export APP_URL=https://yourdomain.com
export DB_HOST=localhost
export DB_NAME=urbanova_db
export DB_USER=production_user
export DB_PASSWORD=secure_password
```

2. **Optimisation**:
```bash
composer dump-autoload --optimize
```

3. **Cache**: Activer le cache en production

4. **Monitoring**: Configurer les logs et alertes

### CI/CD

Exemple de workflow GitHub Actions:

```yaml
name: Deploy

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install --no-dev
      - name: Run migrations
        run: php database/migrate.php
      - name: Deploy to server
        run: |
          # Your deployment script
```

## 📝 Guide d'utilisation

### Pour les porteurs de projets:

1. Créer un compte "Porteur de projet"
2. Naviguer vers "Soumettre un projet"
3. Remplir le formulaire avec les détails du projet
4. Uploader les documents requis
5. Attendre la validation administrative
6. Une fois approuvé, le projet apparaît sur la marketplace

### Pour les investisseurs:

1. Créer un compte "Investisseur"
2. Compléter le formulaire KYC/KYB
3. Attendre l'approbation de l'administrateur
4. Parcourir la marketplace
5. Accéder à la Data Room des projets intéressants
6. Exprimer un intérêt et contacter les promoteurs

### Pour les administrateurs:

1. Cliquer sur l'onglet **Admin** du site et saisir le mot de passe administrateur (défini par `ADMIN_PASSWORD` dans `config/config.php` / `.env`, par défaut : `urbanova`)
2. Voir le tableau de bord et les statistiques
3. Valider ou rejeter les projets soumis
4. Approuver les investisseurs après vérification KYC
5. Gérer les contenus et les utilisateurs

## 🤝 Support

Pour toute question ou problème:

- Email: support@urbanova.cd
- Documentation: docs.urbanova.cd
- Issues: GitHub Issues

## 📄 Licence

Ce projet est propriétaire. Tous droits réservés © 2024 URBANOVA SOLUTIONS.

## 👥 Équipe

URBANOVA SOLUTIONS - Développement Urbain Durable en RD Congo

---

**Note**: Cette plateforme est conçue pour structurer le développement urbain durable en République Démocratique du Congo et en Afrique centrale.
