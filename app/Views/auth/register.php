<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1><?php echo __('auth.register_title'); ?></h1>
        
        <?php if(!empty($interest) || !empty($preselected_role) || !empty($_GET['interest'])): ?>
        <div style="margin-bottom:1.2rem;padding:1rem;background:linear-gradient(135deg,#0f2a44,#1a4d7a);color:#fff;border-radius:12px;">
          <div style="font-weight:800;">🎯 Parcours Investisseur Urbanova</div>
          <div style="font-size:.9rem;opacity:.9;margin-top:.3rem;"><?php if(!empty($interest) || !empty($_GET['interest'])): $pid = htmlspecialchars($interest ?? $_GET['interest']); ?>Vous manifestez votre intérêt pour le <strong>Projet #<?= $pid ?></strong> — votre compte sera créé en tant qu'<strong>Investisseur</strong> et vous serez ramené automatiquement vers le projet pour <strong>Confirmer mon intérêt</strong>. <?php else: ?>Inscription en tant qu'<strong>Investisseur</strong> — rôle pré-sélectionné pour accéder au Marketplace.<?php endif; ?></div>
          <?php if(!empty($interest) || !empty($_GET['interest'])): $pid = htmlspecialchars($interest ?? $_GET['interest']); ?><div style="margin-top:.6rem;"><a href="/marketplace/<?= $pid ?>" style="color:#fff;text-decoration:underline;font-size:.85rem;">← Revenir au projet #<?= $pid ?></a></div><?php endif; ?>
        </div>
        <?php endif; ?>
        <form method="POST" action="/register">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
            <?php if(!empty($interest) || !empty($_GET['interest']) || !empty($pending_interest)): ?><input type="hidden" name="interest" value="<?php echo htmlspecialchars($interest ?? $_GET['interest'] ?? $pending_interest) ?>"><?php endif; ?>
            
            <div class="form-group">
                <label for="first_name"><?php echo __('auth.first_name'); ?></label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            
            <div class="form-group">
                <label for="last_name"><?php echo __('auth.last_name'); ?></label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            
            <div class="form-group">
                <label for="email"><?php echo __('auth.email'); ?></label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="role"><?php echo __('auth.role'); ?></label>
                <select id="role" name="role" required>
                    <option value="promoter" <?php echo (!empty($preselected_role) && $preselected_role==='promoter') ? 'selected' : (empty($preselected_role) ? '' : '') ?>><?php echo __('auth.role_promoter'); ?></option>
                    <option value="investor" <?php echo (!empty($preselected_role) && $preselected_role==='investor') || !empty($interest) || !empty($_GET['interest']) ? 'selected' : '' ?>><?php echo __('auth.role_investor'); ?></option>
                </select>
            </div>

            <div id="investorFields" style="display:none;">
                <div class="form-group" id="investorTypeGroup">
                    <label for="investor_type"><?php echo __('investor.investor_type'); ?></label>
                    <select id="investor_type" name="investor_type">
                        <?php $types = ['business_angel','individual','family_office','investment_fund','bank','investment_company','dfi','corporate_venture','other']; foreach($types as $t): ?>
                            <option value="<?php echo $t; ?>"><?php echo __('investor.type_' . $t); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="company_name"><?php echo __('auth.company_name'); ?></label>
                    <input type="text" id="company_name" name="company_name">
                </div>
                <div class="form-group">
                    <label for="representative_name"><?php echo __('auth.representative_name'); ?></label>
                    <input type="text" id="representative_name" name="representative_name">
                </div>
                <div class="form-group">
                    <label for="position"><?php echo __('auth.position'); ?></label>
                    <input type="text" id="position" name="position">
                </div>
                <div class="form-group">
                    <label for="country"><?php echo __('auth.country'); ?></label>
                    <input type="text" id="country" name="country">
                </div>
                <div class="form-group">
                    <label for="city"><?php echo __('auth.city'); ?></label>
                    <input type="text" id="city" name="city">
                </div>
                <div class="form-group">
                    <label for="address"><?php echo __('auth.address'); ?></label>
                    <textarea id="address" name="address"></textarea>
                </div>
                <div class="form-group">
                    <label for="phone"><?php echo __('auth.phone'); ?> <small style="color:#dc2626;">* requis investisseur</small></label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="website"><?php echo __('auth.website'); ?> <small>(<?php echo __('auth.optional'); ?>)</small></label>
                    <input type="url" id="website" name="website">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password"><?php echo __('auth.password'); ?></label>
                <input type="password" id="password" name="password" required minlength="8">
            </div>
            
            <div class="form-group">
                <label for="password_confirm"><?php echo __('auth.confirm_password'); ?></label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block"><?php echo __('auth.register_button'); ?></button>
        </form>
        
        <div class="auth-footer">
            <p><?php echo __('auth.have_account'); ?> <a href="/login"><?php echo __('auth.login_title'); ?></a></p>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>

<script>
const roleSelect = document.getElementById('role');
const investorFields = document.getElementById('investorFields');

function toggleInvestorFields() {
    const show = roleSelect.value === 'investor';
    investorFields.style.display = show ? 'block' : 'none';
}
roleSelect.addEventListener('change', toggleInvestorFields);
toggleInvestorFields();
<?php if(!empty($interest) || !empty($preselected_role) || !empty($_GET['interest'])): ?>
// Pré-sélection investisseur depuis Marketplace
roleSelect.value='investor';
toggleInvestorFields();
<?php endif; ?>
</script>
