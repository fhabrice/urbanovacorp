<?php ob_start(); $pageTitle='Fiche utilisateur'; ?>
<div class="page-header"><div><h1>Fiche utilisateur #<?=htmlspecialchars($user['id'])?></h1><div class="breadcrumb"><a href="/admin/users">Utilisateurs</a> / #<?=$user['id']?></div></div><a href="/admin/users" class="btn btn-white btn-sm">Retour</a></div>
<div class="grid-2">
<div class="card card-pad">
<h3 style="font-weight:900;margin-bottom:8px">Profil</h3>
<p><b>Nom:</b> <?=htmlspecialchars(trim(($user['first_name']??'').' '.($user['last_name']??''))?:($user['name']??'—'))?></p>
<p><b>Email:</b> <?=htmlspecialchars($user['email'])?></p>
<p><b>Téléphone:</b> <?=htmlspecialchars($user['phone']??'—')?></p>
<p><b>Rôle:</b> <?=htmlspecialchars($user['role'])?> • <b>Statut:</b> <?=\App\Helpers\AdminHelper::getStatusBadge($user['status'])?></p>
<p><b>Pays:</b> <?=htmlspecialchars($user['country']??'—')?> <b>Ville:</b> <?=htmlspecialchars($user['city']??'—')?></p>
<div style="margin-top:10px;display:flex;gap:6px"><a href="/admin/users/<?=$user['id']?>/toggle" class="btn btn-white btn-sm">Activer / Suspendre</a><a href="/admin/users/<?=$user['id']?>/reset-password" class="btn btn-dark btn-sm">Réinitialiser mot de passe</a></div>
</div>
<div class="card card-pad">
<h3 style="font-weight:900;margin-bottom:8px">Onglets</h3>
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px">
<span class="pill" style="background:#f8fafc">Profil</span><span class="pill" style="background:#f8fafc">Activités</span><span class="pill" style="background:#f8fafc">Projets (<?=count($projects??[])?>)</span><span class="pill" style="background:#f8fafc">Investissements (<?=count($investments??[])?>)</span><span class="pill" style="background:#f8fafc">Demandes</span><span class="pill" style="background:#f8fafc">Data Rooms</span><span class="pill" style="background:#f8fafc">Documents</span><span class="pill" style="background:#f8fafc">Historique</span>
</div>
<h4 style="font-size:13px;font-weight:800">Projets</h4>
<ul style="font-size:13px;margin-left:16px"><?php foreach(($projects??[]) as $p): ?><li><?=htmlspecialchars($p['title'])?> — <small class="muted"><?=htmlspecialchars($p['status'])?></small></li><?php endforeach; if(empty($projects)) echo '<li class="muted">Aucun</li>'; ?></ul>
<h4 style="font-size:13px;font-weight:800;margin-top:10px">Investissements</h4>
<ul style="font-size:13px;margin-left:16px"><?php foreach(($investments??[]) as $inv): ?><li><?=htmlspecialchars($inv['reference']??'#'.$inv['id'])?> — <?=number_format($inv['amount'],0,'',' ')?> $ — <?=htmlspecialchars($inv['status'])?> — <?=htmlspecialchars($inv['project_title']??'')?></li><?php endforeach; if(empty($investments)) echo '<li class="muted">Aucun</li>'; ?></ul>
</div>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
