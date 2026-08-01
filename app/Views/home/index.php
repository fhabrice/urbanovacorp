<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="hero">
    <div class="container">
        <h1><?php echo __('home.hero_title'); ?></h1>
        <p><?php echo __('home.hero_subtitle'); ?></p>
        <div class="cta-buttons">
            <a href="/contact" class="btn btn-primary"><?php echo __('home.cta_quote'); ?></a>
            <a href="/projects/submit" class="btn btn-secondary"><?php echo __('home.cta_project'); ?></a>
            <a href="/investor/kyc" class="btn btn-success"><?php echo __('home.cta_invest'); ?></a>
            <a href="/contact" class="btn btn-outline"><?php echo __('home.cta_partner'); ?></a>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['projects_completed']); ?></div>
                <div class="stat-label"><?php echo __('home.stat_projects'); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['total_investments'], 0, '', ' '); ?> $</div>
                <div class="stat-label"><?php echo __('home.stat_investments'); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['housing_units']); ?></div>
                <div class="stat-label"><?php echo __('home.stat_housing'); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['jobs_created']); ?></div>
                <div class="stat-label"><?php echo __('home.stat_jobs'); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="featured-section">
    <div class="container">
        <h2><?php echo __('home.featured_title'); ?></h2>
        <div class="projects-grid">
            <?php foreach ($featuredProjects as $project): ?>
                <div class="project-card">
                    <img src="/uploads/projects/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                    <div class="project-content">
                        <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="location"><?php echo htmlspecialchars($project['city']); ?>, <?php echo htmlspecialchars($project['country']); ?></p>
                        <div class="project-stats">
                            <span><?php echo __('project.funding_sought'); ?>: <?php echo number_format($project['funding_sought']); ?> $</span>
                            <span><?php echo __('project.roi'); ?>: <?php echo $project['roi']; ?>%</span>
                        </div>
                        <a href="/marketplace/<?php echo $project['id']; ?>" class="btn btn-sm"><?php echo __('home.view_project'); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
