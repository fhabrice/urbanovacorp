<?php require_once APP_PATH . '/Views/layouts/header.php';
$p = $project;
$isLogged = $isLogged ?? !empty($_SESSION['user_id']);
$isInvestor = $isInvestor ?? (($_SESSION['user_role'] ?? '')==='investor');
$hasInterest = $hasInterest ?? false;
$hasDataRoomAccess = $hasDataRoomAccess ?? false;
$isFollowing = $isFollowing ?? false;
$confirmInterest = $confirmInterest ?? false;
$sought = $sought ?? (float)($p['funding_sought'] ?? $p['total_cost'] ?? 0);
$mobilized = $mobilized ?? (float)($p['funding_mobilized'] ?? $p['amount_raised'] ?? 0);
$progress = $fundingProgress ?? ($sought>0? round($mobilized/$sought*100,1):0);
$interestRecorded = isset($_GET['interest_recorded']);
?>
<div style="background:#f5f7fb;min-height:100vh;">
  <div style="background:#fff;border-bottom:1px solid #e2e8f0;">
    <div class="container" style="max-width:1100px;margin:0 auto;padding:1rem;">
      <a href="/marketplace" style="color:#64748b;text-decoration:none;font-size:.9rem;">← Retour au Marketplace</a>
      <?php if(!empty($_SESSION['flash_message'])): $flash=$_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
        <div style="margin-top:.8rem;padding:.8rem 1rem;border-radius:10px;background:<?= ($flash['type']??'info')==='success'?'#ecfdf5':'#fffbeb' ?>;border:1px solid <?= ($flash['type']??'info')==='success'?'#a7f3d0':'#fde68a' ?>;color:#0f2a44;"><?= htmlspecialchars($flash['message'] ?? $flash['text'] ?? json_encode($flash)) ?></div>
      <?php endif; ?>
      <?php if($interestRecorded): ?>
        <div style="margin-top:.8rem;padding:1rem;border-radius:12px;background:#ecfdf5;border:1px solid #a7f3d0;display:flex;gap:1rem;align-items:center;">
          <div style="width:36px;height:36px;background:#10b981;color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;">✓</div>
          <div style="flex:1;"><strong>Votre intérêt a bien été enregistré</strong><br><span style="font-size:.9rem;color:#065f46;">Une opportunité a été créée (Admin → CRM → Opportunités) — équipe Investissement notifiée. Vous pouvez demander la Data Room ou prendre un rendez-vous.</span></div>
          <a href="/investor/data-room/<?= (int)$p['id'] ?>" style="background:#0f2a44;color:#fff;padding:.6rem 1rem;border-radius:8px;text-decoration:none;font-weight:700;white-space:nowrap;">Data Room</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="container" style="max-width:1100px;margin:0 auto;padding:1.2rem 1rem 2rem;display:grid;grid-template-columns:1fr 340px;gap:1.4rem;align-items:start;">
    <div>
      <div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(15,42,68,.06);border:1px solid #e2e8f0;">
        <div style="height:380px;background:#e2e8f0;position:relative;">
          <?php $img=$p['image']??$p['cover_image']??null; if($img): ?>
            <img src="/uploads/projects/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
          <?php else: ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0f2a44,#1a4d7a);color:#fff;font-size:3rem;"><i class="fas fa-building"></i></div>
          <?php endif; ?>
          <div style="position:absolute;inset:auto 0 0 0;background:linear-gradient(transparent, rgba(15,42,68,.85));padding:1.4rem;color:#fff;">
            <div style="display:flex;gap:.5rem;margin-bottom:.5rem;"><span style="background:rgba(255,255,255,.2);padding:.25rem .6rem;border-radius:20px;font-size:.75rem;font-weight:700;"><?= htmlspecialchars($p['sector'] ?? $p['type'] ?? 'Projet') ?></span><span style="background:rgba(255,255,255,.15);padding:.25rem .6rem;border-radius:20px;font-size:.75rem;"><?= htmlspecialchars($p['city'] ?? '') ?>, <?= htmlspecialchars($p['country'] ?? '') ?></span></div>
            <h1 style="margin:0;font-size:1.9rem;font-weight:800;line-height:1.2;"><?= htmlspecialchars($p['title']) ?></h1>
            <p style="margin:.4rem 0 0;opacity:.9;font-size:.95rem;"><?= htmlspecialchars($p['country'] ?? '') ?> • Porté par <?= htmlspecialchars($p['promoter_name'] ?? $promoter['full_name'] ?? 'Urbanova') ?></p>
          </div>
          <div style="position:absolute;top:1rem;right:1rem;background:#fff;color:#0f2a44;padding:.45rem .8rem;border-radius:10px;font-weight:800;box-shadow:0 4px 12px rgba(0,0,0,.15);"><?= $progress ?>% financé</div>
        </div>

        <div style="padding:1.4rem;">
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.2rem;">
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:.9rem;text-align:center;"><div style="font-size:.7rem;color:#64748b;font-weight:700;letter-spacing:.05em;">MONTANT TOTAL</div><div style="font-size:1.15rem;font-weight:800;color:#0f2a44;"><?= $p['total_cost']?number_format($p['total_cost']):($sought?number_format($sought):'—') ?> $</div><div style="font-size:.7rem;color:#94a3b8;">Budget global</div></div>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:.9rem;text-align:center;"><div style="font-size:.7rem;color:#64748b;font-weight:700;">FINANCEMENT RECHERCHÉ</div><div style="font-size:1.15rem;font-weight:800;color:#0f2a44;"><?= $sought?number_format($sought):'—' ?> $</div><div style="font-size:.7rem;color:#94a3b8;">Ticket d'entrée</div></div>
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;padding:.9rem;text-align:center;"><div style="font-size:.7rem;color:#065f46;font-weight:700;">DÉJÀ MOBILISÉ</div><div style="font-size:1.15rem;font-weight:800;color:#065f46;"><?= number_format($mobilized) ?> $</div><div style="font-size:.7rem;color:#6ee7b7;"><?= $progress ?>% de l'objectif</div></div>
          </div>

          <div style="height:10px;background:#f1f5f9;border-radius:20px;overflow:hidden;margin-bottom:1.2rem;"><div style="width:<?= $progress ?>%;height:100%;background:linear-gradient(90deg,#0f2a44,#10b981);"></div></div>

          <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1.3rem;">
            <span style="padding:.35rem .7rem;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:20px;font-size:.78rem;"><strong>Type d'investissement :</strong> <?= htmlspecialchars($p['investment_type'] ?? $p['type'] ?? '—') ?></span>
            <span style="padding:.35rem .7rem;background:#fef3c7;border:1px solid #fde68a;border-radius:20px;font-size:.78rem;"><strong>ROI :</strong> <?= htmlspecialchars($p['roi'] ?? '—') ?>%</span>
            <span style="padding:.35rem .7rem;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:20px;font-size:.78rem;"><strong>Durée :</strong> <?= htmlspecialchars($p['duration'] ?? $p['duree'] ?? '—') ?></span>
          </div>

          <!-- Onglets -->
          <div style="display:flex;gap:.4rem;border-bottom:2px solid #f1f5f9;margin-bottom:1rem;">
            <button onclick="switchTab('public')" id="tab-public" style="flex:1;padding:.8rem;border:0;background:#0f2a44;color:#fff;border-radius:8px 8px 0 0;font-weight:700;cursor:pointer;">Informations publiques §10</button>
            <button onclick="switchTab('dataroom')" id="tab-dataroom" style="flex:1;padding:.8rem;border:0;background:#f8fafc;color:#64748b;border-radius:8px 8px 0 0;font-weight:600;cursor:pointer;">Data Room §11 🔒</button>
          </div>

          <div id="panel-public">
            <h3 style="margin:.2rem 0 .6rem;color:#0f2a44;">Description du projet</h3>
            <p style="color:#334155;line-height:1.6;white-space:pre-wrap;"><?= htmlspecialchars($p['description'] ?? 'Aucune description fournie.') ?></p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1.2rem;">
              <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:1rem;">
                <h4 style="margin:0 0 .45rem;color:#0f2a44;font-size:.95rem;">Problème / Opportunité</h4>
                <p style="margin:0;color:#475569;font-size:.9rem;line-height:1.5;"><?= htmlspecialchars($p['problem'] ?? $p['problem_statement'] ?? 'Marché en forte demande, offre insuffisante dans la zone cible.') ?></p>
              </div>
              <div style="background:#f0fdfa;border:1px solid #ccfbf1;border-radius:12px;padding:1rem;">
                <h4 style="margin:0 0 .45rem;color:#0f766e;font-size:.95rem;">Solution proposée</h4>
                <p style="margin:0;color:#134e4a;font-size:.9rem;line-height:1.5;"><?= htmlspecialchars($p['solution'] ?? $p['solution_proposed'] ?? 'Programme immobilier structuré avec modèle économique éprouvé et équipe expérimentée.') ?></p>
              </div>
            </div>

            <div style="margin-top:1.2rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
              <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1rem;">
                <h4 style="margin:0 0 .6rem;color:#0f2a44;">Impact attendu</h4>
                <p style="margin:0;color:#475569;font-size:.9rem;"><?= htmlspecialchars($p['impact'] ?? $p['expected_impact'] ?? 'Création d’emplois, amélioration du cadre de vie et valorisation du territoire.') ?></p>
                <div style="margin-top:.7rem;display:flex;gap:.4rem;flex-wrap:wrap;">
                  <?php $indicators = $p['indicators'] ?? $p['kpi'] ?? null; if($indicators): ?>
                    <span style="background:#f1f5f9;padding:.3rem .6rem;border-radius:20px;font-size:.75rem;"><?= htmlspecialchars($indicators) ?></span>
                  <?php else: ?>
                    <span style="background:#f1f5f9;padding:.3rem .6rem;border-radius:20px;font-size:.75rem;">Emplois : <?= htmlspecialchars($p['jobs'] ?? '35+') ?></span>
                    <span style="background:#f1f5f9;padding:.3rem .6rem;border-radius:20px;font-size:.75rem;">Logements : <?= htmlspecialchars($p['housing_units'] ?? '—') ?></span>
                    <span style="background:#f1f5f9;padding:.3rem .6rem;border-radius:20px;font-size:.75rem;">Surface : <?= htmlspecialchars($p['surface'] ?? '—') ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1rem;">
                <h4 style="margin:0 0 .6rem;color:#0f2a44;">Promoteur — synthèse</h4>
                <div style="display:flex;gap:.8rem;align-items:center;">
                  <div style="width:44px;height:44px;background:#0f2a44;color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;"><?= strtoupper(substr($promoter['full_name'] ?? $p['promoter_name'] ?? 'U',0,1)) ?></div>
                  <div><div style="font-weight:700;color:#0f2a44;"><?= htmlspecialchars($promoter['full_name'] ?? $p['promoter_name'] ?? 'Promoteur Urbanova') ?></div><div style="font-size:.8rem;color:#64748b;"><?= htmlspecialchars($promoter['city'] ?? $p['city'] ?? '') ?> • <?= htmlspecialchars($p['sector'] ?? '') ?></div></div>
                </div>
                <p style="margin:.7rem 0 0;color:#475569;font-size:.85rem;line-height:1.5;">Promoteur vérifié par Urbanova. Détails complets (KYC, références, documents juridiques) disponibles en Data Room pour investisseurs vérifiés.</p>
              </div>
            </div>

            <div style="margin-top:1rem;padding:1rem;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;font-size:.85rem;color:#92400e;">
              <strong>Informations publiques (§10)</strong> : nom, visuels, secteur, localisation générale, description, problème/solution, montants, avancement, type d’investissement, impact, indicateurs, promoteur synthétique. <br>
              Les documents sensibles (§11 : business plan, modèle financier, contrats…) sont en Data Room avec contrôle d’accès.
            </div>
          </div>

          <div id="panel-dataroom" style="display:none;">
            <?php if(!$isLogged): ?>
              <div style="padding:2rem;text-align:center;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;">
                <div style="font-size:2rem;">🔒</div>
                <h3 style="color:#92400e;">Data Room — accès restreint</h3>
                <p style="color:#78350f;">Vous devez disposer d’un compte investisseur pour demander l’accès à la Data Room.<br>Votre projet reste enregistré et vous y serez ramené après inscription.</p>
                <div style="display:flex;gap:.6rem;justify-content:center;margin-top:1rem;flex-wrap:wrap;">
                  <a href="/register?role=investor&interest=<?= (int)$p['id'] ?>" style="background:#0f2a44;color:#fff;padding:.7rem 1.1rem;border-radius:9px;text-decoration:none;font-weight:800;">Créer mon compte investisseur</a>
                  <a href="/login?interest=<?= (int)$p['id'] ?>" style="background:#fff;border:1.5px solid #0f2a44;color:#0f2a44;padding:.7rem 1.1rem;border-radius:9px;text-decoration:none;font-weight:700;">Se connecter</a>
                </div>
              </div>
            <?php elseif(!$isInvestor): ?>
              <div style="padding:2rem;text-align:center;background:#f0fdfa;border:1px solid #99f6e4;border-radius:12px;">
                <h3 style="color:#0f766e;">Activez votre profil investisseur</h3>
                <p style="color:#134e4a;">Vous êtes connecté, mais votre compte n’est pas encore profil investisseur. Activez-le en un clic pour demander la Data Room.</p>
                <form method="POST" action="/investor/activate" style="margin-top:1rem;">
                  <input type="hidden" name="interest" value="<?= (int)$p['id'] ?>">
                  <button type="submit" style="background:#0f766e;color:#fff;padding:.8rem 1.3rem;border-radius:9px;border:0;font-weight:800;cursor:pointer;">Activer mon profil investisseur</button>
                </form>
              </div>
            <?php elseif(($kycStatus ?? 'pending')!=='approved' && !$hasDataRoomAccess): ?>
              <div style="padding:1.4rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                <h3 style="margin:0;color:#0f2a44;">Accès Data Room — KYC requis</h3>
                <p style="color:#475569;">Votre profil investisseur est en cours de vérification. Vous avez déjà manifesté votre intérêt (opportunité CRM créée). Dès validation KYC, vous recevrez l’accès à la Data Room.</p>
                <div style="display:flex;gap:.6rem;margin-top:1rem;flex-wrap:wrap;">
                  <a href="/investor/kyc" style="background:#0f2a44;color:#fff;padding:.7rem 1.1rem;border-radius:9px;text-decoration:none;font-weight:700;">Compléter mon KYC</a>
                  <a href="/investor/messages" style="background:#fff;border:1px solid #e2e8f0;color:#0f2a44;padding:.7rem 1.1rem;border-radius:9px;text-decoration:none;">Prendre un RDV</a>
                </div>
                <?php if($hasInterest): ?><div style="margin-top:.8rem;font-size:.8rem;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;padding:.6rem;border-radius:8px;">✔ Intérêt enregistré — opportunité CRM suivie par l’Équipe Investissement</div><?php endif; ?>
              </div>
            <?php else: ?>
              <div style="padding:1.2rem;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;">
                <h3 style="margin:0 0 .5rem;color:#065f46;">Data Room — accès <?= $hasDataRoomAccess?'autorisé':'sur demande' ?></h3>
                <?php if($hasDataRoomAccess): ?>
                  <p style="margin:0;color:#065f46;">Accès accordé. Vous pouvez consulter les documents confidentiels et engager la due diligence.</p>
                  <a href="/investor/data-room/<?= (int)$p['id'] ?>" style="display:inline-block;margin-top:.8rem;background:#065f46;color:#fff;padding:.7rem 1.1rem;border-radius:9px;text-decoration:none;font-weight:700;">Accéder à la Data Room</a>
                <?php else: ?>
                  <p style="margin:0;color:#475569;">Vous pouvez demander l’accès. L’équipe va qualifier votre profil et ouvrir la Data Room.</p>
                  <a href="/investor/data-room/<?= (int)$p['id'] ?>" style="display:inline-block;margin-top:.8rem;background:#0f2a44;color:#fff;padding:.7rem 1.1rem;border-radius:9px;text-decoration:none;font-weight:700;">Demander l'accès Data Room</a>
                <?php endif; ?>
              </div>
              <div style="margin-top:1rem;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                <div style="padding:.8rem 1rem;background:#f8fafc;font-weight:700;color:#0f2a44;border-bottom:1px solid #e2e8f0;">Documents types (extraits §11 — verrouillés sans accès)</div>
                <div style="padding:1rem;display:grid;gap:.6rem;font-size:.9rem;color:#475569;">
                  <div style="display:flex;justify-content:space-between;padding:.6rem;background:#fff;border:1px solid #f1f5f9;border-radius:8px;"><span>📄 Business plan complet</span><span style="color:#94a3b8;">🔒 Data Room</span></div>
                  <div style="display:flex;justify-content:space-between;padding:.6rem;background:#fff;border:1px solid #f1f5f9;border-radius:8px;"><span>📊 Modèle financier détaillé</span><span style="color:#94a3b8;">🔒 Data Room</span></div>
                  <div style="display:flex;justify-content:space-between;padding:.6rem;background:#fff;border:1px solid #f1f5f9;border-radius:8px;"><span>⚖️ Documents juridiques & titres</span><span style="color:#94a3b8;">🔒 Data Room</span></div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div style="position:sticky;top:1rem;display:flex;flex-direction:column;gap:1rem;">
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:1.2rem;box-shadow:0 8px 24px rgba(15,42,68,.06);">
        <h3 style="margin:0;color:#0f2a44;">Investir dans ce projet</h3>
        <p style="margin:.4rem 0 1rem;color:#64748b;font-size:.88rem;line-height:1.5;">Découvrir d’abord (§13), s’intéresser ensuite — sans friction, sans perte du projet.</p>

        <?php if(!$isLogged): ?>
          <button onclick="handleInterest(<?= (int)$p['id'] ?>)" style="width:100%;background:#0f2a44;color:#fff;padding:.85rem;border-radius:10px;border:0;font-weight:800;cursor:pointer;font-size:.95rem;">Je suis intéressé</button>
          <div style="display:flex;gap:.5rem;margin-top:.6rem;">
            <a href="/marketplace/<?= (int)$p['id'] ?>" style="flex:1;text-align:center;background:#f8fafc;border:1px solid #e2e8f0;padding:.6rem;border-radius:9px;text-decoration:none;color:#0f2a44;font-weight:600;font-size:.85rem;">Voir le projet</a>
            <a href="/register?role=investor&interest=<?= (int)$p['id'] ?>" style="flex:1;text-align:center;background:#fff;border:1.5px solid #0f2a44;color:#0f2a44;padding:.6rem;border-radius:9px;text-decoration:none;font-weight:700;font-size:.85rem;">Créer compte</a>
          </div>
          <p style="margin:.7rem 0 0;font-size:.75rem;color:#64748b;text-align:center;">Vous serez ramené ici après inscription — <strong>Confirmer mon intérêt</strong>.</p>
        <?php elseif(!$isInvestor): ?>
          <div style="padding:.8rem;background:#f0fdfa;border:1px solid #99f6e4;border-radius:10px;font-size:.85rem;color:#134e4a;margin-bottom:.8rem;">Connecté en tant que <strong><?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_email'] ?? 'utilisateur') ?></strong> — activez votre profil investisseur pour manifester votre intérêt.</div>
          <form method="POST" action="/investor/activate">
            <input type="hidden" name="interest" value="<?= (int)$p['id'] ?>">
            <button type="submit" style="width:100%;background:#0f766e;color:#fff;padding:.85rem;border-radius:10px;border:0;font-weight:800;cursor:pointer;">Activer mon profil investisseur</button>
          </form>
          <button onclick="handleInterest(<?= (int)$p['id'] ?>)" style="width:100%;margin-top:.6rem;background:#0f2a44;color:#fff;padding:.75rem;border-radius:10px;border:0;font-weight:700;cursor:pointer;">Je suis intéressé</button>
        <?php else: ?>
          <?php if($hasInterest): ?>
            <div style="padding:.8rem;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;color:#065f46;font-size:.9rem;margin-bottom:.8rem;">✔ Votre intérêt est enregistré<br><span style="font-size:.78rem;">Opportunité CRM créée — Admin → CRM → Opportunités</span></div>
            <div style="display:flex;flex-direction:column;gap:.5rem;">
              <a href="/investor/data-room/<?= (int)$p['id'] ?>" style="text-align:center;background:#0f2a44;color:#fff;padding:.75rem;border-radius:9px;text-decoration:none;font-weight:800;">Demander / Accéder Data Room</a>
              <div style="display:flex;gap:.5rem;">
                <a href="/investor/messages" style="flex:1;text-align:center;background:#fff;border:1px solid #e2e8f0;padding:.6rem;border-radius:9px;text-decoration:none;color:#0f2a44;font-weight:600;font-size:.85rem;">Prendre RDV</a>
                <a href="/contact" style="flex:1;text-align:center;background:#f8fafc;border:1px solid #e2e8f0;padding:.6rem;border-radius:9px;text-decoration:none;color:#0f2a44;font-weight:600;font-size:.85rem;">Contacter</a>
              </div>
              <?php if($isFollowing): ?>
                <a href="/investor/favorites/<?= (int)$p['id'] ?>/remove" style="text-align:center;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:.6rem;border-radius:9px;text-decoration:none;font-size:.85rem;">♥ Suivi — Retirer</a>
              <?php else: ?>
                <a id="follow-btn" href="/investor/favorites/<?= (int)$p['id'] ?>/add" style="text-align:center;background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:.6rem;border-radius:9px;text-decoration:none;font-size:.85rem;">♡ Suivre ce projet</a>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <button onclick="handleInterest(<?= (int)$p['id'] ?>)" style="width:100%;background:#0f2a44;color:#fff;padding:.85rem;border-radius:10px;border:0;font-weight:800;cursor:pointer;font-size:.95rem;">Je suis intéressé</button>
            <button id="confirm-interest-btn" onclick="handleInterest(<?= (int)$p['id'] ?>)" style="display:none;width:100%;margin-top:.6rem;background:#10b981;color:#fff;padding:.75rem;border-radius:10px;border:0;font-weight:700;cursor:pointer;">Confirmer mon intérêt</button>
            <div style="display:flex;gap:.5rem;margin-top:.6rem;">
              <?php if($isFollowing): ?>
                <a href="/investor/favorites/<?= (int)$p['id'] ?>/remove" style="flex:1;text-align:center;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:.6rem;border-radius:9px;text-decoration:none;font-size:.85rem;">♥ Suivi</a>
              <?php else: ?>
                <a href="/investor/favorites/<?= (int)$p['id'] ?>/add" style="flex:1;text-align:center;background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:.6rem;border-radius:9px;text-decoration:none;font-size:.85rem;">♡ Suivre</a>
              <?php endif; ?>
              <a href="/investor/data-room/<?= (int)$p['id'] ?>" style="flex:1;text-align:center;background:#f8fafc;border:1px solid #e2e8f0;padding:.6rem;border-radius:9px;text-decoration:none;color:#0f2a44;font-weight:600;font-size:.85rem;">Data Room</a>
            </div>
            <p style="margin:.6rem 0 0;font-size:.75rem;color:#64748b;">Après votre intérêt : propositions Data Room / RDV / Contacter / Suivre (§7).</p>
          <?php endif; ?>
        <?php endif; ?>

        <div style="margin-top:1rem;padding:.8rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;font-size:.78rem;color:#475569;">
          <strong>Parcours §14 :</strong> Visiteur → Marketplace → Intérêt → Connexion/Inscription → Retour auto → Confirmation → CRM → KYC → Data Room → RDV → Due diligence → Investissement.
        </div>
      </div>

      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:1rem;">
        <h4 style="margin:0 0 .6rem;color:#0f2a44;">À propos de l’investissement</h4>
        <div style="font-size:.85rem;color:#475569;line-height:1.6;">
          <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid #f1f5f9;"><span>Type</span><strong><?= htmlspecialchars($p['investment_type'] ?? $p['type'] ?? '—') ?></strong></div>
          <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid #f1f5f9;"><span>Montant recherché</span><strong><?= $sought?number_format($sought):'—' ?> $</strong></div>
          <div style="display:flex;justify-content:space-between;padding:.4rem 0;"><span>Mobilisé</span><strong><?= number_format($mobilized) ?> $ (<?= $progress ?>%)</strong></div>
        </div>
        <div style="margin-top:.8rem;font-size:.75rem;color:#64748b;padding:.6rem;background:#f8fafc;border-radius:8px;">Les chiffres sont indicatifs et mis à jour par Urbanova. Validation Data Room requise avant engagement.</div>
      </div>
    </div>
  </div>
