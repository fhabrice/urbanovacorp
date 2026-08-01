<!DOCTYPE html>
<html lang="<?php echo $session->get('language', 'fr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('app.name'); ?> - <?php echo isset($pageTitle) ? $pageTitle : __('app.slogan'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="/"><?php echo __('app.name'); ?></a>
            </div>
            <div class="nav-menu">
                <a href="/"><?php echo __('nav.home'); ?></a>
                <a href="/about"><?php echo __('nav.about'); ?></a>
                <a href="/services"><?php echo __('nav.services'); ?></a>
                <a href="/marketplace"><?php echo __('nav.marketplace'); ?></a>
                <a href="/contact"><?php echo __('nav.contact'); ?></a>
            </div>
            <div class="nav-actions">
                <div class="lang-switch">
                    <a href="/lang/fr" class="<?php echo $session->get('language') === 'fr' ? 'active' : ''; ?>">FR</a>
                    <a href="/lang/en" class="<?php echo $session->get('language') === 'en' ? 'active' : ''; ?>">EN</a>
                </div>
                <?php if ($session->has('user_id')): ?>
                    <div class="user-menu">
                        <a href="<?php echo $session->get('user_role') === 'admin' ? '/admin' : '/investor'; ?>">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($session->get('user_name')); ?>
                        </a>
                        <a href="/logout"><i class="fas fa-sign-out-alt"></i></a>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-sm"><?php echo __('nav.login'); ?></a>
                    <a href="/register" class="btn btn-sm btn-primary"><?php echo __('nav.register'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php if ($session->hasFlashMessage('success')): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($session->getFlashMessage('success')); ?>
        </div>
    <?php endif; ?>

    <?php if ($session->hasFlashMessage('error')): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($session->getFlashMessage('error')); ?>
        </div>
    <?php endif; ?>

    <main>
