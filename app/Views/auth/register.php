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

            <div class="form-group" id="investorTypeGroup" style="display:none;">
                <label for="investor_type">Type d'investisseur</label>
                <select id="investor_type" name="investor_type">
                    <?php $types = ['business_angel','individual','family_office','investment_fund','bank','investment_company','dfi','corporate_venture','other']; foreach($types as $t): ?>
                        <option value="<?php echo $t; ?>"><?php echo __('investor.type_' . $t); ?></option>
                    <?php endforeach; ?>
                </select>
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
document.getElementById('role').addEventListener('change', function(e){
    const g = document.getElementById('investorTypeGroup');
    if (e.target.value === 'investor') g.style.display = 'block'; else g.style.display = 'none';
});
</script>
