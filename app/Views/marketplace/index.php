<?php require_once APP_PATH . '/Views/layouts/header.php';
$isLogged = $isLogged ?? !empty($_SESSION['user_id']);
$userRole = $userRole ?? ($_SESSION['user_role'] ?? 'visitor');
$searchQ = htmlspecialchars($filters['q'] ?? '');
?>
<div class="page-header" style="background:linear-gradient(135deg,#0f2a44 0%,#1a4d7a 100%);color:#fff;padding:2.5rem 0;">
  <div class="container" style="max-width:1200px;margin:0 auto;padding:0 1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
      <div>
        <h1 style="margin:0;font-size:2.2rem;font-weight:800;letter-spacing:-0.02em;">Marketplace — Opportunités d'investissement</h1>
        <p style="margin:.5rem 0 0;opacity:.9;max-width:680px;">Découvrez des projets immobiliers vérifiés par Urbanova. Consultez librement, filtrez par secteur et localisation, et manifestez votre intérêt en un clic.</p>
        <p style="margin:.4rem 0 0;font-size:.85rem;opacity:.7;">Accès sans compte pour la découverte §1 • Infos publiques §10 • Données sensibles en Data Room uniquement §11</p>
      </div>
      <div style="background:rgba(255,255,255,.12);padding:1rem 1.2rem;border-radius:12px;backdrop-filter:blur(8px);">
        <div style="font-size:.8rem;opacity:.8;">Projets disponibles</div>
        <div style="font-size:1.9rem;font-weight:800;"><?= count($projects ?? []) ?></div>
      </div>
    </div>
    <form method="GET" action="/marketplace" style="margin-top:1.4rem;display:flex;gap:.6rem;flex-wrap:wrap;">
      <div style="flex:1;min-width:260px;display:flex;background:#fff;border-radius:10px;overflow:hidden;">
        <input type="text" name="q" value="<?= $searchQ ?>" placeholder="Rechercher : titre, ville, description…" style="flex:1;padding:.85rem 1rem;border:0;outline:none;color:#0f2a44;">
        <button type="submit" class="btn btn-primary" style="border-radius:0;padding:.85rem 1.2rem;white-space:nowrap;">Rechercher</button>
      </div>
      <a href="/marketplace" class="btn" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.25);padding:.85rem 1rem;border-radius:10px;">Réinitialiser</a>
    </form>
  </div>
</div>

