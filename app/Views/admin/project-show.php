<?php ob_start();?>
<div class="page-header"><div><h1><?=htmlspecialchars($project['title'])?></h1><div class="breadcrumb"><a href="/admin/projects">Projets</a> / #<?=$project['id']?> • <?=htmlspecialchars($project['reference']??'')?></div></div><div style="display:flex;gap:8px"><a href="/admin/projects/<?=$project['id']?>/review" class="btn btn-dark btn-sm">Validation</a><a href="/admin/projects" class="btn btn-white btn-sm">Retour</a></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<div style="display:flex;gap:8px;flex-wrap:wrap">
<span class="pill" style="background:#f8fafc">Présentation</span><span class="pill" style="background:#f8fafc">Promoteur</span><span class="pill" style="background:#f8fafc">Financement</span><span class="pill" style="background:#f8fafc">Documents</span><span class="pill" style="background:#f8fafc">Data Room</span><span class="pill" style="background:#f8fafc">Investisseurs</span><span class="pill" style="background:#f8fafc">Opportunités</span><span class="pill" style="background:#f8fafc">Investissements</span><span class="pill" style="background:#f8fafc">Campagne</span><span class="pill" style="background:#f8fafc">Activités</span><span class="pill" style="background:#f8fafc">Historique</span>
</div>
</div>
<div class="grid-2">
<div class="card card-pad">
<h3 style="font-weight:900">Informations projet</h3>
<p><b>Nom:</b> <?=htmlspecialchars($project['title'])?></p>
<p><b>Description:</b> <?=htmlspecialchars($project['description']??'—')?></p>
<p><b>Secteur:</b> <?=htmlspecialchars($project['sector']??'—')?> • <b>Sous-secteur:</b> <?=htmlspecialchars($project['sub_sector']??'—')?></p>
<p><b>Pays:</b> <?=htmlspecialchars($project['country']??'—')?> <b>Ville:</b> <?=htmlspecialchars($project['city']??'—')?></p>
<p><b>Promoteur:</b> <?=htmlspecialchars($project['promoter_name']??$project['promoter']??'—')?> • <b>Entreprise:</b> <?=htmlspecialchars($project['company_id']??'—')?></p>
<p><b>Date démarrage prévue:</b> <?=htmlspecialchars($project['start_date']??'—')?></p>
<p><b>Coût total:</b> <?=number_format($project['total_cost']??0,0,'',' ')?> $ • <b>Apport promoteur:</b> <?=number_format($project['equity_contribution']??0,0,'',' ')?> $</p>
<p><b>Financement recherché:</b> <?=number_format($project['funding_sought']??0,0,'',' ')?> $ • <b>Montant levé:</b> <?=number_format($project['funding_mobilized']??$project['amount_raised']??0,0,'',' ')?> $ • <b>Devise:</b> <?=htmlspecialchars($project['currency']??'USD')?></p>
<p><b>Type financement:</b> <?=htmlspecialchars($project['funding_type']??'—')?></p>
</div>
<div class="card card-pad">
<h3 style="font-weight:900">Documents & Data Room</h3>
<ul style="font-size:13px;margin-left:16px"><?php foreach(($documents??[]) as $d): ?><li><?=htmlspecialchars($d['title']??$d['name']??'Doc')?> — <small class="muted"><?=htmlspecialchars($d['document_type']??$d['category']??'')?></small></li><?php endforeach; if(empty($documents)) echo '<li class="muted">Aucun document</li>'; ?></ul>
<h3 style="font-weight:900;margin-top:12px">Investisseurs / Investissements</h3>
<ul style="font-size:13px;margin-left:16px"><?php foreach(($investments??[]) as $inv): ?><li><?=htmlspecialchars($inv['investor_name']??$inv['investor_id'])?> — <?=number_format($inv['amount']??0,0,'',' ')?> $ — <?=htmlspecialchars($inv['status'])?></li><?php endforeach; if(empty($investments)) echo '<li class="muted">Aucun</li>'; ?></ul>
</div>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
