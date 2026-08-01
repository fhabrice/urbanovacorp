<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="hero">
    <div class="container">
        <h1>Structurer les villes africaines de demain</h1>
        <p>Construction, Immobilier, Infrastructures et Investissements pour un développement durable.</p>
        <div class="cta-buttons">
            <a href="/urbanovacorp/?route=contact" class="btn btn-primary">Demander un devis</a>
            <a href="/urbanovacorp/?route=contact" class="btn btn-secondary">Soumettre un projet</a>
            <a href="/urbanovacorp/?route=contact" class="btn btn-success">Investir avec nous</a>
            <a href="/urbanovacorp/?route=contact" class="btn btn-outline">Devenir partenaire</a>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['projects_completed']); ?></div>
                <div class="stat-label">Projets réalisés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['total_investments'], 0, '', ' '); ?> $</div>
                <div class="stat-label">Investissements mobilisés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['housing_units']); ?></div>
                <div class="stat-label">Logements développés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['jobs_created']); ?></div>
                <div class="stat-label">Emplois créés</div>
            </div>
        </div>
    </div>
</div>

<div class="featured-section">
    <div class="container">
        <h2>Projets en vedette</h2>
        <div class="projects-grid">
            <?php foreach ($featuredProjects as $project): ?>
                <div class="project-card">
                    <div class="project-placeholder">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="project-content">
                        <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="location"><?php echo htmlspecialchars($project['city']); ?>, <?php echo htmlspecialchars($project['country']); ?></p>
                        <div class="project-stats">
                            <span>Financement recherché: <?php echo number_format($project['funding_sought']); ?> $</span>
                            <span>ROI: <?php echo $project['roi']; ?>%</span>
                        </div>
                        <a href="/urbanovacorp/?route=contact" class="btn btn-sm">Voir le projet</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
