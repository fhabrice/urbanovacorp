<?php ob_start();?>
<div class="page-header"><div><h1>Commissions</h1><div class="breadcrumb">Admin / Commissions</div></div><div style="display:flex;gap:8px"><a href="/admin/reports" class="btn btn-white btn-sm">Rapport commissions</a><a href="/admin/export?module=commissions&type=csv" class="btn btn-white btn-sm">Exporter</a></div></div>
<div class="table-wrap"><table><thead><tr><th>Transaction</th><th>Projet</th><th>Client</th><th>Montant base</th><th>Taux</th><th>Commission</th><th>Facturé</th><th>Payé</th><th>Solde</th><th>Statut</th></tr></thead><tbody>
<?php foreach(($commissions??[]) as $c): ?>
<tr>
<td><?=htmlspecialchars($c['reference'])?><br><small class="muted">Inv #<?=htmlspecialchars($c['investment_id']??'—')?></small></td>
<td><?=htmlspecialchars($c['project_title']??'#'.$c['project_id'])?></td>
<td><?=htmlspecialchars($c['client_name']??'Client #'.$c['client_id'])?></td>
<td><?=number_format($c['base_amount'],0,'',' ')?> $</td>
<td><?=htmlspecialchars($c['rate'])?>%</td>
<td><b><?=number_format($c['commission_amount']??($c['base_amount']*$c['rate']/100),0,'',' ')?> $</b><br><small class="muted">Formule: Base × Taux</small></td>
<td><?=number_format($c['invoiced']??0,0,'',' ')?> $</td>
<td><?=number_format($c['paid']??0,0,'',' ')?> $</td>
<td><?=number_format($c['balance']??($c['commission_amount']-$c['paid']),0,'',' ')?> $</td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($c['status'])?></td>
</tr>
<?php endforeach; if(empty($commissions)) echo '<tr><td colspan=10 class="muted">Aucune commission — confirm a deal to auto-générer</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
