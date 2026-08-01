# Guide de Configuration Base de Données

## Étape 1: Configurer la base de données

Le fichier `config/config.php` est déjà configuré pour Laragon avec les valeurs par défaut:
- Host: localhost
- User: root
- Password: (vide)
- Database: urbanova_db

## Étape 2: Créer la base de données

Exécutez le script de setup dans votre navigateur:
```
http://localhost/urbanovacorp/setup_database.php
```

Ce script va:
1. Créer la base de données `urbanova_db`
2. Créer toutes les tables nécessaires
3. Insérer des données de test (projets, utilisateurs, investissements)

## Étape 3: Tester l'intégration

Une fois la base de données configurée, allez sur:
```
http://localhost/urbanovacorp/
```

Vous devriez voir:
- ✅ La marketplace avec les projets de la base de données
- ✅ Possibilité de soumettre un nouveau projet (sera inséré dans la base)
- ✅ Formulaire de contact fonctionnel
- ✅ L'espace investisseur (vérifiera l'authentification)

## Étape 4: Vérifier les données

Vous pouvez vérifier les données dans phpMyAdmin:
1. Ouvrez phpMyAdmin via Laragon
2. Sélectionnez la base `urbanova_db`
3. Parcourez les tables:
   - `projects` - Les projets de la marketplace
   - `users` - Les utilisateurs (investisseurs, admins)
   - `investments` - Les investissements
   - `contacts` - Les messages de contact

## Résolution de problèmes

### Erreur: "Database connection failed"
- Vérifiez que Laragon est démarré
- Vérifiez que MySQL fonctionne
- Vérifiez les identifiants dans `config/config.php`

### Erreur: "Table doesn't exist"
- Exécutez `setup_database.php` à nouveau
- Vérifiez que les tables ont été créées dans phpMyAdmin

### Pas de données dans la marketplace
- Vérifiez que les données de test ont été insérées
- Vérifiez la table `projects` dans phpMyAdmin
- Les projets doivent avoir le statut 'approved' ou 'funding' pour apparaître

## Données de test disponibles

### Utilisateurs de test
- Email: `jean.dupont@example.com` | Mot de passe: `password`
- Email: `marie.kouassi@example.com` | Mot de passe: `password`
- Email: `admin@urbanova.cd` | Mot de passe: `password`

### Projets de test
- Résidence Horizon (Goma, RDC)
- Urban Business Park (Kinshasa, RDC)
- Eco City Villas (Kigali, Rwanda)
- Commercial Hub Goma (Goma, RDC)
- Résidence Kivu Green (Goma, RDC)
