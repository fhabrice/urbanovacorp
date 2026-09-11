<?php ob_start();?>
<div class="page-header"><div><h1>Nouvelle entreprise</h1><div class="breadcrumb"><a href="/admin/crm/companies">Entreprises</a> / Création</div></div><a href="/admin/crm/companies" class="btn btn-white btn-sm">Retour</a></div>
<div class="card card-pad">
<form method="POST" action="/admin/crm/companies/create" style="display:grid;gap:10px">
<div><label style="font-size:12px;font-weight:700">Nom *</label><input name="name" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Forme juridique</label><input name="legal_form" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">RCCM *</label><input name="rccm" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">NIF *</label><input name="nif" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">ID National</label><input name="id_national" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Secteur</label><input name="sector" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div><label style="font-size:12px;font-weight:700">Adresse</label><input name="address" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Ville</label><input name="city" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Pays</label><input name="country" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Site web</label><input name="website" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Téléphone</label><input name="phone" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Email</label><input name="email" type="email" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div><label style="font-size:12px;font-weight:700">Responsable Urbanova</label><select name="responsible_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($users??[]) as $u): ?><option value="<?=$u['id']?>"><?=htmlspecialchars($u['name'])?></option><?php endforeach;?></select></div>
<button class="btn btn-primary">Créer entreprise</button>
</form>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
