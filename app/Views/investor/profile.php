<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container investor-profile-page">
    <div class="page-header">
        <h1><?php echo __('investor.profile_title'); ?></h1>
        <p><?php echo __('investor.profile_description'); ?></p>
    </div>

    <form method="POST" action="/investor/profile">
        <div class="form-group">
            <label><?php echo __('investor.years_experience'); ?></label>
            <input type="number" name="years_experience" value="<?php echo htmlspecialchars($profile['years_experience'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><?php echo __('investor.projects_financed'); ?></label>
            <input type="number" name="projects_financed" value="<?php echo htmlspecialchars($profile['projects_financed'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><?php echo __('investor.presentation'); ?></label>
            <textarea name="presentation"><?php echo htmlspecialchars($profile['presentation'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label><?php echo __('investor.references_portfolio'); ?></label>
            <textarea name="references"><?php echo htmlspecialchars($profile['references_portfolio'] ?? ''); ?></textarea>
        </div>

        <h2><?php echo __('investor.preferences_title'); ?></h2>
        <div class="form-group">
            <label><?php echo __('investor.preferred_sectors'); ?></label>
            <select name="preferred_sectors[]" multiple>
                <?php $sectors = ['residential','commercial','industrial','infrastructure','smart_city']; foreach($sectors as $s): $sel = in_array($s, json_decode($preferences['preferred_sectors'] ?? '[]') ?: []) ? 'selected' : ''; ?>
                    <option value="<?php echo $s; ?>" <?php echo $sel; ?>><?php echo __('investor.sector_' . $s); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label><?php echo __('investor.preferred_countries'); ?></label>
            <input type="text" name="preferred_countries" value="<?php echo htmlspecialchars(implode(',', json_decode($preferences['preferred_countries'] ?? '[]') ?: [])); ?>" placeholder="RDC, Rwanda, Burundi">
        </div>
        <div class="form-group">
            <label><?php echo __('investor.investment_types'); ?></label>
            <select name="investment_types[]" multiple>
                <?php $types=['equity','debt','mixed']; foreach($types as $t): $sel = in_array($t, json_decode($preferences['investment_types'] ?? '[]') ?: []) ? 'selected' : ''; ?>
                    <option value="<?php echo $t; ?>" <?php echo $sel; ?>><?php echo __('investor.investment_type_' . $t); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label><?php echo __('investor.investment_min'); ?></label>
            <input type="number" step="0.01" name="investment_min" value="<?php echo htmlspecialchars($profile['investment_min'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><?php echo __('investor.investment_max'); ?></label>
            <input type="number" step="0.01" name="investment_max" value="<?php echo htmlspecialchars($profile['investment_max'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><?php echo __('investor.investment_horizon'); ?></label>
            <input type="text" name="investment_horizon" value="<?php echo htmlspecialchars($profile['investment_horizon'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label><?php echo __('investor.expected_roi'); ?></label>
            <input type="number" step="0.01" name="expected_roi" value="<?php echo htmlspecialchars($profile['expected_roi'] ?? ''); ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo __('investor.profile_save_button'); ?></button>
        </div>
    </form>
</div>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
