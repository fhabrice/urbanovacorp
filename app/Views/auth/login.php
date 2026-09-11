<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1><?php echo __('auth.login_title'); ?></h1>
        
        <?php if(!empty($interest) || !empty($_GET['interest']) || !empty($pending_interest)): $pid = htmlspecialchars($interest ?? $_GET['interest'] ?? $pending_interest); ?>
        <div style="margin-bottom:1.2rem;padding:1rem;background:#0f2a44;color:#fff;border-radius:12px;">
          <div style="font-weight:800;">↩️ Reprise de votre intérêt</div>
          <div style="font-size:.9rem;opacity:.9;margin-top:.3rem;">Vous aviez manifesté votre intérêt pour le <strong>Projet #<?= $pid ?></strong>. Connectez-vous : vous serez ramené automatiquement pour <strong>Confirmer mon intérêt</strong>.</div>
          <div style="margin-top:.6rem;"><a href="/marketplace/<?= $pid ?>" style="color:#fff;text-decoration:underline;font-size:.85rem;">Voir le projet #<?= $pid ?></a></div>
        </div>
        <?php endif; ?>
        <form method="POST" action="/login">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
            <?php if(!empty($interest) || !empty($_GET['interest']) || !empty($pending_interest)): ?><input type="hidden" name="interest" value="<?php echo htmlspecialchars($interest ?? $_GET['interest'] ?? $pending_interest) ?>"><?php endif; ?>
            
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
            <p><?php echo __('auth.no_account'); ?> <a href="/register<?php echo (!empty($interest)||!empty($_GET['interest'])||!empty($pending_interest)) ? '?role=investor&interest='.htmlspecialchars($interest ?? $_GET['interest'] ?? $pending_interest) : '' ?>"><?php echo __('auth.register_title'); ?></a></p>
            <?php if(!empty($interest) || !empty($_GET['interest'])): $pid=htmlspecialchars($interest ?? $_GET['interest']); ?><p style="margin-top:.6rem;"><a href="/register?role=investor&interest=<?= $pid ?>" style="color:#0f2a44;font-weight:700;text-decoration:none;">Créer mon compte investisseur pour ce projet →</a></p><?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
