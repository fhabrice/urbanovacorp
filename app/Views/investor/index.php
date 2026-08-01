<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1><?php echo __('investor.dashboard'); ?></h1>
    </div>

    <?php if ($investor['investor_status'] === 'pending'): ?>
        <div class="alert alert-warning">
            <strong><?php echo __('investor.kyc_pending'); ?></strong>
            <p><?php echo __('investor.kyc_pending_message'); ?></p>
            <a href="/investor/kyc" class="btn btn-primary"><?php echo __('investor.complete_kyc'); ?></a>
        </div>
    <?php elseif ($investor['investor_status'] === 'rejected'): ?>
        <div class="alert alert-error">
            <strong><?php echo __('investor.kyc_rejected'); ?></strong>
            <p><?php echo htmlspecialchars($investor['rejection_reason']); ?></p>
            <a href="/investor/kyc" class="btn btn-primary"><?php echo __('investor.resubmit_kyc'); ?></a>
        </div>
    <?php else: ?>
        <div class="investor-dashboard">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($interests); ?></div>
                    <div class="stat-label"><?php echo __('investor.interests_count'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($investor['investment_capacity'] ?? 0, 0, '', ' '); ?> $</div>
                    <div class="stat-label"><?php echo __('investor.capacity'); ?></div>
                </div>
            </div>

            <div class="interests-section">
                <h2><?php echo __('investor.my_interests'); ?></h2>
                <?php if (empty($interests)): ?>
                    <p><?php echo __('investor.no_interests'); ?></p>
                    <a href="/marketplace" class="btn btn-primary"><?php echo __('investor.browse_projects'); ?></a>
                <?php else: ?>
                    <div class="interests-list">
                        <?php foreach ($interests as $interest): ?>
                            <div class="interest-card">
                                <h3><?php echo htmlspecialchars($interest['title']); ?></h3>
                                <p class="location"><?php echo htmlspecialchars($interest['city']); ?>, <?php echo htmlspecialchars($interest['country']); ?></p>
                                <div class="interest-details">
                                    <span><?php echo __('project.funding_sought'); ?>: <?php echo number_format($interest['funding_sought']); ?> $</span>
                                    <span><?php echo __('project.roi'); ?>: <?php echo $interest['roi']; ?>%</span>
                                </div>
                                <div class="interest-status">
                                    <span class="status status-<?php echo $interest['status']; ?>">
                                        <?php echo __('investor.status_' . $interest['status']); ?>
                                    </span>
                                </div>
                                <div class="interest-actions">
                                    <a href="/investor/data-room/<?php echo $interest['project_id']; ?>" class="btn btn-sm">
                                        <?php echo __('investor.data_room'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
