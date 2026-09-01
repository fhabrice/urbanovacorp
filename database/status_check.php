<?php
/**
 * URBANOVA - Vérification de la connexion et de l'état du schéma
 *
 * Usage (sur le serveur hébergeant la base) :
 *   php database/status_check.php
 *
 * Affiche : connexion, tables présentes, colonnes critiques manquantes,
 * nombre de migrations appliquées.
 */

// Chemins
if (!defined('BASE_PATH'))  define('BASE_PATH', realpath(__DIR__ . '/..'));
if (!defined('APP_PATH'))   define('APP_PATH', BASE_PATH . '/app');
if (!defined('PUBLIC_PATH'))define('PUBLIC_PATH', BASE_PATH . '/public');
if (!defined('CONFIG_PATH'))define('CONFIG_PATH', BASE_PATH . '/config');

$config = require CONFIG_PATH . '/config.php';
$dbConf = $config['database'];

echo "==========================================================\n";
echo " URBANOVA - État de la base de données\n";
echo "==========================================================\n";
echo "Hôte     : " . ($dbConf['host'] ?? '?') . "\n";
echo "Base     : " . ($dbConf['database'] ?? '?') . "\n";
echo "Utilisat : " . ($dbConf['username'] ?? '?') . "\n\n";

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $dbConf['host'] ?? 'localhost',
        $dbConf['port'] ?? '3306',
        $dbConf['database'] ?? '',
        $dbConf['charset'] ?? 'utf8mb4'
    );
    $pdo = new PDO($dsn, $dbConf['username'] ?? '', $dbConf['password'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "[OK] Connexion à la base de données réussie.\n\n";
} catch (Exception $e) {
    echo "[ERREUR] Connexion impossible : " . $e->getMessage() . "\n";
    echo "Vérifiez que la base est hébergée sur CE serveur (localhost) ou\n";
    echo "ajoutez DB_HOST dans config/config.php si elle est distante.\n";
    exit(1);
}

// Tables attendues
$expectedTables = [
    'users', 'investors', 'projects', 'project_documents', 'investor_interests',
    'investor_favorites', 'investor_messages', 'project_visits', 'project_reservations',
    'funding_campaigns', 'investment_offers', 'data_room_permissions', 'data_room_audit_log',
    'notifications', 'permissions', 'role_permissions', 'funding_requests',
    'data_room_requests', 'project_inquiries', 'contacts', 'investments',
    'news', 'site_content',
];

echo "--- Tables ---\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$missing = array_diff($expectedTables, $tables);
foreach ($expectedTables as $t) {
    printf("  %-28s %s\n", $t, in_array($t, $tables, true) ? 'OK' : 'MANQUANTE  <-- lancer database/upgrade_schema.sql');
}

// Colonnes critiques
echo "\n--- Colonnes critiques (projets) ---\n";
$critical = [
    'projects' => ['validation_status', 'funding_raised', 'expected_roi', 'project_type', 'operation_type', 'coordinates_lat', 'coordinates_lng', 'user_id', 'slug', 'promoter'],
    'users' => ['first_name', 'last_name', 'name', 'role'],
    'investors' => ['investor_status', 'invester_type_marker'],
];
$check = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
$hasMissing = false;
foreach ([
    'projects' => ['validation_status', 'project_type', 'operation_type', 'coordinates_lat', 'coordinates_lng', 'user_id', 'video_url', 'price', 'availability', 'brochure_path', 'is_featured'],
    'users' => ['first_name', 'last_name', 'name'],
    'investors' => ['investor_status', 'country', 'city'],
    'investor_messages' => ['project_id'],
    'data_room_permissions' => ['status'],
] as $table => $cols) {
    foreach ($cols as $col) {
        $check->execute([$table, $col]);
        $ok = (int)$check->fetchColumn() > 0;
        if (!$ok) $hasMissing = true;
        printf("  %s.%s %s\n", $table, $col, $ok ? 'OK' : 'MANQUANTE');
    }
}

echo "\n--- Migrations PHP appliquées ---\n";
try {
    $mig = $pdo->query("SELECT COUNT(*) FROM migrations")->fetchColumn();
    echo "  Migrations enregistrées : " . $mig . "\n";
    echo "  Recommandation : exécuter   php database/migrate.php   puis   php database/apply_upgrade.php\n";
} catch (Exception $e) {
    echo "  Table 'migrations' absente (base héritée de investment_schema.sql).\n";
    echo "  → Utiliser le fichier database/upgrade_schema.sql via phpMyAdmin ou\n";
    echo "    exécuter   php database/apply_upgrade.php\n";
}

echo "\n" . ($hasMissing || !empty($missing) ? "[ACTION REQUISE] Exécutez la mise à jour du schéma (voir ci-dessus).\n" : "[OK] Schéma complet, aucun élément manquant.\n");
echo "==========================================================\n";
