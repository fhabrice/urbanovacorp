# Guide d'Installation - URBANOVA SOLUTIONS

Ce guide vous accompagne pas à pas dans l'installation de la plateforme URBANOVA SOLUTIONS.

## Étape 1: Prérequis Système

### Configuration minimale requise:
- **PHP**: 8.1 ou supérieur
- **MySQL/MariaDB**: 5.7 ou supérieur
- **Serveur Web**: Apache 2.4+ ou Nginx 1.18+
- **RAM**: 2GB minimum (4GB recommandé)
- **Espace disque**: 10GB minimum

### Extensions PHP requises:
```bash
php-mbstring
php-json
php-pdo
php-pdo_mysql
php-openssl
php-curl
php-gd
php-xml
php-zip
```

## Étape 2: Téléchargement

### Option A: Cloner depuis Git
```bash
git clone https://github.com/yourusername/urbanovacorp.git
cd urbanovacorp
```

### Option B: Télécharger l'archive
1. Téléchargez le fichier ZIP depuis GitHub
2. Extrayez l'archive dans votre répertoire web
3. Renommez le dossier en `urbanovacorp`

## Étape 3: Installation des dépendances

```bash
# Installer Composer si nécessaire
curl -sS https://getcomposer.org/installer | php
php composer.phar install

# Ou si Composer est déjà installé
composer install
```

## Étape 4: Configuration de la base de données

### 4.1 Créer la base de données

Connectez-vous à MySQL:
```bash
mysql -u root -p
```

Exécutez les commandes SQL:
```sql
CREATE DATABASE urbanova_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'urbanova_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON urbanova_db.* TO 'urbanova_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4.2 Configurer la connexion

Éditez le fichier `config/config.php`:

```php
'database' => [
    'host' => 'localhost',
    'port' => '3306',
    'name' => 'urbanova_db',
    'user' => 'urbanova_user',
    'password' => 'secure_password',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
],
```

## Étape 5: Exécuter les migrations

```bash
# Exécuter toutes les migrations
php database/migrate.php

# Vérifier que les tables sont créées
mysql -u urbanova_user -p urbanova_db -e "SHOW TABLES;"
```

## Étape 6: Configuration du serveur web

### Apache

1. Activer le module rewrite:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

2. Créer un VirtualHost:
```apache
<VirtualHost *:80>
    ServerName urbanova.local
    DocumentRoot /var/www/urbanovacorp/public
    
    <Directory /var/www/urbanovacorp/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/urbanova_error.log
    CustomLog ${APACHE_LOG_DIR}/urbanova_access.log combined
</VirtualHost>
```

3. Activer le site:
```bash
sudo a2ensite urbanova.conf
sudo systemctl reload apache2
```

### Nginx

Créez un fichier de configuration:
```nginx
server {
    listen 80;
    server_name urbanova.local;
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

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Étape 7: Permissions

```bash
# Définir les permissions correctes
sudo chown -R www-data:www-data /var/www/urbanovacorp
sudo chmod -R 755 /var/www/urbanovacorp
sudo chmod -R 777 /var/www/urbanovacorp/public/uploads
sudo chmod -R 777 /var/www/urbanovacorp/storage
```

## Étape 8: Créer le compte administrateur

### Option A: Via MySQL
```sql
INSERT INTO users (email, password, first_name, last_name, role, status) 
VALUES (
    'admin@urbanova.cd', 
    '$2y$10$YourHashedPasswordHere', 
    'Admin', 
    'User', 
    'admin', 
    'active'
);
```

### Option B: Via interface d'inscription
1. Accédez à `http://urbanova.local/register`
2. Créez un compte avec n'importe quel email
3. Manuellement dans MySQL, changez le rôle en 'admin':
```sql
UPDATE users SET role = 'admin', status = 'active' WHERE email = 'your_email@example.com';
```

## Étape 9: Configuration SSL (Recommandé)

### Let's Encrypt ( gratuit)
```bash
sudo certbot --apache -d urbanova.local
```

### Certificat auto-signé (développement)
```bash
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/urbanova.key \
    -out /etc/ssl/certs/urbanova.crt
```

## Étape 10: Tests

1. **Test de connexion**: Accédez à `http://urbanova.local`
2. **Test d'inscription**: Créez un compte utilisateur
3. **Test de connexion**: Connectez-vous avec le compte créé
4. **Test admin**: Connectez-vous avec le compte admin
5. **Test marketplace**: Vérifiez l'affichage des projets

## Étape 11: Configuration de production

### Variables d'environnement
Créez un fichier `.env`:
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://urbanova.cd
DB_HOST=localhost
DB_NAME=urbanova_db
DB_USER=urbanova_user
DB_PASSWORD=secure_password
```

### Optimisation
```bash
# Optimiser l'autoloader
composer dump-autoload --optimize --no-dev

# Activer OPcache
# Dans php.ini: opcache.enable=1
```

### Backup automatique
Ajoutez au crontab:
```bash
# Backup quotidien de la base de données
0 2 * * * /usr/bin/mysqldump -u urbanova_user -psecure_password urbanova_db > /backups/urbanova_$(date +\%Y\%m\%d).sql

# Backup des fichiers uploadés
0 3 * * * tar -czf /backups/uploads_$(date +\%Y\%m\%d).tar.gz /var/www/urbanovacorp/public/uploads
```

## Dépannage

### Erreur: "Database connection failed"
- Vérifiez les identifiants dans `config/config.php`
- Vérifiez que MySQL fonctionne
- Vérifiez que la base de données existe

### Erreur: "Permission denied"
- Vérifiez les permissions des dossiers
- Assurez-vous que www-data a les droits d'écriture

### Erreur: "404 Not Found"
- Vérifiez la configuration du VirtualHost
- Vérifiez que le module rewrite est activé (Apache)
- Vérifiez la configuration Nginx

### Erreur: "CSRF error"
- Vérifiez que les sessions fonctionnent
- Vérifiez les permissions du dossier storage

## Support

Pour toute question lors de l'installation:
- Email: support@urbanova.cd
- Documentation: docs.urbanova.cd
- Issues: GitHub Issues

## Notes importantes

1. **Sécurité**: Changez tous les mots de passe par défaut
2. **SSL**: Utilisez toujours HTTPS en production
3. **Backups**: Configurez des sauvegardes automatiques
4. **Mises à jour**: Gardez PHP et les dépendances à jour
5. **Monitoring**: Surveillez les logs et les performances

---

Bon succès avec votre installation URBANOVA SOLUTIONS ! 🚀
