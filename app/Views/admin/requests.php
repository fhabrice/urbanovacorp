<?php ob_start();?>
<div class="page-header"><div><h1>Demandes</h1><div class="breadcrumb">Admin / Demandes</div></div><a href="/admin/export?module=requests&type=csv" class="btn btn-white btn-sm">Exporter</a></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
<select name="type" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous types</option><option>Investissement</option><option>Financement</option><option>Accompagnement</option><option>Data Room</option><option>Visite</option></select>
<select name="status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous statuts</option><option value="nouveau">Nouveau</option><option value="assigne">Assigné</option><option value="en_traitement">En traitement</option></select>
<button class="btn btn-dark btn-sm">Filtrer</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th data-sort>Référence</th><th>Demandeur</th><th>Type</th><th>Objet</th><th>Projet</th><th>Date</th><th>Responsable</th><th>Priorité</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($requests??[]) as $r): ?>
<tr>
<td><b><?=htmlspecialchars($r['reference']??'REQ-'.$r['id'])?></b></td>
<td><?=htmlspecialchars($r['requester_name']??$r['requester_name_full']??$r['name']??'—')?><br><small class="muted"><?=htmlspecialchars($r['requester_email']??$r['email']??'')?></small></td>
<td><?=htmlspecialchars($r['type'])?></td>
<td><?=htmlspecialchars($r['subject']??$r['objet']??'—')?></td>
<td><?=htmlspecialchars($r['project_title']??'#'.$r['project_id']??'—')?></td>
<td><?=htmlspecialchars(substr($r['created_at']??'',0,10))?></td>
<td><?=htmlspecialchars($r['assignee_name']??$r['assigned_to']??'—')?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($r['priority']??'normale')?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($r['status'])?></td>
<td><a href="/admin/requests/<?=$r['id']?>/assign" class="btn btn-white btn-sm">Assigner</a></td>
</tr>
<?php endforeach; if(empty($requests)) echo '<tr><td colspan=10 class="muted">Aucune demande</td></tr>'; ?>
</tbody></table></div>
<p class="muted" style="font-size:12px;margin-top:8px">Règle: toute nouvelle demande est assignée automatiquement ou placée en file d'attente. L'admin peut réassigner.</p>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
