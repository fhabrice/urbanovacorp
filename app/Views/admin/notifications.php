<?php ob_start();?>
<div class="page-header"><div><h1>Notifications</h1><div class="breadcrumb">Admin / Notifications</div></div><span class="muted">Niveaux: Information, Action requise, Important, Urgent</span></div>
<div class="table-wrap"><table><thead><tr><th>Type</th><th>Titre</th><th>Message</th><th>Niveau</th><th>Utilisateur</th><th>Date</th><th>Lu</th></tr></thead><tbody>
<?php foreach(($notifications??[]) as $n): ?>
<tr>
<td><?=htmlspecialchars($n['type'])?></td>
<td><b><?=htmlspecialchars($n['title'])?></b></td>
<td class="muted" style="max-width:300px;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars(substr($n['message'],0,120))?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($n['priority']??$n['is_read']?'information':'information')?></td>
<td><?=htmlspecialchars($n['user_name']??'#'.$n['user_id'])?></td>
<td><?=htmlspecialchars(substr($n['created_at']??'',0,16))?></td>
<td><?=($n['is_read']??0)?'Oui':'Non'?></td>
</tr>
<?php endforeach; if(empty($notifications)) echo '<tr><td colspan=7 class="muted">Aucune notification — événements: nouveau projet, nouveau compte, Data Room, investissement, tâche échue, KYC, etc.</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
