<?php require_once APP_PATH . '/Views/layouts/header.php'; ?>
<div style="background:#f5f7fb;min-height:80vh;padding:2rem 1rem;">
  <div style="max-width:640px;margin:0 auto;">
    <div style="background:#fff;border-radius:16px;box-shadow:0 10px 30px rgba(15,42,68,.08);border:1px solid #e2e8f0;overflow:hidden;">
      <div style="background:linear-gradient(135deg,#0f2a44,#1a6a8a);color:#fff;padding:1.6rem;">
        <div style="font-size:.8rem;opacity:.8;letter-spacing:.08em;font-weight:700;">URBANOVA — PARCOURS INVESTISSEUR §6</div>
        <h1 style="margin:.4rem 0 .3rem;font-size:1.55rem;font-weight:800;">Activez votre profil Investisseur</h1>
        <p style="margin:0;opacity:.9;line-height:1.5;">Vous êtes connecté<?php if(!empty($user)) echo ' en tant que <strong>'.htmlspecialchars($user['email'] ?? '').'</strong>'; ?>. Vous n’avez pas besoin de créer un second compte — votre compte actuel va être enrichi d’un <strong>profil Investisseur</strong>.</p>
      </div>
      <?php if(!empty($project)): ?>
        <div style="margin:1.2rem 1.6rem 0;padding:1rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;display:flex;gap:1rem;align-items:center;">
          <div style="width:64px;height:64px;background:#e2e8f0;border-radius:10px;overflow:hidden;flex-shrink:0;">
            <?php $img=$project['image']??null; if($img): ?><img src="/uploads/projects/<?= htmlspecialchars($img) ?>" style="width:100%;height:100%;object-fit:cover;"><?php else: ?><div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#64748b;"><i class="fas fa-building"></i></div><?php endif; ?>
          </div>
          <div style="flex:1;">
            <div style="font-size:.75rem;color:#64748b;font-weight:700;">PROJET SÉLECTIONNÉ</div>
            <div style="font-weight:800;color:#0f2a44;"><?= htmlspecialchars($project['title']) ?></div>
            <div style="font-size:.85rem;color:#475569;"><?= htmlspecialchars($project['city'] ?? '') ?>, <?= htmlspecialchars($project['country'] ?? '') ?> • <?= htmlspecialchars($project['sector'] ?? '') ?></div>
          </div>
          <a href="/marketplace/<?= (int)$project['id'] ?>" style="color:#0f2a44;font-weight:700;text-decoration:none;font-size:.85rem;white-space:nowrap;">Voir fiche →</a>
        </div>
      <?php endif; ?>
      <div style="padding:1.6rem;">
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;padding:.9rem;font-size:.88rem;color:#065f46;line-height:1.5;margin-bottom:1rem;">
          <strong>Ce qui se passe après l’activation :</strong><br>• Vous serez redirigé vers le projet pour <strong>Confirmer mon intérêt</strong> en un clic • Votre intérêt créera une <strong>opportunité CRM</strong> (Admin → CRM → Opportunités, statut <em>Nouveau intérêt</em>) • L’Équipe Investissement sera notifiée.
        </div>
        <form method="POST" action="/investor/activate<?php echo !empty($interest) ? '?interest='.urlencode($interest) : '' ?>">
          <?php if(!empty($interest)): ?><input type="hidden" name="interest" value="<?= htmlspecialchars($interest) ?>"><?php endif; ?>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
          <div style="margin-bottom:1rem;">
            <label style="font-weight:700;color:#0f2a44;font-size:.9rem;">Type d'investisseur <small style="color:#64748b;font-weight:400;">(vous pourrez modifier ensuite)</small></label>
            <select name="investor_type" style="width:100%;margin-top:.4rem;padding:.7rem;border:1px solid #e2e8f0;border-radius:9px;">
              <option value="individual">Particulier / Business Angel</option>
              <option value="family_office">Family Office</option>
              <option value="investment_fund">Fonds d'investissement</option>
              <option value="bank">Banque</option>
              <option value="corporate_venture">Corporate Venture</option>
              <option value="dfi">Institution de financement</option>
              <option value="other">Autre</option>
            </select>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;margin-bottom:1rem;">
            <div><label style="font-weight:600;color:#334155;font-size:.85rem;">Téléphone</label><input type="tel" name="phone" placeholder="+243…" style="width:100%;margin-top:.3rem;padding:.7rem;border:1px solid #e2e8f0;border-radius:9px;"></div>
            <div><label style="font-weight:600;color:#334155;font-size:.85rem;">Pays</label><input type="text" name="country" placeholder="RDC" style="width:100%;margin-top:.3rem;padding:.7rem;border:1px solid #e2e8f0;border-radius:9px;"></div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;margin-bottom:1.2rem;">
            <div><label style="font-weight:600;color:#334155;font-size:.85rem;">Ville</label><input type="text" name="city" placeholder="Goma" style="width:100%;margin-top:.3rem;padding:.7rem;border:1px solid #e2e8f0;border-radius:9px;"></div>
            <div><label style="font-weight:600;color:#334155;font-size:.85rem;">Adresse</label><input type="text" name="address" placeholder="Adresse" style="width:100%;margin-top:.3rem;padding:.7rem;border:1px solid #e2e8f0;border-radius:9px;"></div>
          </div>
          <button type="submit" style="width:100%;background:#0f766e;color:#fff;padding:.9rem;border-radius:10px;border:0;font-weight:800;cursor:pointer;font-size:1rem;">Activer mon profil investisseur</button>
          <div style="text-align:center;margin-top:.7rem;font-size:.78rem;color:#64748b;">En activant, vous acceptez les conditions Urbanova. Vous pourrez compléter votre KYC ensuite pour accéder à la Data Room.</div>
        </form>
        <div style="display:flex;gap:.6rem;margin-top:1rem;">
          <?php if(!empty($interest)): ?><a href="/marketplace/<?= htmlspecialchars($interest) ?>" style="flex:1;text-align:center;background:#f8fafc;border:1px solid #e2e8f0;padding:.7rem;border-radius:9px;text-decoration:none;color:#0f2a44;font-weight:600;">Retour au projet</a><?php endif; ?>
          <a href="/marketplace" style="flex:1;text-align:center;background:#fff;border:1px solid #e2e8f0;padding:.7rem;border-radius:9px;text-decoration:none;color:#64748b;">Continuer la découverte</a>
        </div>
      </div>
    </div>
    <div style="text-align:center;margin-top:1rem;font-size:.78rem;color:#64748b;">Besoin d’aide ? <a href="/contact" style="color:#0f2a44;font-weight:700;">Contacter Urbanova</a></div>
  </div>
</div>
<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
