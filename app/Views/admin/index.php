<?php ob_start(); ?>

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
                        <div class="item-actions">
                            <span class="status status-<?php echo $project['status']; ?>">
                                <?php echo __('admin.' . $project['status']); ?>
                            </span>
                            <div class="action-buttons">
                                <?php if ($project['status'] === 'pending' || $project['status'] === 'submitted'): ?>
                                    <a href="/admin/projects/<?php echo $project['id']; ?>/approve"
                                       class="btn btn-sm btn-success"
                                       title="<?php echo __('admin.approve'); ?>">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="/admin/projects/<?php echo $project['id']; ?>/reject"
                                       class="btn btn-sm btn-warning"
                                       title="<?php echo __('admin.reject'); ?>">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="/marketplace/<?php echo $project['id']; ?>"
                                   class="btn btn-sm btn-primary"
                                   title="<?php echo __('admin.view'); ?>"
                                   target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/admin/projects/<?php echo $project['id']; ?>/delete"
                                   class="btn btn-sm btn-danger"
                                   title="<?php echo __('admin.delete'); ?>"
                                   onclick="return confirm('<?php echo __('admin.confirm_delete_project'); ?>');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="/admin/projects" class="btn btn-outline btn-sm view-all-link">
                <i class="fas fa-list"></i> <?php echo __('admin.view_all_projects'); ?>
            </a>
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
                            <h3><?php echo htmlspecialchars($investor['full_name'] ?? ($investor['first_name'] . ' ' . $investor['last_name'])); ?></h3>
                            <p><?php echo htmlspecialchars($investor['email']); ?></p>
                        </div>
                        <div class="item-actions">
                            <span class="status status-<?php echo $investor['investor_status']; ?>">
                                <?php echo __('admin.' . $investor['investor_status']); ?>
                            </span>
                            <div class="action-buttons">
                                <?php if ($investor['investor_status'] === 'pending'): ?>
                                    <a href="/admin/investors/<?php echo $investor['id']; ?>/approve"
                                       class="btn btn-sm btn-success"
                                       title="<?php echo __('admin.approve'); ?>">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="/admin/investors/<?php echo $investor['id']; ?>/reject"
                                       class="btn btn-sm btn-warning"
                                       title="<?php echo __('admin.reject'); ?>">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="/admin/investors"
                                   class="btn btn-sm btn-primary"
                                   title="<?php echo __('admin.view'); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="/admin/investors" class="btn btn-outline btn-sm view-all-link">
                <i class="fas fa-list"></i> <?php echo __('admin.view_all_investors'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
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

/* Item action area: badge + buttons side by side */
.item-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}

.action-buttons {
    display: flex;
    gap: 0.4rem;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: opacity 0.2s, transform 0.1s;
}
.btn:hover { opacity: 0.85; transform: translateY(-1px); }
.btn:active { transform: translateY(0); }

.btn-sm {
    padding: 0.35rem 0.6rem;
    font-size: 0.8rem;
    border-radius: 4px;
}

.btn-success { background-color: #27ae60; color: white; }
.btn-danger  { background-color: #e74c3c; color: white; }
.btn-warning { background-color: #f39c12; color: white; }
.btn-primary { background-color: var(--primary-color, #3498db); color: white; }
.btn-outline {
    background-color: transparent;
    color: var(--primary-color, #3498db);
    border: 1px solid var(--primary-color, #3498db);
}

.view-all-link {
    display: inline-flex;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .admin-sections {
        grid-template-columns: 1fr;
    }
    .recent-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .item-actions {
        flex-wrap: wrap;
    }
}
</style>

<?php $content = ob_get_clean(); require_once APP_PATH . '/Views/layouts/admin-layout.php'; ?>
