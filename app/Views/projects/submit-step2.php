<?php ob_start(); ?>

<h1>Étape 2 : Données financières</h1>
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
    <input type="hidden" name="step" value="2">

    <label>Valeur totale du projet<input type="number" step="0.01" name="total_cost" value="<?php echo htmlspecialchars($data['total_cost'] ?? ''); ?>" required></label>
    <label>Montant déjà investi<input type="number" step="0.01" name="amount_invested" value="<?php echo htmlspecialchars($data['amount_invested'] ?? ''); ?>"></label>
    <label>Montant recherché<input type="number" step="0.01" name="funding_sought" value="<?php echo htmlspecialchars($data['funding_sought'] ?? ''); ?>" required></label>
    <label>Devise<input type="text" name="currency" value="<?php echo htmlspecialchars($data['currency'] ?? 'USD'); ?>" required></label>

    <h3>Type de financement recherché</h3>
    <?php $fin=['Capital (Equity)','Dette','Financement mixte','Partenariat stratégique']; foreach($fin as $f): ?>
        <label><input type="checkbox" name="finance_types[]" value="<?php echo $f; ?>" <?php echo (isset($data['finance_types']) && in_array($f,$data['finance_types']))?'checked':''; ?>> <?php echo $f; ?></label>
    <?php endforeach; ?>

    <h3>Informations financières</h3>
    <label>Coût du terrain<input type="number" step="0.01" name="cost_land" value="<?php echo htmlspecialchars($data['cost_land'] ?? ''); ?>"></label>
    <label>Coût de construction<input type="number" step="0.01" name="cost_construction" value="<?php echo htmlspecialchars($data['cost_construction'] ?? ''); ?>"></label>
    <label>Honoraires techniques<input type="number" step="0.01" name="fees" value="<?php echo htmlspecialchars($data['fees'] ?? ''); ?>"></label>
    <label>Fonds de roulement<input type="number" step="0.01" name="working_capital" value="<?php echo htmlspecialchars($data['working_capital'] ?? ''); ?>"></label>
    <label>Prévision du chiffre d'affaires<input type="number" step="0.01" name="forecast_revenue" value="<?php echo htmlspecialchars($data['forecast_revenue'] ?? ''); ?>"></label>
    <label>Prévision des bénéfices<input type="number" step="0.01" name="forecast_profit" value="<?php echo htmlspecialchars($data['forecast_profit'] ?? ''); ?>"></label>
    <label>Retour sur investissement (ROI)<input type="number" step="0.01" name="roi" value="<?php echo htmlspecialchars($data['roi'] ?? ''); ?>"></label>
    <label>Durée estimée du projet<input type="text" name="project_duration" value="<?php echo htmlspecialchars($data['project_duration'] ?? ''); ?>"></label>

    <h3>Commercialisation</h3>
    <label>Prix de vente estimé<input type="number" step="0.01" name="sale_price" value="<?php echo htmlspecialchars($data['sale_price'] ?? ''); ?>"></label>
    <label>Prix de location estimé<input type="number" step="0.01" name="rental_price" value="<?php echo htmlspecialchars($data['rental_price'] ?? ''); ?>"></label>
    <label>Nombre d'unités à vendre<input type="number" name="units_for_sale" value="<?php echo htmlspecialchars($data['units_for_sale'] ?? ''); ?>"></label>
    <label>Nombre d'unités à louer<input type="number" name="units_for_rent" value="<?php echo htmlspecialchars($data['units_for_rent'] ?? ''); ?>"></label>

    <div class="form-actions">
        <button type="submit" name="action" value="prev">Précédent</button>
        <button type="submit" name="action" value="next">Suivant</button>
    </div>
</form>

<?php $content = ob_get_clean(); require_once APP_PATH . '/Views/layouts/header.php'; ?>