</div>

<!-- Modales identiques à index (réutilisées) -->
<div id="modal-visitor" style="display:none;position:fixed;inset:0;background:rgba(15,42,68,.55);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:#fff;max-width:540px;width:100%;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);">
    <div style="padding:1.6rem 1.6rem 1rem;">
      <div style="width:48px;height:48px;background:#fef3c7;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">🔒</div>
      <h3 style="margin:1rem 0 .4rem;color:#0f2a44;">Créez votre compte investisseur</h3>
      <p style="margin:0;color:#475569;line-height:1.5;">Vous devez disposer d’un compte investisseur Urbanova pour manifester votre intérêt. <strong>Votre projet reste enregistré</strong>.</p>
    </div>
    <div style="padding:0 1.6rem 1.6rem;display:flex;flex-direction:column;gap:.6rem;">
      <a href="/register?role=investor&interest=<?= (int)$p['id'] ?>" class="btn" style="background:#0f2a44;color:#fff;padding:.85rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:800;">Créer mon compte investisseur</a>
      <a href="/login?interest=<?= (int)$p['id'] ?>" class="btn" style="background:#fff;border:1.5px solid #0f2a44;color:#0f2a44;padding:.85rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:700;">Se connecter</a>
      <button onclick="closeModals()" style="background:#f1f5f9;color:#0f2a44;border:0;padding:.75rem;border-radius:10px;font-weight:600;cursor:pointer;">Continuer à consulter</button>
    </div>
  </div>
