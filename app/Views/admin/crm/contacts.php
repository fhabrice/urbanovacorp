<?php ob_start();?>
<div class="page-header"><div><h1>Contacts CRM</h1><div class="breadcrumb">Admin / CRM / Contacts</div></div><div style="display:flex;gap:8px"><a href="/admin/crm/leads/create" class="btn btn-white btn-sm">Nouveau contact</a><a href="/admin/export?module=contacts&type=csv" class="btn btn-white btn-sm">Exporter</a></div></div>
<div class="card card-pad" style="margin-bottom:12px"><form method="GET" style="display:flex;gap:8px"><input name="q" value="<?=htmlspecialchars($_GET['q']??'')?>" placeholder="Recherche..." style="flex:1;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><button class="btn btn-dark btn-sm">Filtrer</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>WhatsApp</th><th>Fonction</th><th>Entreprise</th><th>Pays/Ville</th><th>Type</th><th>Responsable</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($contacts??[]) as $c): ?>
<tr>
<td><b><?=htmlspecialchars(($c['first_name']??'').' '.($c['last_name']??$c['name']??''))?></b></td>
<td><?=htmlspecialchars($c['email']??'—')?></td>
<td><?=htmlspecialchars($c['phone']??'—')?></td>
<td><?=htmlspecialchars($c['whatsapp']??'—')?></td>
<td><?=htmlspecialchars($c['function_title']??$c['function']??'—')?></td>
<td><?=htmlspecialchars($c['company_name']??$c['company']??'—')?></td>
<td><?=htmlspecialchars(($c['country']??'').' '.($c['city']??''))?></td>
<td><?=htmlspecialchars($c['type']??'—')?></td>
<td><?=htmlspecialchars($c['resp_name']??'—')?></td>
<td><a href="/admin/crm/contacts/<?=$c['id']?>" class="btn btn-white btn-sm"><i class="fa-regular fa-eye"></i></a></td>
</tr>
<?php endforeach; if(empty($contacts)) echo '<tr><td colspan=10 class="muted">Aucun contact</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
