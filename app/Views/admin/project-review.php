<?php ob_start();?>
<div class="page-header"><div><h1>Validation projet — <?=htmlspecialchars($project['title'])?></h1><div class="breadcrumb"><a href="/admin/projects">Projets</a> / Validation</div></div><a href="/admin/projects/<?=$project['id']?>" class="btn btn-white btn-sm">Retour fiche</a></div>
<div class="card card-pad">
<h3 style="font-weight:900;margin-bottom:10px">Checklist (Section 26)</h3>
<form method="GET" action="" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px">
<?php $checks=['Identité vérifiée','RCCM disponible','NIF disponible','Business plan','Pitch deck','Modèle financier','États financiers','Risques analysés','Besoin de financement clair','Informations complètes']; foreach($checks as $c): ?>
<label style="display:flex;gap:8px;align-items:center;padding:8px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc"><input type="checkbox"> <?=htmlspecialchars($c)?></label>
<?php endforeach; ?>
</form>
<div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
<a href="/admin/projects/<?=$project['id']?>/approve" onclick="return confirm('Confirmez-vous l\'approbation de ce projet ?')" class="btn btn-primary"><i class="fa-solid fa-check"></i> Approuver</a>
<a href="/admin/projects/<?=$project['id']?>/request-info" class="btn btn-white"><i class="fa-solid fa-circle-question"></i> Demander correction</a>
<form method="POST" action="/admin/projects/<?=$project['id']?>/reject" style="display:flex;gap:8px" onsubmit="return confirm('Un commentaire est obligatoire en cas de rejet. Confirmer ?')">
<input name="reason" placeholder="Motif de rejet (obligatoire)" required style="padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px;min-width:260px">
<button class="btn btn-white" style="color:#ef4444;border-color:#fecaca"><i class="fa-solid fa-xmark"></i> Rejeter</button>
</form>
</div>
<p class="muted" style="font-size:12px;margin-top:8px">Un commentaire est obligatoire en cas de rejet. La traçabilité est assurée via le journal d'audit.</p>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