</div>
<div id="modal-activate" style="display:none;position:fixed;inset:0;background:rgba(15,42,68,.55);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:#fff;max-width:540px;width:100%;border-radius:16px;overflow:hidden;">
    <div style="padding:1.6rem;">
      <h3 style="margin:0;color:#0f2a44;">Activez votre profil Investisseur</h3>
      <p style="color:#475569;">Vous n’avez pas besoin de créer un second compte — activez votre profil.</p>
    </div>
    <div style="padding:0 1.6rem 1.6rem;display:flex;flex-direction:column;gap:.6rem;">
      <form method="POST" action="/investor/activate"><input type="hidden" name="interest" value="<?= (int)$p['id'] ?>"><button type="submit" style="width:100%;background:#0f766e;color:#fff;padding:.85rem;border-radius:10px;border:0;font-weight:800;cursor:pointer;">Activer mon profil investisseur</button></form>
      <button onclick="closeModals()" style="background:#f1f5f9;color:#0f2a44;border:0;padding:.75rem;border-radius:10px;font-weight:600;cursor:pointer;">Plus tard</button>
    </div>
  </div>
</div>
<div id="modal-success" style="display:none;position:fixed;inset:0;background:rgba(15,42,68,.55);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:#fff;max-width:560px;width:100%;border-radius:16px;overflow:hidden;">
    <div style="padding:1.6rem;">
      <h3 style="color:#0f2a44;margin:0;">✓ Votre intérêt a bien été enregistré</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-top:1rem;">
        <a href="/investor/data-room/<?= (int)$p['id'] ?>" style="background:#0f2a44;color:#fff;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:700;">Demander la Data Room</a>
        <a href="/investor/messages" style="background:#fff;border:1.5px solid #0f2a44;color:#0f2a44;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;font-weight:700;">Prendre RDV</a>
        <a href="/contact" style="background:#f8fafc;border:1px solid #e2e8f0;color:#0f2a44;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;">Contacter Urbanova</a>
        <a href="/investor/favorites/<?= (int)$p['id'] ?>/add" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:.75rem;border-radius:10px;text-align:center;text-decoration:none;">♡ Suivre</a>
      </div>
    </div>
    <div style="padding:0 1.6rem 1.6rem;"><button onclick="closeModals()" style="width:100%;background:#f1f5f9;color:#0f2a44;border:0;padding:.75rem;border-radius:10px;font-weight:600;cursor:pointer;">Fermer</button></div>
  </div>
