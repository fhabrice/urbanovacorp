<!DOCTYPE html>
<html lang="<?php echo $session->get('language', 'fr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('app.name'); ?> - <?php echo __('admin.title'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem 1rem;
        }

        .sidebar-header h2 {
            margin-bottom: 2rem;
            font-size: 1.5rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar-nav a {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: background-color 0.3s;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: rgba(255,255,255,0.1);
        }

        .sidebar-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .admin-content {
            padding: 2rem;
            background-color: var(--bg-color);
        }

        @media (max-width: 768px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }
            
            .admin-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
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

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><?php echo __('admin.title'); ?></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> <?php echo __('admin.dashboard'); ?>
                </a>
                <a href="/admin/projects" class="nav-link">
                    <i class="fas fa-building"></i> <?php echo __('admin.projects'); ?>
                </a>
                <a href="/admin/news" class="nav-link">
                    <i class="fas fa-newspaper"></i> <?php echo __('admin.news'); ?>
                </a>
                <a href="/admin/investors" class="nav-link">
                    <i class="fas fa-users"></i> <?php echo __('admin.investors'); ?>
                </a>
                <a href="/admin/statistics" class="nav-link">
                    <i class="fas fa-chart-bar"></i> <?php echo __('admin.statistics'); ?>
                </a>
                <div class="sidebar-footer">
                    <a href="/"><i class="fas fa-home"></i> <?php echo __('nav.home'); ?></a>
                    <a href="/logout"><i class="fas fa-sign-out-alt"></i> <?php echo __('nav.logout'); ?></a>
                </div>
            </nav>
        </aside>

        <main class="admin-content">
            <?php echo $content ?? ''; ?>
        </main>
    </div>

    <script src="/assets/js/main.js"></script>
</body>
</html>
