<?php ob_start(); ?>

<div class="page-header">
    <h1><?php echo __('admin.statistics'); ?></h1>
</div>

<div class="statistics-container">
    <div class="stat-section">
        <h2><?php echo __('admin.projects'); ?> par statut</h2>
        <div class="chart-container">
            <?php foreach ($stats['projects_by_status'] as $item): ?>
                <div class="stat-bar">
                    <div class="bar-label"><?php echo __('admin.' . $item['status']); ?></div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: <?php echo ($item['count'] / max(array_column($stats['projects_by_status'], 'count'))) * 100; ?>%"></div>
                    </div>
                    <div class="bar-value"><?php echo $item['count']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="stat-section">
        <h2><?php echo __('admin.projects'); ?> par type</h2>
        <div class="chart-container">
            <?php foreach ($stats['projects_by_type'] as $item): ?>
                <div class="stat-bar">
                    <div class="bar-label"><?php echo __('project.type_' . $item['type']); ?></div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: <?php echo ($item['count'] / max(array_column($stats['projects_by_type'], 'count'))) * 100; ?>%"></div>
                    </div>
                    <div class="bar-value"><?php echo $item['count']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="stat-section">
        <h2><?php echo __('admin.investors'); ?> par statut</h2>
        <div class="chart-container">
            <?php foreach ($stats['investors_by_status'] as $item): ?>
                <div class="stat-bar">
                    <div class="bar-label"><?php echo __('admin.' . $item['investor_status']); ?></div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: <?php echo ($item['count'] / max(array_column($stats['investors_by_status'], 'count'))) * 100; ?>%"></div>
                    </div>
                    <div class="bar-value"><?php echo $item['count']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="stat-section">
        <h2><?php echo __('admin.investors'); ?> par type</h2>
        <div class="chart-container">
            <?php foreach ($stats['investors_by_type'] as $item): ?>
                <div class="stat-bar">
                    <div class="bar-label"><?php echo $item['type'] === 'individual' ? __('investor.individual') : __('investor.corporate'); ?></div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: <?php echo ($item['count'] / max(array_column($stats['investors_by_type'], 'count'))) * 100; ?>%"></div>
                    </div>
                    <div class="bar-value"><?php echo $item['count']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.statistics-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.stat-section {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-section h2 {
    margin-bottom: 1.5rem;
    color: var(--primary-color);
}

.chart-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.bar-label {
    width: 150px;
    font-size: 0.875rem;
}

.bar-wrapper {
    flex: 1;
    height: 30px;
    background-color: var(--light-color);
    border-radius: 4px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    background-color: var(--secondary-color);
    transition: width 0.3s;
}

.bar-value {
    width: 50px;
    text-align: right;
    font-weight: bold;
}

@media (max-width: 768px) {
    .statistics-container {
        grid-template-columns: 1fr;
    }
    
    .stat-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .bar-label,
    .bar-value {
        width: 100%;
    }
    
    .bar-wrapper {
        width: 100%;
    }
}
</style>

<?php $content = ob_get_clean(); require_once APP_PATH . '/Views/layouts/admin-layout.php'; ?>
