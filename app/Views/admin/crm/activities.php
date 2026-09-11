<?php ob_start();?>
<div class="page-header"><div><h1>Activités CRM</h1><div class="breadcrumb">Admin / CRM / Activités</div></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="POST" action="/admin/crm/activities/create" style="display:grid;gap:8px">
<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px">
<select name="type" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="appel">Appel</option><option value="email">Email</option><option value="whatsapp">WhatsApp</option><option value="reunion">Réunion</option><option value="visite">Visite</option><option value="note">Note</option></select>
<input name="activity_date" type="date" value="<?=date('Y-m-d')?>" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<input name="activity_time" type="time" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<input name="summary" placeholder="Résumé" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px">
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
<input name="result" placeholder="Résultat" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<input name="next_action" placeholder="Prochaine action" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px">
</div>
<button class="btn btn-primary btn-sm">Enregistrer activité</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th>Type</th><th>Contact</th><th>Opportunité</th><th>Projet</th><th>Responsable</th><th>Date</th><th>Heure</th><th>Résumé</th><th>Résultat</th><th>Prochaine action</th></tr></thead><tbody>
<?php foreach(($activities??[]) as $a): ?>
<tr><td><?=htmlspecialchars($a['type'])?></td><td><?=htmlspecialchars($a['contact_name']??'—')?></td><td><?=htmlspecialchars($a['opp_name']??'—')?></td><td><?=htmlspecialchars($a['project_title']??'—')?></td><td><?=htmlspecialchars($a['resp_name']??'—')?></td><td><?=htmlspecialchars($a['activity_date'])?></td><td><?=htmlspecialchars($a['activity_time']??'—')?></td><td><?=htmlspecialchars($a['summary']??'—')?></td><td><?=htmlspecialchars($a['result']??'—')?></td><td><?=htmlspecialchars($a['next_action']??'—')?></td></tr>
<?php endforeach; if(empty($activities)) echo '<tr><td colspan=10 class="muted">Aucune activité</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
