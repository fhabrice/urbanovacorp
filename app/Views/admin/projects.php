<?php require_once APP_PATH . '/Views/layouts/admin-layout.php'; ?>

<div class="page-header">
    <h1><?php echo __('admin.projects'); ?></h1>
</div>

<div class="projects-table-container">
    <?php if (empty($projects)): ?>
        <div class="no-data">
            <p><?php echo __('admin.no_projects'); ?></p>
        </div>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo __('project.name'); ?></th>
                    <th><?php echo __('project.type'); ?></th>
                    <th><?php echo __('project.location'); ?></th>
                    <th><?php echo __('project.funding_sought'); ?></th>
                    <th><?php echo __('admin.status'); ?></th>
                    <th><?php echo __('admin.approve'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?php echo $project['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                            <br>
                            <small><?php echo htmlspecialchars($project['full_name'] ?? ($project['first_name'] . ' ' . $project['last_name'])); ?></small>
                        </td>
                        <td><?php echo __('project.type_' . $project['type']); ?></td>
                        <td><?php echo htmlspecialchars($project['city']); ?>, <?php echo htmlspecialchars($project['country']); ?></td>
                        <td><?php echo number_format($project['funding_sought']); ?> $</td>
                        <td>
                            <span class="status status-<?php echo $project['status']; ?>">
                                <?php echo __('admin.' . $project['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($project['status'] === 'pending' || $project['status'] === 'submitted'): ?>
                                    <a href="/admin/projects/<?php echo $project['id']; ?>/approve" class="btn btn-sm btn-success" title="<?php echo __('admin.approve'); ?>">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="/admin/projects/<?php echo $project['id']; ?>/reject" class="btn btn-sm btn-danger" title="<?php echo __('admin.reject'); ?>">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="/marketplace/<?php echo $project['id']; ?>" class="btn btn-sm btn-primary" title="<?php echo __('admin.view'); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/admin/projects/<?php echo $project['id']; ?>/delete" class="btn btn-sm btn-danger" title="<?php echo __('admin.delete'); ?>" onclick="return confirm('<?php echo __('admin.confirm_delete_project'); ?>');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
.admin-table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-table th,
.admin-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.admin-table th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 600;
}

.admin-table tr:hover {
    background-color: var(--bg-color);
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
</style>
