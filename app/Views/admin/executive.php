<?php ob_start();?>
<div class="page-header">
    <div><h1>Dashboard Direction</h1><div class="breadcrumb">Admin / Direction • KPI stratégiques</div></div>
    <a href="/admin/dashboard" class="btn btn-white btn-sm"><i class="fa-solid fa-arrow-left"></i> Retour pilotage</a>
</div>

<div class="kpi-grid">
    <?php $cards=[
        ['Valeur projets', number_format($stats['valeur_projets']??0,0,'',' ').' $','#0B132B','fa-city'],
        ['Financement recherché', number_format($stats['financement_recherche']??0,0,'',' ').' $','#D4AF37','fa-sack-dollar'],
        ['Financement levé', number_format($stats['financement_leve']??0,0,'',' ').' $','#10B981','fa-piggy-bank'],
        ['Pipeline (pondéré)', number_format($stats['pipeline']??0,0,'',' ').' $','#0ea5e9','fa-diagram-project'],
        ['Investissements', ($stats['investissements']??0).' deals','#6d28d9','fa-handshake'],
        ['Commissions', number_format($stats['commissions']??0,0,'',' ').' $','#475569','fa-percent'],
        ['Nouveaux investisseurs (30j)', ($stats['nouveaux_investisseurs']??0),'#f59e0b','fa-user-plus'],
        ['Taux conversion', ($stats['taux_conversion']??0).' %','#ef4444','fa-arrow-trend-up'],
    ]; foreach($cards as $c): ?>
    <div class="card kpi-card"><div class="kpi-icon" style="background:<?php echo $c[2];?>"><i class="fa-solid <?php echo $c[3];?>"></i></div><div><div class="kpi-value"><?php echo $c[1];?></div><div class="kpi-label"><?php echo $c[0];?></div></div></div>
    <?php endforeach; ?>
</div>

<div class="grid-2" style="margin-top:14px">
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:900;margin-bottom:10px">Top projets (par levée)</h3>
        <div class="table-wrap"><table><thead><tr><th>Projet</th><th>Objectif</th><th>Levé</th><th>%</th></tr></thead><tbody>
        <?php foreach(($topProjects??[]) as $p): $pct=$p['pct']??0; ?>
        <tr><td><?php echo htmlspecialchars($p['title']); ?></td><td><?php echo number_format($p['objectif']??0,0,'',' ');?> $</td><td><?php echo number_format($p['leve']??0,0,'',' ');?> $</td><td><?php echo $pct;?>%</td></tr>
        <?php endforeach; if(empty($topProjects)) echo '<tr><td colspan=4 class="muted">Aucun</td></tr>'; ?>
        </tbody></table></div>
    </div>
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:900;margin-bottom:10px">Top opportunités</h3>
        <div class="table-wrap"><table><thead><tr><th>Opportunité</th><th>Montant</th><th>Prob.</th><th>Resp.</th></tr></thead><tbody>
        <?php foreach(($topOpps??[]) as $o): ?>
        <tr><td><?php echo htmlspecialchars($o['name']); ?> <br><small class="muted"><?php echo $o['stage'];?></small></td><td><?php echo number_format($o['amount'],0,'',' ');?> $</td><td><?php echo $o['probability'];?>%</td><td><?php echo htmlspecialchars($o['resp']);?></td></tr>
        <?php endforeach; if(empty($topOpps)) echo '<tr><td colspan=4 class="muted">Aucune</td></tr>'; ?>
        </tbody></table></div>
    </div>
</div>

<div class="card card-pad" style="margin-top:14px">
    <h3 style="font-size:13px;font-weight:900;margin-bottom:10px">Top commerciaux</h3>
    <div class="table-wrap"><table><thead><tr><th>Commercial</th><th>Opportunités</th><th>Valeur gagnée</th><th>Taux</th></tr></thead><tbody>
    <?php foreach(($topCommerciaux??[]) as $c): ?>
    <tr><td><?php echo htmlspecialchars($c['name']??'—'); ?></td><td><?php echo $c['opps']; ?></td><td><?php echo number_format($c['gagne'],0,'',' ');?> $</td><td>—</td></tr>
    <?php endforeach; if(empty($topCommerciaux)) echo '<tr><td colspan=4 class="muted">Aucun</td></tr>'; ?>
    </tbody></table></div>
</div>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
