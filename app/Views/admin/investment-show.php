<?php ob_start();?>
<div class="page-header"><div><h1><?=htmlspecialchars($investment['reference']??'#'.$investment['id'])?></h1><div class="breadcrumb"><a href="/admin/investments">Investissements</a> / #<?=$investment['id']?></div></div><a href="/admin/investments" class="btn btn-white btn-sm">Retour</a></div>
<div class="card card-pad">
<p><b>Investisseur:</b> <?=htmlspecialchars($investment['investor_name']??$investment['investor_id'])?> • <b>Projet:</b> <?=htmlspecialchars($investment['project_title']??$investment['project_id'])?> • <b>Campagne:</b> <?=htmlspecialchars($investment['campaign_id']??'—')?></p>
<p><b>Montant:</b> <?=number_format($investment['amount'],0,'',' ')?> <?=htmlspecialchars($investment['currency']??'USD')?> • <b>Date engagement:</b> <?=htmlspecialchars($investment['engagement_date']??substr($investment['created_at']??'',0,10))?> • <b>Date paiement:</b> <?=htmlspecialchars($investment['payment_date']??'—')?></p>
<p><b>Type:</b> <?=htmlspecialchars($investment['investment_type']??'—')?> • <b>Mode paiement:</b> <?=htmlspecialchars($investment['payment_mode']??'—')?> • <b>Statut:</b> <?=\App\Helpers\AdminHelper::getStatusBadge($investment['status'])?></p>
<p><b>Commission Urbanova:</b> <?=number_format($investment['commission_amount']??0,0,'',' ')?> $ (<?=htmlspecialchars($investment['commission_rate']??'3')?>%)</p>
<p><b>Notes:</b> <?=htmlspecialchars($investment['notes']??'—')?></p>
<?php if($investment['status']!=='confirme'): ?><a href="/admin/investments/<?=$investment['id']?>/confirm" class="btn btn-primary" onclick="return confirm('Confirmer?')">Confirmer investissement — MAJ auto</a><?php endif; ?>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
