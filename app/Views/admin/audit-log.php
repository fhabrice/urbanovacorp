<?php ob_start();?>
<div class="page-header"><div><h1>Journal d'audit</h1><div class="breadcrumb">Admin / Audit • Traçabilité complète</div></div><a href="/admin/export?module=audit&type=csv" class="btn btn-white btn-sm">Exporter</a></div>
<div class="table-wrap"><table><thead><tr><th>Utilisateur</th><th>Action</th><th>Module</th><th>Entité</th><th>Ancienne valeur</th><th>Nouvelle valeur</th><th>Date</th><th>Heure</th><th>IP</th></tr></thead><tbody>
<?php foreach(($logs??[]) as $l): ?>
<tr>
<td><?=htmlspecialchars($l['user_name']??'—')?> (#<?=htmlspecialchars($l['user_id']??'—')?>)</td>
<td><span class="pill" style="background:#f1f5f9"><?=htmlspecialchars($l['action'])?></span></td>
<td><?=htmlspecialchars($l['module'])?></td>
<td><?=htmlspecialchars($l['entity_type']??'—')?> #<?=htmlspecialchars($l['entity_id']??'')?></td>
<td class="muted" style="max-width:150px;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars(substr($l['old_value']??'—',0,80))?></td>
<td class="muted" style="max-width:150px;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars(substr($l['new_value']??'—',0,80))?></td>
<td><?=htmlspecialchars(substr($l['created_at']??'',0,10))?></td>
<td><?=htmlspecialchars(substr($l['created_at']??'',11,5))?></td>
<td><?=htmlspecialchars($l['ip_address']??'—')?></td>
</tr>
<?php endforeach; if(empty($logs)) echo '<tr><td colspan=9 class="muted">Aucun log — actions sensibles: suppression, validation, rejet, modification montant, accès Data Room, permission</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
