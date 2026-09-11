<?php ob_start();?>
<div class="page-header"><div><h1>Investissements</h1><div class="breadcrumb">Admin / Investissements</div></div><div style="display:flex;gap:8px"><a href="/admin/export?module=investments&type=csv" class="btn btn-white btn-sm">CSV</a></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
<input name="q" value="<?=htmlspecialchars($_GET['q']??'')?>" placeholder="Réf, investisseur, projet..." style="flex:1;min-width:200px;padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<select name="status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous statuts</option><option value="interet">Intérêt</option><option value="engagement">Engagement</option><option value="confirme">Confirmé</option><option value="annule">Annulé</option></select>
<button class="btn btn-dark btn-sm">Filtrer</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th data-sort>Référence</th><th>Investisseur</th><th>Projet</th><th>Campagne</th><th data-sort>Montant</th><th>Date</th><th>Statut</th><th>Commission</th><th>Responsable</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($investments??[]) as $inv): ?>
<tr>
<td><b><?=htmlspecialchars($inv['reference']??'#'.$inv['id'])?></b><br><small class="muted">#<?=$inv['id']?></small></td>
<td><?=htmlspecialchars($inv['investor_name']??'Invest #'.$inv['investor_id'])?></td>
<td><?=htmlspecialchars($inv['project_title']??'#'.$inv['project_id'])?></td>
<td><?=htmlspecialchars($inv['campaign_title']??'—')?></td>
<td><?=number_format($inv['amount'],0,'',' ')?> <?=htmlspecialchars($inv['currency']??'USD')?></td>
<td><?=htmlspecialchars(substr($inv['created_at']??$inv['engagement_date']??'',0,10))?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($inv['status'])?></td>
<td><?=number_format($inv['commission_amount']??0,0,'',' ')?> $ (<?=htmlspecialchars($inv['commission_rate']??'3')?>%)</td>
<td class="muted"><?=htmlspecialchars($inv['responsible_id']??'—')?></td>
<td><div style="display:flex;gap:6px"><a href="/admin/investments/<?=$inv['id']?>" class="btn btn-white btn-sm"><i class="fa-regular fa-eye"></i></a><?php if($inv['status']!=='confirme'): ?><a href="/admin/investments/<?=$inv['id']?>/confirm" onclick="return confirm('Confirmer cet investissement? Mise à jour auto projet/campagne/commission')" class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i></a><?php endif; ?></div></td>
</tr>
<?php endforeach; if(empty($investments)) echo '<tr><td colspan=10 class="muted">Aucun investissement</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
