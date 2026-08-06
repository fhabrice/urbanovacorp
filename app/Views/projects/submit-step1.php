<?php ob_start(); ?>

<h1>Étape 1 : Informations générales</h1>
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
    <input type="hidden" name="step" value="1">

    <h2>Informations sur le porteur du projet</h2>
    <label>Nom du porteur / Entreprise<input type="text" name="promoter_name" value="<?php echo htmlspecialchars($data['promoter_name'] ?? ''); ?>" required></label>
    <label>Type de porteur
        <select name="promoter_type" required>
            <?php $types=['Particulier','Entreprise','Promoteur','Coopérative','Institution']; foreach($types as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo (isset($data['promoter_type']) && $data['promoter_type']==$t)?'selected':''; ?>><?php echo $t; ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Nom du responsable<input type="text" name="contact_name" value="<?php echo htmlspecialchars($data['contact_name'] ?? ''); ?>" required></label>
    <label>Téléphone<input type="text" name="contact_phone" value="<?php echo htmlspecialchars($data['contact_phone'] ?? ''); ?>" required></label>
    <label>E-mail<input type="email" name="contact_email" value="<?php echo htmlspecialchars($data['contact_email'] ?? ''); ?>" required></label>
    <label>Adresse<input type="text" name="contact_address" value="<?php echo htmlspecialchars($data['contact_address'] ?? ''); ?>"></label>
    <label>Site web (optionnel)<input type="url" name="website" value="<?php echo htmlspecialchars($data['website'] ?? ''); ?>"></label>

    <h2>Informations sur le projet</h2>
    <label>Nom du projet<input type="text" name="project_name" value="<?php echo htmlspecialchars($data['project_name'] ?? ''); ?>" required></label>
    <label>Type de projet
        <select name="project_type" required>
            <?php $pt=['Résidentiel','Commercial','Mixte','Industriel','Hôtel','Bureau','Lotissement','Autre']; foreach($pt as $p): ?>
                <option value="<?php echo $p; ?>" <?php echo (isset($data['project_type']) && $data['project_type']==$p)?'selected':''; ?>><?php echo $p; ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Type d'opération
        <select name="operation_type" required>
            <?php $ops=['Vente','Location','Levée de fonds','Vente + Levée de fonds']; foreach($ops as $o): ?>
                <option value="<?php echo $o; ?>" <?php echo (isset($data['operation_type']) && $data['operation_type']==$o)?'selected':''; ?>><?php echo $o; ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Pays<input type="text" name="country" value="<?php echo htmlspecialchars($data['country'] ?? ''); ?>" required></label>
    <label>Province / État<input type="text" name="province" value="<?php echo htmlspecialchars($data['province'] ?? ''); ?>"></label>
    <label>Ville<input type="text" name="city" value="<?php echo htmlspecialchars($data['city'] ?? ''); ?>" required></label>
    <label>Commune / Quartier<input type="text" name="commune" value="<?php echo htmlspecialchars($data['commune'] ?? ''); ?>"></label>
    <label>Coordonnées GPS<input type="text" name="gps" value="<?php echo htmlspecialchars($data['gps'] ?? ''); ?>"></label>
    <label>Description détaillée<textarea name="description" rows="6" required><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea></label>

    <label>État d'avancement
        <select name="project_status" required>
            <?php $st=['Idée','Études terminées','Terrain acquis','Construction en cours','Projet terminé']; foreach($st as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo (isset($data['project_status']) && $data['project_status']==$s)?'selected':''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Date estimative de livraison<input type="date" name="estimated_delivery_date" value="<?php echo htmlspecialchars($data['estimated_delivery_date'] ?? ''); ?>"></label>

    <div class="form-actions">
        <button type="submit" name="action" value="next">Suivant</button>
    </div>
</form>

<?php $content = ob_get_clean(); require_once APP_PATH . '/Views/layouts/header.php'; ?>