</div>
<div id="modal-confirm" style="display:none;position:fixed;inset:0;background:rgba(15,42,68,.6);backdrop-filter:blur(6px);z-index:10000;align-items:center;justify-content:center;padding:1rem;">
  <div style="background:#fff;max-width:520px;width:100%;border-radius:16px;padding:1.6rem;box-shadow:0 20px 60px rgba(0,0,0,.25);text-align:center;">
    <div style="width:52px;height:52px;background:#dcfce7;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:1.5rem;">↩️</div>
    <h3 style="margin:1rem 0 .4rem;color:#0f2a44;">Bon retour ! Confirmez votre intérêt</h3>
    <p style="margin:0;color:#475569;">Vous aviez manifesté votre intérêt pour <strong><?= htmlspecialchars($p['title']) ?></strong>. Cliquez pour confirmer et créer votre opportunité CRM.</p>
    <div style="display:flex;flex-direction:column;gap:.6rem;margin-top:1.2rem;">
      <button onclick="handleInterest(<?= (int)$p['id'] ?>)" style="background:#0f2a44;color:#fff;padding:.85rem;border-radius:10px;border:0;font-weight:800;cursor:pointer;">Confirmer mon intérêt</button>
      <button onclick="document.getElementById('modal-confirm').style.display='none'" style="background:#f1f5f9;color:#0f2a44;border:0;padding:.75rem;border-radius:10px;font-weight:600;cursor:pointer;">Plus tard</button>
    </div>
  </div>
