<?php ob_start();?>
<div class="page-header"><div><h1>Nouvelle tâche</h1><div class="breadcrumb"><a href="/admin/crm/tasks">Tâches</a> / Création</div></div><a href="/admin/crm/tasks" class="btn btn-white btn-sm">Retour</a></div>
<div class="card card-pad">
<form method="POST" action="/admin/crm/tasks/create" style="display:grid;gap:10px">
<div><label style="font-size:12px;font-weight:700">Titre *</label><input name="title" required style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
<div><label style="font-size:12px;font-weight:700">Description</label><textarea name="description" rows="3" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></textarea></div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Responsable</label><select name="responsible_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($users??[]) as $u): ?><option value="<?=$u['id']?>"><?=htmlspecialchars($u['name'])?></option><?php endforeach;?></select></div>
<div><label style="font-size:12px;font-weight:700">Priorité</label><select name="priority" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="faible">Faible</option><option value="normale" selected>Normale</option><option value="haute">Haute</option><option value="urgente">Urgente</option></select></div>
<div><label style="font-size:12px;font-weight:700">Échéance</label><input name="due_date" type="date" value="<?=date('Y-m-d', strtotime('+3 days'))?>" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
<div><label style="font-size:12px;font-weight:700">Contact</label><select name="contact_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($contacts??[]) as $c): ?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option><?php endforeach;?></select></div>
<div><label style="font-size:12px;font-weight:700">Projet</label><select name="project_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($projects??[]) as $p): ?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['title'])?></option><?php endforeach;?></select></div>
<div><label style="font-size:12px;font-weight:700">Opportunité</label><select name="opportunity_id" style="width:100%;padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">—</option><?php foreach(($opportunities??[]) as $o): ?><option value="<?=$o['id']?>"><?=htmlspecialchars($o['name'])?></option><?php endforeach;?></select></div>
</div>
<button class="btn btn-primary">Créer tâche</button>
</form>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
