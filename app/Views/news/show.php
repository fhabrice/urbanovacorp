<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
        <p class="news-meta"><?php echo htmlspecialchars($article['category']); ?> · <?php echo date('d/m/Y', strtotime($article['published_at'])); ?></p>
    </div>
</div>

<div class="news-detail container">
    <?php if (!empty($article['image'])): ?>
        <div class="detail-image">
            <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
        </div>
    <?php endif; ?>
    <div class="detail-content">
        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
