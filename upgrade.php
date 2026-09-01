<?php
/**
 * URBANOVA - Installeur web de mise à jour du schéma (Modules 1-2-3)
 *
 * Usage sur hébergeur mutualisé (sans SSH) :
 *   1. Téléversez ce fichier à la racine du site (à côté de index.php).
 *   2. Ouvrez :   https://votre-domaine/upgrade.php
 *   3. Saisissez le mot de passe administrateur (défaut : urbanova).
 *   4. La mise à jour s'applique, puis le fichier se supprime automatiquement.
 *
 * Le script est IDEMPOTENT : ré-exécutable sans risque.
 */

// ---------------------------------------------------------------
// Chemins
// ---------------------------------------------------------------
if (!defined('BASE_PATH'))  define('BASE_PATH', __DIR__);
if (!defined('APP_PATH'))   define('APP_PATH', BASE_PATH . '/app');
if (!defined('PUBLIC_PATH'))define('PUBLIC_PATH', BASE_PATH . '/public');
if (!defined('CONFIG_PATH'))define('CONFIG_PATH', BASE_PATH . '/config');

// ---------------------------------------------------------------
// Config + mot de passe admin
// ---------------------------------------------------------------
$adminPassword = 'urbanova';
try {
    $cfg = file_exists(CONFIG_PATH . '/config.php') ? require CONFIG_PATH . '/config.php' : [];
    if (!empty($cfg['security']['admin_password'])) {
        $adminPassword = (string) $cfg['security']['admin_password'];
    }
} catch (\Throwable $e) {
    $adminPassword = 'urbanova';
}

$sqlFile = __DIR__ . '/database/upgrade_schema.sql';

// ---------------------------------------------------------------
// Session + authentification
// ---------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_name('urbanova_session');
    session_start();
}

$authorized = !empty($_SESSION['upgrade_authorized']);
$error = '';

