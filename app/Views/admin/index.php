<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2><?php echo __('admin.title'); ?></h2>
        </div>
        <nav class="sidebar-nav">
            <a href="/admin" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i> <?php echo __('admin.dashboard'); ?>
            </a>
            <a href="/admin/projects" class="nav-link">
                <i class="fas fa-building"></i> <?php echo __('admin.projects'); ?>
            </a>
            <a href="/admin/investors" class="nav-link">
                <i class="fas fa-users"></i> <?php echo __('admin.investors'); ?>
            </a>
            <a href="/admin/statistics" class="nav-link">
                <i class="fas fa-chart-bar"></i> <?php echo __('admin.statistics'); ?>
            </a>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="page-header">
            <h1><?php echo __('admin.dashboard'); ?></h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #3498db;">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $stats['total_projects']; ?></div>
                    <div class="stat-label"><?php echo __('admin.total_projects'); ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: #f39c12;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $stats['pending_projects']; ?></div>
                    <div class="stat-label"><?php echo __('admin.pending_projects'); ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: #27ae60;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $stats['approved_projects']; ?></div>
                    <div class="stat-label"><?php echo __('admin.approved_projects'); ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: #9b59b6;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $stats['total_investors']; ?></div>
                    <div class="stat-label"><?php echo __('admin.total_investors'); ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: #e74c3c;">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo $stats['pending_investors']; ?></div>
                    <div class="stat-label"><?php echo __('admin.pending_investors'); ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: #1abc9c;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number"><?php echo number_format($stats['total_funding_sought'], 0, '', ' '); ?> $</div>
                    <div class="stat-label"><?php echo __('admin.total_funding_sought'); ?></div>
                </div>
            </div>
        </div>

        <div class="admin-sections">
            <div class="admin-section">
                <h2><?php echo __('admin.recent_projects'); ?></h2>
                <?php if (empty($recentProjects)): ?>
                    <p><?php echo __('admin.no_projects'); ?></p>
                <?php else: ?>
                    <div class="recent-list">
                        <?php foreach ($recentProjects as $project): ?>
                            <div class="recent-item">
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?></p>
                                </div>
                                <div class="item-status">
                                    <span class="status status-<?php echo $project['status']; ?>">
                                        <?php echo __('admin.' . $project['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="admin-section">
                <h2><?php echo __('admin.recent_investors'); ?></h2>
                <?php if (empty($recentInvestors)): ?>
                    <p><?php echo __('admin.no_investors'); ?></p>
                <?php else: ?>
                    <div class="recent-list">
                        <?php foreach ($recentInvestors as $investor): ?>
                            <div class="recent-item">
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']); ?></h3>
                                    <p><?php echo htmlspecialchars($investor['email']); ?></p>
                                </div>
                                <div class="item-status">
                                    <span class="status status-<?php echo $investor['investor_status']; ?>">
                                        <?php echo __('admin.' . $investor['investor_status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

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

.admin-content {
    padding: 2rem;
    background-color: var(--bg-color);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-label {
    color: var(--muted-color);
    font-size: 0.875rem;
}

.admin-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.admin-section {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-section h2 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.recent-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.recent-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background-color: var(--bg-color);
    border-radius: 4px;
}

.item-info h3 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.item-info p {
    color: var(--muted-color);
    font-size: 0.875rem;
}

.status {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.status-submitted,
.status-pending {
    background-color: #f39c12;
    color: white;
}

.status-approved {
    background-color: #27ae60;
    color: white;
}

.status-rejected {
    background-color: #e74c3c;
    color: white;
}

@media (max-width: 768px) {
    .admin-layout {
        grid-template-columns: 1fr;
    }
    
    .admin-sidebar {
        display: none;
    }
    
    .admin-sections {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
