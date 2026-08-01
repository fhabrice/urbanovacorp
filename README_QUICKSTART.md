# Démarrage Rapide - URBANOVA SOLUTIONS

## Problème: Le site ne s'affiche pas

### Solution 1: Utiliser la version simplifiée

1. **Testez d'abord PHP**: Ouvrez `http://localhost/urbanovacorp/test.php`
   - Si vous voyez phpinfo(), PHP fonctionne
   - Sinon, vérifiez que Laragon fonctionne

2. **Testez la structure**: Ouvrez `http://localhost/urbanovacorp/simple.php`
   - Cela vous montrera les dossiers et fichiers manquants

3. **Utilisez la version simplifiée**: Ouvrez `http://localhost/urbanovacorp/start.php`
   - C'est une version qui fonctionne sans composer

### Solution 2: Installer les dépendances

Ouvrez un terminal dans le dossier du projet:

```bash
cd C:\laragon\www\urbanovacorp
composer install
```

Puis accédez à: `http://localhost/urbanovacorp/`

### Solution 3: Configuration de Laragon

1. **Vérifiez que Laragon est démarré**
2. **Cliquez sur "Menu" > "www"** 
3. **Cliquez sur "urbanovacorp"**
4. **Le site devrait s'ouvrir dans le navigateur**

### Solution 4: Vérifier la base de données

1. **Ouvrez phpMyAdmin** (via Laragon)
2. **Créez la base de données**:
   ```sql
   CREATE DATABASE urbanova_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. **Configurez le fichier** `config/config.php` avec vos identifiants MySQL

### Solution 5: Permissions (Windows)

Assurez-vous que Laragon a les droits d'écriture:
- Cliquez droit sur le dossier `urbanovacorp`
- Propriétés > Sécurité
- Assurez-vous que "Tout le monde" a les droits de lecture/écriture

## Fichiers de test disponibles

- `test.php` - Test PHP basique
- `simple.php` - Vérification de la structure
- `start.php` - Version simplifiée du site

## Prochaines étapes

1. Testez `http://localhost/urbanovacorp/simple.php`
2. Corrigez les erreurs affichées
3. Testez `http://localhost/urbanovacorp/start.php`
4. Une fois que cela fonctionne, installez composer

## Support

Si vous avez toujours des problèmes:
1. Vérifiez les logs de Laragon
2. Vérifiez que PHP 8.1+ est installé
3. Vérifiez que MySQL fonctionne
