<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?php echo __('governance.title'); ?></h1>
        <p><?php echo __('governance.subtitle'); ?></p>
    </div>
</div>

<div class="governance-section">
    <div class="container">
        <div class="governance-content">
            <div class="governance-text">
                <div class="card">
                    <h2><?php echo __('governance.structure'); ?></h2>
                    <p><?php echo __('governance.structure_text'); ?></p>
                </div>

                <div class="card">
                    <h2><?php echo __('governance.committee'); ?></h2>
                    <p><?php echo __('governance.committee_text'); ?></p>
                </div>

                <div class="card">
                    <h2><?php echo __('governance.transparency'); ?></h2>
                    <p><?php echo __('governance.transparency_text'); ?></p>
                </div>
            </div>

            <div class="org-chart">
                <h2><?php echo __('governance.org_chart'); ?></h2>
                <div class="chart-container">
                    <div class="chart-level level-1">
                        <div class="chart-box ceo">
                            <div class="box-title"><?php echo __('governance.ceo'); ?></div>
                            <div class="box-name">Directeur Général</div>
                        </div>
                    </div>
                    
                    <div class="chart-level level-2">
                        <div class="chart-box">
                            <div class="box-title"><?php echo __('governance.operations'); ?></div>
                            <div class="box-name">Directeur Opérations</div>
                        </div>
                        <div class="chart-box">
                            <div class="box-title"><?php echo __('governance.finance'); ?></div>
                            <div class="box-name">Directeur Financier</div>
                        </div>
                        <div class="chart-box">
                            <div class="box-title"><?php echo __('governance.technical'); ?></div>
                            <div class="box-name">Directeur Technique</div>
                        </div>
                    </div>
                    
                    <div class="chart-level level-3">
                        <div class="chart-box">
                            <div class="box-title"><?php echo __('governance.projects'); ?></div>
                            <div class="box-name">Chef de Projets</div>
                        </div>
                        <div class="chart-box">
                            <div class="box-title"><?php echo __('governance.human_resources'); ?></div>
                            <div class="box-name">RH</div>
                        </div>
                        <div class="chart-box">
                            <div class="box-title"><?php echo __('governance.commercial'); ?></div>
                            <div class="box-name">Commercial</div>
                        </div>
                        <div class="chart-box">
                            <div class="box-title"><?php echo __('governance.legal'); ?></div>
                            <div class="box-name">Juridique</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.governance-section {
    padding: 3rem 0;
}

.governance-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
}

.governance-text {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.org-chart {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chart-container {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    align-items: center;
}

.chart-level {
    display: flex;
    gap: 1rem;
    justify-content: center;
    width: 100%;
}

.chart-box {
    background-color: var(--primary-color);
    color: white;
    padding: 1rem;
    border-radius: 4px;
    text-align: center;
    min-width: 150px;
}

.chart-box.ceo {
    background-color: var(--secondary-color);
}

.box-title {
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.box-name {
    font-size: 0.875rem;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .governance-content {
        grid-template-columns: 1fr;
    }
    
    .chart-level {
        flex-direction: column;
    }
    
    .chart-box {
        width: 100%;
    }
}
</style>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
