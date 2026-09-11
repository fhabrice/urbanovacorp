<?php ob_start();?>
<div class="page-header"><div><h1>Tâches & Relances</h1><div class="breadcrumb">Admin / CRM / Tâches</div></div><div style="display:flex;gap:8px"><a href="/admin/crm/tasks/create" class="btn btn-primary btn-sm">Nouvelle tâche</a></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
<select name="status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous statuts</option><option value="a_faire">À faire</option><option value="en_cours">En cours</option><option value="terminee">Terminée</option></select>
<select name="priority" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Toutes priorités</option><option value="urgente">Urgente</option><option value="haute">Haute</option><option value="normale">Normale</option><option value="faible">Faible</option></select>
<button class="btn btn-dark btn-sm">Filtrer</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th>Titre</th><th>Responsable</th><th>Contact/Projet/Opportunité</th><th>Priorité</th><th>Échéance</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($tasks??[]) as $t): ?>
<tr>
<td><b><?=htmlspecialchars($t['title'])?></b><br><small class="muted"><?=htmlspecialchars(substr($t['description']??'',0,80))?></small></td>
<td><?=htmlspecialchars($t['resp_name']??'—')?></td>
<td class="muted" style="font-size:12px"><?=htmlspecialchars($t['opp_name']??'')?> <?=htmlspecialchars($t['project_title']??'')?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($t['priority'])?></td>
<td><?=htmlspecialchars($t['due_date'])?> <?=htmlspecialchars($t['due_time']??'')?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($t['status'])?></td>
<td><div style="display:flex;gap:4px">
<a href="/admin/crm/tasks/<?=$t['id']?>/status?status=en_cours" class="btn btn-white btn-sm">En cours</a>
<a href="/admin/crm/tasks/<?=$t['id']?>/status?status=terminee" class="btn btn-white btn-sm" style="color:#059669">Terminée</a>
</div></td>
</tr>
<?php endforeach; if(empty($tasks)) echo '<tr><td colspan=7 class="muted">Aucune tâche — automatisation: lead sans activité 5j → création relance auto</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
