<?php ob_start();?>
<div class="page-header"><div><h1><?=htmlspecialchars($investor['full_name'])?></h1><div class="breadcrumb"><a href="/admin/investors">Investisseurs</a> / #<?=$investor['id']?></div></div><a href="/admin/investors" class="btn btn-white btn-sm">Retour</a></div>
<div class="card card-pad" style="margin-bottom:12px">
<div style="display:flex;gap:6px;flex-wrap:wrap"><span class="pill" style="background:#f8fafc">Profil</span><span class="pill" style="background:#f8fafc">Opportunités</span><span class="pill" style="background:#f8fafc">Projets suivis</span><span class="pill" style="background:#f8fafc">Data Rooms</span><span class="pill" style="background:#f8fafc">Investissements</span><span class="pill" style="background:#f8fafc">Documents</span><span class="pill" style="background:#f8fafc">Activités</span></div>
</div>
<div class="grid-2">
<div class="card card-pad">
<h3 style="font-weight:900">Informations</h3>
<p><b>Identité:</b> <?=htmlspecialchars($investor['full_name'])?> • <?=htmlspecialchars($investor['investor_type']??$investor['type'])?></p>
<p><b>Entreprise:</b> <?=htmlspecialchars($investor['company_name']??'—')?></p>
<p><b>Pays:</b> <?=htmlspecialchars($investor['country']??'—')?> • <b>Téléphone:</b> <?=htmlspecialchars($investor['phone']??'—')?></p>
<p><b>Capacité financière:</b> <?= $investor['investment_capacity']?number_format($investor['investment_capacity'],0,'',' ').' $':'—'?></p>
<p><b>Ticket:</b> <?= $investor['ticket_minimum']?number_format($investor['ticket_minimum'],0,'',' ').' $':'—'?> → <?= $investor['ticket_maximum']?number_format($investor['ticket_maximum'],0,'',' ').' $':'—'?></p>
<p><b>Secteurs:</b> <?=htmlspecialchars($investor['investment_sectors']??$investor['sectors']??'—')?></p>
<p><b>Géographies:</b> <?=htmlspecialchars($investor['geographies']??'—')?></p>
<p><b>Niveau risque:</b> <?=htmlspecialchars($investor['risk_profile']??$investor['risk_level']??'—')?> • <b>Horizon:</b> <?=htmlspecialchars($investor['investment_horizon']??'—')?></p>
<div style="margin-top:10px;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px">
<b>KYC Statut:</b> <?=\App\Helpers\AdminHelper::getStatusBadge($investor['kyc_status']??$investor['investor_status'])?><br>
<small class="muted">Documents: pièce identité, adresse, société, source des fonds</small><br>
<div style="margin-top:8px;display:flex;gap:6px"><a href="/admin/investors/<?=$investor['id']?>/approve" class="btn btn-primary btn-sm">Valider KYC</a><a href="/admin/investors/<?=$investor['id']?>/request-info" class="btn btn-white btn-sm">Demander infos</a><a href="/admin/investors/<?=$investor['id']?>/reject" class="btn btn-white btn-sm" style="color:#ef4444">Rejeter</a></div>
</div>
</div>
<div class="card card-pad">
<h3 style="font-weight:900">Investissements & Data Rooms</h3>
<ul style="font-size:13px;margin-left:16px"><?php foreach(($investments??[]) as $inv): ?><li><?=htmlspecialchars($inv['project_title']??'#'.$inv['project_id'])?> — <?=number_format($inv['amount'],0,'',' ')?> $ — <?=htmlspecialchars($inv['status'])?></li><?php endforeach; if(empty($investments)) echo '<li class="muted">Aucun investissement</li>'; ?></ul>
<h3 style="font-weight:900;margin-top:10px">Accès Data Room</h3>
<ul style="font-size:13px;margin-left:16px"><?php foreach(($accesses??[]) as $a): ?><li><?=htmlspecialchars($a['project_title']??'#'.$a['project_id'])?> — <?=htmlspecialchars($a['status'])?></li><?php endforeach; if(empty($accesses)) echo '<li class="muted">Aucun</li>'; ?></ul>
</div>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
