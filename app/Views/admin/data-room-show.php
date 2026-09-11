<?php ob_start();?>
<div class="page-header"><div><h1>Data Room — <?=htmlspecialchars($room['project_title']??'Projet #'.$room['project_id'])?></h1><div class="breadcrumb"><a href="/admin/data-rooms">Data Rooms</a> / #<?=htmlspecialchars($room['id']??$room['project_id'])?></div></div><a href="/admin/data-rooms" class="btn btn-white btn-sm">Retour</a></div>
<div class="grid-2">
<div class="card card-pad">
<h3 style="font-weight:900">Documents (catégories)</h3>
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px">
<?php $cats=['juridique','corporate','financier','commercial','technique','fiscal','rh','contrats','licences','etudes','autres']; foreach($cats as $cat): ?><span class="pill" style="background:#f8fafc"><?=htmlspecialchars($cat)?></span><?php endforeach; ?>
</div>
<div class="table-wrap"><table><thead><tr><th>Nom</th><th>Catégorie</th><th>Version</th><th>Date upload</th><th>Uploader</th><th>Confidentialité</th><th>Téléchargement</th></tr></thead><tbody>
<?php foreach(($documents??[]) as $d): ?>
<tr><td><?=htmlspecialchars($d['title']??$d['name']??'Doc')?></td><td><?=htmlspecialchars($d['category']??$d['document_type']??'—')?></td><td><?=htmlspecialchars($d['version']??'1.0')?></td><td><?=htmlspecialchars(substr($d['created_at']??'',0,10))?></td><td><?=htmlspecialchars($d['uploaded_by']??'—')?></td><td><?=\App\Helpers\AdminHelper::getStatusBadge($d['confidentiality']??'confidentiel')?></td><td><?=($d['download_allowed']??1)?'Oui':'Non'?></td></tr>
<?php endforeach; if(empty($documents)) echo '<tr><td colspan=7 class="muted">Aucun document</td></tr>'; ?>
</tbody></table></div>
</div>
<div class="card card-pad">
<h3 style="font-weight:900">Demandes d'accès — Workflow</h3>
<p class="muted" style="font-size:12px">Investisseur demande → notif admin → vérif KYC → NDA si nécessaire → approbation admin → accès accordé</p>
<div class="table-wrap" style="margin-top:8px"><table><thead><tr><th>Investisseur</th><th>Statut</th><th>NDA</th><th>Demandé</th><th>Action</th></tr></thead><tbody>
<?php foreach(($accesses??[]) as $a): ?>
<tr><td><?=htmlspecialchars($a['investor_name']??'Inv #'.$a['investor_id'])?></td><td><?=\App\Helpers\AdminHelper::getStatusBadge($a['status'])?></td><td><?=($a['nda_signed']??0)?'Signé':'Requis'?></td><td><?=htmlspecialchars(substr($a['requested_at']??$a['created_at']??'',0,10))?></td><td><?php if($a['status']==='demande' || $a['status']==='en_verification'): ?><a href="/admin/data-rooms/access/<?=$a['id']?>/approve" class="btn btn-primary btn-sm">Autoriser</a><?php else: ?><span class="muted">—</span><?php endif; ?></td></tr>
<?php endforeach; if(empty($accesses)) echo '<tr><td colspan=5 class="muted">Aucune demande</td></tr>'; ?>
</tbody></table></div>
<h3 style="font-weight:900;margin-top:12px">Traçabilité — Top investisseurs actifs</h3>
<ul style="font-size:13px;margin-left:16px"><?php foreach(($logs??[]) as $l): ?><li><?=htmlspecialchars($l['investor_name']??'—')?> — <?=htmlspecialchars($l['action'])?> — <?=htmlspecialchars(substr($l['created_at']??'',0,16))?> — <?=htmlspecialchars($l['ip_address']??'')?></li><?php endforeach; if(empty($logs)) echo '<li class="muted">Aucune activité</li>'; ?></ul>
</div>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
