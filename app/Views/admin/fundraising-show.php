<?php ob_start();?>
<div class="page-header"><div><h1><?=htmlspecialchars($campaign['title']??$campaign['project_title'])?></h1><div class="breadcrumb"><a href="/admin/fundraising">Levées</a> / Campagne</div></div><a href="/admin/fundraising" class="btn btn-white btn-sm">Retour</a></div>
<div class="grid-2">
<div class="card card-pad">
<p><b>Projet:</b> <?=htmlspecialchars($campaign['project_title'])?></p>
<p><b>Objectif:</b> <?=number_format($campaign['objective']??$campaign['target_amount']??0,0,'',' ')?> <?=htmlspecialchars($campaign['currency']??'USD')?></p>
<p><b>Ticket min/max:</b> <?=number_format($campaign['ticket_min']??0,0,'',' ')?> / <?=number_format($campaign['ticket_max']??0,0,'',' ')?> USD</p>
<p><b>Dates:</b> <?=htmlspecialchars($campaign['start_date']??'')?> → <?=htmlspecialchars($campaign['end_date']??'')?></p>
<p><b>Statut:</b> <?=\App\Helpers\AdminHelper::getStatusBadge($campaign['status'])?> • <b>Description:</b> <?=htmlspecialchars($campaign['description']??'—')?></p>
<?php $obj=$campaign['objective']??$campaign['target_amount']??0; $raised=$campaign['amount_raised']??0; $pct=$obj?round($raised/$obj*100,1):0; ?>
<div style="margin-top:10px"><b>Progression:</b> <?= $pct ?>% — <?=number_format($raised,0,'',' ')?> / <?=number_format($obj,0,'',' ')?> USD <div style="width:100%;background:#f1f5f9;border-radius:8px;height:10px;overflow:hidden;margin-top:6px"><div style="width:<?=min(100,$pct)?>%;height:100%;background:#10B981"></div></div></div>
</div>
<div class="card card-pad">
<h3 style="font-weight:900">Investissements liés</h3>
<ul style="font-size:13px;margin-left:16px"><?php foreach(($investments??[]) as $inv): ?><li><?=htmlspecialchars($inv['investor_name']??'Investisseur #'.$inv['investor_id'])?> — <?=number_format($inv['amount'],0,'',' ')?> $ — <?=htmlspecialchars($inv['status'])?></li><?php endforeach; if(empty($investments)) echo '<li class="muted">Aucun</li>'; ?></ul>
</div>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
