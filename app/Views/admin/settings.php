<?php ob_start();?>
<div class="page-header"><div><h1>Paramètres & Administration</h1><div class="breadcrumb">Admin / Paramètres • RBAC • Rôles</div></div></div>
<div class="grid-2">
<div class="card card-pad">
<h3 style="font-weight:900">Utilisateurs internes</h3>
<p class="muted" style="font-size:12px">Route: /admin/settings/admin-users</p>
<div class="table-wrap" style="margin-top:8px"><table><thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Statut</th><th>Dernière connexion</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($admins??[]) as $a): ?>
<tr><td><?=htmlspecialchars($a['full_name']??$a['name']??'—')?></td><td><?=htmlspecialchars($a['email'])?></td><td><?=htmlspecialchars($a['phone']??'—')?></td><td><span class="pill" style="background:#f1f5f9"><?=htmlspecialchars($a['role'])?></span></td><td><?=\App\Helpers\AdminHelper::getStatusBadge($a['status'])?></td><td><?=htmlspecialchars($a['last_login_at']??'—')?></td><td><div style="display:flex;gap:6px"><a href="#" class="btn btn-white btn-sm">Modifier rôle</a><a href="#" class="btn btn-white btn-sm">Suspendre</a></div></td></tr>
<?php endforeach; if(empty($admins)) echo '<tr><td colspan=7 class="muted">Aucun admin interne</td></tr>'; ?>
</tbody></table></div>
<div style="margin-top:8px"><a href="#" class="btn btn-primary btn-sm"><i class="fa-solid fa-user-plus"></i> Inviter admin</a></div>
</div>
<div class="card card-pad">
<h3 style="font-weight:900">Rôles (Spec 62)</h3>
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px">
<?php foreach(($roles??[]) as $r): ?><span class="pill" style="background:#f8fafc;border-color:#e2e8f0"><?=htmlspecialchars($r['display_name'])?> <small class="muted">(<?=htmlspecialchars($r['name'])?>)</small></span><?php endforeach; if(empty($roles)) echo '<span class="muted">Aucun</span>'; ?>
</div>
<h3 style="font-weight:900;margin-top:14px">Matrice des permissions (Spec 63)</h3>
<p class="muted" style="font-size:12px">Voir / Créer / Modifier / Supprimer / Approuver / Exporter par module</p>
<div class="table-wrap" style="margin-top:8px"><table><thead><tr><th>Module</th><th>Super Admin</th><th>CRM</th><th>Projet</th><th>Finance</th></tr></thead><tbody>
<tr><td>CRM</td><td>Tout</td><td>Tout</td><td>Lecture</td><td>Lecture</td></tr>
<tr><td>Projets</td><td>Tout</td><td>Lecture</td><td>Tout</td><td>Lecture</td></tr>
<tr><td>Investissements</td><td>Tout</td><td>Lecture</td><td>Lecture</td><td>Tout</td></tr>
<tr><td>Commissions</td><td>Tout</td><td>Non</td><td>Lecture</td><td>Tout</td></tr>
<tr><td>Paramètres</td><td>Tout</td><td>Non</td><td>Non</td><td>Non</td></tr>
</tbody></table></div>
<h3 style="font-weight:900;margin-top:14px">Permissions détaillées</h3>
<ul style="font-size:12px;margin-left:16px"><?php foreach(($permissions??[]) as $p): ?><li><b><?=htmlspecialchars($p['name'])?></b> — <?=htmlspecialchars($p['description'])?> <small class="muted">(<?=htmlspecialchars($p['module'])?>)</small></li><?php endforeach; ?></ul>
</div>
</div>
<div class="card card-pad" style="margin-top:14px">
<h3 style="font-weight:900">Règles & Sécurité</h3>
<ul style="font-size:13px;margin-left:16px;line-height:1.8">
<li>Soft Delete privilégié — restauration par Super Admin</li>
<li>Duplication empêchée: email, téléphone, RCCM, NIF, référence — avertissement “Un contact similaire existe déjà.”</li>
<li>Identifiants auto: PRJ-2026-0001, LEAD-2026-0001, OPP-2026-0001, INV-2026-0001, REQ-2026-0001</li>
<li>Cohérence financière: montant <0 interdit, levé > objectif sans validation interdit, commission > transaction interdit, proba >100% interdit</li>
<li>Emails auto & automatisations (A1-A10) configurables</li>
<li>HTTPS, hash mots de passe, 2FA Admin recommandé, logs sécurité, backup quotidien</li>
</ul>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
