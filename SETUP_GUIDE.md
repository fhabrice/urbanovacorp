# Guide de Configuration Base de Données - URBANOVA SOLUTIONS

## 🚀 Plateforme Prête pour la Production

La plateforme est configurée pour être utilisée avec des données réelles. Aucune donnée de démonstration n'est chargée par défaut.

## Étape 1: Configurer la base de données

Le fichier `config/config.php` est déjà configuré pour Laragon avec les valeurs par défaut:
- Host: localhost
- User: root
- Password: (vide)
- Database: urbanova_db

**Pour la production**, modifiez ces valeurs avec vos identifiants de base de données réels.

## Étape 2: Créer la base de données (VIDE)

Exécutez le script de setup dans votre navigateur:
```
http://localhost/urbanovacorp/setup_database.php
```

Ce script va:
1. ✅ Créer la base de données `urbanova_db`
2. ✅ Créer toutes les tables nécessaires
3. ✅ Laisser la base de données vide (prête pour les vraies données)

## Étape 3: Tester la plateforme vide

Une fois la base de données configurée, allez sur:
```
http://localhost/urbanovacorp/
```

Vous devriez voir:
- ✅ Marketplace vide (avec message "Aucun projet ne correspond")
- ✅ Possibilité de soumettre un nouveau projet
- ✅ Formulaire de contact fonctionnel
- ✅ Interface prête pour les vraies données

## 📊 Optionnel: Charger des données de démonstration

Pour le développement uniquement, vous pouvez charger des données de test:

Exécutez:
```
http://localhost/urbanovacorp/load_demo_data.php
```

Cela ajoutera:
- 5 projets de démonstration
- 3 utilisateurs de test
- 5 investissements
- 2 messages de contact

**⚠️ Important:** N'utilisez PAS `load_demo_data.php` en production!

## 🔍 Vérifier les données dans phpMyAdmin

1. Ouvrez phpMyAdmin via Laragon
2. Sélectionnez la base `urbanova_db`
3. Parcourez les tables:
   - `projects` - Les projets de la marketplace
   - `users` - Les utilisateurs (investisseurs, admins)
   - `investments` - Les investissements
   - `contacts` - Les messages de contact

## 🛠️ Résolution de problèmes

### Erreur: "Database connection failed"
- Vérifiez que Laragon est démarré
- Vérifiez que MySQL fonctionne
- Vérifiez les identifiants dans `config/config.php`

### Erreur: "Table doesn't exist"
- Exécutez `setup_database.php` à nouveau
- Vérifiez que les tables ont été créées dans phpMyAdmin

### Marketplace vide
- C'est normal ! La plateforme est vide par défaut
- Ajoutez des projets via le formulaire de soumission
- Ou chargez les données de démo avec `load_demo_data.php`

## 👤 Données de test (uniquement après load_demo_data.php)

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

## 📝 Ajouter des vraies données

Pour utiliser la plateforme avec des données réelles:

1. **Créer un compte admin** via phpMyAdmin dans la table `users`
2. **Soumettre des projets** via le formulaire "Levée de fonds"
3. **Les projets apparaîtront** dans la marketplace après approbation (statut 'approved' ou 'funding')
4. **Les contacts** seront enregistrés dans la table `contacts`

## 🌐 Déploiement en production

Pour déployer en production:

1. Modifiez `config/config.php` avec vos identifiants de base de données
2. Exécutez `setup_database.php` sur le serveur de production
3. Configurez les variables d'environnement (si nécessaire)
4. La plateforme est prête à recevoir les vraies données !
