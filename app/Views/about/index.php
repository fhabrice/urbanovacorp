<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?php echo __('about.title'); ?></h1>
        <p><?php echo __('about.subtitle'); ?></p>
    </div>
</div>

<div class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="mission-vision">
                <div class="card">
                    <h2><?php echo __('about.mission'); ?></h2>
                    <p><?php echo __('about.mission_text'); ?></p>
                </div>
                <div class="card">
                    <h2><?php echo __('about.vision'); ?></h2>
                    <p><?php echo __('about.vision_text'); ?></p>
                </div>
            </div>

            <div class="values-section">
                <h2><?php echo __('about.values'); ?></h2>
                <div class="values-grid">
                    <div class="value-card">
                        <i class="fas fa-star"></i>
                        <h3><?php echo __('about.value_excellence'); ?></h3>
                        <p><?php echo __('about.value_excellence_text'); ?></p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3><?php echo __('about.value_integrity'); ?></h3>
                        <p><?php echo __('about.value_integrity_text'); ?></p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-lightbulb"></i>
                        <h3><?php echo __('about.value_innovation'); ?></h3>
                        <p><?php echo __('about.value_innovation_text'); ?></p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-leaf"></i>
                        <h3><?php echo __('about.value_sustainability'); ?></h3>
                        <p><?php echo __('about.value_sustainability_text'); ?></p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-hand-holding-heart"></i>
                        <h3><?php echo __('about.value_impact'); ?></h3>
                        <p><?php echo __('about.value_impact_text'); ?></p>
                    </div>
                </div>
            </div>

            <div class="certifications">
                <h2><?php echo __('about.certifications'); ?></h2>
                <div class="cert-badges">
                    <div class="cert-badge">ISO 9001</div>
                    <div class="cert-badge">ISO 14001</div>
                    <div class="cert-badge">ISO 45001</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
