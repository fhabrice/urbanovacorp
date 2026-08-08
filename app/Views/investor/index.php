<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container investor-page">
    <div class="page-header">
        <h1><?php echo __('investor.dashboard'); ?></h1>
        <?php if (!empty($_SESSION['user_name'])): ?>
            <p class="welcome-text"><?php echo __('investor.welcome'); ?>, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
        <?php endif; ?>
    </div>

    <div class="investor-grid">
        <aside class="investor-sidebar">
            <div class="profile-card">
                <h2><?php echo __('investor.my_profile'); ?></h2>
                <p><?php echo htmlspecialchars($_SESSION['user_name'] ?? __('investor.investor')); ?></p>
                <p class="profile-role"><?php echo __('investor.' . ($investor['type'] ?? 'individual')); ?></p>
                <span class="status status-<?php echo $investor['investor_status']; ?>">
                    <?php echo __('investor.status_' . $investor['investor_status']); ?>
                </span>
            </div>

            <nav class="investor-nav">
                <a href="/investor"><?php echo __('investor.dashboard'); ?></a>
                <a href="/investor/profile"><?php echo __('investor.profile_title'); ?></a>
                <a href="/investor/kyc"><?php echo __('investor.kyc_title'); ?></a>
                <a href="/investor/messages"><?php echo __('investor.messages_title'); ?></a>
                <a href="/marketplace"><?php echo __('nav.marketplace'); ?></a>
                <a href="/logout"><?php echo __('nav.logout'); ?></a>
            </nav>
        </aside>

        <section class="investor-main">
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
                <?php
                    $totalInterestAmount = 0;
                    foreach ($interests as $interest) {
                        $totalInterestAmount += (float) $interest['investment_amount'];
                    }
                ?>
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($interests); ?></div>
                        <div class="stat-label"><?php echo __('investor.interests_count'); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($investor['investment_capacity'] ?? 0, 0, '', ' '); ?> $</div>
                        <div class="stat-label"><?php echo __('investor.capacity'); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($totalInterestAmount, 0, '', ' '); ?> $</div>
                        <div class="stat-label"><?php echo __('investor.investment_total'); ?></div>
                    </div>
                </div>

                <div class="investor-summary">
                    <h2><?php echo __('investor.profile_summary'); ?></h2>
                    <div class="summary-grid">
                        <div><strong><?php echo __('investor.type'); ?>:</strong> <?php echo __('investor.' . ($investor['type'] ?? 'individual')); ?></div>
                        <div><strong><?php echo __('investor.capacity'); ?>:</strong> <?php echo number_format($investor['investment_capacity'] ?? 0, 0, '', ' '); ?> $</div>
                        <div><strong><?php echo __('investor.investment_sectors'); ?>:</strong> <?php echo htmlspecialchars($investor['investment_sectors'] ?? '-'); ?></div>
                        <div><strong><?php echo __('investor.risk_profile'); ?>:</strong> <?php echo htmlspecialchars($investor['risk_profile'] ?? '-'); ?></div>
                        <div><strong><?php echo __('investor.location'); ?>:</strong> <?php echo htmlspecialchars($investor['city'] ?? '-') . ', ' . htmlspecialchars($investor['country'] ?? '-'); ?></div>
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
                                    <div class="interest-details">
                                        <span><?php echo __('investor.investment_amount'); ?>: <?php echo number_format($interest['investment_amount'], 0, '', ' '); ?> $</span>
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

                <div class="favorites-section">
                    <h2><?php echo __('investor.my_favorites'); ?></h2>
                    <?php if (empty($favorites)): ?>
                        <p><?php echo __('investor.no_favorites'); ?></p>
                        <a href="/marketplace" class="btn btn-primary"><?php echo __('investor.browse_projects'); ?></a>
                    <?php else: ?>
                        <div class="favorites-list">
                            <?php foreach ($favorites as $favorite): ?>
                                <div class="favorite-card">
                                    <h3><?php echo htmlspecialchars($favorite['title']); ?></h3>
                                    <p class="location"><?php echo htmlspecialchars($favorite['city']); ?>, <?php echo htmlspecialchars($favorite['country']); ?></p>
                                    <div class="favorite-details">
                                        <span><?php echo __('project.funding_sought'); ?>: <?php echo number_format($favorite['funding_sought']); ?> $</span>
                                        <span><?php echo __('project.roi'); ?>: <?php echo $favorite['roi']; ?>%</span>
                                    </div>
                                    <div class="favorite-actions">
                                        <a href="/marketplace/project/<?php echo $favorite['project_id']; ?>" class="btn btn-sm">
                                            <?php echo __('investor.view_project'); ?>
                                        </a>
                                        <a href="/investor/favorites/<?php echo $favorite['project_id']; ?>/remove" class="btn btn-sm btn-danger">
                                            <?php echo __('investor.remove_favorite'); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
