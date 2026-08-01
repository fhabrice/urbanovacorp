<!DOCTYPE html>
<html lang="<?php echo $session['language'] ?? 'fr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URBANOVA SOLUTIONS - <?php echo isset($pageTitle) ? $pageTitle : 'Structurer les villes africaines de demain'; ?></title>
    <link rel="stylesheet" href="/urbanovacorp/public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="/urbanovacorp/?route=/">URBANOVA SOLUTIONS</a>
            </div>
            <div class="nav-menu">
                <a href="/urbanovacorp/?route=/">Accueil</a>
                <a href="/urbanovacorp/?route=about">À propos</a>
                <a href="/urbanovacorp/?route=services">Services</a>
                <a href="/urbanovacorp/?route=contact">Contact</a>
            </div>
            <div class="nav-actions">
                <div class="lang-switch">
                    <a href="/urbanovacorp/?route=lang&lang=fr" class="<?php echo ($session['language'] ?? 'fr') === 'fr' ? 'active' : ''; ?>">FR</a>
                    <a href="/urbanovacorp/?route=lang&lang=en" class="<?php echo ($session['language'] ?? 'fr') === 'en' ? 'active' : ''; ?>">EN</a>
                </div>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['flash_success']); ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['flash_error']); ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <main>
