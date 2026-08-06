<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container investor-data-room">
    <div class="page-header">
        <h1><?php echo htmlspecialchars($project['title']); ?></h1>
        <p><?php echo htmlspecialchars($project['city']); ?>, <?php echo htmlspecialchars($project['country']); ?></p>
    </div>

    <div class="data-room-grid">
        <aside class="project-sidebar">
            <div class="project-summary-card">
                <h2><?php echo __('project.overview'); ?></h2>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                <div class="project-meta">
                    <div><strong><?php echo __('investor.project_sector'); ?>:</strong> <?php echo htmlspecialchars($project['sector'] ?? '-'); ?></div>
                    <div><strong><?php echo __('project.funding_sought'); ?>:</strong> <?php echo number_format($project['funding_sought']); ?> $</div>
                    <div><strong><?php echo __('project.funding_mobilized'); ?>:</strong> <?php echo number_format($project['funding_mobilized']); ?> $</div>
                    <div><strong><?php echo __('project.roi'); ?>:</strong> <?php echo $project['roi']; ?>%</div>
                    <div><strong><?php echo __('project.tri'); ?>:</strong> <?php echo $project['tri']; ?>%</div>
                    <div><strong><?php echo __('investor.payback_period'); ?>:</strong> <?php echo htmlspecialchars($project['payback_period'] ?? '-'); ?> <?php echo __('investor.months'); ?></div>
                </div>
            </div>
        </aside>

        <main class="project-main">
            <section class="documents-section">
                <h2><?php echo __('investor.documents'); ?></h2>
                <?php if (empty($documents)): ?>
                    <p><?php echo __('investor.no_documents'); ?></p>
                <?php else: ?>
                    <div class="documents-list">
                        <?php foreach ($documents as $document): ?>
                            <div class="document-card">
                                <div class="document-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="document-info">
                                    <h3><?php echo htmlspecialchars($document['title']); ?></h3>
                                    <p class="document-type"><?php echo __('investor.doc_type_' . $document['document_type']); ?></p>
                                </div>
                                <div class="document-actions">
                                    <a href="/uploads/documents/<?php echo htmlspecialchars($document['file_path']); ?>" 
                                       class="btn btn-sm" target="_blank">
                                        <i class="fas fa-download"></i> <?php echo __('investor.download'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="interest-section">
                <h2><?php echo __('investor.express_interest'); ?></h2>
                <?php if ($interest): ?>
                    <div class="interest-card status-card">
                        <div><strong><?php echo __('investor.investment_amount'); ?>:</strong> <?php echo number_format($interest['investment_amount'] ?? 0, 0, '', ' '); ?> $</div>
                        <div><strong><?php echo __('investor.investment_status'); ?>:</strong> <?php echo __('investor.status_' . $interest['status']); ?></div>
                        <div><strong><?php echo __('investor.message'); ?>:</strong> <?php echo nl2br(htmlspecialchars($interest['message'] ?? '-')); ?></div>
                        <?php if ($interest['nda_signed']): ?>
                            <div class="alert alert-success"><?php echo __('investor.nda_signed'); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$interest || !$interest['nda_signed']): ?>
                    <form method="POST" action="/investor/interest/<?php echo $project['id']; ?>">
                        <div class="form-group">
                            <label><?php echo __('investor.investment_amount'); ?> (USD)</label>
                            <input type="number" name="investment_amount" 
                                   value="<?php echo htmlspecialchars($interest['investment_amount'] ?? ''); ?>"
                                   placeholder="Ex: 50000" required>
                        </div>
                        <div class="form-group">
                            <label><?php echo __('investor.message'); ?></label>
                            <textarea name="message" rows="4" 
                                      placeholder="<?php echo __('investor.message_placeholder'); ?>"><?php echo htmlspecialchars($interest['message'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo __('investor.send'); ?></button>
                    </form>
                <?php endif; ?>
            </section>
        </main>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
