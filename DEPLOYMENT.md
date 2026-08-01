# Guide de Déploiement - URBANOVA SOLUTIONS

Ce guide couvre le déploiement de la plateforme URBANOVA SOLUTIONS en environnement de production.

## Pré-déploiement

### Checklist de sécurité

- [ ] Changer tous les mots de passe par défaut
- [ ] Configurer SSL/TLS
- [ ] Configurer le firewall
- [ ] Activer les backups automatiques
- [ ] Configurer le monitoring
- [ ] Mettre à jour tous les packages
- [ ] Désactiver le mode debug
- [ ] Configurer les variables d'environnement

### Tests de pré-production

1. **Tests fonctionnels**
   - Inscription utilisateur
   - Connexion admin
   - Soumission de projet
   - Validation KYC
   - Marketplace
   - Filtres

2. **Tests de performance**
   - Temps de chargement < 3s
   - Base de données optimisée
   - Cache activé

3. **Tests de sécurité**
   - Scan de vulnérabilités
   - Test de pénétration
   - Validation SSL

## Configuration de production

### 1. Variables d'environnement

Créez le fichier `.env`:

```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://urbanova.cd

# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=urbanova_prod
DB_USER=urbanova_prod
DB_PASSWORD=VERY_SECURE_PASSWORD

# Security
SESSION_NAME=urbanova_session
SESSION_LIFETIME=7200

# Email
EMAIL_FROM=contact@urbanova.cd
EMAIL_FROM_NAME=URBANOVA SOLUTIONS
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=noreply@urbanova.cd
SMTP_PASSWORD=SMTP_PASSWORD
```

### 2. Configuration PHP

```ini
# /etc/php/8.1/fpm/php.ini

memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M
max_input_vars = 3000

# Performance
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 60

# Security
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
```

### 3. Configuration MySQL

```sql
-- Optimisation de la base de données
SET GLOBAL innodb_buffer_pool_size = 1G;
SET GLOBAL innodb_log_file_size = 256M;
SET GLOBAL innodb_flush_log_at_trx_commit = 2;
SET GLOBAL query_cache_size = 64M;
```

## Déploiement

### Méthode 1: Déploiement manuel

```bash
# 1. Backup de la production actuelle
mysqldump -u user -p database > backup_$(date +%Y%m%d).sql
tar -czf backup_files_$(date +%Y%m%d).tar.gz /var/www/urbanovacorp

# 2. Transférer les fichiers
rsync -avz --exclude 'node_modules' --exclude '.git' \
    /local/path/urbanovacorp/ user@server:/var/www/

# 3. Installer les dépendances
ssh user@server
cd /var/www/urbanovacorp
composer install --no-dev --optimize-autoloader

# 4. Exécuter les migrations
php database/migrate.php

# 5. Configurer les permissions
chown -R www-data:www-data /var/www/urbanovacorp
chmod -R 755 /var/www/urbanovacorp
chmod -R 777 /var/www/urbanovacorp/public/uploads
chmod -R 777 /var/www/urbanovacorp/storage

# 6. Redémarrer les services
systemctl restart php8.1-fpm
systemctl restart nginx
```

### Méthode 2: CI/CD avec GitHub Actions

Créez `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v2
      
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Run migrations
      run: php database/migrate.php
      
    - name: Deploy to server
      uses: easingthemes/ssh-deploy@v2
      env:
        SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
        REMOTE_HOST: ${{ secrets.REMOTE_HOST }}
        REMOTE_USER: ${{ secrets.REMOTE_USER }}
        TARGET: /var/www/urbanovacorp
        
    - name: Restart services
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.REMOTE_HOST }}
        username: ${{ secrets.REMOTE_USER }}
        key: ${{ secrets.SSH_PRIVATE_KEY }}
        script: |
          sudo systemctl restart php8.1-fpm
          sudo systemctl restart nginx
```

## Configuration SSL

### Let's Encrypt (recommandé)

