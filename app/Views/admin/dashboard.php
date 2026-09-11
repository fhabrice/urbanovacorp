<?php ob_start(); $pageTitle='Tableau de bord'; ?>
<div class="page-header">
    <div>
        <h1>Tableau de bord — Pilotage Urbanova</h1>
        <div class="breadcrumb"><a href="/admin">Admin</a> / Dashboard • <span class="muted">Données temps réel</span></div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <form method="GET" style="display:flex;gap:6px;align-items:center">
            <select name="period" onchange="this.form.submit()" class="btn btn-white btn-sm" style="padding:7px 10px">
                <option value="today" <?php echo $period==='today'?'selected':'';?>>Aujourd’hui</option>
                <option value="7days" <?php echo $period==='7days'?'selected':'';?>>7 derniers jours</option>
                <option value="month" <?php echo $period==='month'?'selected':'';?>>Mois</option>
                <option value="quarter" <?php echo $period==='quarter'?'selected':'';?>>Trimestre</option>
                <option value="year" <?php echo $period==='year'?'selected':'';?>>Année</option>
                <option value="custom" <?php echo $period==='custom'?'selected':'';?>>Période personnalisée</option>
            </select>
        </form>
        <a href="/admin/export?module=projects&type=csv" class="btn btn-white btn-sm"><i class="fa-solid fa-file-csv"></i> CSV</a>
        <a href="/admin/export?module=projects&type=excel" class="btn btn-white btn-sm"><i class="fa-regular fa-file-excel"></i> Excel</a>
        <a href="/admin/reports" class="btn btn-dark btn-sm"><i class="fa-solid fa-chart-pie"></i> Rapports</a>
    </div>
</div>

<!-- KPI GRID - 18 cards per spec 4.3 -->
<div class="kpi-grid">
    <?php
    $kpis = [
        ['label'=>'Total utilisateurs','value'=>number_format($stats['total_users']??0),'icon'=>'fa-users','color'=>'#3A506B'],
        ['label'=>'Nouveaux utilisateurs','value'=>number_format($stats['new_users']??0),'icon'=>'fa-user-plus','color'=>'#0ea5e9'],
        ['label'=>'Projets en attente','value'=>number_format($stats['projects_pending']??0),'icon'=>'fa-clock','color'=>'#f59e0b'],
        ['label'=>'Projets publiés','value'=>number_format($stats['projects_published']??0),'icon'=>'fa-building-circle-check','color'=>'#10B981'],
        ['label'=>'Montant total recherché','value'=>number_format($stats['total_sought']??0,0,'',' ').' $','icon'=>'fa-sack-dollar','color'=>'#1C2541'],
        ['label'=>'Investisseurs enregistrés','value'=>number_format($stats['total_investors']??0),'icon'=>'fa-user-tie','color'=>'#6d28d9'],
        ['label'=>'Opportunités ouvertes','value'=>number_format($stats['opportunities_open']??0),'icon'=>'fa-bullseye','color'=>'#e11d48'],
        ['label'=>'Valeur du pipeline','value'=>number_format($stats['pipeline_value']??0,0,'',' ').' $','icon'=>'fa-diagram-project','color'=>'#0e7490'],
        ['label'=>'Investissements réalisés','value'=>number_format($stats['investments_done']??0),'icon'=>'fa-handshake','color'=>'#059669'],
        ['label'=>'Montant total investi','value'=>number_format($stats['total_invested']??0,0,'',' ').' $','icon'=>'fa-money-bill-trend-up','color'=>'#0f766e'],
        ['label'=>'Campagnes actives','value'=>number_format($stats['campaigns_active']??0),'icon'=>'fa-bullhorn','color'=>'#d97706'],
        ['label'=>'Montant total levé','value'=>number_format($stats['total_raised']??0,0,'',' ').' $','icon'=>'fa-piggy-bank','color'=>'#1d4ed8'],
        ['label'=>'Data Rooms en attente','value'=>number_format($stats['data_rooms_pending']??0),'icon'=>'fa-vault','color'=>'#7c3aed'],
        ['label'=>'Demandes à traiter','value'=>number_format($stats['requests_pending']??0),'icon'=>'fa-inbox','color'=>'#f43f5e'],
        ['label'=>'Visites en attente','value'=>number_format($stats['visits_pending']??0),'icon'=>'fa-calendar-check','color'=>'#0891b2'],
        ['label'=>'Relances du jour','value'=>number_format($stats['relances_today']??0),'icon'=>'fa-bell','color'=>'#ef4444'],
        ['label'=>'Commissions attendues','value'=>number_format($stats['commissions_expected']??0,0,'',' ').' $','icon'=>'fa-percent','color'=>'#475569'],
        ['label'=>'Commissions encaissées','value'=>number_format($stats['commissions_collected']??0,0,'',' ').' $','icon'=>'fa-coins','color'=>'#15803d'],
    ];
    foreach($kpis as $k): ?>
    <div class="card kpi-card">
        <div class="kpi-icon" style="background:<?php echo $k['color']; ?>"><i class="fa-solid <?php echo $k['icon']; ?>"></i></div>
        <div>
            <div class="kpi-value"><?php echo $k['value']; ?></div>
            <div class="kpi-label"><?php echo $k['label']; ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Actions rapides Spec 4.6 + Centre d'actions Spec 5 -->
