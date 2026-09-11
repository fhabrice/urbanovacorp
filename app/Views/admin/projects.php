<?php ob_start();?>
<div class="page-header"><div><h1>Projets</h1><div class="breadcrumb">Admin / Projets</div></div><div style="display:flex;gap:8px"><a href="/admin/export?module=projects&type=csv" class="btn btn-white btn-sm">CSV</a><a href="/admin/export?module=projects&type=excel" class="btn btn-white btn-sm">Excel</a><a href="/admin/reports" class="btn btn-white btn-sm">Rapports</a></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
<input name="q" value="<?=htmlspecialchars($_GET['q']??'')?>" placeholder="Référence, projet, promoteur..." style="flex:1;min-width:220px;padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px">
<select name="status" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous statuts</option><option value="draft">Brouillon</option><option value="submitted">Soumis</option><option value="approved">Approuvé</option><option value="published">Publié</option><option value="funded">Financé</option></select>
<select name="sector" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Tous secteurs</option><option value="Résidentiel">Résidentiel</option><option value="Commercial">Commercial</option></select>
<button class="btn btn-dark btn-sm">Filtrer</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th data-sort>Référence</th><th data-sort>Projet</th><th>Promoteur</th><th>Secteur</th><th>Localisation</th><th data-sort>Coût total</th><th data-sort>Financement recherché</th><th data-sort>Montant levé</th><th>Statut</th><th data-sort>Date soumission</th><th>Responsable</th><th>Actions</th></tr></thead><tbody>
<?php foreach(($projects??[]) as $p): $prom=$p['promoter_name']??$p['promoter']??'—'; $ref=$p['reference']??'PRJ-'.$p['id']; $sought=$p['funding_sought']??$p['total_cost']??0; $raised=$p['funding_mobilized']??$p['amount_raised']??0; ?>
<tr>
<td><b><?=htmlspecialchars($ref)?></b><br><small class="muted">#<?=$p['id']?></small></td>
<td><b><?=htmlspecialchars($p['title'])?></b><br><small class="muted"><?=htmlspecialchars($p['type']??'')?></small></td>
<td><?=htmlspecialchars($prom)?><br><small class="muted"><?=htmlspecialchars($p['email']??'')?></small></td>
<td><?=htmlspecialchars($p['sector']??'—')?></td>
<td><?=htmlspecialchars(($p['city']??'').', '.($p['country']??''))?></td>
<td><?=number_format($p['total_cost']??0,0,'',' ')?> $</td>
<td><?=number_format($sought,0,'',' ')?> $</td>
<td><?=number_format($raised,0,'',' ')?> $</td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($p['status'])?></td>
<td><?=htmlspecialchars(substr($p['created_at']??'',0,10))?></td>
<td class="muted">—</td>
<td><div style="display:flex;gap:6px">
<a href="/admin/projects/<?=$p['id']?>" class="btn btn-white btn-sm"><i class="fa-regular fa-eye"></i></a>
<a href="/admin/projects/<?=$p['id']?>/review" class="btn btn-white btn-sm" title="Validation"><i class="fa-solid fa-clipboard-check"></i></a>
<a href="/admin/projects/<?=$p['id']?>/approve" onclick="return confirm('Confirmez-vous l\'approbation de ce projet ?')" class="btn btn-white btn-sm" style="color:#059669"><i class="fa-solid fa-check"></i></a>
<a href="/admin/projects/<?=$p['id']?>/request-info" class="btn btn-white btn-sm" title="Info"><i class="fa-solid fa-circle-question"></i></a>
<a href="/admin/projects/<?=$p['id']?>/delete" onclick="return confirm('Cette action peut être restaurée uniquement par un Super Admin. Confirmer ?')" class="btn btn-white btn-sm" style="color:#ef4444"><i class="fa-regular fa-trash-can"></i></a>
</div></td>
</tr>
<?php endforeach; if(empty($projects)) echo '<tr><td colspan=12 class="muted">Aucun projet</td></tr>'; ?>
</tbody></table></div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
