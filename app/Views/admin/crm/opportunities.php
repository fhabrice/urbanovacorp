<?php ob_start();?>
<div class="page-header"><div><h1>Opportunités</h1><div class="breadcrumb">Admin / CRM / Opportunités</div></div><div style="display:flex;gap:8px"><a href="/admin/crm/opportunities/create" class="btn btn-primary btn-sm">Ajouter</a><a href="/admin/crm/pipeline" class="btn btn-white btn-sm">Voir Pipeline Kanban</a><a href="/admin/export?module=opportunities&type=csv" class="btn btn-white btn-sm">Exporter</a></div></div>
<div class="card card-pad" style="margin-bottom:12px">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
<input name="q" value="<?=htmlspecialchars($_GET['q']??'')?>" placeholder="Recherche opportunité..." style="flex:1;min-width:200px;padding:8px;border:1px solid #e2e8f0;border-radius:8px">
<select name="stage" style="padding:8px;border:1px solid #e2e8f0;border-radius:8px"><option value="">Toutes étapes</option><option value="nouveau">Nouveau</option><option value="proposition">Proposition</option><option value="negociation">Négociation</option><option value="gagne">Gagné</option></select>
<button class="btn btn-dark btn-sm">Filtrer</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th data-sort>Référence</th><th>Opportunité</th><th>Contact</th><th>Entreprise</th><th>Projet</th><th>Type</th><th data-sort>Montant</th><th>Devise</th><th>Probabilité</th><th>Valeur pondérée</th><th>Étape</th><th>Responsable</th><th>Clôture estimée</th></tr></thead><tbody>
<?php foreach(($opportunities??[]) as $o): $weighted=($o['amount']*$o['probability']/100); ?>
<tr>
<td><b><?=htmlspecialchars($o['reference'])?></b></td>
<td><?=htmlspecialchars($o['name'])?></td>
<td><?=htmlspecialchars($o['contact_name']??'—')?></td>
<td><?=htmlspecialchars($o['company_name']??'—')?></td>
<td><?=htmlspecialchars($o['project_title']??'—')?></td>
<td><?=htmlspecialchars($o['type'])?></td>
<td><?=number_format($o['amount'],0,'',' ')?></td>
<td><?=htmlspecialchars($o['currency'])?></td>
<td><?=htmlspecialchars($o['probability'])?>%</td>
<td><b><?=number_format($weighted,0,'',' ')?> $</b></td>
<td><?=\App\Helpers\AdminHelper::getStatusBadge($o['stage'])?></td>
<td><?=htmlspecialchars($o['resp_name']??'—')?></td>
<td><?=htmlspecialchars($o['expected_close']??'—')?></td>
</tr>
<?php endforeach; if(empty($opportunities)) echo '<tr><td colspan=13 class="muted">Aucune opportunité</td></tr>'; ?>
</tbody></table></div>
<p class="muted" style="font-size:12px;margin-top:8px">Règle valeur pondérée: Montant × Probabilité (ex: 250 000 USD × 50% = 125 000 USD)</p>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
