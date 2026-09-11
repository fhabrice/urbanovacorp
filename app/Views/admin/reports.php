<?php ob_start();?>
<div class="page-header"><div><h1>Rapports</h1><div class="breadcrumb">Admin / Rapports</div></div><div style="display:flex;gap:8px"><a href="/admin/export?module=projects&type=csv" class="btn btn-white btn-sm">Excel</a><a href="/admin/export?module=projects&type=excel" class="btn btn-white btn-sm">PDF</a></div></div>
<div class="grid-2">
<div class="card card-pad">
<h3 style="font-weight:900">Rapport CRM</h3>
<p>Leads créés: <b><?= $crm['leads_created']??0?></b> • Convertis: <b><?= $crm['leads_converted']??0 ?></b> • Opportunités: <b><?= $crm['opportunities']??0 ?></b></p>
<p>Taux conversion: <b><?php $conv=($crm['leads_created']??0)? round(($crm['leads_converted']??0)/($crm['leads_created']??1)*100,1):0; echo $conv;?>%</b> • Valeur pipeline: <b><?=number_format($crm['pipeline_value']??0,0,'',' ')?> $</b></p>
<p>Valeur gagnée: <b><?=number_format($crm['won']??0,0,'',' ')?> $</b> • Valeur perdue: <b><?=number_format($crm['lost']??0,0,'',' ')?> $</b></p>
<h4 style="font-size:12px;font-weight:800;margin-top:8px">Performance par commercial</h4>
<ul style="font-size:12px;margin-left:16px"><?php foreach(($crm['by_user']??[]) as $u): ?><li><?=htmlspecialchars($u['name']??'—')?> — <?= $u['cnt']?> opps — <?=number_format($u['tot'],0,'',' ')?> $</li><?php endforeach;?></ul>
</div>
<div class="card card-pad">
<h3 style="font-weight:900">Rapport Investissements</h3>
<p>Montant total: <b><?=number_format($investReport['total']??0,0,'',' ')?> $</b> • Transactions: <b><?= $investReport['count']??0?></b> • Ticket moyen: <b><?=number_format($investReport['avg']??0,0,'',' ')?> $</b></p>
<h4 style="font-size:12px;font-weight:800;margin-top:8px">Par secteur</h4>
<ul style="font-size:12px;margin-left:16px"><?php foreach(($investReport['by_sector']??[]) as $s): ?><li><?=htmlspecialchars($s['sector']??'—')?> — <?=number_format($s['tot'],0,'',' ')?> $</li><?php endforeach;?></ul>
</div>
</div>
<div class="card card-pad" style="margin-top:14px">
<h3 style="font-weight:900">Rapport levée de fonds (par projet)</h3>
<div class="table-wrap"><table><thead><tr><th>Projet</th><th>Objectif</th><th>Levé</th><th>Reste</th><th>%</th><th>Investisseurs</th><th>Durée</th></tr></thead><tbody>
<?php foreach(($fundReport??[]) as $f): $reste=($f['objectif']??0)-($f['leve']??0); ?>
<tr><td><?=htmlspecialchars($f['title'])?></td><td><?=number_format($f['objectif']??0,0,'',' ')?> $</td><td><?=number_format($f['leve']??0,0,'',' ')?> $</td><td><?=number_format($reste,0,'',' ')?> $</td><td><?=$f['pct']?>%</td><td><?=$f['nb']??0?></td><td class="muted">—</td></tr>
<?php endforeach; ?>
</tbody></table></div>
</div>
<div class="card card-pad" style="margin-top:14px">
<h3 style="font-weight:900">Rapport commissions</h3>
<p>Attendues: <b><?=number_format($commReport['expected']??0,0,'',' ')?> $</b> • Facturées: <b><?=number_format($commReport['invoiced']??0,0,'',' ')?> $</b> • Encaissées: <b><?=number_format($commReport['collected']??0,0,'',' ')?> $</b> • Impayées: <b><?=number_format(($commReport['expected']??0)-($commReport['collected']??0),0,'',' ')?> $</b></p>
<canvas id="commChart" height="120"></canvas>
</div>
<script>
try{ new Chart(document.getElementById('commChart'),{type:'doughnut',data:{labels:['Attendues','Encaissées','Impayées'],datasets:[{data:[<?php echo (int)($commReport['expected']??0);?>,<?php echo (int)($commReport['collected']??0);?>,<?php echo max(0,(int)($commReport['expected']??0)-(int)($commReport['collected']??0));?>],backgroundColor:['#0B132B','#10B981','#f59e0b']}]},options:{responsive:true}});}catch(e){}
</script>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
