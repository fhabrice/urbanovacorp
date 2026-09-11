<?php ob_start();?>
<div class="page-header"><div><h1>Nouvelle opportunité</h1><div class="breadcrumb"><a href="/admin/crm/opportunities">Opportunités</a> / Création</div></div><a href="/admin/crm/opportunities" class="btn btn-white btn-sm">Retour</a></div>
<div class="card card-pad">
<form method="POST" action="/admin/crm/opportunities/create" style="display:grid;gap:10px">
<div><label style="font-size:12px;font-weight:700">Nom opportunité *</label><input name="name" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Contact</label><select name="contact_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($contacts??[]) as $c): ?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option><?php endforeach;?></select></div>
<div><label style="font-size:12px;font-weight:700">Entreprise</label><select name="company_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($companies??[]) as $co): ?><option value="<?=$co['id']?>"><?=htmlspecialchars($co['name'])?></option><?php endforeach;?></select></div>
<div><label style="font-size:12px;font-weight:700">Projet</label><select name="project_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($projects??[]) as $p): ?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['title'])?></option><?php endforeach;?></select></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Type</label><select name="type" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="investissement">Investissement</option><option value="levee_fonds">Levée de fonds</option><option value="accompagnement">Accompagnement</option><option value="conseil">Conseil</option><option value="immobilier">Immobilier</option><option value="partenariat">Partenariat</option><option value="vente">Vente</option></select></div>
<div><label style="font-size:12px;font-weight:700">Montant *</label><input name="amount" type="number" min="0" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Devise</label><select name="currency" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option>USD</option><option>CDF</option><option>EUR</option></select></div>
<div><label style="font-size:12px;font-weight:700">Probabilité %</label><input name="probability" type="number" min="0" max="100" value="50" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Étape</label><select name="stage" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="nouveau">Nouveau</option><option value="qualifie">Qualifié</option><option value="contact_etabli">Contact établi</option><option value="rendez_vous">Rendez-vous</option><option value="proposition">Proposition</option><option value="due_diligence">Due diligence</option><option value="negociation">Négociation</option><option value="engagement">Engagement</option><option value="gagne">Gagné</option></select></div>
<div><label style="font-size:12px;font-weight:700">Responsable</label><select name="responsible_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($users??[]) as $u): ?><option value="<?=$u['id']?>"><?=htmlspecialchars($u['name'])?></option><?php endforeach;?></select></div>
<div><label style="font-size:12px;font-weight:700">Date clôture estimée</label><input name="expected_close" type="date" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div><label style="font-size:12px;font-weight:700">Notes</label><textarea name="notes" rows="3" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></textarea></div>
<button class="btn btn-primary">Créer opportunité</button>
</form>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
