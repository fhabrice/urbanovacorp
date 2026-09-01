<?php
/**
 * URBANOVA - Application de la mise à jour du schéma (Modules 1-2-3)
 *
 * Usage (sur le serveur hébergeant la base) :
 *   php database/apply_upgrade.php
 *
 * Équivalent de l'import de database/upgrade_schema.sql via phpMyAdmin.
 * Le script est idempotent : exécutable plusieurs fois sans erreur.
 */

if (!defined('BASE_PATH'))  define('BASE_PATH', realpath(__DIR__ . '/..'));
if (!defined('APP_PATH'))   define('APP_PATH', BASE_PATH . '/app');
if (!defined('PUBLIC_PATH'))define('PUBLIC_PATH', BASE_PATH . '/public');
if (!defined('CONFIG_PATH'))define('CONFIG_PATH', BASE_PATH . '/config');

$config = require CONFIG_PATH . '/config.php';
$dbConf = $config['database'];

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
} catch (Exception $e) {
    echo "Connexion impossible : " . $e->getMessage() . "\n";
    exit(1);
}

$file = __DIR__ . '/upgrade_schema.sql';
if (!file_exists($file)) {
    echo "Fichier introuvable : $file\n";
    exit(1);
}

$sql = file_get_contents($file);

// Découpe en instructions (en respectant les chaînes entre guillemets)
$statements = [];
$buffer = '';
$inSingle = false;
$inDouble = false;
$len = strlen($sql);
for ($i = 0; $i < $len; $i++) {
    $ch = $sql[$i];
    if ($ch === "'" && !$inDouble) {
        if ($inSingle && isset($sql[$i + 1]) && $sql[$i + 1] === "'") {
            $buffer .= "''"; $i++; continue;
        }
        $inSingle = !$inSingle;
    } elseif ($ch === '"' && !$inSingle) {
        $inDouble = !$inDouble;
    } elseif ($ch === ';' && !$inSingle && !$inDouble) {
        $stmt = trim($buffer);
        if ($stmt !== '' && strpos($stmt, '--') !== 0) {
            $statements[] = $stmt;
        }
        $buffer = '';
        continue;
    }
    $buffer .= $ch;
}
if (trim($buffer) !== '') $statements[] = trim($buffer);

echo "Instructions SQL à exécuter : " . count($statements) . "\n";

$success = 0;
$skipped = 0;
foreach ($statements as $stmt) {
    // Ignorer les lignes de commentaires pures
    $clean = preg_replace('/^\s*--.*$/m', '', $stmt);
    if (trim($clean) === '') continue;

    try {
        $pdo->exec($stmt);
        $success++;
        echo "  [OK] " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 90) . "\n";
    } catch (Exception $e) {
        // Certains SGBD n'acceptent pas les variables utilisateur préparées
        // ou renvoient un avertissement bénin ; on continue.
        $skipped++;
        echo "  [--] " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 90) . "  -> " . $e->getMessage() . "\n";
    }
    // Les instructions SET @... sont utiles mais facultatives après la première passe
    if (strpos($stmt, 'SET @') === 0) {
        // pas de reset : les variables restent définies dans la session
    }
}

echo "\nTerminé : $success instruction(s) exécutée(s), $skipped ignorée(s).\n";
echo "Vérifiez l'état avec :  php database/status_check.php\n";
