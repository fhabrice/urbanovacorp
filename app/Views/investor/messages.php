<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container investor-messages-page">
    <div class="page-header">
        <h1><?php echo __('investor.messages_title'); ?></h1>
        <p><?php echo __('investor.messages_description'); ?></p>
    </div>

    <div class="messages-layout">
        <!-- Send Message Form -->
        <aside class="message-form-section">
            <div class="card">
                <h2><?php echo __('investor.send_message'); ?></h2>
                <form method="POST" action="/investor/messages/send">
                    <div class="form-group">
                        <label><?php echo __('investor.message_subject'); ?></label>
                        <input type="text" name="subject" required placeholder="<?php echo __('investor.message_subject_placeholder'); ?>">
                    </div>
                    <div class="form-group">
                        <label><?php echo __('investor.message_content'); ?></label>
                        <textarea name="message" rows="6" required placeholder="<?php echo __('investor.message_content_placeholder'); ?>"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo __('investor.send'); ?></button>
                </form>
            </div>
        </aside>

        <!-- Messages List -->
        <main class="messages-list-section">
            <h2><?php echo __('investor.message_history'); ?></h2>
            <?php if (empty($messages)): ?>
                <div class="no-messages">
                    <p><?php echo __('investor.no_messages'); ?></p>
                </div>
            <?php else: ?>
                <div class="messages-list">
                    <?php foreach ($messages as $message): ?>
                        <div class="message-card status-<?php echo $message['status']; ?>">
                            <div class="message-header">
                                <h3><?php echo htmlspecialchars($message['subject']); ?></h3>
                                <span class="message-date"><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></span>
                            </div>
                            <div class="message-body">
                                <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                            </div>
                            <?php if ($message['admin_reply']): ?>
                                <div class="message-reply">
                                    <div class="reply-header">
                                        <strong><?php echo __('investor.admin_reply'); ?></strong>
                                        <?php if ($message['admin_first_name']): ?>
                                            <span>- <?php echo htmlspecialchars($message['admin_first_name'] . ' ' . $message['admin_last_name']); ?></span>
                                        <?php endif; ?>
                                        <span class="reply-date"><?php echo date('d/m/Y H:i', strtotime($message['admin_replied_at'])); ?></span>
                                    </div>
                                    <div class="reply-body">
                                        <p><?php echo nl2br(htmlspecialchars($message['admin_reply'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="message-status">
                                <span class="status-badge status-<?php echo $message['status']; ?>">
                                    <?php echo __('investor.message_status_' . $message['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<style>
.investor-messages-page {
    padding: 2rem 0;
}

.messages-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 2rem;
}

.message-form-section .card {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.message-form-section .card h2 {
    margin-bottom: 1.5rem;
    color: var(--primary-color);
}

.messages-list-section h2 {
    margin-bottom: 1.5rem;
    color: var(--primary-color);
}

.no-messages {
    text-align: center;
    padding: 3rem;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.messages-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message-card {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left: 4px solid var(--muted-color);
}

.message-card.status-unread {
    border-left-color: var(--primary-color);
}

.message-card.status-replied {
    border-left-color: var(--success-color);
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.message-header h3 {
    margin: 0;
    font-size: 1.1rem;
}

.message-date {
    color: var(--muted-color);
    font-size: 0.875rem;
}

.message-body {
    margin-bottom: 1rem;
    color: var(--text-color);
    line-height: 1.5;
}

.message-reply {
    background-color: var(--light-color);
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.reply-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.reply-date {
    color: var(--muted-color);
    font-size: 0.875rem;
}

.reply-body {
    color: var(--text-color);
    line-height: 1.5;
}

.message-status {
    display: flex;
    justify-content: flex-end;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.status-unread {
    background-color: var(--primary-color);
    color: white;
}

.status-badge.status-read {
    background-color: var(--muted-color);
    color: white;
}

.status-badge.status-replied {
    background-color: var(--success-color);
    color: white;
}

@media (max-width: 768px) {
    .messages-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
