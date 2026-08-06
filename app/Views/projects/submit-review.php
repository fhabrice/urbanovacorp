<?php ob_start(); ?>

<h1>Étape 4 : Vérification et Validation</h1>
<?php if (!empty($_SESSION['project_submission_errors'])): ?>
    <div class="error-list">
        <ul>
        <?php foreach ($_SESSION['project_submission_errors'] as $field => $msg): ?>
            <li><?php echo htmlspecialchars($msg); ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['project_submission_errors']); ?>
<?php endif; ?>
<form action="/projects/submit" method="POST">
    <input type="hidden" name="step" value="4">

    <h3>Récapitulatif</h3>
    <div class="summary">
        <?php foreach ($data as $k => $v): ?>
            <div><strong><?php echo htmlspecialchars($k); ?>:</strong> <?php echo is_array($v)?htmlspecialchars(implode(', ', $v)):htmlspecialchars($v); ?></div>
        <?php endforeach; ?>
    </div>

    <h3>Déclarations obligatoires</h3>
    <label><input type="checkbox" name="agree_exact" required> Je certifie que toutes les informations fournies sont exactes.</label>
    <label><input type="checkbox" name="agree_authorized" required> Je déclare être autorisé à représenter ce projet.</label>
    <label><input type="checkbox" name="agree_tos" required> J'accepte les Conditions Générales d'Utilisation de la plateforme Urbanova.</label>
    <label><input type="checkbox" name="agree_contact" required> J'autorise Urbanova à analyser mon projet et à me contacter pour toute demande d'information complémentaire.</label>
    <label><input type="checkbox" name="agree_publish" required> J'accepte que mon projet soit publié sur la Marketplace uniquement après validation par Urbanova.</label>
    <label><input type="checkbox" name="agree_commission" required> J'accepte que, dans le cadre d'une mission de levée de fonds, Urbanova puisse percevoir une commission de succès comprise entre 3 % et 5 % du montant effectivement levé.</label>

    <div class="form-actions">
        <button type="submit" name="action" value="prev">Précédent</button>
        <button type="submit" name="action" value="submit">Soumettre le projet</button>
    </div>
</form>

<?php $content = ob_get_clean(); require_once APP_PATH . '/Views/layouts/header.php'; ?>
