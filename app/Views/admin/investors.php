<?php ob_start(); ?>

<div class="page-header">
    <h1><?php echo __('admin.investors'); ?></h1>
</div>

<div class="investors-table-container">
    <?php if (empty($investors)): ?>
        <div class="no-data">
            <p><?php echo __('admin.no_investors'); ?></p>
        </div>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo __('auth.name'); ?></th>
                    <th><?php echo __('auth.email'); ?></th>
                    <th>Type</th>
                    <th><?php echo __('admin.status'); ?></th>
                    <th><?php echo __('admin.approve'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($investors as $investor): ?>
                    <tr>
                        <td><?php echo $investor['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($investor['email']); ?></td>
                        <td><?php echo $investor['investor_type'] ? __('investor.type_' . $investor['investor_type']) : ($investor['type'] === 'individual' ? __('investor.individual') : __('investor.corporate')); ?></td>
                        <td>
                            <span class="status status-<?php echo $investor['investor_status']; ?>">
                                <?php echo __('admin.' . $investor['investor_status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($investor['investor_status'] === 'pending' || $investor['investor_status'] === 'additional_info'): ?>
                                    <a href="/admin/investors/<?php echo $investor['id']; ?>/approve" class="btn btn-sm btn-success" title="<?php echo __('admin.approve'); ?>">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="/admin/investors/<?php echo $investor['id']; ?>/request-info" class="btn btn-sm btn-warning" title="<?php echo __('admin.request_info'); ?>">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    <form method="POST" action="/admin/investors/<?php echo $investor['id']; ?>/reject" style="display:inline;">
                                        <button type="submit" class="btn btn-sm btn-danger" title="<?php echo __('admin.reject'); ?>" onclick="return confirm('<?php echo __('admin.confirm_reject_investor'); ?>');">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
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
