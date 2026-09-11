<?php ob_start();?>
<div class="page-header"><div><h1>Levées de fonds</h1><div class="breadcrumb">Admin / Levées de fonds</div></div><a href="/admin/export?module=funding&type=csv" class="btn btn-white btn-sm">Exporter</a></div>
<div class="table-wrap"><table><thead><tr><th>Campagne</th><th>Projet</th><th>Objectif</th><th>Levé</th><th>Progression</th><th>Investisseurs</th><th>Début</th><th>Fin</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($campaigns??[]) as $c): $obj=$c['target_amount']??$c['objective']??$c['funding_sought']??0; $raised=$c['amount_raised']??0; $pct=$obj?round($raised/$obj*100,1):0; ?>
<tr>
<td><b><?=htmlspecialchars($c['title']??$c['project_title']??'Campagne #'.$c['id'])?></b><br><small class="muted"><?=htmlspecialchars($c['reference']??'')?></small></td>
<td><?=htmlspecialchars($c['project_title']??'—')?></td>
<td><?=number_format($obj,0,'',' ')?> <?=$c['currency']??'USD'?></td>
<td><?=number_format($raised,0,'',' ')?> <?=$c['currency']??'USD'?></td>
<td><div style="width:100px;background:#f1f5f9;border-radius:8px;height:8px;overflow:hidden"><div style="width:<?=min(100,$pct)?>%;height:100%;background:<?= $pct>=100?'#10B981':($pct>=80?'#f59e0b':'#0B132B')?>"></div></div><small class="muted"><?=$pct?>%</small></td>
<td class="muted">—</td>
<td><?=htmlspecialchars($c['start_date']??substr($c['created_at']??'',0,10))?></td>
<td><?=htmlspecialchars($c['end_date']??'—')?></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($c['status']??'preparation')?></td>
<td><a href="/admin/fundraising/<?=$c['id']?>" class="btn btn-white btn-sm"><i class="fa-regular fa-eye"></i></a></td>
</tr>
<?php endforeach; if(empty($campaigns)) echo '<tr><td colspan=10 class="muted">Aucune campagne</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
