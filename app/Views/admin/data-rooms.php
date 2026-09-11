<?php ob_start();?>
<div class="page-header"><div><h1>Data Rooms</h1><div class="breadcrumb">Admin / Data Room</div></div><div style="display:flex;gap:8px"><a href="/admin/export?module=dataroom&type=csv" class="btn btn-white btn-sm">Exporter</a></div></div>
<div class="table-wrap"><table><thead><tr><th>Projet</th><th>Promoteur</th><th>Documents</th><th>Accès</th><th>Demandes</th><th>Statut</th><th>Date création</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($rooms??[]) as $r): ?>
<tr>
<td><b><?=htmlspecialchars($r['project_title']??'#'.$r['project_id'])?></b><br><small class="muted"><?=htmlspecialchars($r['project_id']??'')?></small></td>
<td><?=htmlspecialchars($r['promoter_name']??'—')?></td>
<td><?=htmlspecialchars($r['doc_count']??0)?></td>
<td><?=htmlspecialchars($r['access_count']??0)?></td>
<td class="muted"><?=htmlspecialchars($r['access_count']??0)?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($r['status']??'brouillon')?></td>
<td><?=htmlspecialchars(substr($r['created_at']??'',0,10))?></td>
<td><a href="/admin/data-rooms/<?= $r['id']??$r['project_id']?>" class="btn btn-white btn-sm"><i class="fa-regular fa-eye"></i> Voir</a></td>
</tr>
<?php endforeach; if(empty($rooms)) echo '<tr><td colspan=8 class="muted">Aucune Data Room</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
