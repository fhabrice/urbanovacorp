<?php ob_start();?>
<div class="page-header"><div><h1>Visites / Réservations</h1><div class="breadcrumb">Admin / Rendez-vous</div></div><a href="/admin/export?module=appointments&type=csv" class="btn btn-white btn-sm">Exporter</a></div>
<div class="table-wrap"><table><thead><tr><th>Utilisateur</th><th>Projet / Bien</th><th>Date</th><th>Heure</th><th>Agent</th><th>Objet</th><th>Statut</th><th>Notes</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($appointments??[]) as $a): ?>
<tr>
<td><?=htmlspecialchars($a['user_name']??$a['visitor_name']??$a['customer_name']??'—')?><br><small class="muted"><?=htmlspecialchars($a['visitor_email']??$a['customer_email']??'')?></small></td>
<td><?=htmlspecialchars($a['project_title']??'#'.$a['project_id']??'—')?></td>
<td><?=htmlspecialchars($a['appointment_date']??$a['preferred_date']??substr($a['created_at']??'',0,10))?></td>
<td><?=htmlspecialchars($a['appointment_time']??$a['preferred_time']??'—')?></td>
<td><?=htmlspecialchars($a['agent_id']??'—')?></td>
<td><?=htmlspecialchars($a['subject']??$a['reservation_type']??$a['message']??'—')?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($a['status'])?></td>
<td class="muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars($a['notes']??$a['message']??'—')?></td>
<td><div style="display:flex;gap:6px"><a href="#" class="btn btn-white btn-sm" onclick="return confirm('Confirmer visite?')">Confirmer</a><a href="#" class="btn btn-white btn-sm">Reporter</a></div></td>
</tr>
<?php endforeach; if(empty($appointments)) echo '<tr><td colspan=9 class="muted">Aucun rendez-vous</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
