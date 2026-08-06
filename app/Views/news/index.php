<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?php echo __('news.title'); ?></h1>
        <p><?php echo __('news.subtitle'); ?></p>
    </div>
</div>

<div class="news-list container">
    <?php if (empty($newsItems)): ?>
        <div class="no-data">
            <p><?php echo __('news.no_items'); ?></p>
        </div>
    <?php else: ?>
        <div class="news-grid">
            <?php foreach ($newsItems as $item): ?>
                <article class="news-card">
                    <?php if (!empty($item['image'])): ?>
                        <div class="news-image">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="news-body">
                        <h2><a href="/urbanovacorp/?route=news/show-<?php echo urlencode($item['slug']); ?>"><?php echo htmlspecialchars($item['title']); ?></a></h2>
                        <p class="news-meta"><?php echo htmlspecialchars($item['category']); ?> · <?php echo date('d/m/Y', strtotime($item['published_at'])); ?></p>
                        <p><?php echo htmlspecialchars($item['excerpt']); ?></p>
                        <a href="/urbanovacorp/?route=news/show-<?php echo urlencode($item['slug']); ?>" class="btn btn-secondary"><?php echo __('news.read_more'); ?></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
