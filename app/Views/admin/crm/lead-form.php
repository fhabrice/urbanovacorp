<?php ob_start(); $isEdit=!empty($lead);?>
<div class="page-header"><div><h1><?= $isEdit?'Modifier prospect':'Nouveau prospect'?></h1><div class="breadcrumb"><a href="/admin/crm/leads">Prospects</a> / <?= $isEdit?'Edit':'Création'?></div></div><a href="/admin/crm/leads" class="btn btn-white btn-sm">Retour</a></div>
<div class="card card-pad">
<form method="POST" action="<?= $isEdit?'/admin/crm/leads/'.$lead['id'].'/edit':'/admin/crm/leads/create'?>" style="display:grid;gap:12px">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Prénom</label><input name="first_name" value="<?=htmlspecialchars($lead['first_name']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Nom *</label><input name="last_name" value="<?=htmlspecialchars($lead['last_name']??'')?>" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Téléphone *</label><input name="phone" value="<?=htmlspecialchars($lead['phone']??'')?>" placeholder="ou email obligatoire" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Email *</label><input name="email" type="email" value="<?=htmlspecialchars($lead['email']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Type *</label><select name="type" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="investisseur" <?=($lead['type']??'')==='investisseur'?'selected':''?>>Investisseur</option><option value="porteur_projet" <?=($lead['type']??'')==='porteur_projet'?'selected':''?>>Porteur de projet</option><option value="client">Client</option><option value="proprietaire">Propriétaire</option><option value="partenaire">Partenaire</option><option value="institution">Institution</option><option value="prestataire">Prestataire</option></select></div>
<div><label style="font-size:12px;font-weight:700">Source *</label><select name="source" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="site_web">Site web</option><option value="linkedin">LinkedIn</option><option value="whatsapp">WhatsApp</option><option value="facebook">Facebook</option><option value="instagram">Instagram</option><option value="email">Email</option><option value="evenement">Événement</option><option value="recommandation">Recommandation</option><option value="appel">Appel</option><option value="prospection">Prospection</option><option value="partenaire">Partenaire</option></select></div>
<div><label style="font-size:12px;font-weight:700">Responsable *</label><select name="assigned_to" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Choisir</option><?php foreach(($users??[]) as $u): ?><option value="<?=$u['id']?>" <?=($lead['assigned_to']??'')==$u['id']?'selected':''?>><?=htmlspecialchars($u['name'])?></option><?php endforeach; ?></select></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Entreprise</label><input name="company_name" value="<?=htmlspecialchars($lead['company_name']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Pays</label><input name="country" value="<?=htmlspecialchars($lead['country']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Ville</label><input name="city" value="<?=htmlspecialchars($lead['city']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Fonction</label><input name="function_title" value="<?=htmlspecialchars($lead['function_title']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Secteur</label><input name="sector" value="<?=htmlspecialchars($lead['sector']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Projet intéressé</label><select name="project_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Aucun</option><?php foreach(($projects??[]) as $p): ?><option value="<?=$p['id']?>" <?=($lead['project_id']??'')==$p['id']?'selected':''?>><?=htmlspecialchars($p['title'])?></option><?php endforeach; ?></select></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Montant potentiel</label><input name="amount_potential" type="number" value="<?=htmlspecialchars($lead['amount_potential']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Date prochaine action</label><input name="next_action_date" type="date" value="<?=htmlspecialchars($lead['next_action_date']??'')?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div><label style="font-size:12px;font-weight:700">Commentaire</label><textarea name="comment" rows="3" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><?=htmlspecialchars($lead['comment']??'')?></textarea></div>
<div><label style="font-size:12px;font-weight:700">Statut</label><select name="status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="nouveau">Nouveau</option><option value="a_qualifier">À qualifier</option><option value="qualifie">Qualifié</option><option value="contacte">Contacté</option><option value="rendez_vous">Rendez-vous</option><option value="opportunite">Opportunité</option></select></div>
<button class="btn btn-primary"><?= $isEdit?'Mettre à jour':'Créer prospect'?></button>
</form>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
