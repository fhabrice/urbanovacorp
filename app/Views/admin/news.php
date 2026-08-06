<?php ob_start(); ?>

<div class="page-header">
    <h1><?php echo __('admin.news'); ?></h1>
    <a href="/admin/news/create" class="btn btn-primary btn-sm"><?php echo __('admin.create_news'); ?></a>
</div>

<div class="projects-table-container">
    <?php if (empty($news)): ?>
        <div class="no-data">
            <p><?php echo __('admin.no_news'); ?></p>
        </div>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo __('admin.title'); ?></th>
                    <th><?php echo __('admin.category'); ?></th>
                    <th><?php echo __('admin.status'); ?></th>
                    <th><?php echo __('admin.published_at'); ?></th>
                    <th><?php echo __('admin.actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($news as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td>
                            <span class="status status-<?php echo $item['status']; ?>">
                                <?php echo __('admin.' . $item['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $item['published_at'] ? htmlspecialchars($item['published_at']) : '-'; ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="/admin/news/<?php echo $item['id']; ?>/edit" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/admin/news/<?php echo $item['id']; ?>/delete" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('admin.confirm_delete_news'); ?>');">
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

<?php $content = ob_get_clean(); require_once APP_PATH . '/Views/layouts/admin-layout.php'; ?>