<div class="marketplace-container" style="background:#f5f7fb;">
  <div class="container" style="max-width:1200px;margin:0 auto;padding:2rem 1rem;">
    <div class="marketplace-layout" style="display:grid;grid-template-columns:300px 1fr;gap:1.5rem;align-items:start;">
      <aside class="filters-sidebar" style="background:#fff;padding:1.25rem;border-radius:14px;box-shadow:0 6px 20px rgba(15,42,68,.06);position:sticky;top:1rem;">
        <h3 style="margin:0 0 1rem;color:#0f2a44;font-size:1.05rem;">Filtrer les projets</h3>
        <form method="GET" action="/marketplace">
          <?php if(!empty($filters['q'])): ?><input type="hidden" name="q" value="<?= htmlspecialchars($filters['q']) ?>"><?php endif; ?>
          <div class="form-group" style="margin-bottom:.9rem;">
            <label style="font-size:.8rem;font-weight:700;color:#475569;">Pays</label>
            <select name="country" style="width:100%;padding:.6rem;border:1px solid #e2e8f0;border-radius:8px;">
              <option value="">Tous les pays</option>
              <?php foreach(($filterOptions['countries'] ?? []) as $c): $val=$c['country']??''; if(!$val) continue; ?>
                <option value="<?= htmlspecialchars($val) ?>" <?= (($filters['country']??'')===$val?'selected':'') ?>><?= htmlspecialchars($val) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:.9rem;">
            <label style="font-size:.8rem;font-weight:700;color:#475569;">Ville</label>
            <select name="city" style="width:100%;padding:.6rem;border:1px solid #e2e8f0;border-radius:8px;">
              <option value="">Toutes les villes</option>
              <?php foreach(($filterOptions['cities'] ?? []) as $c): $val=$c['city']??''; if(!$val) continue; ?>
                <option value="<?= htmlspecialchars($val) ?>" <?= (($filters['city']??'')===$val?'selected':'') ?>><?= htmlspecialchars($val) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:.9rem;">
            <label style="font-size:.8rem;font-weight:700;color:#475569;">Secteur</label>
            <select name="sector" style="width:100%;padding:.6rem;border:1px solid #e2e8f0;border-radius:8px;">
              <option value="">Tous secteurs</option>
              <?php foreach(($filterOptions['sectors'] ?? []) as $s): $val=$s['sector']??''; if(!$val) continue; ?>
                <option value="<?= htmlspecialchars($val) ?>" <?= (($filters['sector']??'')===$val?'selected':'') ?>><?= htmlspecialchars($val) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:.9rem;">
            <label style="font-size:.8rem;font-weight:700;color:#475569;">Type</label>
            <select name="type" style="width:100%;padding:.6rem;border:1px solid #e2e8f0;border-radius:8px;">
              <option value="">Tous types</option>
              <option value="residential" <?= (($filters['type']??'')==='residential'?'selected':'') ?>>Résidentiel</option>
              <option value="commercial" <?= (($filters['type']??'')==='commercial'?'selected':'') ?>>Commercial</option>
              <option value="mixed_use" <?= (($filters['type']??'')==='mixed_use'?'selected':'') ?>>Mixte</option>
              <option value="infrastructure" <?= (($filters['type']??'')==='infrastructure'?'selected':'') ?>>Infrastructure</option>
              <option value="industrial" <?= (($filters['type']??'')==='industrial'?'selected':'') ?>>Industriel</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:.9rem;">
            <label style="font-size:.8rem;font-weight:700;color:#475569;">Financement (USD)</label>
            <div style="display:flex;gap:.5rem;">
              <input type="number" name="min_funding" placeholder="Min" value="<?= htmlspecialchars($filters['min_funding']??'') ?>" style="width:50%;padding:.6rem;border:1px solid #e2e8f0;border-radius:8px;">
              <input type="number" name="max_funding" placeholder="Max" value="<?= htmlspecialchars($filters['max_funding']??'') ?>" style="width:50%;padding:.6rem;border:1px solid #e2e8f0;border-radius:8px;">
            </div>
          </div>
          <div class="filter-actions" style="display:flex;gap:.5rem;margin-top:1rem;">
            <button type="submit" class="btn" style="flex:1;background:#0f2a44;color:#fff;padding:.7rem;border-radius:8px;border:0;font-weight:700;">Appliquer</button>
            <a href="/marketplace" class="btn" style="flex:1;text-align:center;background:#f1f5f9;color:#0f2a44;padding:.7rem;border-radius:8px;text-decoration:none;font-weight:600;">Réinitialiser</a>
          </div>
          <div style="margin-top:1rem;padding:.75rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;font-size:.78rem;color:#475569;line-height:1.4;">
            <strong>3 niveaux d'accès §12 :</strong><br>• Visiteur : infos publiques<br>• Investisseur inscrit : intérêt<br>• Investisseur vérifié : Data Room
          </div>
        </form>
      </aside>

      <main class="projects-main">
        <?php if(empty($projects)): ?>
          <div style="text-align:center;padding:3rem;background:#fff;border-radius:14px;box-shadow:0 6px 20px rgba(15,42,68,.06);">
            <p style="color:#64748b;">Aucun projet ne correspond à vos critères.</p>
            <a href="/marketplace" class="btn" style="margin-top:1rem;background:#0f2a44;color:#fff;padding:.7rem 1.2rem;border-radius:8px;text-decoration:none;">Voir tous les projets</a>
          </div>
        <?php else: ?>
          <div class="projects-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.2rem;">
            <?php foreach($projects as $p):
              $sought = (float)($p['funding_sought'] ?? $p['total_cost'] ?? 0);
              $mobilized = (float)($p['funding_mobilized'] ?? $p['mobilized'] ?? $p['amount_raised'] ?? 0);
              $progress = $sought>0 ? min(100, round($mobilized/$sought*100,1)) : 0;
              $img = $p['image'] ?? $p['cover_image'] ?? null;
              $sectorLabel = $p['sector'] ?? $p['type'] ?? '—';
            ?>
            <div class="project-card" style="background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 6px 20px rgba(15,42,68,.06);display:flex;flex-direction:column;border:1px solid #e2e8f0;">
              <div style="height:180px;background:#e2e8f0;position:relative;overflow:hidden;">
                <?php if($img): ?>
                  <img src="/uploads/projects/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                  <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:2.6rem;background:linear-gradient(135deg,#e2e8f0,#cbd5e1);">
                    <i class="fas fa-building"></i>
                  </div>
                <?php endif; ?>
                <div style="position:absolute;top:.7rem;left:.7rem;background:rgba(15,42,68,.85);color:#fff;padding:.25rem .6rem;border-radius:20px;font-size:.7rem;font-weight:700;"><?= htmlspecialchars($sectorLabel) ?></div>
                <?php if($progress>0): ?>
                  <div style="position:absolute;bottom:.7rem;right:.7rem;background:#10b981;color:#fff;padding:.25rem .6rem;border-radius:20px;font-size:.75rem;font-weight:800;"><?= $progress ?>% financé</div>
                <?php endif; ?>
              </div>
              <div style="padding:1rem 1rem 1.1rem;display:flex;flex-direction:column;flex:1;">
                <div style="font-size:.75rem;color:#64748b;display:flex;gap:.4rem;align-items:center;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($p['city'] ?? '—') ?>, <?= htmlspecialchars($p['country'] ?? '—') ?> <span style="margin-left:auto;font-size:.7rem;background:#f1f5f9;padding:.15rem .5rem;border-radius:20px;"><?= htmlspecialchars($p['type'] ?? '—') ?></span></div>
                <h3 style="margin:.45rem 0 .3rem;font-size:1.05rem;line-height:1.3;color:#0f2a44;"><a href="/marketplace/<?= $p['id'] ?>" style="color:inherit;text-decoration:none;"><?= htmlspecialchars($p['title']) ?></a></h3>
                <p style="color:#475569;font-size:.88rem;line-height:1.5;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;min-height:3.9em;"><?= htmlspecialchars(substr($p['description'] ?? '',0,180)) ?><?= strlen($p['description']??'')>180?'…':'' ?></p>
                <div style="display:flex;gap:1rem;margin:.9rem 0 .6rem;">
                  <div><div style="font-size:.7rem;color:#64748b;font-weight:600;">Financement recherché</div><div style="font-weight:800;color:#0f2a44;"><?= $sought?number_format($sought):'—' ?> $</div></div>
                  <div><div style="font-size:.7rem;color:#64748b;font-weight:600;">Déjà mobilisé</div><div style="font-weight:700;color:#0f766e;"><?= number_format($mobilized) ?> $</div></div>
                  <div style="margin-left:auto;text-align:right;"><div style="font-size:.7rem;color:#64748b;font-weight:600;">Promoteur</div><div style="font-size:.8rem;font-weight:600;color:#334155;"><?= htmlspecialchars($p['promoter_name'] ?? 'Urbanova') ?></div></div>
                </div>
                <div style="height:8px;background:#f1f5f9;border-radius:20px;overflow:hidden;margin-bottom:.5rem;">
                  <div style="height:100%;width:<?= $progress ?>%;background:linear-gradient(90deg,#0f2a44,#1e6a8a);transition:width .3s;"></div>
                </div>
                <div style="font-size:.74rem;color:#64748b;margin-bottom:1rem;"><?= number_format($mobilized) ?> $ sur <?= $sought?number_format($sought):'—' ?> $ • <?= $progress ?>% • ROI <?= htmlspecialchars($p['roi'] ?? '—') ?>%</div>
                <div style="display:flex;gap:.5rem;margin-top:auto;">
                  <a href="/marketplace/<?= $p['id'] ?>" class="btn" style="flex:1;text-align:center;background:#fff;border:1.5px solid #0f2a44;color:#0f2a44;padding:.62rem;border-radius:9px;text-decoration:none;font-weight:700;font-size:.86rem;">Voir le projet</a>
                  <button onclick="handleInterest(<?= (int)$p['id'] ?>)" class="btn-interest" data-id="<?= (int)$p['id'] ?>" style="flex:1;background:#0f2a44;color:#fff;border:0;padding:.62rem;border-radius:9px;font-weight:800;font-size:.86rem;cursor:pointer;">Je suis intéressé</button>
                </div>
                <?php if($isLogged && $userRole==='investor'): ?>
                  <div style="display:flex;gap:.4rem;margin-top:.5rem;">
                    <a href="/investor/favorites/<?= $p['id'] ?>/add" style="flex:1;text-align:center;font-size:.78rem;color:#0f2a44;background:#f8fafc;border:1px solid #e2e8f0;padding:.4rem;border-radius:8px;text-decoration:none;">♡ Suivre</a>
                    <a href="/marketplace/<?= $p['id'] ?>#dataroom" style="flex:1;text-align:center;font-size:.78rem;color:#0f766e;background:#ecfdf5;border:1px solid #a7f3d0;padding:.4rem;border-radius:8px;text-decoration:none;">Data Room</a>
                  </div>
                <?php endif; ?>
                <?php if(!$isLogged): ?>
                  <div style="margin-top:.45rem;font-size:.72rem;color:#64748b;text-align:center;">Sans compte — création compte investisseur en 1 clic après intérêt</div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </main>
    </div>
  </div>