<div class="grid-2" style="margin-top:14px">
    <div class="card card-pad">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <h3 style="font-size:14px;color:#0f172a;font-weight:900"><i class="fa-solid fa-bolt" style="color:var(--brand-gold)"></i> Actions rapides</h3>
            <span class="muted" style="font-size:12px">ERP • CRM • Pilotage</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px">
            <a href="/admin/crm/leads/create" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nouveau prospect</a>
            <a href="/admin/investors" class="btn btn-white"><i class="fa-solid fa-user-tie"></i> Nouvel investisseur</a>
            <a href="/admin/projects" class="btn btn-white"><i class="fa-solid fa-building"></i> Nouveau projet</a>
            <a href="/admin/crm/opportunities/create" class="btn btn-white"><i class="fa-solid fa-bullseye"></i> Nouvelle opportunité</a>
            <a href="/admin/investments" class="btn btn-white"><i class="fa-solid fa-money-bill"></i> Nouvel investissement</a>
            <a href="/admin/fundraising" class="btn btn-white"><i class="fa-solid fa-hand-holding-dollar"></i> Nouvelle campagne</a>
            <a href="/admin/crm/tasks/create" class="btn btn-white"><i class="fa-solid fa-clipboard"></i> Nouvelle tâche</a>
            <a href="/admin/appointments" class="btn btn-dark"><i class="fa-regular fa-calendar"></i> Nouveau rendez-vous</a>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
            <a href="/admin/export?module=opportunities&type=csv" class="btn btn-white btn-sm"><i class="fa-solid fa-download"></i> Exporter CRM</a>
            <a href="/admin/search" class="btn btn-white btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Recherche globale</a>
        </div>
    </div>

    <div class="card card-pad">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <h3 style="font-size:14px;color:#0f172a;font-weight:900"><i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b"></i> Actions requises</h3>
            <a href="/admin/notifications" class="muted" style="font-size:12px;text-decoration:none">Voir tout →</a>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px">
            <?php foreach(($actions??[]) as $a): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc">
                <div style="display:flex;align-items:center;gap:10px">
                    <span style="width:8px;height:8px;border-radius:50%;background:<?php echo $a['color']==='amber'?'#f59e0b':($a['color']==='emerald'?'#10B981':($a['color']==='red'?'#ef4444':($a['color']==='blue'?'#0ea5e9':'#64748b')));?>"></span>
                    <span style="font-size:13px;font-weight:600;color:#0f172a"><?php echo htmlspecialchars($a['label']); ?></span>
                </div>
                <div style="display:flex;gap:6px">
                    <a href="<?php echo $a['link']; ?>" class="btn btn-white btn-sm"><?php echo $a['action']; ?></a>
                    <a href="<?php echo $a['link']; ?>" class="btn btn-dark btn-sm" style="padding:6px 8px"><i class="fa-solid fa-eye"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Graphs Spec 4.5 -->
<div class="grid-2" style="margin-top:14px">
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:800;color:#0f172a;margin-bottom:10px">Évolution des inscriptions</h3>
        <div class="chart-box"><canvas id="chartUsers"></canvas></div>
    </div>
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:800;color:#0f172a;margin-bottom:10px">Projets déposés / approuvés / financés</h3>
        <div class="chart-box"><canvas id="chartProjects"></canvas></div>
    </div>
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:800;color:#0f172a;margin-bottom:10px">Montants investis par période</h3>
        <div class="chart-box"><canvas id="chartInvest"></canvas></div>
    </div>
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:800;color:#0f172a;margin-bottom:10px">Valeur pipeline par étape</h3>
        <div class="chart-box"><canvas id="chartPipeline"></canvas></div>
    </div>
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:800;color:#0f172a;margin-bottom:10px">Répartition par secteurs</h3>
        <div class="chart-box"><canvas id="chartSectors"></canvas></div>
    </div>
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:800;color:#0f172a;margin-bottom:10px">Financement : recherché vs levé</h3>
        <div class="chart-box"><canvas id="chartFunding"></canvas></div>
    </div>
