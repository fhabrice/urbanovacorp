<?php ob_start();?>
<div class="page-header"><div><h1>Entreprises</h1><div class="breadcrumb">Admin / CRM / Entreprises</div></div><div style="display:flex;gap:8px"><a href="/admin/crm/companies/create" class="btn btn-primary btn-sm">Ajouter</a><a href="/admin/export?module=companies&type=csv" class="btn btn-white btn-sm">Exporter</a></div></div>
<div class="card card-pad" style="margin-bottom:12px"><form method="GET" style="display:flex;gap:8px"><input name="q" placeholder="Nom, RCCM, NIF..." value="<?=htmlspecialchars($_GET['q']??'')?>" style="flex:1;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><button class="btn btn-dark btn-sm">Filtrer</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Entreprise</th><th>Forme</th><th>RCCM</th><th>NIF</th><th>ID National</th><th>Secteur</th><th>Adresse / Ville / Pays</th><th>Contact</th><th>Responsable Urbanova</th></tr></thead><tbody>
<?php foreach(($companies??[]) as $co): ?>
<tr>
<td><b><?=htmlspecialchars($co['name'])?></b><br><small class="muted"><?=htmlspecialchars($co['reference'])?></small></td>
<td><?=htmlspecialchars($co['legal_form']??'—')?></td>
<td><?=htmlspecialchars($co['rccm']??'—')?></td>
<td><?=htmlspecialchars($co['nif']??'—')?></td>
<td><?=htmlspecialchars($co['id_national']??'—')?></td>
<td><?=htmlspecialchars($co['sector']??'—')?></td>
<td><?=htmlspecialchars(($co['address']??'').' '.($co['city']??'').' '.($co['country']??''))?></td>
<td><?=htmlspecialchars($co['phone']??'—')?><br><small class="muted"><?=htmlspecialchars($co['email']??'')?></small></td>
<td><?=htmlspecialchars($co['resp_name']??'—')?></td>
</tr>
<?php endforeach; if(empty($companies)) echo '<tr><td colspan=9 class="muted">Aucune entreprise</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