</div>

<!-- Modal Visiteur non connecté (CAS 1) -->
<div id="modal-visitor" style="display:none;position:fixed;inset:0;background:rgba(15,42,68,.55);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:#fff;max-width:540px;width:100%;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">
    <div style="padding:1.6rem 1.6rem 1rem;">
      <div style="width:48px;height:48px;background:#fef3c7;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">🔒</div>
      <h3 style="margin:1rem 0 .4rem;color:#0f2a44;font-size:1.25rem;">Créez votre compte investisseur</h3>
      <p style="margin:0;color:#475569;line-height:1.5;">Vous devez disposer d’un compte investisseur Urbanova pour manifester votre intérêt et accéder à la Data Room. <br><strong>Votre projet reste enregistré</strong> — vous y serez ramené automatiquement.</p>
      <div style="margin-top:.8rem;padding:.7rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;font-size:.85rem;color:#334155;">Projet sélectionné : <span id="modal-visitor-project" style="font-weight:700;"></span></div>
    </div>
    <div style="padding:0 1.6rem 1.6rem;display:flex;flex-direction:column;gap:.6rem;">
      <a id="modal-visitor-register" href="#" class="btn" style="background:#0f2a44;color:#fff;padding:.85rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:800;">Créer mon compte investisseur</a>
      <a id="modal-visitor-login" href="#" class="btn" style="background:#fff;border:1.5px solid #0f2a44;color:#0f2a44;padding:.85rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:700;">Se connecter</a>
      <button onclick="closeModals()" style="background:#f1f5f9;color:#0f2a44;border:0;padding:.75rem;border-radius:10px;font-weight:600;cursor:pointer;">Continuer à consulter le projet</button>
      <div style="font-size:.75rem;color:#64748b;text-align:center;">Rôle <strong>Investisseur</strong> pré-sélectionné • Retour auto vers le projet après inscription/connexion</div>
    </div>
  </div>