</div>

<!-- Top lists -->
<div class="grid-2" style="margin-top:14px">
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:900;color:#0f172a;margin-bottom:10px"><i class="fa-solid fa-ranking-star"></i> Top projets</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Projet</th><th>Objectif</th><th>Levé</th><th>Progression</th></tr></thead>
                <tbody>
                    <?php foreach(($topProjects??[]) as $p): 
                        $obj=(float)($p['funding_sought']??0); $lev=(float)($p['raised']??$p['funding_mobilized']??0); $pct=$obj?round($lev/$obj*100,1):0;
                    ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($p['title']); ?></b><br><small class="muted"><?php echo htmlspecialchars(($p['city']??'').' '.($p['country']??'')); ?></small></td>
                        <td><?php echo number_format($obj,0,'',' '); ?> $</td>
                        <td><?php echo number_format($lev,0,'',' '); ?> $</td>
                        <td><span class="pill" style="background:<?php echo $pct>=80?'#dcfce7':($pct>=40?'#fef9c3':'#fee2e2'); ?>;border-color:#e2e8f0"><?php echo $pct; ?>%</span></td>
                    </tr>
                    <?php endforeach; if(empty($topProjects)) echo '<tr><td colspan=4 class="muted">Aucun projet</td></tr>'; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card card-pad">
        <h3 style="font-size:13px;font-weight:900;color:#0f172a;margin-bottom:10px"><i class="fa-solid fa-users"></i> Top investisseurs</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Investisseur</th><th>Email</th><th>Capacité</th></tr></thead>
                <tbody>
                    <?php foreach(($topInvestors??[]) as $inv): ?>
                    <tr><td><?php echo htmlspecialchars($inv['name'] ?? '—'); ?></td><td class="muted"><?php echo htmlspecialchars($inv['email']??'—'); ?></td><td><?php echo $inv['investment_capacity']?number_format($inv['investment_capacity'],0,'',' ').' $':'—'; ?></td></tr>
                    <?php endforeach; if(empty($topInvestors)) echo '<tr><td colspan=3 class="muted">Aucun investisseur</td></tr>'; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const cs = <?php echo json_encode($charts??[]); ?>;
try{
 // users
 if(cs.users){
   new Chart(document.getElementById('chartUsers'),{type:'line',data:{labels:cs.users.map(r=>r.m),datasets:[{label:'Inscriptions',data:cs.users.map(r=>r.c),borderColor:'#D4AF37',backgroundColor:'rgba(212,175,55,0.18)',tension:.35,fill:true}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}});
 }
 if(cs.projects_by_status){
   new Chart(document.getElementById('chartProjects'),{type:'doughnut',data:{labels:cs.projects_by_status.map(r=>r.status),datasets:[{data:cs.projects_by_status.map(r=>r.c),backgroundColor:['#0B132B','#D4AF37','#10B981','#ef4444','#0ea5e9','#f59e0b']}]},options:{responsive:true,maintainAspectRatio:false}});
 }
 if(cs.investments){
   new Chart(document.getElementById('chartInvest'),{type:'bar',data:{labels:cs.investments.map(r=>r.m),datasets:[{label:'Investi',data:cs.investments.map(r=>r.t),backgroundColor:'#1C2541'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}});
 }
 if(cs.pipeline){
   new Chart(document.getElementById('chartPipeline'),{type:'bar',data:{labels:cs.pipeline.map(r=>r.stage),datasets:[{label:'Valeur pondérée',data:cs.pipeline.map(r=>r.v),backgroundColor:'#3A506B'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}});
 }
 if(cs.sectors){
   new Chart(document.getElementById('chartSectors'),{type:'pie',data:{labels:cs.sectors.map(r=>r.s),datasets:[{data:cs.sectors.map(r=>r.c),backgroundColor:['#0B132B','#D4AF37','#10B981','#f59e0b','#0ea5e9','#e11d48']}]},options:{responsive:true,maintainAspectRatio:false}});
 }
 if(cs.funding){
   new Chart(document.getElementById('chartFunding'),{type:'bar',data:{labels:['Recherché','Levé'],datasets:[{label:'$',data:[cs.funding.sought||0,cs.funding.raised||0],backgroundColor:['#0B132B','#D4AF37']}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}});
 }
}catch(e){ console.log(e); }
</script>

<?php $content = ob_get_clean(); require APP_PATH . '/Views/layouts/admin-layout.php'; ?>
