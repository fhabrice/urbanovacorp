<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1><?php echo __('auth.register_title'); ?></h1>
        
        <form method="POST" action="/register">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
            
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
                    <option value="promoter"><?php echo __('auth.role_promoter'); ?></option>
                    <option value="investor"><?php echo __('auth.role_investor'); ?></option>
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
                    <label for="phone"><?php echo __('auth.phone'); ?></label>
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
</script>
