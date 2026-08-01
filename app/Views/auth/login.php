<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1><?php echo __('auth.login_title'); ?></h1>
        
        <form method="POST" action="/login">
            <div class="form-group">
                <label for="email"><?php echo __('auth.email'); ?></label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password"><?php echo __('auth.password'); ?></label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block"><?php echo __('auth.login_button'); ?></button>
        </form>
        
        <div class="auth-footer">
            <p><?php echo __('auth.no_account'); ?> <a href="/register"><?php echo __('auth.register_title'); ?></a></p>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
