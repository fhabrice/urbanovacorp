<?php ob_start();?>
<div class="page-header"><div><h1>Prospects</h1><div class="breadcrumb"><a href="/admin/dashboard">Dashboard</a> / CRM / Prospects</div></div><div style="display:flex;gap:8px"><a href="/admin/crm/leads/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Ajouter</a><a href="/admin/export?module=leads&type=csv" class="btn btn-white btn-sm">Exporter</a></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
<input name="q" value="<?=htmlspecialchars($_GET['q']??'')?>" placeholder="Recherche nom, email, téléphone..." style="flex:1;min-width:200px;padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<select name="status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous statuts</option><option value="nouveau">Nouveau</option><option value="qualifie">Qualifié</option><option value="contacte">Contacté</option><option value="converti">Converti</option><option value="perdu">Perdu</option></select>
<select name="source" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Toutes sources</option><option value="site_web">Site web</option><option value="linkedin">LinkedIn</option><option value="whatsapp">WhatsApp</option></select>
<select name="type" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous types</option><option value="investisseur">Investisseur</option><option value="porteur_projet">Porteur</option></select>
<button class="btn btn-dark btn-sm">Filtrer</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th data-sort>Référence</th><th>Nom</th><th>Entreprise</th><th>Téléphone</th><th>Email</th><th>Type</th><th>Source</th><th>Projet concerné</th><th data-sort>Montant potentiel</th><th>Responsable</th><th>Statut</th><th data-sort>Date création</th><th>Prochaine action</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($leads??[]) as $l): ?>
<tr>
<td><b><?=htmlspecialchars($l['reference'])?></b></td>
<td><?=htmlspecialchars(trim(($l['first_name']??'').' '.($l['last_name'])))?></td>
<td><?=htmlspecialchars($l['company_name']??'—')?></td>
<td><?=htmlspecialchars($l['phone']??'—')?></td>
<td><?=htmlspecialchars($l['email']??'—')?></td>
<td><?=htmlspecialchars($l['type'])?></td>
<td><?=htmlspecialchars($l['source'])?></td>
<td><?=htmlspecialchars($l['project_title']??'#'.$l['project_id']??'—')?></td>
<td><?= $l['amount_potential']?number_format($l['amount_potential'],0,'',' ').' $':'—'?></td>
<td><?=htmlspecialchars($l['responsible_name']??'—')?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($l['status'])?></td>
<td><?=htmlspecialchars(substr($l['created_at']??'',0,10))?></td>
<td><?=htmlspecialchars($l['next_action_date']??'—')?></td>
<td><div style="display:flex;gap:4px;flex-wrap:wrap">
<a href="/admin/crm/leads/<?=$l['id']?>/edit" class="btn btn-white btn-sm" title="Modifier"><i class="fa-regular fa-pen-to-square"></i></a>
<a href="/admin/crm/leads/<?=$l['id']?>/convert" class="btn btn-white btn-sm" title="Convertir" onclick="return confirm('Convertir en contact/opportunité?')"><i class="fa-solid fa-right-left"></i></a>
<a href="/admin/crm/leads/<?=$l['id']?>/delete" class="btn btn-white btn-sm" style="color:#ef4444" onclick="return confirm('Supprimer? Soft Delete')"><i class="fa-regular fa-trash-can"></i></a>
</div></td>
</tr>
<?php endforeach; if(empty($leads)) echo '<tr><td colspan=14 class="muted">Aucun prospect</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
