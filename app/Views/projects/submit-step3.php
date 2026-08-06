<?php ob_start(); ?>

<h1>Étape 3 : Documents requis</h1>
<?php if (!empty($_SESSION['project_submission_errors'])): ?>
    <div class="error-list">
        <ul>
        <?php foreach ($_SESSION['project_submission_errors'] as $field => $msg): ?>
            <li><?php echo htmlspecialchars($msg); ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['project_submission_errors']); ?>
<?php endif; ?>
<form action="/projects/submit" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="step" value="3">

    <h3>Documents administratifs</h3>
    <label>Registre de commerce (RCCM)<input type="file" name="rccm" accept="application/pdf,image/*"></label>
    <label>Numéro d'identification fiscale (NIF)<input type="file" name="nif" accept="application/pdf,image/*"></label>
    <label>Statuts de l'entreprise<input type="file" name="statutes" accept="application/pdf,image/*"></label>
    <label>Pièce d'identité du porteur<input type="file" name="id_card" accept="application/pdf,image/*"></label>
    <label>Procuration (si applicable)<input type="file" name="power_of_attorney" accept="application/pdf,image/*"></label>

    <h3>Documents fonciers</h3>
    <label>Certificat d'enregistrement / Titre foncier<input type="file" name="land_title" accept="application/pdf,image/*"></label>
    <label>Contrat de vente du terrain<input type="file" name="sale_contract" accept="application/pdf,image/*"></label>
    <label>Plan cadastral<input type="file" name="cadastral_plan" accept="application/pdf,image/*"></label>
    <label>Certificat de bornage<input type="file" name="demarcation_cert" accept="application/pdf,image/*"></label>
    <label>Attestation de propriété<input type="file" name="ownership_attest" accept="application/pdf,image/*"></label>

    <h3>Documents techniques</h3>
    <label>Plan de masse<input type="file" name="site_plan" accept="application/pdf,image/*"></label>
    <label>Plans architecturaux<input type="file" name="architectural_plans" accept="application/pdf,image/*"></label>
    <label>Plans techniques<input type="file" name="technical_plans" accept="application/pdf,image/*"></label>
    <label>Étude géotechnique<input type="file" name="geotech_study" accept="application/pdf,image/*"></label>
    <label>Étude topographique<input type="file" name="topo_study" accept="application/pdf,image/*"></label>
    <label>Étude de faisabilité<input type="file" name="feasibility_study" accept="application/pdf,image/*"></label>

    <h3>Autres documents</h3>
    <label>Business Plan<input type="file" name="business_plan" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,image/*"></label>
    <label>Pitch Deck<input type="file" name="pitch_deck" accept=".pdf,.ppt,.pptx,image/*"></label>
    <label>Modèle financier<input type="file" name="financial_model" accept=".pdf,.xls,.xlsx,.csv,image/*"></label>
    <label>Photos<input type="file" id="photos" name="photos[]" accept="image/*" multiple></label>
    <div id="photosPreview" class="preview-grid"></div>
    <label>Rendus 3D<input type="file" id="renders" name="renders[]" accept="image/*" multiple></label>
    <div id="rendersPreview" class="preview-grid"></div>
    <label>Vidéos<input type="file" id="videos" name="videos[]" accept="video/*" multiple></label>
    <div id="videosPreview" class="preview-grid"></div>

    <div class="form-actions">
        <button type="submit" name="action" value="prev">Précédent</button>
        <button type="submit" name="action" value="next">Suivant</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function(){
    function previewFiles(inputId, containerId, isVideo){
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);
        if (!input) return;
        input.addEventListener('change', function(e){
            container.innerHTML = '';
            const files = Array.from(e.target.files || []);
            files.forEach(file => {
                const url = URL.createObjectURL(file);
                const el = document.createElement(isVideo? 'video' : 'img');
                if (isVideo) {
                    el.controls = true;
                    el.width = 160;
                } else {
                    el.width = 160;
                }
                el.src = url;
                container.appendChild(el);
            });
        });
    }

    previewFiles('photos','photosPreview', false);
    previewFiles('renders','rendersPreview', false);
    previewFiles('videos','videosPreview', true);
});
</script>

<?php $content = ob_get_clean(); require_once APP_PATH . '/Views/layouts/header.php'; ?>