if (!empty($_POST['password'])) {
    if (hash_equals($adminPassword, (string) $_POST['password'])) {
        $_SESSION['upgrade_authorized'] = true;
        $authorized = true;
    } else {
        $error = 'Mot de passe incorrect.';
    }
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ---------------------------------------------------------------
// Affichage du formulaire
// ---------------------------------------------------------------
if (!$authorized) {
    ?><!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Urbanova - Mise à jour base de données</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f1f5f9;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
.card{background:#fff;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.12);padding:40px;max-width:420px;width:92%}
h1{color:#1C2541;font-size:22px;margin:0 0 8px}
p{color:#64748b;font-size:14px;line-height:1.6;margin:0 0 20px}
input[type=password]{width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:10px;margin-bottom:14px;box-sizing:border-box}
button{width:100%;padding:13px;background:#1C2541;color:#fff;border:0;border-radius:10px;font-weight:bold;cursor:pointer}
button:hover{background:#0B132B}
.err{color:#dc2626;font-size:13px;margin-bottom:12px}
</style></head><body>
<div class="card"><h1>🛠️ Urbanova — Mise à jour</h1>
<p>Cette page applique la mise à jour du schéma de la base de données (Modules Marketplace, Levée de fonds, Espace Investisseur).</p>
<?php if ($error): ?><div class="err"><?php echo e($error); ?></div><?php endif; ?>
<form method="post"><input type="password" name="password" placeholder="Mot de passe administrateur" required autofocus>
<button type="submit">Lancer la mise à jour</button></form>
</div></body></html><?php
    exit;
}

// ---------------------------------------------------------------
// Authentifié : exécution
// ---------------------------------------------------------------
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Urbanova - Résultat de la mise à jour</title>
<style>
body{font-family:Consolas,Menlo,monospace;background:#0f172a;color:#e2e8f0;padding:24px;margin:0;font-size:13px}
h1{color:#D4AF37;font-size:20px}
.ok{color:#10B981}.warn{color:#f59e0b}.bad{color:#f87171}
.box{background:#1e293b;border-radius:12px;padding:20px;max-width:900px;margin:0 auto}
a{color:#D4AF37}
</style></head><body>
<div class="box"><h1>🛠️ Urbanova — Résultat de la mise à jour</h1>
<?php

if (!file_exists($sqlFile)) {
    echo '<p class="bad">Fichier introuvable : database/upgrade_schema.sql<br>';
    echo 'Vérifiez que l\'archive du projet a bien été extraite à la racine du site.</p></div></body></html>';
    exit;
}

// Connexion à la base
try {
    $dbConf = require CONFIG_PATH . '/config.php';
    $dbConf = $dbConf['database'] ?? $dbConf;
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $dbConf['host'] ?? 'localhost',
        $dbConf['port'] ?? '3306',
        $dbConf['database'] ?? $dbConf['name'] ?? '',
        $dbConf['charset'] ?? 'utf8mb4');
    $pdo = new PDO($dsn, $dbConf['username'] ?? '', $dbConf['password'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo '<p><span class="ok">[OK]</span> Connexion à la base « ' . e($dbConf['database'] ?? '') . ' » réussie.</p>';
} catch (Exception $e) {
    echo '<p class="bad">[ERREUR] Connexion impossible : ' . e($e->getMessage()) . '</p>';
    echo '<p>Vérifiez les identifiants dans config/config.php (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD).</p></div></body></html>';
    exit;
}

// Lecture du fichier SQL
$sql = file_get_contents($sqlFile);

// Découpe en instructions (respect des chaînes entre guillemets)
$statements = [];
$buffer = '';
$inSingle = false;
$inDouble = false;
$len = strlen($sql);
for ($i = 0; $i < $len; $i++) {
    $ch = $sql[$i];
    if ($ch === "'" && !$inDouble) {
        if ($inSingle && isset($sql[$i + 1]) && $sql[$i + 1] === "'") { $buffer .= "''"; $i++; continue; }
        $inSingle = !$inSingle;
    } elseif ($ch === '"' && !$inSingle) {
        $inDouble = !$inDouble;
    } elseif ($ch === ';' && !$inSingle && !$inDouble) {
        $stmt = trim($buffer);
        if ($stmt !== '') $statements[] = $stmt;
        $buffer = '';
        continue;
    }
    $buffer .= $ch;
}
if (trim($buffer) !== '') $statements[] = trim($buffer);

echo '<p>Instructions : ' . count($statements) . ' — exécution...</p><pre>';

$okCount = 0;
$skipCount = 0;
$failed = 0;
foreach ($statements as $stmt) {
    $clean = preg_replace('/^\s*--.*$/m', '', $stmt);
    if (trim($clean) === '') continue;
    try {
        $pdo->exec($stmt);
        $okCount++;
        echo '<span class="ok">[OK]</span> ' . e(mb_substr(preg_replace('/\s+/', ' ', $stmt), 0, 120)) . "\n";
    } catch (Exception $ex) {
        // Variables utilisateur non supportées ou avertissement bénin -> on continue
        $skipCount++;
        echo '<span class="warn">[--]</span> ' . e(mb_substr(preg_replace('/\s+/', ' ', $stmt), 0, 120)) . ' (' . e($ex->getMessage()) . ")\n";
    }
}

echo "</pre>";

// ---------------------------------------------------------------
// Vérification finale : objets attendus par l'application
// ---------------------------------------------------------------
$checks = [
    ['Table news (actualités)', "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'news'"],
    ['Table site_content (contenus)', "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'site_content'"],
    ['Colonne projects.is_featured', "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'is_featured'"],
    ['Colonne projects.validation_status', "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'validation_status'"],
    ['Colonne projects.project_type', "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'project_type'"],
    ['Colonne projects.slug', "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projects' AND COLUMN_NAME = 'slug'"],
];
echo '<p>--- Vérification ---</p>';
$missingChecks = 0;
foreach ($checks as $checkRow) {
    list($label, $sql) = $checkRow;
    try {
        $found = (int)$pdo->query($sql)->fetchColumn() > 0;
    } catch (Exception $ex) {
        $found = false;
    }
    if ($found) {
        echo '<p><span class="ok">[OK]</span> ' . e($label) . '</p>';
    } else {
        $missingChecks++;
        echo '<p><span class="bad">[MANQUANT]</span> ' . e($label) . '</p>';
    }
}

if ($okCount > 0 || $skipCount > 0) {
    echo '<p><span class="ok">✔ Mise à jour terminée :</span> ' . $okCount . ' instruction(s) exécutée(s), ' . $skipCount . ' ignorée(s) (colonnes/tables déjà présentes).</p>';
    if ($missingChecks > 0) {
        echo '<p class="bad">⚠ ' . $missingChecks . ' élément(s) encore manquant(s). Réessayez, ou importez database/upgrade_schema.sql via phpMyAdmin dans la base « ' . e($dbConf['database'] ?? '') . ' ».</p>';
    } else {
        echo '<p class="ok">✔ La base est à jour : Marketplace, Levée de fonds, Data Room, projets phares, actualités et contenus du site sont prêts.</p>';
    }
    echo '<p><a href="/">→ Retour au site</a></p>';

    // Auto-suppression (une fois réussi : la base est à jour et le script est ré-exécutable ailleurs)
    try {
        @unlink(__FILE__);
        echo '<p class="warn">ℹ Le fichier upgrade.php a été supprimé (bonne pratique : il ne doit pas rester accessible).</p>';
    } catch (Exception $e) {
        echo '<p class="warn">⚠ Supprimez manuellement le fichier upgrade.php de votre hébergement.</p>';
    }
} else {
    echo '<p class="bad">Aucune instruction exécutée. Consultez les messages ci-dessus.</p>';
}

echo '</div></body></html>';