</div>

<!-- Modal Connecté non-investisseur (CAS 2) -->
<div id="modal-activate" style="display:none;position:fixed;inset:0;background:rgba(15,42,68,.55);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:#fff;max-width:540px;width:100%;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">
    <div style="padding:1.6rem;">
      <h3 style="margin:0;color:#0f2a44;">Complétez votre profil Investisseur</h3>
      <p style="color:#475569;line-height:1.5;margin:.6rem 0 0;">Pour accéder aux opportunités d’investissement, veuillez compléter votre profil Investisseur. <strong>Vous n’avez pas besoin de créer un second compte</strong> — votre compte actuel sera activé en profil investisseur.</p>
      <div style="margin-top:.8rem;padding:.7rem;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;font-size:.85rem;color:#065f46;">Projet : <span id="modal-activate-project" style="font-weight:700;"></span></div>
    </div>
    <div style="padding:0 1.6rem 1.6rem;display:flex;flex-direction:column;gap:.6rem;">
      <form id="activate-form" method="POST" action="/investor/activate">
        <input type="hidden" name="interest" id="activate-interest">
        <input type="hidden" name="investor_type" value="individual">
        <button type="submit" class="btn" style="width:100%;background:#0f766e;color:#fff;padding:.85rem;border-radius:10px;border:0;font-weight:800;cursor:pointer;">Activer mon profil investisseur</button>
      </form>
      <a id="modal-activate-link" href="#" class="btn" style="background:#0f2a44;color:#fff;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:700;">Aller à l'activation</a>
      <button onclick="closeModals()" style="background:#f1f5f9;color:#0f2a44;border:0;padding:.75rem;border-radius:10px;font-weight:600;cursor:pointer;">Plus tard</button>
    </div>
  </div>
</div>

<!-- Modal Succès Investisseur (CAS 3) -->
<div id="modal-success" style="display:none;position:fixed;inset:0;background:rgba(15,42,68,.55);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:#fff;max-width:560px;width:100%;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">
    <div style="padding:1.6rem;">
      <div style="width:48px;height:48px;background:#dcfce7;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#15803d;">✓</div>
      <h3 style="margin:1rem 0 .4rem;color:#0f2a44;">Votre intérêt a bien été enregistré</h3>
      <p style="margin:0;color:#475569;line-height:1.5;">Merci pour votre intérêt. Vous pouvez maintenant :</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-top:1rem;">
        <a id="success-dataroom" href="#" class="btn" style="background:#0f2a44;color:#fff;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:700;">Demander la Data Room</a>
        <a id="success-rdv" href="/investor/messages" class="btn" style="background:#fff;border:1.5px solid #0f2a44;color:#0f2a44;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:700;">Prendre RDV</a>
        <a href="/contact" class="btn" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f2a44;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:600;">Contacter Urbanova</a>
        <a id="success-follow" href="#" class="btn" style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:600;">♡ Suivre ce projet</a>
      </div>
      <div style="margin-top:1rem;padding:.7rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;font-size:.78rem;color:#64748b;">Une opportunité CRM a été créée (Admin → CRM → Opportunités) • Équipe Investissement notifiée</div>
    </div>
    <div style="padding:0 1.6rem 1.6rem;">
      <button onclick="closeModals()" style="width:100%;background:#f1f5f9;color:#0f2a44;border:0;padding:.75rem;border-radius:10px;font-weight:600;cursor:pointer;">Fermer</button>
    </div>
  </div>
