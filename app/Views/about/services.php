<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?php echo __('services.title'); ?></h1>
        <p><?php echo __('services.subtitle'); ?></p>
    </div>
</div>

<div class="services-section">
    <div class="container">
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-hard-hat"></i>
                </div>
                <h2><?php echo __('services.construction'); ?></h2>
                <p><?php echo __('services.construction_desc'); ?></p>
                <ul>
                    <li><?php echo __('services.construction_point1'); ?></li>
                    <li><?php echo __('services.construction_point2'); ?></li>
                    <li><?php echo __('services.construction_point3'); ?></li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h2><?php echo __('services.real_estate'); ?></h2>
                <p><?php echo __('services.real_estate_desc'); ?></p>
                <ul>
                    <li><?php echo __('services.real_estate_point1'); ?></li>
                    <li><?php echo __('services.real_estate_point2'); ?></li>
                    <li><?php echo __('services.real_estate_point3'); ?></li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-drafting-compass"></i>
                </div>
                <h2><?php echo __('services.engineering'); ?></h2>
                <p><?php echo __('services.engineering_desc'); ?></p>
                <ul>
                    <li><?php echo __('services.engineering_point1'); ?></li>
                    <li><?php echo __('services.engineering_point2'); ?></li>
                    <li><?php echo __('services.engineering_point3'); ?></li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h2><?php echo __('services.facility'); ?></h2>
                <p><?php echo __('services.facility_desc'); ?></p>
                <ul>
                    <li><?php echo __('services.facility_point1'); ?></li>
                    <li><?php echo __('services.facility_point2'); ?></li>
                    <li><?php echo __('services.facility_point3'); ?></li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-recycle"></i>
                </div>
                <h2><?php echo __('services.sanitation'); ?></h2>
                <p><?php echo __('services.sanitation_desc'); ?></p>
                <ul>
                    <li><?php echo __('services.sanitation_point1'); ?></li>
                    <li><?php echo __('services.sanitation_point2'); ?></li>
                    <li><?php echo __('services.sanitation_point3'); ?></li>
                </ul>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h2><?php echo __('services.sustainability'); ?></h2>
                <p><?php echo __('services.sustainability_desc'); ?></p>
                <ul>
                    <li><?php echo __('services.sustainability_point1'); ?></li>
                    <li><?php echo __('services.sustainability_point2'); ?></li>
                    <li><?php echo __('services.sustainability_point3'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.services-section {
    padding: 3rem 0;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.service-card {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.service-icon {
    font-size: 3rem;
    color: var(--secondary-color);
    margin-bottom: 1rem;
}

.service-card h2 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.service-card p {
    color: var(--muted-color);
    margin-bottom: 1rem;
}

.service-card ul {
    list-style: none;
    padding-left: 0;
}

.service-card li {
    padding-left: 1.5rem;
    position: relative;
    margin-bottom: 0.5rem;
}

.service-card li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--success-color);
    font-weight: bold;
}
</style>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
