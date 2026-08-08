<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?php echo __('marketplace.title'); ?></h1>
        <p><?php echo __('marketplace.description'); ?></p>
    </div>
</div>

<div class="marketplace-container">
    <div class="container">
        <!-- Debug info -->
        <div class="alert alert-info" style="margin-bottom: 2rem;">
            <strong>Debug Info:</strong> 
            <?php 
            error_log("Marketplace view loaded");
            error_log("Projects: " . (isset($projects) ? 'set, count=' . count($projects) : 'NOT set'));
            error_log("Filters: " . print_r($filters ?? [], true));
            error_log("FilterOptions: " . print_r($filterOptions ?? [], true));
            ?>
            <?php if (isset($projects)): ?>
                Projects array exists. Count: <?php echo count($projects); ?>
            <?php else: ?>
                Projects array is NOT set
            <?php endif; ?>
        </div>

        <div class="marketplace-layout">
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <div class="filter-toggle">
                    <button id="toggleFilters" class="btn btn-outline btn-sm">
                        <i class="fas fa-filter"></i> <?php echo __('marketplace.show_filters'); ?>
                    </button>
                </div>

                <div class="filters-content" id="filtersContent">
                    <h3><?php echo __('marketplace.filter'); ?></h3>
                    
                    <form method="GET" action="/marketplace">
                        <div class="form-group">
                            <label><?php echo __('project.country'); ?></label>
                            <select name="country">
                                <option value=""><?php echo __('marketplace.filter'); ?></option>
                                <?php if (!empty($filterOptions['countries'])): ?>
                                    <?php foreach ($filterOptions['countries'] as $country): ?>
                                        <option value="<?php echo htmlspecialchars($country['country']); ?>" 
                                                <?php echo isset($filters['country']) && $filters['country'] === $country['country'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['country']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo __('project.city'); ?></label>
                            <select name="city">
                                <option value=""><?php echo __('marketplace.filter'); ?></option>
                                <?php if (!empty($filterOptions['cities'])): ?>
                                    <?php foreach ($filterOptions['cities'] as $city): ?>
                                        <option value="<?php echo htmlspecialchars($city['city']); ?>"
                                                <?php echo isset($filters['city']) && $filters['city'] === $city['city'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($city['city']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo __('project.sector'); ?></label>
                            <select name="sector">
                                <option value=""><?php echo __('marketplace.filter'); ?></option>
                                <?php if (!empty($filterOptions['sectors'])): ?>
                                    <?php foreach ($filterOptions['sectors'] as $sector): ?>
                                        <option value="<?php echo htmlspecialchars($sector['sector']); ?>"
                                                <?php echo isset($filters['sector']) && $filters['sector'] === $sector['sector'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($sector['sector']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo __('project.type'); ?></label>
                            <select name="type">
                                <option value=""><?php echo __('marketplace.filter'); ?></option>
                                <option value="residential" <?php echo isset($filters['type']) && $filters['type'] === 'residential' ? 'selected' : ''; ?>>
                                    <?php echo __('project.type_residential'); ?>
                                </option>
                                <option value="commercial" <?php echo isset($filters['type']) && $filters['type'] === 'commercial' ? 'selected' : ''; ?>>
                                    <?php echo __('project.type_commercial'); ?>
                                </option>
                                <option value="mixed_use" <?php echo isset($filters['type']) && $filters['type'] === 'mixed_use' ? 'selected' : ''; ?>>
                                    <?php echo __('project.type_mixed_use'); ?>
                                </option>
                                <option value="infrastructure" <?php echo isset($filters['type']) && $filters['type'] === 'infrastructure' ? 'selected' : ''; ?>>
                                    <?php echo __('project.type_infrastructure'); ?>
                                </option>
                                <option value="industrial" <?php echo isset($filters['type']) && $filters['type'] === 'industrial' ? 'selected' : ''; ?>>
                                    <?php echo __('project.type_industrial'); ?>
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo __('marketplace.funding_range'); ?> (USD)</label>
                            <div class="range-inputs">
                                <input type="number" name="min_funding" placeholder="<?php echo __('marketplace.min_funding'); ?>"
                                       value="<?php echo htmlspecialchars($filters['min_funding'] ?? ''); ?>">
                                <input type="number" name="max_funding" placeholder="<?php echo __('marketplace.max_funding'); ?>"
                                       value="<?php echo htmlspecialchars($filters['max_funding'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo __('marketplace.min_roi'); ?> (%)</label>
                            <input type="number" name="min_roi" step="0.1"
                                   value="<?php echo htmlspecialchars($filters['min_roi'] ?? ''); ?>">
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary"><?php echo __('marketplace.apply_filters'); ?></button>
                            <a href="/marketplace" class="btn btn-outline"><?php echo __('marketplace.reset'); ?></a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Projects Grid -->
            <main class="projects-main">
                <?php if (!isset($projects) || empty($projects)): ?>
                    <div class="no-projects">
                        <p><?php echo __('marketplace.no_projects'); ?></p>
                        <a href="/" class="btn btn-primary"><?php echo __('nav.home'); ?></a>
                    </div>
                <?php else: ?>
                    <div class="projects-grid">
                        <?php foreach ($projects as $project): ?>
                            <div class="project-card">
                                <?php if ($project['image']): ?>
                                    <img src="/uploads/projects/<?php echo htmlspecialchars($project['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($project['title']); ?>">
                                <?php else: ?>
                                    <div class="project-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="project-content">
                                    <div class="project-type">
                                        <span class="badge badge-<?php echo $project['type']; ?>">
                                            <?php echo __('project.type_' . $project['type']); ?>
                                        </span>
                                    </div>
                                    
                                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                                    
                                    <p class="location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($project['city']); ?>, <?php echo htmlspecialchars($project['country']); ?>
                                    </p>
                                    
                                    <p class="description"><?php echo substr(htmlspecialchars($project['description']), 0, 150); ?>...</p>
                                    
                                    <div class="project-stats">
                                        <div class="stat">
                                            <span class="label"><?php echo __('project.funding_sought'); ?>:</span>
                                            <span class="value"><?php echo number_format($project['funding_sought']); ?> $</span>
                                        </div>
                                        <div class="stat">
                                            <span class="label"><?php echo __('project.roi'); ?>:</span>
                                            <span class="value"><?php echo $project['roi']; ?>%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo min(100, ($project['funding_mobilized'] / $project['funding_sought']) * 100); ?>%"></div>
                                        </div>
                                        <div class="progress-text">
                                            <?php echo number_format($project['funding_mobilized']); ?> $ <?php echo __('marketplace.of'); ?> <?php echo number_format($project['funding_sought']); ?> $
                                        </div>
                                    </div>
                                    
                                    <div class="project-actions">
                                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'investor'): ?>
                                            <a href="/investor/favorites/<?php echo $project['id']; ?>/add" class="btn btn-outline btn-sm" title="<?php echo __('investor.add_to_favorites'); ?>">
                                                <i class="fas fa-heart"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="/marketplace/<?php echo $project['id']; ?>" class="btn btn-primary">
                                            <?php echo __('marketplace.view_details'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>

<style>
.marketplace-container {
    padding: 2rem 0;
}

.marketplace-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
}

.filters-sidebar {
    background-color: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: fit-content;
}

.filter-toggle {
    margin-bottom: 1rem;
}

.filters-content h3 {
    margin-bottom: 1.5rem;
    color: var(--primary-color);
}

.range-inputs {
    display: flex;
    gap: 0.5rem;
}

.range-inputs input {
    width: 50%;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.filter-actions .btn {
    flex: 1;
}

.projects-main {
    min-height: 500px;
}

.no-projects {
    text-align: center;
    padding: 3rem;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.project-placeholder {
    width: 100%;
    height: 200px;
    background-color: var(--light-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--muted-color);
}

.project-type {
    margin-bottom: 0.5rem;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.badge-residential {
    background-color: #3498db;
    color: white;
}

.badge-commercial {
    background-color: #e74c3c;
    color: white;
}

.badge-mixed_use {
    background-color: #9b59b6;
    color: white;
}

.badge-infrastructure {
    background-color: #f39c12;
    color: white;
}

.badge-industrial {
    background-color: #34495e;
    color: white;
}

.project-content .location {
    color: var(--muted-color);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.project-content .description {
    color: var(--text-color);
    margin-bottom: 1rem;
    line-height: 1.4;
}

.project-progress {
    margin: 1rem 0;
}

.progress-bar {
    height: 8px;
    background-color: var(--light-color);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background-color: var(--success-color);
    transition: width 0.3s;
}

.progress-text {
    font-size: 0.875rem;
    color: var(--muted-color);
}

.project-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.project-actions .btn {
    flex: 1;
}

@media (max-width: 768px) {
    .marketplace-layout {
        grid-template-columns: 1fr;
    }
    
    .filters-content {
        display: none;
    }
    
    .filters-content.active {
        display: block;
    }
}
</style>

<script>
const toggleFilters = document.getElementById('toggleFilters');
if (toggleFilters) {
    toggleFilters.addEventListener('click', function() {
        const filtersContent = document.getElementById('filtersContent');
        filtersContent.classList.toggle('active');
        
        if (filtersContent.classList.contains('active')) {
            this.innerHTML = '<i class="fas fa-filter"></i> <?php echo __('marketplace.hide_filters'); ?>';
        } else {
            this.innerHTML = '<i class="fas fa-filter"></i> <?php echo __('marketplace.show_filters'); ?>';
        }
    });
}
</script>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
