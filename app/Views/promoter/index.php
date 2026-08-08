<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container promoter-dashboard">
    <div class="page-header">
        <h1><?php echo __('promoter.dashboard'); ?></h1>
        <p><?php echo __('promoter.welcome'); ?></p>
    </div>

    <!-- Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_projects']; ?></div>
            <div class="stat-label"><?php echo __('promoter.total_projects'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['draft']; ?></div>
            <div class="stat-label"><?php echo __('promoter.draft_projects'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['submitted']; ?></div>
            <div class="stat-label"><?php echo __('promoter.submitted_projects'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['published']; ?></div>
            <div class="stat-label"><?php echo __('promoter.published_projects'); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['sold']; ?></div>
            <div class="stat-label"><?php echo __('promoter.sold_projects'); ?></div>
        </div>
    </div>

    <div class="promoter-layout">
        <!-- Projects List -->
        <section class="projects-section">
            <div class="section-header">
                <h2><?php echo __('promoter.my_projects'); ?></h2>
                <a href="/projects/submit" class="btn btn-primary"><?php echo __('promoter.add_project'); ?></a>
            </div>
            
            <?php if (empty($projects)): ?>
                <div class="no-data">
                    <p><?php echo __('promoter.no_projects'); ?></p>
                    <a href="/projects/submit" class="btn btn-primary"><?php echo __('promoter.create_first_project'); ?></a>
                </div>
            <?php else: ?>
                <div class="projects-table">
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo __('project.title'); ?></th>
                                <th><?php echo __('project.type'); ?></th>
                                <th><?php echo __('project.status'); ?></th>
                                <th><?php echo __('project.funding_sought'); ?></th>
                                <th><?php echo __('project.funding_mobilized'); ?></th>
                                <th><?php echo __('promoter.actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($project['title']); ?></td>
                                    <td><?php echo __('project.type_' . $project['type']); ?></td>
                                    <td>
                                        <span class="status status-<?php echo $project['status']; ?>">
                                            <?php echo __('project.status_' . $project['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($project['funding_sought']); ?> $</td>
                                    <td><?php echo number_format($project['funding_mobilized']); ?> $</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="/projects/<?php echo $project['id']; ?>" class="btn btn-sm" title="<?php echo __('promoter.view'); ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($project['status'] === 'draft'): ?>
                                                <a href="/projects/<?php echo $project['id']; ?>/edit" class="btn btn-sm" title="<?php echo __('promoter.edit'); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Recent Activity -->
        <aside class="activity-sidebar">
            <!-- Visit Requests -->
            <div class="card">
                <h3><?php echo __('promoter.recent_visits'); ?></h3>
                <?php if (empty($visits)): ?>
                    <p><?php echo __('promoter.no_visits'); ?></p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($visits as $visit): ?>
                            <div class="activity-item">
                                <div class="activity-title"><?php echo htmlspecialchars($visit['visitor_name']); ?></div>
                                <div class="activity-project"><?php echo htmlspecialchars($visit['project_title']); ?></div>
                                <div class="activity-date"><?php echo date('d/m/Y', strtotime($visit['preferred_date'])); ?> à <?php echo date('H:i', strtotime($visit['preferred_time'])); ?></div>
                                <div class="activity-status">
                                    <span class="status status-<?php echo $visit['status']; ?>">
                                        <?php echo __('visit.status_' . $visit['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Reservations -->
            <div class="card">
                <h3><?php echo __('promoter.recent_reservations'); ?></h3>
                <?php if (empty($reservations)): ?>
                    <p><?php echo __('promoter.no_reservations'); ?></p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($reservations as $reservation): ?>
                            <div class="activity-item">
                                <div class="activity-title"><?php echo htmlspecialchars($reservation['customer_name']); ?></div>
                                <div class="activity-project"><?php echo htmlspecialchars($reservation['project_title']); ?></div>
                                <div class="activity-type"><?php echo __('reservation.type_' . $reservation['reservation_type']); ?></div>
                                <div class="activity-status">
                                    <span class="status status-<?php echo $reservation['status']; ?>">
                                        <?php echo __('reservation.status_' . $reservation['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<style>
.promoter-dashboard {
    padding: 2rem 0;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-label {
    color: var(--muted-color);
    font-size: 0.875rem;
}

.promoter-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.projects-table {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.projects-table table {
    width: 100%;
    border-collapse: collapse;
}

.projects-table th,
.projects-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.projects-table th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.no-data {
    text-align: center;
    padding: 3rem;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.activity-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.card {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card h3 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    padding: 1rem;
    background-color: var(--light-color);
    border-radius: 4px;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.activity-project,
.activity-type,
.activity-date {
    font-size: 0.875rem;
    color: var(--muted-color);
    margin-bottom: 0.25rem;
}

.activity-status {
    margin-top: 0.5rem;
}

@media (max-width: 768px) {
    .promoter-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
