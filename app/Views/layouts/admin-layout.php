<!DOCTYPE html>
<html lang="<?php echo $session->get('language', 'fr') ?? 'fr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URBANOVA ADMIN — <?php echo $pageTitle ?? 'Pilotage'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --brand-dark:#0B132B; --brand-navy:#1C2541; --brand-blue:#3A506B;
            --brand-gold:#D4AF37; --brand-success:#10B981; --bg:#f1f5f9;
            --sidebar: 280px;
        }
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:Inter, system-ui, -apple-system, Segoe UI, sans-serif;background:var(--bg);color:#334155}
        /* layout */
        .admin-shell{display:flex;min-height:100vh}
        .admin-sidebar{width:var(--sidebar);background:linear-gradient(180deg,var(--brand-dark),#0f1f3a);color:#cbd5e1;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:30;border-right:1px solid #1e293b}
        .sidebar-brand{padding:20px 18px;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;gap:10px}
        .brand-icon{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--brand-gold),#f59e0b);display:flex;align-items:center;justify-content:center;color:var(--brand-dark);font-weight:900}
        .brand-text b{color:white;letter-spacing:1px}
        .brand-text small{color:#94a3b8;font-size:10px;letter-spacing:2px;text-transform:uppercase}
        .nav-section{padding:14px 10px}
        .nav-label{font-size:10px;letter-spacing:1.2px;text-transform:uppercase;color:#64748b;padding:8px 10px;font-weight:700}
        .nav-link{color:#cbd5e1;text-decoration:none;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:13.5px;transition:all .18s}
        .nav-link:hover{background:rgba(255,255,255,0.06);color:white}
        .nav-link.active{background:rgba(212,175,55,0.14);color:var(--brand-gold);border:1px solid rgba(212,175,55,0.22)}
        .nav-link i{width:18px;text-align:center;font-size:14px}
        .nav-sub{margin-left:18px;border-left:1px solid rgba(255,255,255,0.08);padding-left:8px;margin-top:4px}
        .nav-sub a{font-size:13px;padding:7px 10px}
        .badge{margin-left:auto;background:var(--brand-gold);color:var(--brand-dark);font-size:11px;font-weight:800;padding:2px 6px;border-radius:20px}
        /* topbar */
        .main-area{margin-left:var(--sidebar);flex:1;display:flex;flex-direction:column;min-width:0}
        .topbar{position:sticky;top:0;z-index:20;background:white;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;gap:14px;padding:10px 18px}
        .search-box{flex:1;max-width:560px;position:relative}
        .search-box input{width:100%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:10px 14px 10px 36px;font-size:14px;outline:none}
        .search-box input:focus{border-color:var(--brand-gold);box-shadow:0 0 0 3px rgba(212,175,55,0.18)}
        .search-box i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8}
        .top-actions{display:flex;align-items:center;gap:10px}
        .icon-btn{width:38px;height:38px;border-radius:10px;border:1px solid #e2e8f0;background:white;display:flex;align-items:center;justify-content:center;color:#475569;text-decoration:none;position:relative}
        .icon-btn:hover{background:#f8fafc}
        .notif-dot{position:absolute;top:-4px;right:-4px;width:16px;height:16px;background:#ef4444;color:white;font-size:10px;font-weight:800;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid white}
        .user-chip{display:flex;align-items:center;gap:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:6px 10px}
        .avatar{width:32px;height:32px;border-radius:50%;background:var(--brand-dark);color:var(--brand-gold);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px}
        /* content */
        .content-wrap{padding:18px}
        .page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:14px;flex-wrap:wrap}
        .page-header h1{font-size:22px;color:var(--brand-dark);font-weight:900}
        .breadcrumb{font-size:12px;color:#64748b}
        .breadcrumb a{color:#64748b;text-decoration:none}
        .breadcrumb a:hover{color:var(--brand-blue)}
        .card{background:white;border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 1px 2px rgba(0,0,0,0.04)}
        .card-pad{padding:16px}
        /* KPI */
        .kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
        @media(max-width:1200px){.kpi-grid{grid-template-columns:repeat(3,1fr)}}
        @media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,1fr)} .admin-sidebar{transform:translateX(-100%);position:fixed} .main-area{margin-left:0} .topbar{padding-left:56px}}
        @media(max-width:600px){.kpi-grid{grid-template-columns:1fr}}
        .kpi-card{padding:14px;display:flex;align-items:center;gap:12px}
        .kpi-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:18px}
        .kpi-value{font-size:18px;font-weight:900;color:var(--brand-dark)}
        .kpi-label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
        /* buttons */
        .btn{display:inline-flex;align-items:center;gap:8px;padding:9px 14px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;border:1px solid transparent;cursor:pointer;transition:all .15s}
        .btn-primary{background:var(--brand-gold);color:var(--brand-dark);border-color:#eab308}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(212,175,55,.25)}
        .btn-dark{background:var(--brand-dark);color:white}
        .btn-white{background:white;border-color:#e2e8f0;color:#334155}
        .btn-white:hover{background:#f8fafc}
        .btn-sm{padding:6px 10px;font-size:12px;border-radius:8px}
        /* table */
        .table-wrap{overflow:auto;border-radius:12px;border:1px solid #e2e8f0;background:white}
        table{width:100%;border-collapse:collapse;font-size:13px}
        th{background:#f8fafc;color:#475569;text-align:left;padding:10px 12px;font-size:11px;letter-spacing:.6px;text-transform:uppercase;border-bottom:1px solid #e2e8f0;white-space:nowrap}
        td{padding:11px 12px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
        tr:hover td{background:#f8fafc}
        /* utilities */
        .muted{color:#64748b}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        @media(max-width:900px){.grid-2{grid-template-columns:1fr}}
        .pill{padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;border:1px solid}
        .mobile-toggle{position:fixed;left:12px;top:12px;z-index:40;width:40px;height:40px;border-radius:10px;background:white;border:1px solid #e2e8f0;display:none;align-items:center;justify-content:center}
        @media(max-width:900px){.mobile-toggle{display:flex}}
        .alert{margin:12px;padding:12px 14px;border-radius:12px;border:1px solid;font-size:13px}
        .alert-success{background:#ecfdf5;border-color:#a7f3d0;color:#065f46}
        .alert-error{background:#fef2f2;border-color:#fecaca;color:#991b1b}
        .chart-box{height:260px}
    </style>
</head>
<body>
<button class="mobile-toggle" onclick="document.querySelector('.admin-sidebar').style.transform=document.querySelector('.admin-sidebar').style.transform==='translateX(0px)'?'translateX(-100%)':'translateX(0px)'"><i class="fa-solid fa-bars"></i></button>

<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-city"></i></div>
            <div class="brand-text">
                <b>URBANOVA</b><br><small>Admin • ERP • CRM</small>
            </div>
        </div>

        <?php
            $uri = $_SERVER['REQUEST_URI'] ?? '/admin';
            $is = fn($p)=> strpos($uri, $p)===0 ? 'active' : '';
        ?>

        <div class="nav-section">
            <div class="nav-label">Pilotage</div>
            <a class="nav-link <?php echo $is('/admin/dashboard') || $uri==='/admin'?'active':''; ?>" href="/admin/dashboard"><i class="fa-solid fa-chart-line"></i> Tableau de bord <span class="badge">KPI</span></a>
            <a class="nav-link <?php echo $is('/admin/executive')?'active':''; ?>" href="/admin/executive"><i class="fa-solid fa-crown"></i> Direction</a>
            <a class="nav-link <?php echo $is('/admin/audit-log')?'active':''; ?>" href="/admin/audit-log"><i class="fa-solid fa-shield-halved"></i> Journal d'audit</a>
        </div>

        <div class="nav-section">
            <div class="nav-label">CRM & Commercial</div>
            <a class="nav-link <?php echo $is('/admin/crm/leads')?'active':''; ?>" href="/admin/crm/leads"><i class="fa-solid fa-user-plus"></i> Prospects</a>
            <a class="nav-link <?php echo $is('/admin/crm/contacts')?'active':''; ?>" href="/admin/crm/contacts"><i class="fa-regular fa-address-book"></i> Contacts</a>
            <a class="nav-link <?php echo $is('/admin/crm/companies')?'active':''; ?>" href="/admin/crm/companies"><i class="fa-regular fa-building"></i> Entreprises</a>
            <a class="nav-link <?php echo $is('/admin/crm/opportunities')?'active':''; ?>" href="/admin/crm/opportunities"><i class="fa-solid fa-bullseye"></i> Opportunités</a>
            <a class="nav-link <?php echo $is('/admin/crm/pipeline')?'active':''; ?>" href="/admin/crm/pipeline"><i class="fa-solid fa-diagram-project"></i> Pipeline <span style="margin-left:auto;font-size:11px;color:#94a3b8">Kanban</span></a>
            <a class="nav-link <?php echo $is('/admin/crm/activities')?'active':''; ?>" href="/admin/crm/activities"><i class="fa-solid fa-list-check"></i> Activités</a>
            <a class="nav-link <?php echo $is('/admin/crm/tasks')?'active':''; ?>" href="/admin/crm/tasks"><i class="fa-solid fa-clipboard-check"></i> Tâches</a>
            <a class="nav-link <?php echo $is('/admin/crm/relances')?'active':''; ?>" href="/admin/crm/relances"><i class="fa-solid fa-bell"></i> Relances</a>
            <a class="nav-link <?php echo $is('/admin/crm/rendez-vous')?'active':''; ?>" href="/admin/crm/rendez-vous"><i class="fa-regular fa-calendar"></i> Rendez-vous</a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Plateforme</div>
            <a class="nav-link <?php echo $is('/admin/projects')?'active':''; ?>" href="/admin/projects"><i class="fa-solid fa-building-columns"></i> Projets</a>
            <a class="nav-link <?php echo $is('/admin/users')?'active':''; ?>" href="/admin/users"><i class="fa-solid fa-users-gear"></i> Utilisateurs</a>
            <a class="nav-link <?php echo $is('/admin/investors')?'active':''; ?>" href="/admin/investors"><i class="fa-solid fa-user-tie"></i> Investisseurs</a>
            <a class="nav-link <?php echo $is('/admin/fundraising')?'active':''; ?>" href="/admin/fundraising"><i class="fa-solid fa-hand-holding-dollar"></i> Levées de fonds</a>
            <a class="nav-link <?php echo $is('/admin/investments')?'active':''; ?>" href="/admin/investments"><i class="fa-solid fa-money-bill-trend-up"></i> Investissements</a>
            <a class="nav-link <?php echo $is('/admin/commissions')?'active':''; ?>" href="/admin/commissions"><i class="fa-solid fa-percent"></i> Commissions</a>
            <a class="nav-link <?php echo $is('/admin/data-rooms')?'active':''; ?>" href="/admin/data-rooms"><i class="fa-solid fa-vault"></i> Data Room</a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Opérations</div>
            <a class="nav-link <?php echo $is('/admin/requests')?'active':''; ?>" href="/admin/requests"><i class="fa-solid fa-inbox"></i> Demandes</a>
            <a class="nav-link <?php echo $is('/admin/appointments')?'active':''; ?>" href="/admin/appointments"><i class="fa-regular fa-calendar"></i> Visites / Réservations</a>
            <a class="nav-link <?php echo $is('/admin/notifications')?'active':''; ?>" href="/admin/notifications"><i class="fa-regular fa-bell"></i> Notifications</a>
            <a class="nav-link <?php echo $is('/admin/reports')?'active':''; ?>" href="/admin/reports"><i class="fa-solid fa-chart-pie"></i> Rapports</a>
            <a class="nav-link <?php echo $is('/admin/news')?'active':''; ?>" href="/admin/news"><i class="fa-regular fa-newspaper"></i> Contenus</a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Administration</div>
            <a class="nav-link <?php echo $is('/admin/settings') && !strpos($uri,'admin-users')?'active':''; ?>" href="/admin/settings"><i class="fa-solid fa-gear"></i> Paramètres</a>
            <a class="nav-link <?php echo $is('/admin/settings/admin-users')?'active':''; ?>" href="/admin/settings/admin-users"><i class="fa-solid fa-user-shield"></i> Équipe & Rôles</a>
            <a class="nav-link" href="/logout"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
        </div>
        <div style="padding:12px;color:#64748b;font-size:11px;text-align:center;border-top:1px solid rgba(255,255,255,0.06)">© <?php echo date('Y'); ?> Urbanova • ERP v1</div>
    </aside>

    <div class="main-area">
        <div class="topbar">
            <form class="search-box" method="GET" action="/admin/search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Recherche globale : projets, contacts, investisseurs, opportunités, transactions..." />
            </form>
            <div class="top-actions">
                <a class="icon-btn" href="/admin/notifications" title="Notifications"><i class="fa-regular fa-bell"></i><span class="notif-dot">3</span></a>
                <a class="icon-btn" href="/admin/crm/tasks" title="Relances du jour"><i class="fa-solid fa-bolt"></i></a>
                <div class="user-chip">
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A',0,1)); ?></div>
                    <div style="line-height:1">
                        <div style="font-size:12px;font-weight:800;color:#0f172a"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></div>
                        <div style="font-size:11px;color:#64748b"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'admin'); ?></div>
                    </div>
                    <i class="fa-solid fa-chevron-down" style="font-size:11px;color:#94a3b8"></i>
                </div>
            </div>
        </div>

        <div class="content-wrap">
            <?php if ($session->hasFlashMessage('success')): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($session->getFlashMessage('success')); ?></div>
            <?php endif; ?>
            <?php if ($session->hasFlashMessage('error')): ?>
                <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($session->getFlashMessage('error')); ?></div>
            <?php endif; ?>

            <?php echo $content ?? ''; ?>
        </div>
    </div>
</div>

<script>
// tables: sortable + simple filter hook
document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelectorAll('[data-sort]').forEach(th=>{
    th.style.cursor='pointer';
    th.addEventListener('click', ()=>{
      const table=th.closest('table'); const idx=Array.from(th.parentNode.children).indexOf(th);
      const asc=th.dataset.dir!=='asc';
      th.dataset.dir=asc?'asc':'desc';
      const rows=Array.from(table.querySelectorAll('tbody tr'));
      rows.sort((a,b)=>{
        const A=a.children[idx]?.innerText.trim()||''; const B=b.children[idx]?.innerText.trim()||'';
        const nA=parseFloat(A.replace(/[^0-9.-]/g,'')); const nB=parseFloat(B.replace(/[^0-9.-]/g,''));
        if(!isNaN(nA)&&!isNaN(nB)) return asc? nA-nB : nB-nA;
        return asc? A.localeCompare(B): B.localeCompare(A);
      });
      const tb=table.querySelector('tbody'); rows.forEach(r=>tb.appendChild(r));
    });
  });
});
function confirmAction(msg){ return confirm(msg); }
</script>
</body>
</html>
