<?php ob_start();?>
<div class="page-header"><div><h1>Utilisateurs</h1><div class="breadcrumb">Admin / Utilisateurs</div></div><div style="display:flex;gap:8px"><a href="/admin/export?module=users&type=csv" class="btn btn-white btn-sm">Exporter CSV</a><a href="/admin/export?module=users&type=excel" class="btn btn-white btn-sm">Excel</a></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
<input name="q" value="<?=htmlspecialchars($_GET['q']??'')?>" placeholder="Recherche nom, email..." style="flex:1;min-width:200px;padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px">
<select name="status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous statuts</option><option value="active" <?=($_GET['status']??'')==='active'?'selected':''?>>Actif</option><option value="pending" <?=($_GET['status']??'')==='pending'?'selected':''?>>Pending</option><option value="suspended" <?=($_GET['status']??'')==='suspended'?'selected':''?>>Suspendu</option></select>
<select name="type" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous types</option><option value="entrepreneur">Entrepreneur</option><option value="investor">Investisseur</option><option value="admin">Admin</option></select>
<button class="btn btn-dark btn-sm">Filtrer</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th data-sort>ID</th><th data-sort>Nom</th><th>Email</th><th>Téléphone</th><th>Type</th><th>Pays</th><th data-sort>Date inscription</th><th>Statut</th><th>Dernière connexion</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($users??[]) as $u): $name=trim(($u['first_name']??'').' '.($u['last_name']??''))?:($u['name']??'—'); ?>
<tr>
<td><?= $u['id']?></td>
<td><b><?=htmlspecialchars($name)?></b></td>
<td><?=htmlspecialchars($u['email'])?></td>
<td><?=htmlspecialchars($u['phone']??'—')?></td>
<td><span class="pill" style="background:#f1f5f9"><?=htmlspecialchars($u['role'])?></span></td>
<td><?=htmlspecialchars($u['country']??'—')?></td>
<td><?=htmlspecialchars(substr($u['created_at']??'',0,10))?></td>
<td><?= \App\Helpers\AdminHelper::getStatusBadge($u['status'], ucfirst($u['status'])) ?></td>
<td><?=htmlspecialchars($u['last_login_at']??'—')?></td>
<td><div style="display:flex;gap:6px">
<a href="/admin/users/<?=$u['id']?>" class="btn btn-white btn-sm" title="Voir"><i class="fa-regular fa-eye"></i></a>
<?php if($u['status']==='active'): ?><a href="/admin/users/<?=$u['id']?>/toggle?action=suspend" class="btn btn-white btn-sm" title="Suspendre"><i class="fa-solid fa-pause"></i></a><?php else: ?><a href="/admin/users/<?=$u['id']?>/toggle?action=activate" class="btn btn-white btn-sm" title="Activer"><i class="fa-solid fa-play"></i></a><?php endif; ?>
<a href="/admin/users/<?=$u['id']?>/reset-password" onclick="return confirm('Réinitialiser mot de passe?')" class="btn btn-white btn-sm" title="Reset"><i class="fa-solid fa-key"></i></a>
</div></td>
</tr>
<?php endforeach; if(empty($users)) echo '<tr><td colspan=10 class="muted">Aucun utilisateur</td></tr>'; ?>
</tbody></table></div>
<div style="margin-top:10px;display:flex;gap:8px">
<span class="muted" style="font-size:12px">Pagination: 20 / 50 / 100 — Tri par colonnes cliquables — Export CSV/Excel/PDF</span>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