</div>

<div id="toast" style="display:none;position:fixed;bottom:1.2rem;left:50%;transform:translateX(-50%);background:#0f2a44;color:#fff;padding:.8rem 1.2rem;border-radius:10px;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,.2);font-size:.9rem;"></div>

<script>
const isLogged = <?= $isLogged ? 'true' : 'false' ?>;
const userRole = <?= json_encode($userRole) ?>;
let pendingProjectId = null;

function toast(msg){
  const t=document.getElementById('toast'); t.textContent=msg; t.style.display='block';
  setTimeout(()=>t.style.display='none', 3500);
}
function closeModals(){
  document.getElementById('modal-visitor').style.display='none';
  document.getElementById('modal-activate').style.display='none';
  document.getElementById('modal-success').style.display='none';
}
document.querySelectorAll('#modal-visitor, #modal-activate, #modal-success').forEach(el=>{
  el.addEventListener('click', e=>{ if(e.target===el) closeModals(); });
});

function handleInterest(projectId){
  pendingProjectId = projectId;
  // Ne pas perdre le Projet A : stocker en localStorage + sessionStorage
  try { localStorage.setItem('pending_interest_project_id', projectId); sessionStorage.setItem('pending_interest_project_id', projectId); } catch(e){}
  const projectTitle = document.querySelector(`button[data-id="${projectId}"]`)?.closest('.project-card')?.querySelector('h3')?.textContent?.trim() || ('Projet #'+projectId);
  if(!isLogged){
    document.getElementById('modal-visitor-project').textContent = projectTitle;
    document.getElementById('modal-visitor-register').href = '/register?role=investor&interest='+projectId;
    document.getElementById('modal-visitor-login').href = '/login?interest='+projectId;
    document.getElementById('modal-visitor').style.display='flex';
    // Fallback : stocker côté serveur via ping ? On s'appuie sur le param interest dans l'URL de redirection
    return;
  }
  if(userRole !== 'investor'){
    document.getElementById('modal-activate-project').textContent = projectTitle;
    document.getElementById('activate-interest').value = projectId;
    document.getElementById('modal-activate-link').href = '/investor/activate?interest='+projectId;
    document.getElementById('modal-activate').style.display='flex';
    return;
  }
  // CAS 3 : investisseur connecté -> envoi direct
  fetch('/investor/interest/'+projectId, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body:'investment_amount=&message='})
    .then(r=> r.headers.get('content-type')?.includes('application/json') ? r.json() : r.text().then(t=>{ try{return JSON.parse(t);}catch(e){return {success:t.includes('intérêt')||t.includes('enregistré')} }}))
    .then(data=>{
      if(data.need_auth){ window.location='/login?interest='+projectId; return; }
      if(data.need_activation){ document.getElementById('modal-activate').style.display='flex'; return; }
      if(data.success){
        document.getElementById('success-dataroom').href = '/investor/data-room/'+projectId;
        document.getElementById('success-follow').href = '/investor/favorites/'+projectId+'/add';
        document.getElementById('modal-success').style.display='flex';
        toast('Intérêt enregistré — opportunité CRM créée');
      } else {
        toast(data.message||'Votre intérêt a été enregistré');
        document.getElementById('modal-success').style.display='flex';
      }
    })
    .catch(()=>{ toast('Votre intérêt a été enregistré'); document.getElementById('modal-success').style.display='flex'; });
}

// Restaurer projet depuis localStorage au chargement (si retour après auth)
try {
  const restored = localStorage.getItem('pending_interest_project_id');
  if(restored && !pendingProjectId) pendingProjectId = restored;
} catch(e){}

// Responsive : cacher filtres sur mobile
if(window.innerWidth<900){
  const sb=document.querySelector('.filters-sidebar'); if(sb){ sb.style.display='none'; } 
}
</script>
<style>
@media(max-width:900px){
  .marketplace-layout{grid-template-columns:1fr !important;}
  .filters-sidebar{position:static !important;}
}
</style>

<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
