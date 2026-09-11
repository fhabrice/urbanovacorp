<?php ob_start();?>
<div class="page-header"><div><h1>Investisseurs</h1><div class="breadcrumb">Admin / Investisseurs</div></div><div style="display:flex;gap:8px"><a href="/admin/export?module=investors&type=csv" class="btn btn-white btn-sm">CSV</a><a href="/admin/reports" class="btn btn-white btn-sm">Rapports</a></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
<input name="q" value="<?=htmlspecialchars($_GET['q']??'')?>" placeholder="Nom, email, type..." style="flex:1;min-width:200px;padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<select name="status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous KYC</option><option value="pending">En attente</option><option value="approved">Validé</option><option value="rejected">Rejeté</option></select>
<select name="type" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous types</option><option value="individual">Particulier</option><option value="family_office">Family Office</option><option value="investment_fund">Fonds</option><option value="bank">Banque</option></select>
<button class="btn btn-dark btn-sm">Filtrer</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th>Nom</th><th>Type</th><th>Pays</th><th>Ticket min/max</th><th>Secteurs</th><th>KYC</th><th>Investissements</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($investors??[]) as $inv): $type=$inv['investor_type']??$inv['type']??'—'; $kyc=$inv['investor_status']??$inv['kyc_status']??'—'; ?>
<tr>
<td><b><?=htmlspecialchars($inv['full_name']??($inv['first_name'].' '.$inv['last_name']))?></b><br><small class="muted"><?=htmlspecialchars($inv['email'])?></small></td>
<td><?=htmlspecialchars($type)?></td>
<td><?=htmlspecialchars($inv['country']??'—')?></td>
<td><?= $inv['ticket_minimum']?number_format($inv['ticket_minimum'],0,'',' ').' $':'—'?> / <?= $inv['ticket_maximum']?number_format($inv['ticket_maximum'],0,'',' ').' $':'—'?></td>
<td class="muted" style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars($inv['investment_sectors']??$inv['sectors']??'—')?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($kyc)?></td>
<td class="muted">—</td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($inv['user_status']??'active')?></td>
<td><div style="display:flex;gap:6px">
<a href="/admin/investors/<?=$inv['id']?>" class="btn btn-white btn-sm"><i class="fa-regular fa-eye"></i></a>
<a href="/admin/investors/<?=$inv['id']?>/approve" class="btn btn-white btn-sm" style="color:#059669"><i class="fa-solid fa-check"></i></a>
<a href="/admin/investors/<?=$inv['id']?>/request-info" class="btn btn-white btn-sm"><i class="fa-solid fa-circle-question"></i></a>
<a href="/admin/investors/<?=$inv['id']?>/reject" onclick="return confirm('Rejeter KYC?')" class="btn btn-white btn-sm" style="color:#ef4444"><i class="fa-solid fa-xmark"></i></a>
</div></td>
</tr>
<?php endforeach; if(empty($investors)) echo '<tr><td colspan=9 class="muted">Aucun investisseur</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
