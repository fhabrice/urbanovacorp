<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1><?php echo __('investor.kyc_title'); ?></h1>
        <p><?php echo __('investor.kyc_description'); ?></p>
    </div>

    <div class="kyc-form-container">
        <form method="POST" action="/investor/kyc" enctype="multipart/form-data">
            <div class="form-section">
                <h2>Type d'investisseur</h2>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="type" value="individual" <?php echo ($investor['type'] ?? '') === 'individual' ? 'checked' : ''; ?> required>
                        <?php echo __('investor.individual'); ?>
                    </label>
                    <label>
                        <input type="radio" name="type" value="corporate" <?php echo ($investor['type'] ?? '') === 'corporate' ? 'checked' : ''; ?> required>
                        <?php echo __('investor.corporate'); ?>
                    </label>
                </div>
            </div>

            <div class="form-section">
                <h2>Informations personnelles</h2>
                <div class="form-group">
                    <label>Nationalité</label>
                    <input type="text" name="nationality" value="<?php echo htmlspecialchars($investor['nationality'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($investor['phone'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <textarea name="address" required><?php echo htmlspecialchars($investor['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Ville</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($investor['city'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Pays</label>
                    <input type="text" name="country" value="<?php echo htmlspecialchars($investor['country'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-section individual-only">
                <h2>Document d'identité</h2>
                <div class="form-group">
                    <label>Type de document</label>
                    <select name="id_document_type">
                        <option value="passport">Passeport</option>
                        <option value="id_card">Carte d'identité</option>
                        <option value="driving_license">Permis de conduire</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Numéro de document</label>
                    <input type="text" name="id_document_number" value="<?php echo htmlspecialchars($investor['id_document_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Date d'expiration</label>
                    <input type="date" name="id_document_expiry" value="<?php echo htmlspecialchars($investor['id_document_expiry'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Document (PDF, JPG, PNG)</label>
                    <input type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="form-group">
                    <label>Photo de profil (JPG, PNG) - Optionnel</label>
                    <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png">
                </div>
            </div>

            <div class="form-section corporate-only" style="display: none;">
                <h2>Informations entreprise</h2>
                <div class="form-group">
                    <label>Nom de l'entreprise</label>
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($investor['company_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Numéro d'enregistrement (RCCM)</label>
                    <input type="text" name="company_registration_number" value="<?php echo htmlspecialchars($investor['company_registration_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Identifiant fiscal</label>
                    <input type="text" name="company_tax_id" value="<?php echo htmlspecialchars($investor['company_tax_id'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-section corporate-only" style="display: none;">
                <h2>Documents entreprise</h2>
                <div class="form-group">
                    <label>Certificat d'immatriculation / RCCM (PDF, JPG, PNG)</label>
                    <input type="file" name="company_registration_doc" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="form-group">
                    <label>Statuts de l'entreprise (PDF, JPG, PNG)</label>
                    <input type="file" name="company_statutes" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="form-group">
                    <label>Pièce d'identité du représentant légal (PDF, JPG, PNG)</label>
                    <input type="file" name="representative_id" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="form-group">
                    <label>Pouvoir ou mandat de représentation (PDF, JPG, PNG) - Optionnel</label>
                    <input type="file" name="power_of_attorney" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="form-section">
                <h2>Profil d'investissement</h2>
                <div class="form-group">
                    <label>Capacité d'investissement (USD)</label>
                    <input type="number" name="investment_capacity" value="<?php echo htmlspecialchars($investor['investment_capacity'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Secteurs d'investissement</label>
                    <input type="text" name="investment_sectors" value="<?php echo htmlspecialchars($investor['investment_sectors'] ?? ''); ?>" placeholder="Immobilier, Infrastructure, etc.">
                </div>
                <div class="form-group">
                    <label>Profil de risque</label>
                    <select name="risk_profile" required>
                        <option value="conservative">Conservateur</option>
                        <option value="moderate">Modéré</option>
                        <option value="aggressive">Dynamique</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h2>Documents additionnels</h2>
                <div class="form-group">
                    <label>Justificatif de domicile (PDF, JPG, PNG)</label>
                    <input type="file" name="proof_address" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="form-group">
                    <label>Documents financiers (PDF, JPG, PNG)</label>
                    <input type="file" name="financial_docs" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="form-section">
                <h2>Déclarations obligatoires</h2>
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="declarations[]" value="accuracy" required>
                        <span>Je certifie l'exactitude des informations fournies.</span>
                    </label>
                </div>
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="declarations[]" value="terms" required>
                        <span>J'accepte les Conditions Générales d'Utilisation de la plateforme.</span>
                    </label>
                </div>
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="declarations[]" value="privacy" required>
                        <span>J'accepte la Politique de confidentialité.</span>
                    </label>
                </div>
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="declarations[]" value="verification" required>
                        <span>J'autorise Urbanova à vérifier les informations transmises.</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo __('investor.submit_kyc'); ?></button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const individualSections = document.querySelectorAll('.individual-only');
    const corporateSections = document.querySelectorAll('.corporate-only');

    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'individual') {
                individualSections.forEach(section => section.style.display = 'block');
                corporateSections.forEach(section => section.style.display = 'none');
            } else {
                individualSections.forEach(section => section.style.display = 'none');
                corporateSections.forEach(section => section.style.display = 'block');
            }
        });
    });

    // Initialize based on current selection
    const selectedType = document.querySelector('input[name="type"]:checked');
    if (selectedType) {
        selectedType.dispatchEvent(new Event('change'));
    }
});
</script>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
