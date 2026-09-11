<?php ob_start();?>
<div class="page-header"><div><h1><?=htmlspecialchars(trim(($contact['first_name']??'').' '.($contact['last_name']??$contact['name']??'')))?></h1><div class="breadcrumb"><a href="/admin/crm/contacts">Contacts</a> / Fiche</div></div><a href="/admin/crm/contacts" class="btn btn-white btn-sm">Retour</a></div>
<div class="card card-pad" style="margin-bottom:12px"><div style="display:flex;gap:6px;flex-wrap:wrap"><span class="pill" style="background:#f8fafc">Profil</span><span class="pill" style="background:#f8fafc">Opportunités (<?=count($opportunities??[])?>)</span><span class="pill" style="background:#f8fafc">Projets</span><span class="pill" style="background:#f8fafc">Investissements</span><span class="pill" style="background:#f8fafc">Activités</span><span class="pill" style="background:#f8fafc">Rendez-vous</span><span class="pill" style="background:#f8fafc">Documents</span><span class="pill" style="background:#f8fafc">Historique</span></div></div>
<div class="grid-2">
<div class="card card-pad">
<p><b>Email:</b> <?=htmlspecialchars($contact['email']??'—')?> <b>Téléphone:</b> <?=htmlspecialchars($contact['phone']??'—')?></p>
<p><b>WhatsApp:</b> <?=htmlspecialchars($contact['whatsapp']??'—')?> <b>Fonction:</b> <?=htmlspecialchars($contact['function_title']??'—')?></p>
<p><b>Entreprise:</b> <?=htmlspecialchars($contact['company_name']??'—')?> <b>Pays:</b> <?=htmlspecialchars($contact['country']??'—')?> <b>Ville:</b> <?=htmlspecialchars($contact['city']??'—')?></p>
<p><b>Notes:</b> <?=htmlspecialchars($contact['notes']??$contact['message']??'—')?></p>
</div>
<div class="card card-pad">
<h3 style="font-weight:900">Opportunités liées</h3><ul style="font-size:13px;margin-left:16px"><?php foreach(($opportunities??[]) as $o): ?><li><?=htmlspecialchars($o['name'])?> — <?=number_format($o['amount'],0,'',' ')?> $ — <?=htmlspecialchars($o['stage'])?></li><?php endforeach; if(empty($opportunities)) echo '<li class="muted">Aucune</li>'; ?></ul>
<h3 style="font-weight:900;margin-top:10px">Activités</h3><ul style="font-size:13px;margin-left:16px"><?php foreach(($activities??[]) as $a): ?><li><?=htmlspecialchars($a['type'])?> — <?=htmlspecialchars($a['summary'])?> (<?=htmlspecialchars($a['activity_date'])?>)</li><?php endforeach; if(empty($activities)) echo '<li class="muted">Aucune</li>'; ?></ul>
</div>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