</div>
<div id="toast" style="display:none;position:fixed;bottom:1.2rem;left:50%;transform:translateX(-50%);background:#0f2a44;color:#fff;padding:.8rem 1.2rem;border-radius:10px;z-index:9999;">Intérêt enregistré</div>

<script>
const isLogged = <?= $isLogged?'true':'false' ?>;
const userRole = <?= json_encode($_SESSION['user_role'] ?? 'visitor') ?>;
const confirmInterest = <?= $confirmInterest?'true':'false' ?>;
function toast(m){const t=document.getElementById('toast');t.textContent=m;t.style.display='block';setTimeout(()=>t.style.display='none',3200);}
function closeModals(){document.getElementById('modal-visitor').style.display='none';document.getElementById('modal-activate').style.display='none';document.getElementById('modal-success').style.display='none';}
document.querySelectorAll('#modal-visitor,#modal-activate,#modal-success,#modal-confirm').forEach(el=>el.addEventListener('click',e=>{if(e.target===el)el.style.display='none';}));
function switchTab(tab){
  document.getElementById('panel-public').style.display= tab==='public'?'block':'none';
  document.getElementById('panel-dataroom').style.display= tab==='dataroom'?'block':'none';
  document.getElementById('tab-public').style.background= tab==='public'?'#0f2a44':'#f8fafc';
  document.getElementById('tab-public').style.color= tab==='public'?'#fff':'#64748b';
  document.getElementById('tab-dataroom').style.background= tab==='dataroom'?'#0f2a44':'#f8fafc';
  document.getElementById('tab-dataroom').style.color= tab==='dataroom'?'#fff':'#64748b';
}
function handleInterest(projectId){
  try{localStorage.setItem('pending_interest_project_id',projectId);sessionStorage.setItem('pending_interest_project_id',projectId);}catch(e){}
  if(!isLogged){
    document.getElementById('modal-visitor').style.display='flex';
    return;
  }
  if(userRole!=='investor'){
    document.getElementById('modal-activate').style.display='flex';
    return;
  }
  fetch('/investor/interest/'+projectId, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body:'investment_amount=&message='})
    .then(r=> r.headers.get('content-type')?.includes('application/json') ? r.json() : r.text().then(t=>{try{return JSON.parse(t);}catch(e){return {success: true}}}))
    .then(data=>{
      if(data.need_auth){ window.location='/login?interest='+projectId; return;}
      if(data.need_activation){ document.getElementById('modal-activate').style.display='flex'; return;}
      document.getElementById('modal-success').style.display='flex';
      toast('Votre intérêt a bien été enregistré — opportunité CRM créée');
      document.getElementById('modal-confirm').style.display='none';
    })
    .catch(()=>{ document.getElementById('modal-success').style.display='flex'; toast('Votre intérêt a bien été enregistré');});
}
// Retour auto après inscription/connexion : afficher Confirmer mon intérêt
if(confirmInterest){
  document.getElementById('modal-confirm').style.display='flex';
  const btn=document.getElementById('confirm-interest-btn'); if(btn) btn.style.display='block';
  // Nettoyer localStorage après usage
  try{ localStorage.removeItem('pending_interest_project_id');}catch(e){}
}
// Gérer ?confirm_interest=1 dans URL même si PHP flag non set (fallback)
if(new URLSearchParams(location.search).has('confirm_interest') || new URLSearchParams(location.search).has('confirm')){
  document.getElementById('modal-confirm').style.display='flex';
}
</script>
<style>
@media(max-width:900px){ .container[style*="grid-template-columns:1fr 340px"]{grid-template-columns:1fr !important;} }
</style>
</div>
<?php require_once APP_PATH . '/Views/layouts/footer.php'; ?>