```bash
# Installer Certbot
sudo apt install certbot python3-certbot-nginx

# Obtenir le certificat
sudo certbot --nginx -d urbanova.cd

# Renouvellement automatique
sudo certbot renew --dry-run
```

### Certificat commercial

1. Acheter un certificat SSL
2. Télécharger les fichiers (.crt, .key)
3. Configurer Nginx:

```nginx
server {
    listen 443 ssl http2;
    server_name urbanova.cd;

    ssl_certificate /etc/ssl/certs/urbanova.crt;
    ssl_certificate_key /etc/ssl/private/urbanova.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Reste de la configuration...
}

server {
    listen 80;
    server_name urbanova.cd;
    return 301 https://$server_name$request_uri;
}
```

## Backup et restauration

### Script de backup automatique

```bash
#!/bin/bash
# /usr/local/bin/urbanova_backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/urbanova"
RETENTION_DAYS=30

# Créer le répertoire de backup
mkdir -p $BACKUP_DIR

# Backup de la base de données
mysqldump -u urbanova_prod -p$DB_PASSWORD urbanova_prod | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup des fichiers
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/urbanovacorp/public/uploads

# Nettoyer les vieux backups
find $BACKUP_DIR -name "*.gz" -mtime +$RETENTION_DAYS -delete

# Upload vers le cloud (optionnel)
# aws s3 cp $BACKUP_DIR/db_$DATE.sql.gz s3://urbanova-backups/
```

### Crontab pour backup automatique

```bash
# Ajouter au crontab
0 2 * * * /usr/local/bin/urbanova_backup.sh
```

### Restauration

```bash
# Restaurer la base de données
gunzip < /backups/urbanova/db_20240101_020000.sql.gz | mysql -u urbanova_prod -p urbanova_prod

# Restaurer les fichiers
tar -xzf /backups/urbanova/files_20240101_020000.tar.gz -C /
```

## Monitoring

### 1. Logs

Configurez la rotation des logs:

```bash
# /etc/logrotate.d/urbanova
/var/log/urbanova/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 2. Monitoring avec Uptime Robot

- Configurez des monitors pour:
  - HTTP: https://urbanova.cd
  - Keywords: "URBANOVA SOLUTIONS"
  - Interval: 5 minutes

### 3. Monitoring des performances

Utilisez New Relic ou Blackfire:

```bash
# Installer New Relic
sudo apt install newrelic-php5-common
newrelic-install install
```

## Sécurité avancée

### 1. Firewall

```bash
# UFW configuration
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 2. Fail2Ban

```bash
# Installer Fail2Ban
sudo apt install fail2ban

# Configuration pour WordPress/PHP
[php-url-fopen]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 3
```

### 3. Hardening PHP

```ini
# Désactiver les fonctions dangereuses
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

# Activer open_basedir
open_basedir = /var/www/urbanovacorp:/tmp
```

## Mise à jour

### Procédure de mise à jour

```bash
# 1. Backup
/usr/local/bin/urbanova_backup.sh

# 2. Mettre à jour le code
git pull origin main

# 3. Mettre à jour les dépendances
composer update --no-dev

# 4. Exécuter les migrations
php database/migrate.php

# 5. Vider le cache
rm -rf storage/cache/*

# 6. Redémarrer les services
systemctl restart php8.1-fpm
systemctl restart nginx
```

## Support technique

### En cas de problème

1. **Vérifier les logs**: `/var/log/nginx/error.log`, `/var/log/php/error.log`
2. **Vérifier les services**: `systemctl status nginx php8.1-fpm mysql`
3. **Vérifier l'espace disque**: `df -h`
4. **Vérifier la mémoire**: `free -m`

### Contact support

- Email: support@urbanova.cd
- Téléphone: +243 XXX XXX XXX
- Documentation: docs.urbanova.cd

---

**Note**: Ce guide doit être adapté selon votre infrastructure spécifique.
