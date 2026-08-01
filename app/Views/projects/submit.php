<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1><?php echo __('project.submit_title'); ?></h1>
        <p><?php echo __('project.submit_description'); ?></p>
    </div>

    <div class="project-submit-container">
        <form method="POST" action="/projects/submit" enctype="multipart/form-data">
            <!-- Basic Information -->
            <div class="form-section">
                <h2><?php echo __('project.basic_info'); ?></h2>
                
                <div class="form-group">
                    <label for="title"><?php echo __('project.name'); ?> *</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="description"><?php echo __('project.description'); ?> *</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>

                <div class="form-group">
                    <label for="type"><?php echo __('project.type'); ?> *</label>
                    <select id="type" name="type" required>
                        <option value="residential"><?php echo __('project.type_residential'); ?></option>
                        <option value="commercial"><?php echo __('project.type_commercial'); ?></option>
                        <option value="mixed_use"><?php echo __('project.type_mixed_use'); ?></option>
                        <option value="infrastructure"><?php echo __('project.type_infrastructure'); ?></option>
                        <option value="industrial"><?php echo __('project.type_industrial'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sector"><?php echo __('project.sector'); ?></label>
                    <input type="text" id="sector" name="sector">
                </div>
            </div>

            <!-- Location -->
            <div class="form-section">
                <h2><?php echo __('project.location'); ?></h2>
                
                <div class="form-group">
                    <label for="country"><?php echo __('project.country'); ?> *</label>
                    <input type="text" id="country" name="country" required>
                </div>

                <div class="form-group">
                    <label for="city"><?php echo __('project.city'); ?> *</label>
                    <input type="text" id="city" name="city" required>
                </div>

                <div class="form-group">
                    <label for="address"><?php echo __('project.address'); ?></label>
                    <textarea id="address" name="address" rows="2"></textarea>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="form-section">
                <h2><?php echo __('project.financial_info'); ?></h2>
                
                <div class="form-group">
                    <label for="total_cost"><?php echo __('project.total_cost'); ?> (USD) *</label>
                    <input type="number" id="total_cost" name="total_cost" required>
                </div>

                <div class="form-group">
                    <label for="equity_contribution"><?php echo __('project.equity_contribution'); ?> (USD)</label>
                    <input type="number" id="equity_contribution" name="equity_contribution">
                </div>

                <div class="form-group">
                    <label for="funding_sought"><?php echo __('project.funding_sought'); ?> (USD) *</label>
                    <input type="number" id="funding_sought" name="funding_sought" required>
                </div>

                <div class="form-group">
                    <label for="roi"><?php echo __('project.roi'); ?> (%)</label>
                    <input type="number" id="roi" name="roi" step="0.01">
                </div>

                <div class="form-group">
                    <label for="tri"><?php echo __('project.tri'); ?> (%)</label>
                    <input type="number" id="tri" name="tri" step="0.01">
                </div>

                <div class="form-group">
                    <label for="payback_period"><?php echo __('project.payback_period'); ?></label>
                    <input type="number" id="payback_period" name="payback_period">
                </div>

                <div class="form-group">
                    <label for="project_duration"><?php echo __('project.project_duration'); ?></label>
                    <input type="number" id="project_duration" name="project_duration">
                </div>
            </div>

            <!-- Impact -->
            <div class="form-section">
                <h2>Impact</h2>
                
                <div class="form-group">
                    <label for="housing_units"><?php echo __('project.housing_units'); ?></label>
                    <input type="number" id="housing_units" name="housing_units" value="0">
                </div>

                <div class="form-group">
                    <label for="jobs_created"><?php echo __('project.jobs_created'); ?></label>
                    <input type="number" id="jobs_created" name="jobs_created" value="0">
                </div>
            </div>

            <!-- Image -->
            <div class="form-section">
                <h2><?php echo __('project.upload_image'); ?></h2>
                
                <div class="form-group">
                    <label for="project_image">Image du projet (JPG, PNG)</label>
                    <input type="file" id="project_image" name="project_image" accept=".jpg,.jpeg,.png">
                </div>
            </div>

            <!-- Documents -->
            <div class="form-section">
                <h2><?php echo __('project.upload_documents'); ?></h2>
                
                <div class="form-group">
                    <label for="business_plan"><?php echo __('project.business_plan'); ?> (PDF, DOC)</label>
                    <input type="file" id="business_plan" name="business_plan" accept=".pdf,.doc,.docx">
                </div>

                <div class="form-group">
                    <label for="pitch_deck"><?php echo __('project.pitch_deck'); ?> (PDF, PPT)</label>
                    <input type="file" id="pitch_deck" name="pitch_deck" accept=".pdf,.ppt,.pptx">
                </div>

                <div class="form-group">
                    <label for="financial_model"><?php echo __('project.financial_model'); ?> (PDF, XLS)</label>
                    <input type="file" id="financial_model" name="financial_model" accept=".pdf,.xls,.xlsx">
                </div>

                <div class="form-group">
                    <label for="feasibility_study"><?php echo __('project.feasibility_study'); ?> (PDF)</label>
                    <input type="file" id="feasibility_study" name="feasibility_study" accept=".pdf">
                </div>

                <div class="form-group">
                    <label for="land_title"><?php echo __('project.land_title'); ?> (PDF)</label>
                    <input type="file" id="land_title" name="land_title" accept=".pdf">
                </div>

                <div class="form-group">
                    <label for="plans"><?php echo __('project.plans'); ?> (PDF)</label>
                    <input type="file" id="plans" name="plans" accept=".pdf">
                </div>

                <div class="form-group">
                    <label for="permits"><?php echo __('project.permits'); ?> (PDF)</label>
                    <input type="file" id="permits" name="permits" accept=".pdf">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo __('project.submit'); ?></button>
        </form>
    </div>
</div>

<style>
.project-submit-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-section {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.form-section h2 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--secondary-color);
    padding-bottom: 0.5rem;
}
</style>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
