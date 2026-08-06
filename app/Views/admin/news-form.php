<?php ob_start(); ?>

<div class="page-header">
    <h1><?php echo $newsItem ? __('admin.edit_news') : __('admin.create_news'); ?></h1>
</div>

<form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="form-group">
        <label for="title"><?php echo __('admin.title'); ?></label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($newsItem['title'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="excerpt"><?php echo __('admin.excerpt'); ?></label>
        <textarea id="excerpt" name="excerpt"><?php echo htmlspecialchars($newsItem['excerpt'] ?? ''); ?></textarea>
    </div>

    <div class="form-group">
        <label for="content"><?php echo __('admin.content'); ?></label>
        <textarea id="content" name="content" rows="8"><?php echo htmlspecialchars($newsItem['content'] ?? ''); ?></textarea>
    </div>

    <div class="form-group">
        <label for="category"><?php echo __('admin.category'); ?></label>
        <select id="category" name="category" required>
            <option value="entreprise" <?php echo (isset($newsItem['category']) && $newsItem['category'] === 'entreprise') ? 'selected' : ''; ?>><?php echo __('admin.category_enterprise'); ?></option>
            <option value="projets" <?php echo (isset($newsItem['category']) && $newsItem['category'] === 'projets') ? 'selected' : ''; ?>><?php echo __('admin.category_projects'); ?></option>
            <option value="marché" <?php echo (isset($newsItem['category']) && $newsItem['category'] === 'marché') ? 'selected' : ''; ?>><?php echo __('admin.category_market'); ?></option>
            <option value="partenariats" <?php echo (isset($newsItem['category']) && $newsItem['category'] === 'partenariats') ? 'selected' : ''; ?>><?php echo __('admin.category_partnerships'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="image"><?php echo __('admin.image'); ?></label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($newsItem['image'])): ?>
            <div class="preview">
                <img id="imagePreview" src="<?php echo htmlspecialchars($newsItem['image']); ?>" alt="<?php echo htmlspecialchars($newsItem['title']); ?>">
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group-inline">
        <div>
            <label for="status"><?php echo __('admin.status'); ?></label>
            <select id="status" name="status">
                <option value="draft" <?php echo (isset($newsItem['status']) && $newsItem['status'] === 'draft') ? 'selected' : ''; ?>><?php echo __('admin.draft'); ?></option>
                <option value="published" <?php echo (isset($newsItem['status']) && $newsItem['status'] === 'published') ? 'selected' : ''; ?>><?php echo __('admin.published'); ?></option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><?php echo $newsItem ? __('admin.update_news') : __('admin.save_news'); ?></button>
</form>

<style>
.admin-form {
    max-width: 800px;
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.preview {
    margin-top: 1rem;
}

.preview img {
    max-width: 240px;
    border-radius: 6px;
    border: 1px solid #eee;
}

.form-group-inline {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    margin-bottom: 1.25rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.25rem;
    border: none;
    border-radius: 4px;
    color: white;
    background-color: var(--primary-color);
    cursor: pointer;
}
</style>

<?php $content = ob_get_clean(); require_once APP_PATH . '/Views/layouts/admin-layout.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const input = document.getElementById('image');
    const previewContainer = document.querySelector('.preview');
    let previewImg = document.getElementById('imagePreview');

    if (!previewContainer) {
        // create preview container
        const div = document.createElement('div');
        div.className = 'preview';
        input.parentNode.appendChild(div);
        previewImg = document.createElement('img');
        previewImg.id = 'imagePreview';
        div.appendChild(previewImg);
    }

    input.addEventListener('change', function(e){
        const file = e.target.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function(ev){
            document.getElementById('imagePreview').src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });
});
</script>