<?php

namespace App\Controllers;

use App\Helpers\AdminHelper;

class AdminController extends Controller
{
    private function safeFetchOne($db, $sql, $params = [], $default = []){
        try { $res = $db->fetchOne($sql, $params); return $res ?: $default; } catch (\Exception $e) { return $default; }
    }
    private function safeFetchAll($db, $sql, $params = []){
        try { return $db->fetchAll($sql, $params); } catch (\Exception $e) { return []; }
    }
    private function tableExists($db, $table){
        try { $db->fetchOne("SELECT 1 FROM `$table` LIMIT 1"); return true; } catch(\Exception $e){ return false; }
    }

    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $db = $this->getDb();
        $request = $this->getRequest();
        $filter = $request->getParam('period', 'month');
        $customStart = $request->getParam('start');
        $customEnd = $request->getParam('end');

        // date filter logic
        $dateCondition = "";
        $params = [];
        $now = date('Y-m-d');
        switch($filter){
            case 'today': $dateCondition = "DATE(created_at)=CURDATE()"; break;
            case '7days': $dateCondition = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"; break;
            case 'month': $dateCondition = "MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())"; break;
            case 'quarter': $dateCondition = "QUARTER(created_at)=QUARTER(NOW()) AND YEAR(created_at)=YEAR(NOW())"; break;
            case 'year': $dateCondition = "YEAR(created_at)=YEAR(NOW())"; break;
            case 'custom': if($customStart && $customEnd){ $dateCondition="DATE(created_at) BETWEEN ? AND ?"; $params=[$customStart,$customEnd]; } break;
        }

        // KPI calculations with fallbacks
        $stats = [];
        try {
            $stats['total_users'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM users",[],['c'=>0])['c'];
            $stats['new_users'] = $dateCondition ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM users WHERE $dateCondition",$params,['c'=>0])['c'] : $stats['total_users'];
            $stats['projects_pending'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM projects WHERE status IN ('pending','submitted','under_review','soumis','en_verification','en_analyse','brouillon','draft')",[],['c'=>0])['c'];
            $stats['projects_published'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM projects WHERE status IN ('approved','published','publie','finance','funded','levee_active','fundraising_active')",[],['c'=>0])['c'];
            $stats['total_sought'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(funding_sought),0) as t FROM projects",[],['t'=>0])['t'];
            $stats['total_investors'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM investors",[],['c'=>0])['c'];
            $stats['opportunities_open'] = $this->tableExists($db,'opportunities') ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM opportunities WHERE stage NOT IN ('gagne','perdu','suspendu') AND deleted_at IS NULL",[],['c'=>0])['c'] : 0;
            $stats['pipeline_value'] = $this->tableExists($db,'opportunities') ? $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount * probability/100),0) as t FROM opportunities WHERE deleted_at IS NULL AND stage NOT IN ('gagne','perdu')",[],['t'=>0])['t'] : 0;
            $stats['investments_done'] = $this->tableExists($db,'investments') ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM investments WHERE status='confirme' AND deleted_at IS NULL",[],['c'=>0])['c'] : $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM investments WHERE status='confirmed'",[],['c'=>0])['c'];
            $stats['total_invested'] = $this->tableExists($db,'investments') ? $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount),0) as t FROM investments WHERE status='confirme' AND deleted_at IS NULL",[],['t'=>0])['t'] : $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount),0) as t FROM investments WHERE status='confirmed'",[],['t'=>0])['t'];
            $stats['campaigns_active'] = $this->tableExists($db,'funding_campaigns') ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM funding_campaigns WHERE status='active'",[],['c'=>0])['c'] : $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM funding_campaigns WHERE status='active'",[],['c'=>0])['c'];
            $stats['total_raised'] = $this->tableExists($db,'funding_campaigns') ? $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount_raised),0) as t FROM funding_campaigns",[],['t'=>0])['t'] : $this->safeFetchOne($db,"SELECT COALESCE(SUM(funding_mobilized),0) as t FROM projects",[],['t'=>0])['t'];
            $stats['data_rooms_pending'] = $this->tableExists($db,'data_rooms') ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM data_rooms WHERE status='en_attente'",[],['c'=>0])['c'] : $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM data_room_accesses WHERE status='demande'",[],['c'=>0])['c'];
            $stats['requests_pending'] = $this->tableExists($db,'requests') ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM requests WHERE status='nouveau' AND deleted_at IS NULL",[],['c'=>0])['c'] : $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM contacts WHERE status='new'",[],['c'=>0])['c'];
            $stats['visits_pending'] = $this->tableExists($db,'appointments') ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM appointments WHERE status='demandee' AND deleted_at IS NULL",[],['c'=>0])['c'] : $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM project_visits WHERE status='pending'",[],['c'=>0])['c'];
            $stats['relances_today'] = $this->tableExists($db,'tasks') ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM tasks WHERE due_date=CURDATE() AND status IN ('a_faire','en_cours') AND deleted_at IS NULL",[],['c'=>0])['c'] : 0;
            $stats['commissions_expected'] = $this->tableExists($db,'commissions') ? $this->safeFetchOne($db,"SELECT COALESCE(SUM(commission_amount),0) as t FROM commissions WHERE status='a_facturer' AND deleted_at IS NULL",[],['t'=>0])['t'] : 0;
            $stats['commissions_collected'] = $this->tableExists($db,'commissions') ? $this->safeFetchOne($db,"SELECT COALESCE(SUM(paid),0) as t FROM commissions WHERE status='payee' AND deleted_at IS NULL",[],['t'=>0])['t'] : 0;

            // fallback for legacy columns
            if ($stats['total_sought']==0) $stats['total_sought'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(funding_sought),0) as t FROM projects",[],['t'=>0])['t'];
            if ($stats['total_raised']==0) $stats['total_raised'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(funding_raised),0) as t FROM projects",[],['t'=>0])['t'];
        } catch (\Exception $e) {
            $stats = array_fill_keys(['total_users','new_users','projects_pending','projects_published','total_sought','total_investors','opportunities_open','pipeline_value','investments_done','total_invested','campaigns_active','total_raised','data_rooms_pending','requests_pending','visits_pending','relances_today','commissions_expected','commissions_collected'], 0);
        }

        // Centre d'actions (required actions)
        $actions = [];
        try {
            $actions[] = ['label'=> $stats['projects_pending'].' projets à valider', 'count'=>$stats['projects_pending'], 'link'=>'/admin/projects', 'action'=>'Voir', 'color'=>'amber'];
            $kycPending = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM investors WHERE investor_status='pending' OR kyc_status='en_verification'",[],['c'=>0])['c'];
            $actions[] = ['label'=> $kycPending.' KYC à vérifier', 'count'=>$kycPending, 'link'=>'/admin/investors', 'action'=>'Vérifier', 'color'=>'blue'];
            $actions[] = ['label'=> $stats['data_rooms_pending'].' demandes Data Room', 'count'=>$stats['data_rooms_pending'], 'link'=>'/admin/data-rooms', 'action'=>'Traiter', 'color'=>'emerald'];
            $actions[] = ['label'=> $stats['relances_today'].' prospects à relancer', 'count'=>$stats['relances_today'], 'link'=>'/admin/crm/tasks', 'action'=>'Relancer', 'color'=>'red'];
            $invConfirm = $this->tableExists($db,'investments') ? $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM investments WHERE status='en_attente_paiement'",[],['c'=>0])['c'] : 0;
            $actions[] = ['label'=> $invConfirm.' investissements à confirmer', 'count'=>$invConfirm, 'link'=>'/admin/investments', 'action'=>'Confirmer', 'color'=>'violet'];
            $actions[] = ['label'=> $stats['requests_pending'].' demandes non traitées', 'count'=>$stats['requests_pending'], 'link'=>'/admin/requests', 'action'=>'Assigner', 'color'=>'slate'];
        } catch(\Exception $e){}

        // Graph data
        $charts = [];
        try {
            // Users evolution last 6 months
            $charts['users'] = $this->safeFetchAll($db,"SELECT DATE_FORMAT(created_at,'%Y-%m') as m, COUNT(*) as c FROM users GROUP BY m ORDER BY m DESC LIMIT 6");
            $charts['users'] = array_reverse($charts['users']);
            // Projects by status
            $charts['projects_by_status'] = $this->safeFetchAll($db,"SELECT status, COUNT(*) as c FROM projects GROUP BY status");
            // Investments per month
            if($this->tableExists($db,'investments')){
                $charts['investments'] = $this->safeFetchAll($db,"SELECT DATE_FORMAT(created_at,'%Y-%m') as m, COALESCE(SUM(amount),0) as t FROM investments WHERE status='confirme' GROUP BY m ORDER BY m DESC LIMIT 6");
                $charts['investments']=array_reverse($charts['investments']);
            } else {
                $charts['investments'] = $this->safeFetchAll($db,"SELECT DATE_FORMAT(created_at,'%Y-%m') as m, COUNT(*) as t FROM investments GROUP BY m ORDER BY m DESC LIMIT 6");
            }
            // Pipeline by stage
            if($this->tableExists($db,'opportunities')){
                $charts['pipeline'] = $this->safeFetchAll($db,"SELECT stage, COALESCE(SUM(amount * probability/100),0) as v, COUNT(*) as c FROM opportunities WHERE deleted_at IS NULL GROUP BY stage");
            } else $charts['pipeline']=[];
            // Sectors
            $charts['sectors'] = $this->safeFetchAll($db,"SELECT COALESCE(sector,'Autre') as s, COUNT(*) as c FROM projects GROUP BY sector");
            // Funding sought vs raised
            $charts['funding'] = [
                'sought' => $stats['total_sought'],
                'raised' => $stats['total_raised']
            ];
        } catch(\Exception $e){ $charts=[]; }

        // Top projects/investisseurs for dashboard
        $topProjects = $this->safeFetchAll($db,"SELECT p.id, p.title, p.city, p.country, p.funding_sought, COALESCE(p.funding_mobilized, p.amount_raised,0) as raised, p.status FROM projects p ORDER BY p.funding_sought DESC LIMIT 5");
        $topInvestors = $this->safeFetchAll($db,"SELECT i.id, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as name, u.email, i.investment_capacity FROM investors i JOIN users u ON i.user_id=u.id ORDER BY i.investment_capacity DESC LIMIT 5");

        return $this->view('admin/dashboard', [
            'stats'=>$stats,
            'actions'=>$actions,
            'charts'=>$charts,
            'topProjects'=>$topProjects,
            'topInvestors'=>$topInvestors,
            'period'=>$filter,
            'pageTitle'=>'Tableau de bord'
        ]);
    }

    public function executive()
    {
        $db = $this->getDb();
        $stats = [];
        try{
            $stats['valeur_projets'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(total_cost),0) as t FROM projects",[],['t'=>0])['t'];
            $stats['financement_recherche'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(funding_sought),0) as t FROM projects",[],['t'=>0])['t'];
            $stats['financement_leve'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(COALESCE(funding_mobilized,amount_raised,0)),0) as t FROM projects",[],['t'=>0])['t'];
            $stats['pipeline'] = $this->tableExists($db,'opportunities') ? $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount * probability/100),0) as t FROM opportunities WHERE deleted_at IS NULL",[],['t'=>0])['t'] : 0;
            $stats['investissements'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM investments",[],['c'=>0])['c'];
            $stats['commissions'] = $this->tableExists($db,'commissions') ? $this->safeFetchOne($db,"SELECT COALESCE(SUM(commission_amount),0) as t FROM commissions WHERE deleted_at IS NULL",[],['t'=>0])['t'] : 0;
            $stats['nouveaux_investisseurs'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM investors WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",[],['c'=>0])['c'];
            $stats['taux_conversion'] = $this->tableExists($db,'opportunities') ? $this->safeFetchOne($db,"SELECT CASE WHEN COUNT(*)=0 THEN 0 ELSE ROUND(SUM(CASE WHEN stage='gagne' THEN 1 ELSE 0 END)/COUNT(*)*100,1) END as t FROM opportunities WHERE deleted_at IS NULL",[],['t'=>0])['t'] : 0;
        } catch(\Exception $e){}

        $topProjects = $this->safeFetchAll($db,"SELECT p.id, p.title, p.funding_sought, COALESCE(p.funding_mobilized,0) as levé, CASE WHEN funding_sought>0 THEN ROUND(COALESCE(funding_mobilized,0)/funding_sought*100,1) ELSE 0 END as prog FROM projects p ORDER BY levé DESC LIMIT 5");
        $topOpps = $this->tableExists($db,'opportunities') ? $this->safeFetchAll($db,"SELECT o.name, o.amount, o.probability, o.stage, COALESCE(CONCAT(u.first_name,' ',u.last_name), '—') as resp FROM opportunities o LEFT JOIN users u ON o.responsible_id=u.id WHERE o.deleted_at IS NULL ORDER BY o.amount DESC LIMIT 5") : [];
        $topCommerciaux = $this->tableExists($db,'opportunities') ? $this->safeFetchAll($db,"SELECT u.id, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as name, COUNT(o.id) as opps, COALESCE(SUM(CASE WHEN o.stage='gagne' THEN o.amount ELSE 0 END),0) as gagne FROM users u LEFT JOIN opportunities o ON o.responsible_id=u.id AND o.deleted_at IS NULL GROUP BY u.id ORDER BY gagne DESC LIMIT 5") : [];

        return $this->view('admin/executive', [
            'stats'=>$stats, 'topProjects'=>$topProjects, 'topOpps'=>$topOpps, 'topCommerciaux'=>$topCommerciaux, 'pageTitle'=>'Dashboard Direction'
        ]);
    }

    // --- USERS (Spec 19-20) ---
    public function users()
    {
        $db = $this->getDb();
        $q = $this->getRequest()->getParam('q');
        $status = $this->getRequest()->getParam('status');
        $type = $this->getRequest()->getParam('type');
        $sql = "SELECT u.*, (SELECT COUNT(*) FROM projects p WHERE p.user_id=u.id) as projects_count FROM users u WHERE 1=1";
        $params=[];
        if($q){ $sql.=" AND (u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.name LIKE ?)"; $like="%$q%"; $params=array_merge($params,[$like,$like,$like,$like]); }
        if($status){ $sql.=" AND u.status=?"; $params[]=$status; }
        if($type){ $sql.=" AND u.role=?"; $params[]=$type; }
        $sql.=" ORDER BY u.created_at DESC LIMIT 100";
        $users = $this->safeFetchAll($db,$sql,$params);
        return $this->view('admin/users', ['users'=>$users,'pageTitle'=>'Utilisateurs']);
    }
    public function userView($id){ $db=$this->getDb(); $u=$this->safeFetchOne($db,"SELECT * FROM users WHERE id=?",[$id],null); if(!$u){ $this->getSession()->setFlashMessage('error','Utilisateur introuvable'); return $this->redirect('/admin/users'); } $projects=$this->safeFetchAll($db,"SELECT * FROM projects WHERE user_id=? ORDER BY created_at DESC LIMIT 10",[$id]); $investments=$this->safeFetchAll($db,"SELECT inv.*, p.title as project_title FROM investments inv LEFT JOIN projects p ON inv.project_id=p.id WHERE inv.investor_id IN (SELECT id FROM investors WHERE user_id=?) LIMIT 20",[$id]); return $this->view('admin/user-show', ['user'=>$u,'projects'=>$projects,'investments'=>$investments,'pageTitle'=>'Fiche utilisateur']); }
    public function toggleUserStatus($id){
        $db=$this->getDb(); $session=$this->getSession();
        $action=$this->getRequest()->getParam('action','toggle');
        $u=$this->safeFetchOne($db,"SELECT * FROM users WHERE id=?",[$id],null);
        if(!$u){ $session->setFlashMessage('error','Utilisateur introuvable'); return $this->redirect('/admin/users'); }
        $old=$u['status'];
        $new = $old==='active'?'suspended':'active';
        if($action==='activate') $new='active'; if($action==='suspend') $new='suspended'; if($action==='block') $new='suspended';
        $db->execute("UPDATE users SET status=? WHERE id=?",[$new,$id]);
        AdminHelper::auditLog($db,$session->get('user_id'),'update','users',$id,['status'=>$old],['status'=>$new],"Changement statut utilisateur $id");
        $session->setFlashMessage('success','Statut mis à jour');
        return $this->redirect('/admin/users');
    }
    public function resetPassword($id){
        $db=$this->getDb(); $session=$this->getSession();
        $new = password_hash('Urbanova2026!', PASSWORD_DEFAULT);
        $db->execute("UPDATE users SET password=? WHERE id=?",[$new,$id]);
        $session->setFlashMessage('success','Mot de passe réinitialisé à Urbanova2026!');
        return $this->redirect('/admin/users');
    }

    // --- PROJECTS (Spec 21-26) ---
    public function projects()
    {
        $db = $this->getDb();
        $req = $this->getRequest();
        $q=$req->getParam('q'); $status=$req->getParam('status'); $sector=$req->getParam('sector');
        $sql="SELECT p.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as promoter_name, u.email FROM projects p LEFT JOIN users u ON p.user_id=u.id WHERE 1=1";
        $params=[];
        if($q){ $sql.=" AND (p.title LIKE ? OR p.reference LIKE ? OR p.city LIKE ?)"; $like="%$q%"; $params=array_merge($params,[$like,$like,$like]);}
        if($status){ $sql.=" AND p.status=?"; $params[]=$status; }
        if($sector){ $sql.=" AND p.sector=?"; $params[]=$sector; }
        $sql.=" ORDER BY p.created_at DESC LIMIT 100";
        $projects=$this->safeFetchAll($db,$sql,$params);
        return $this->view('admin/projects', ['projects'=>$projects,'pageTitle'=>'Projets']);
    }
    public function projectShow($id){
        $db=$this->getDb();
        $p=$this->safeFetchOne($db,"SELECT p.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as promoter_name, u.email FROM projects p LEFT JOIN users u ON p.user_id=u.id WHERE p.id=?",[$id],null);
        if(!$p){ $this->getSession()->setFlashMessage('error','Projet introuvable'); return $this->redirect('/admin/projects'); }
        $docs=$this->safeFetchAll($db,"SELECT * FROM project_documents WHERE project_id=? ORDER BY created_at DESC",[$id]);
        $investments=$this->safeFetchAll($db,"SELECT inv.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as investor_name FROM investments inv LEFT JOIN users u ON inv.investor_id = u.id WHERE inv.project_id=? LIMIT 20",[$id]);
        return $this->view('admin/project-show', ['project'=>$p,'documents'=>$docs,'investments'=>$investments,'pageTitle'=>$p['title']]);
    }
    public function projectReview($id){
        $db=$this->getDb();
        $p=$this->safeFetchOne($db,"SELECT * FROM projects WHERE id=?",[$id],null);
        if(!$p){ $this->getSession()->setFlashMessage('error','Projet introuvable'); return $this->redirect('/admin/projects'); }
        return $this->view('admin/project-review', ['project'=>$p,'pageTitle'=>'Validation projet']);
    }
    public function approveProject($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');
        $project = $this->safeFetchOne($db,"SELECT * FROM projects WHERE id = ?",[$id],null);
        if (!$project) { $session->setFlashMessage('error','Projet introuvable'); return $this->redirect('/admin/projects'); }
        $old=$project['status'];
        $db->execute("UPDATE projects SET status = 'approved', validation_status = 'approved', reviewed_at = NOW(), reviewed_by = ? WHERE id = ?",[$userId, $id]);
        AdminHelper::auditLog($db,$userId,'approbation','projects',$id,['status'=>$old],['status'=>'approved'],"Approbation projet #$id");
        $this->notifyProjectOwner($id, 'approved');
        $session->setFlashMessage('success','Projet approuvé');
        return $this->redirect('/admin/projects');
    }
    public function rejectProject($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $userId = $session->get('user_id');
        $reason = $this->getRequest()->getBodyParam('reason') ?? $this->getRequest()->getParam('reason') ?? 'Rejeté par administrateur';
        $project = $this->safeFetchOne($db,"SELECT * FROM projects WHERE id = ?",[$id],null);
        if (!$project) { $session->setFlashMessage('error','Projet introuvable'); return $this->redirect('/admin/projects'); }
        if(empty(trim($reason))){ $session->setFlashMessage('error','Un commentaire est obligatoire en cas de rejet'); return $this->redirect('/admin/projects/'.$id.'/review'); }
        $db->execute("UPDATE projects SET status = 'rejected', validation_status = 'rejected', reviewed_at = NOW(), reviewed_by = ?, rejection_reason = ? WHERE id = ?",[$userId,$reason,$id]);
        AdminHelper::auditLog($db,$userId,'rejet','projects',$id,['status'=>$project['status']],['status'=>'rejected'],"Rejet projet #$id : $reason");
        $this->notifyProjectOwner($id, 'rejected');
        $session->setFlashMessage('success','Projet rejeté');
        return $this->redirect('/admin/projects');
    }
    public function requestProjectInfo($id){
        $db=$this->getDb(); $session=$this->getSession();
        $p=$this->safeFetchOne($db,"SELECT * FROM projects WHERE id=?",[$id],null);
        if(!$p){ $session->setFlashMessage('error','Projet introuvable'); return $this->redirect('/admin/projects');}
        $db->execute("UPDATE projects SET status='info_requested', validation_status='info_requested' WHERE id=?",[$id]);
        $session->setFlashMessage('success','Informations demandées au promoteur');
        return $this->redirect('/admin/projects/'.$id.'/review');
    }
    private function notifyProjectOwner($projectId, $action)
    {
        $db = $this->getDb();
        $project = $this->safeFetchOne($db,"SELECT p.id, p.title, p.user_id, u.email, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as name FROM projects p LEFT JOIN users u ON p.user_id = u.id WHERE p.id = ?",[$projectId],null);
        if (!$project || empty($project['email'])) return;
        // Simulate email
        try{ $db->execute("INSERT INTO notifications (user_id, type, title, message, link, priority) VALUES (?,?,?,?,?,?)",[$project['user_id'], $action==='approved'?'project_status':'project_status', $action==='approved'?'Projet approuvé':'Projet rejeté', "Votre projet '{$project['title']}' a été $action", "/projects/{$project['id']}", 'important']); }catch(\Exception $e){}
    }
    public function deleteProject($id)
    {
        $db = $this->getDb();
        $session = $this->getSession();
        $project = $this->safeFetchOne($db,"SELECT * FROM projects WHERE id = ?",[$id],null);
        if (!$project) { $session->setFlashMessage('error','Projet introuvable'); return $this->redirect('/admin/projects'); }
        // soft delete if column exists else hard
        try{ $db->execute("UPDATE projects SET deleted_at=NOW() WHERE id = ?",[$id]); }catch(\Exception $e){ $db->execute("DELETE FROM projects WHERE id = ?",[$id]); }
        AdminHelper::auditLog($db,$session->get('user_id'),'suppression','projects',$id,$project,null,"Suppression projet #$id");
        $session->setFlashMessage('success','Projet supprimé (restauration possible par Super Admin)');
        return $this->redirect('/admin/projects');
    }

    // --- INVESTORS (Spec 27-30) ---
    public function investors()
    {
        $db = $this->getDb();
        $q=$this->getRequest()->getParam('q'); $status=$this->getRequest()->getParam('status'); $type=$this->getRequest()->getParam('type');
        $sql="SELECT i.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as full_name, u.email, u.status as user_status FROM investors i JOIN users u ON i.user_id=u.id WHERE 1=1";
        $params=[];
        if($q){ $sql.=" AND (u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR i.company_name LIKE ?)"; $like="%$q%"; $params=array_merge($params,[$like,$like,$like,$like]);}
        if($status){ $sql.=" AND i.investor_status=?"; $params[]=$status; }
        if($type){ $sql.=" AND i.investor_type=?"; $params[]=$type; }
        $sql.=" ORDER BY i.created_at DESC LIMIT 100";
        $investors=$this->safeFetchAll($db,$sql,$params);
        return $this->view('admin/investors', ['investors'=>$investors,'pageTitle'=>'Investisseurs']);
    }
    public function investorShow($id){
        $db=$this->getDb();
        $inv=$this->safeFetchOne($db,"SELECT i.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as full_name, u.email, u.phone, u.status as user_status FROM investors i JOIN users u ON i.user_id=u.id WHERE i.id=?",[$id],null);
        if(!$inv){ $this->getSession()->setFlashMessage('error','Investisseur introuvable'); return $this->redirect('/admin/investors');}
        $investments=$this->safeFetchAll($db,"SELECT inv.*, p.title as project_title FROM investments inv LEFT JOIN projects p ON inv.project_id=p.id WHERE inv.investor_id=? LIMIT 20",[$id]);
        $accesses=$this->safeFetchAll($db,"SELECT dra.*, p.title as project_title FROM data_room_accesses dra LEFT JOIN projects p ON dra.project_id=p.id WHERE dra.investor_id=? LIMIT 20",[$id]);
        return $this->view('admin/investor-show', ['investor'=>$inv,'investments'=>$investments,'accesses'=>$accesses,'pageTitle'=>$inv['full_name']]);
    }
    public function approveInvestor($id){ $db=$this->getDb(); $session=$this->getSession(); $userId=$session->get('user_id'); $investor=$this->safeFetchOne($db,"SELECT * FROM investors WHERE id = ?",[$id],null); if(!$investor){ $session->setFlashMessage('error','Investisseur introuvable'); return $this->redirect('/admin/investors'); } $db->execute("UPDATE investors SET investor_status = 'approved', kyc_status='valide', kyc_reviewed_at = NOW(), kyc_reviewed_by = ? WHERE id = ?",[$userId,$id]); $db->execute("UPDATE users SET status = 'active' WHERE id = ?",[$investor['user_id']]); AdminHelper::auditLog($db,$userId,'approbation','investors',$id,['status'=>$investor['investor_status']],['status'=>'approved'],"KYC approuvé #$id"); $session->setFlashMessage('success','Investisseur approuvé'); return $this->redirect('/admin/investors'); }
    public function requestInvestorInfo($id){ $db=$this->getDb(); $session=$this->getSession(); $investor=$this->safeFetchOne($db,"SELECT i.*, u.email FROM investors i JOIN users u ON i.user_id = u.id WHERE i.id = ?",[$id],null); if(!$investor){ $session->setFlashMessage('error','Investisseur introuvable'); return $this->redirect('/admin/investors'); } $db->execute("UPDATE investors SET investor_status = 'additional_info', kyc_status='incomplet', kyc_reviewed_at = NOW() WHERE id = ?",[$id]); try{ $db->execute("INSERT INTO notifications (user_id, type, title, message, priority) VALUES (?,?,?, ?,?)",[$investor['user_id'],'kyc_status','Informations complémentaires requises','Veuillez fournir les documents manquants.','important']); }catch(\Exception $e){} $session->setFlashMessage('success','Demande d\'informations envoyée'); return $this->redirect('/admin/investors'); }
    public function rejectInvestor($id){ $db=$this->getDb(); $session=$this->getSession(); $userId=$session->get('user_id'); $investor=$this->safeFetchOne($db,"SELECT * FROM investors WHERE id = ?",[$id],null); if(!$investor){ $session->setFlashMessage('error','Investisseur introuvable'); return $this->redirect('/admin/investors'); } $reason=$this->getRequest()->getBodyParam('reason') ?? 'Rejeté par administrateur'; $db->execute("UPDATE investors SET investor_status = 'rejected', kyc_status='rejete', kyc_reviewed_at = NOW(), kyc_reviewed_by = ?, rejection_reason = ? WHERE id = ?",[$userId,$reason,$id]); $db->execute("UPDATE users SET status = 'suspended' WHERE id = ?",[$investor['user_id']]); AdminHelper::auditLog($db,$userId,'rejet','investors',$id,['status'=>$investor['investor_status']],['status'=>'rejected'],"KYC rejeté #$id"); $session->setFlashMessage('success','Investisseur rejeté'); return $this->redirect('/admin/investors'); }

    // --- FUNDRAISING (Spec 31-33) ---
    public function fundraising(){
        $db=$this->getDb();
        $campaigns=$this->safeFetchAll($db,"SELECT fc.*, p.title as project_title, p.funding_sought as objective_fallback FROM funding_campaigns fc LEFT JOIN projects p ON fc.project_id=p.id ORDER BY fc.created_at DESC LIMIT 100");
        // fallback if no campaigns, show projects
        if(empty($campaigns)){
            $campaigns=$this->safeFetchAll($db,"SELECT p.id as project_id, p.title as project_title, p.funding_sought as target_amount, COALESCE(p.funding_mobilized,0) as amount_raised, p.status FROM projects p ORDER BY p.created_at DESC LIMIT 20");
        }
        return $this->view('admin/fundraising', ['campaigns'=>$campaigns,'pageTitle'=>'Levées de fonds']);
    }
    public function fundraisingShow($id){
        $db=$this->getDb();
        $c=$this->safeFetchOne($db,"SELECT fc.*, p.title as project_title FROM funding_campaigns fc LEFT JOIN projects p ON fc.project_id=p.id WHERE fc.id=?",[$id],null);
        if(!$c){ $this->getSession()->setFlashMessage('error','Campagne introuvable'); return $this->redirect('/admin/fundraising');}
        $investments=$this->safeFetchAll($db,"SELECT inv.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as investor_name FROM investments inv LEFT JOIN investors i ON inv.investor_id=i.id LEFT JOIN users u ON i.user_id=u.id WHERE inv.campaign_id=? LIMIT 50",[$id]);
        return $this->view('admin/fundraising-show', ['campaign'=>$c,'investments'=>$investments,'pageTitle'=>$c['title'] ?? $c['project_title']]);
    }

    // --- INVESTMENTS (Spec 34-37) ---
    public function investments(){
        $db=$this->getDb();
        $q=$this->getRequest()->getParam('q'); $status=$this->getRequest()->getParam('status');
        $sql="SELECT inv.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as investor_name, p.title as project_title, fc.title as campaign_title FROM investments inv LEFT JOIN investors i ON inv.investor_id=i.id LEFT JOIN users u ON i.user_id=u.id LEFT JOIN projects p ON inv.project_id=p.id LEFT JOIN funding_campaigns fc ON inv.campaign_id=fc.id WHERE inv.deleted_at IS NULL";
        $params=[];
        if($q){ $sql.=" AND (inv.reference LIKE ? OR p.title LIKE ? OR u.email LIKE ?)"; $like="%$q%"; $params=array_merge($params,[$like,$like,$like]);}
        if($status){ $sql.=" AND inv.status=?"; $params[]=$status; }
        $sql.=" ORDER BY inv.created_at DESC LIMIT 100";
        // try with deleted_at, fallback without
        try{ $list=$db->fetchAll($sql,$params); }catch(\Exception $e){
            $sql=str_replace("AND inv.deleted_at IS NULL","",$sql);
            $list=$this->safeFetchAll($db,$sql,$params);
        }
        return $this->view('admin/investments', ['investments'=>$list,'pageTitle'=>'Investissements']);
    }
    public function investmentShow($id){
        $db=$this->getDb();
        $inv=$this->safeFetchOne($db,"SELECT inv.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as investor_name, p.title as project_title FROM investments inv LEFT JOIN investors i ON inv.investor_id=i.id LEFT JOIN users u ON i.user_id=u.id LEFT JOIN projects p ON inv.project_id=p.id WHERE inv.id=?",[$id],null);
        if(!$inv){ $this->getSession()->setFlashMessage('error','Investissement introuvable'); return $this->redirect('/admin/investments');}
        return $this->view('admin/investment-show', ['investment'=>$inv,'pageTitle'=>$inv['reference']]);
    }
    public function confirmInvestment($id){
        $db=$this->getDb(); $session=$this->getSession();
        $inv=$this->safeFetchOne($db,"SELECT * FROM investments WHERE id=?",[$id],null);
        if(!$inv){ $session->setFlashMessage('error','Investissement introuvable'); return $this->redirect('/admin/investments');}
        $db->execute("UPDATE investments SET status='confirme' WHERE id=?",[$id]);
        // Mise à jour automatique : projet, campagne, commission
        try{
            $db->execute("UPDATE projects SET funding_mobilized = COALESCE(funding_mobilized,0)+?, amount_raised = COALESCE(amount_raised,0)+? WHERE id=?", [$inv['amount'],$inv['amount'],$inv['project_id']]);
            if(!empty($inv['campaign_id'])){
                $db->execute("UPDATE funding_campaigns SET amount_raised = COALESCE(amount_raised,0)+? WHERE id=?", [$inv['amount'],$inv['campaign_id']]);
                // 80% & 100% notifications
                $camp=$this->safeFetchOne($db,"SELECT * FROM funding_campaigns WHERE id=?",[$inv['campaign_id']],null);
                if($camp){
                    $prog = $camp['objective']>0 ? ($camp['amount_raised']/$camp['objective']*100) : 0;
                    if($prog>=100){ $db->execute("UPDATE funding_campaigns SET status='objectif_atteint' WHERE id=? AND status!='objectif_atteint'",[$camp['id']]); }
                    elseif($prog>=80){ try{ $db->execute("INSERT INTO notifications (user_id, type, title, message, priority) SELECT id, 'investment_update','Campagne à 80%','Campagne ". $camp['title']." a atteint 80%','important' FROM users WHERE role='admin'"); }catch(\Exception $e){} }
                }
            }
            // Commission
            $rate=$inv['commission_rate'] ?? 3.00;
            $commission=AdminHelper::commission($inv['amount'],$rate);
            $ref=AdminHelper::generateReference($db,'COM');
            try{ $db->execute("INSERT INTO commissions (reference, investment_id, project_id, client_id, base_amount, rate, status) VALUES (?,?,?,?,?,?, 'a_facturer')",[$ref,$inv['id'],$inv['project_id'],$inv['investor_id'],$inv['amount'],$rate]); }catch(\Exception $e){}
        }catch(\Exception $e){}
        AdminHelper::auditLog($db,$session->get('user_id'),'confirmation','investments',$id,['status'=>$inv['status']],['status'=>'confirme'],"Investment confirmé #$id, mise à jour projet/campagne/commission");
        $session->setFlashMessage('success','Investissement confirmé — projet, campagne et commission mis à jour');
        return $this->redirect('/admin/investments');
    }

    // --- COMMISSIONS (Spec 38-39) ---
    public function commissions(){
        $db=$this->getDb();
        $list=$this->safeFetchAll($db,"SELECT c.*, p.title as project_title, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as client_name FROM commissions c LEFT JOIN projects p ON c.project_id=p.id LEFT JOIN investors i ON c.client_id=i.id LEFT JOIN users u ON i.user_id=u.id WHERE c.deleted_at IS NULL ORDER BY c.created_at DESC LIMIT 100");
        if(empty($list)) $list=$this->safeFetchAll($db,"SELECT c.*, p.title as project_title FROM commissions c LEFT JOIN projects p ON c.project_id=p.id ORDER BY c.created_at DESC LIMIT 100");
        return $this->view('admin/commissions', ['commissions'=>$list,'pageTitle'=>'Commissions']);
    }

    // --- DATA ROOM (Spec 40-45) ---
    public function dataRooms(){
        $db=$this->getDb();
        $rooms=$this->safeFetchAll($db,"SELECT dr.*, p.title as project_title, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as promoter_name, (SELECT COUNT(*) FROM data_room_documents d WHERE d.data_room_id=dr.id) as doc_count, (SELECT COUNT(*) FROM data_room_accesses a WHERE a.data_room_id=dr.id) as access_count FROM data_rooms dr LEFT JOIN projects p ON dr.project_id=p.id LEFT JOIN users u ON dr.promoter_id=u.id ORDER BY dr.created_at DESC LIMIT 100");
        if(empty($rooms)){
            // fallback show projects with documents
            $rooms=$this->safeFetchAll($db,"SELECT p.id as project_id, p.title as project_title, p.status, (SELECT COUNT(*) FROM project_documents pd WHERE pd.project_id=p.id) as doc_count FROM projects p ORDER BY p.created_at DESC LIMIT 20");
        }
        return $this->view('admin/data-rooms', ['rooms'=>$rooms,'pageTitle'=>'Data Rooms']);
    }
    public function dataRoomShow($id){
        $db=$this->getDb();
        $room=$this->safeFetchOne($db,"SELECT dr.*, p.title as project_title FROM data_rooms dr LEFT JOIN projects p ON dr.project_id=p.id WHERE dr.id=?",[$id],null);
        if(!$room) $room=$this->safeFetchOne($db,"SELECT p.id as project_id, p.title as project_title, p.status FROM projects p WHERE p.id=?",[$id],null);
        $docs=$this->safeFetchAll($db,"SELECT * FROM data_room_documents WHERE data_room_id=? ORDER BY created_at DESC",[$id]);
        if(empty($docs)) $docs=$this->safeFetchAll($db,"SELECT * FROM project_documents WHERE project_id=? ORDER BY created_at DESC",[$id]);
        $accesses=$this->safeFetchAll($db,"SELECT dra.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as investor_name FROM data_room_accesses dra LEFT JOIN investors i ON dra.investor_id=i.id LEFT JOIN users u ON i.user_id=u.id WHERE dra.data_room_id=? LIMIT 100",[$id]);
        $logs=$this->safeFetchAll($db,"SELECT dal.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as investor_name FROM data_room_audit_log dal LEFT JOIN investors i ON dal.investor_id=i.id LEFT JOIN users u ON i.user_id=u.id WHERE dal.project_id=? ORDER BY dal.created_at DESC LIMIT 50",[$id]);
        return $this->view('admin/data-room-show', ['room'=>$room,'documents'=>$docs,'accesses'=>$accesses,'logs'=>$logs,'pageTitle'=>'Data Room']);
    }
    public function approveDataRoomAccess($id){
        $db=$this->getDb(); $session=$this->getSession();
        $acc=$this->safeFetchOne($db,"SELECT * FROM data_room_accesses WHERE id=?",[$id],null);
        if(!$acc){ $session->setFlashMessage('error','Accès introuvable'); return $this->redirect('/admin/data-rooms');}
        $db->execute("UPDATE data_room_accesses SET status='autorise', decided_at=NOW(), decided_by=? WHERE id=?",[$session->get('user_id'),$id]);
        AdminHelper::auditLog($db,$session->get('user_id'),'approbation','data_rooms',$id,null,null,"Accès Data Room autorisé #$id");
        $session->setFlashMessage('success','Accès autorisé');
        return $this->redirect('/admin/data-rooms/'.$acc['data_room_id']);
    }

    // --- REQUESTS (Spec 46-49) ---
    public function requests(){
        $db=$this->getDb();
        $list=$this->safeFetchAll($db,"SELECT r.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as requester_name_full, p.title as project_title, COALESCE(CONCAT(a.first_name,' ',a.last_name),a.name) as assignee_name FROM requests r LEFT JOIN users u ON r.requester_id=u.id LEFT JOIN projects p ON r.project_id=p.id LEFT JOIN users a ON r.assigned_to=a.id WHERE r.deleted_at IS NULL ORDER BY r.created_at DESC LIMIT 100");
        if(empty($list)) $list=$this->safeFetchAll($db,"SELECT c.*, c.name as requester_name_full, c.subject as subject FROM contacts c ORDER BY c.created_at DESC LIMIT 50");
        return $this->view('admin/requests', ['requests'=>$list,'pageTitle'=>'Demandes']);
    }
    public function assignRequest($id){
        $db=$this->getDb(); $session=$this->getSession();
        $agent=$this->getRequest()->getParam('agent', $session->get('user_id'));
        try{ $db->execute("UPDATE requests SET assigned_to=?, status='assigne' WHERE id=?",[$agent,$id]); }catch(\Exception $e){ $session->setFlashMessage('error','Impossible d\'assigner'); return $this->redirect('/admin/requests');}
        $session->setFlashMessage('success','Demande assignée');
        return $this->redirect('/admin/requests');
    }

    // --- APPOINTMENTS (Spec 50) ---
    public function appointments(){
        $db=$this->getDb();
        $list=$this->safeFetchAll($db,"SELECT ap.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as user_name, p.title as project_title FROM appointments ap LEFT JOIN users u ON ap.user_id=u.id LEFT JOIN projects p ON ap.project_id=p.id WHERE ap.deleted_at IS NULL ORDER BY ap.appointment_date DESC, ap.appointment_time DESC LIMIT 100");
        if(empty($list)){
            $visits=$this->safeFetchAll($db,"SELECT pv.*, p.title as project_title FROM project_visits pv LEFT JOIN projects p ON pv.project_id=p.id ORDER BY pv.preferred_date DESC LIMIT 50");
            $reserv=$this->safeFetchAll($db,"SELECT pr.*, p.title as project_title FROM project_reservations pr LEFT JOIN projects p ON pr.project_id=p.id ORDER BY pr.created_at DESC LIMIT 50");
            $list=array_merge($visits,$reserv);
        }
        return $this->view('admin/appointments', ['appointments'=>$list,'pageTitle'=>'Visites / Réservations']);
    }

    // --- NOTIFICATIONS (Spec 51-52) ---
    public function notifications(){
        $db=$this->getDb();
        $list=$this->safeFetchAll($db,"SELECT n.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as user_name FROM notifications n LEFT JOIN users u ON n.user_id=u.id ORDER BY n.created_at DESC LIMIT 100");
        return $this->view('admin/notifications', ['notifications'=>$list,'pageTitle'=>'Notifications']);
    }

    // --- REPORTS (Spec 56-60) ---
    public function reports(){
        $db=$this->getDb();
        $crm = [];
        try{
            $crm['leads_created'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM leads WHERE deleted_at IS NULL",[],['c'=>0])['c'];
            $crm['leads_converted'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM leads WHERE status='converti'",[],['c'=>0])['c'];
            $crm['opportunities'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM opportunities WHERE deleted_at IS NULL",[],['c'=>0])['c'];
            $crm['pipeline_value'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount*probability/100),0) as t FROM opportunities WHERE deleted_at IS NULL",[],['t'=>0])['t'];
            $crm['won'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount),0) as t FROM opportunities WHERE stage='gagne'",[],['t'=>0])['t'];
            $crm['lost'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount),0) as t FROM opportunities WHERE stage='perdu'",[],['t'=>0])['t'];
            $crm['by_user'] = $this->safeFetchAll($db,"SELECT COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as name, COUNT(o.id) as cnt, COALESCE(SUM(o.amount),0) as tot FROM opportunities o LEFT JOIN users u ON o.responsible_id=u.id WHERE o.deleted_at IS NULL GROUP BY u.id LIMIT 10");
        }catch(\Exception $e){}
        $investReport = [];
        try{
            $investReport['total'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(amount),0) as t FROM investments",[],['t'=>0])['t'];
            $investReport['count'] = $this->safeFetchOne($db,"SELECT COUNT(*) as c FROM investments",[],['c'=>0])['c'];
            $investReport['avg'] = $investReport['count'] ? round($investReport['total']/$investReport['count'],2) : 0;
            $investReport['by_sector'] = $this->safeFetchAll($db,"SELECT p.sector, COALESCE(SUM(inv.amount),0) as tot FROM investments inv LEFT JOIN projects p ON inv.project_id=p.id GROUP BY p.sector");
        }catch(\Exception $e){}
        $fundReport = $this->safeFetchAll($db,"SELECT p.title, p.funding_sought as objectif, COALESCE(p.funding_mobilized,0) as leve, CASE WHEN p.funding_sought>0 THEN ROUND(COALESCE(p.funding_mobilized,0)/p.funding_sought*100,1) ELSE 0 END as pct, (SELECT COUNT(*) FROM investments inv WHERE inv.project_id=p.id) as nb FROM projects p ORDER BY pct DESC LIMIT 20");
        $commReport = [];
        try{
            $commReport['expected'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(commission_amount),0) as t FROM commissions WHERE status='a_facturer'",[],['t'=>0])['t'];
            $commReport['invoiced'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(invoiced),0) as t FROM commissions",[],['t'=>0])['t'];
            $commReport['collected'] = $this->safeFetchOne($db,"SELECT COALESCE(SUM(paid),0) as t FROM commissions",[],['t'=>0])['t'];
        }catch(\Exception $e){}

        return $this->view('admin/reports', ['crm'=>$crm,'investReport'=>$investReport,'fundReport'=>$fundReport,'commReport'=>$commReport,'pageTitle'=>'Rapports']);
    }

    // --- AUDIT LOG (Spec 64) ---
    public function auditLog(){
        $db=$this->getDb();
        $logs=$this->safeFetchAll($db,"SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 100");
        return $this->view('admin/audit-log', ['logs'=>$logs,'pageTitle'=>'Journal d\'audit']);
    }

    // --- SETTINGS (Spec 61-63, 66) ---
    public function settings(){
        $db=$this->getDb();
        $admins=$this->safeFetchAll($db,"SELECT u.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as full_name FROM users u WHERE u.role IN ('admin','super_admin','direction','admin_projets','admin_investissement','commercial','finance','content_manager','support') ORDER BY u.created_at DESC LIMIT 50");
        $roles=$this->safeFetchAll($db,"SELECT * FROM roles ORDER BY name");
        $perms=$this->safeFetchAll($db,"SELECT * FROM permissions ORDER BY module, name");
        return $this->view('admin/settings', ['admins'=>$admins,'roles'=>$roles,'permissions'=>$perms,'pageTitle'=>'Paramètres']);
    }
    public function adminUsers(){
        return $this->settings();
    }

    // --- SEARCH GLOBALE (Spec 53) ---
    public function search(){
        $db=$this->getDb();
        $q=$this->getRequest()->getParam('q');
        if(!$q) return $this->redirect('/admin/dashboard');
        $like="%$q%";
        $results=[];
        $results['projects']=$this->safeFetchAll($db,"SELECT id, title, status FROM projects WHERE title LIKE ? OR reference LIKE ? LIMIT 10",[$like,$like]);
        $results['users']=$this->safeFetchAll($db,"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name, email FROM users WHERE email LIKE ? OR first_name LIKE ? OR last_name LIKE ? LIMIT 10",[$like,$like,$like]);
        $results['opportunities']=$this->tableExists($db,'opportunities') ? $this->safeFetchAll($db,"SELECT id, name, stage FROM opportunities WHERE name LIKE ? OR reference LIKE ? LIMIT 10",[$like,$like]) : [];
        $results['investors']=$this->safeFetchAll($db,"SELECT i.id, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as name FROM investors i JOIN users u ON i.user_id=u.id WHERE u.email LIKE ? LIMIT 10",[$like]);
        return $this->view('admin/search', ['q'=>$q,'results'=>$results,'pageTitle'=>'Recherche: '.$q]);
    }

    // --- EXPORT (Spec 55) ---
    public function export(){
        $type=$this->getRequest()->getParam('type','csv');
        $module=$this->getRequest()->getParam('module','projects');
        $db=$this->getDb();
        $data=[];
        if($module==='projects') $data=$this->safeFetchAll($db,"SELECT * FROM projects LIMIT 500");
        elseif($module==='users') $data=$this->safeFetchAll($db,"SELECT * FROM users LIMIT 500");
        elseif($module==='investments') $data=$this->safeFetchAll($db,"SELECT * FROM investments LIMIT 500");
        else $data=[['message'=>'Export '.$module]];

        if($type==='csv'){
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$module.'-'.date('Ymd').'.csv"');
            $out=fopen('php://output','w');
            if(!empty($data)){ fputcsv($out,array_keys($data[0])); foreach($data as $row) fputcsv($out,$row); }
            fclose($out); exit;
        } elseif($type==='json'){
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="'.$module.'-'.date('Ymd').'.json"');
            echo json_encode($data, JSON_PRETTY_PRINT); exit;
        }
        $this->getSession()->setFlashMessage('success','Export '.$type.' généré');
        return $this->redirect('/admin/'.$module);
    }

    // --- existing news & stats keep ---
    public function statistics()
    {
        $db = $this->getDb();
        $stats = [
            'projects_by_status' => $this->safeFetchAll($db,"SELECT status, COUNT(*) as count FROM projects GROUP BY status"),
            'projects_by_type' => $this->safeFetchAll($db,"SELECT type, COUNT(*) as count FROM projects WHERE status = 'approved' GROUP BY type"),
            'projects_by_country' => $this->safeFetchAll($db,"SELECT country, COUNT(*) as count FROM projects WHERE status = 'approved' GROUP BY country"),
            'investors_by_status' => $this->safeFetchAll($db,"SELECT investor_status, COUNT(*) as count FROM investors GROUP BY investor_status"),
            'investors_by_type' => $this->safeFetchAll($db,"SELECT type, COUNT(*) as count FROM investors GROUP BY type"),
            'funding_trends' => $this->safeFetchAll($db,"SELECT DATE(created_at) as date, SUM(funding_sought) as total FROM projects WHERE status = 'approved' GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 30"),
        ];
        return $this->view('admin/statistics', ['stats'=>$stats]);
    }
    public function news(){ $db=$this->getDb(); $news=$this->safeFetchAll($db,"SELECT n.*, u.first_name, u.last_name FROM news n LEFT JOIN users u ON n.author_id = u.id ORDER BY n.created_at DESC"); return $this->view('admin/news', ['news'=>$news]); }
    public function createNews(){ return $this->view('admin/news-form', ['newsItem'=>null,'action'=>'/admin/news/create','method'=>'POST']); }
    public function storeNews(){ $request=$this->getRequest(); $db=$this->getDb(); $session=$this->getSession(); $userId=$session->get('user_id'); if(!$request->isPost()) return $this->redirect('/admin/news'); $title=trim($request->getBodyParam('title')); $excerpt=trim($request->getBodyParam('excerpt')); $content=trim($request->getBodyParam('content')); $category=trim($request->getBodyParam('category')); $status=trim($request->getBodyParam('status'))?:'draft'; if(empty($title)||empty($category)){ $session->setFlashMessage('error','Champs requis'); return $this->redirect('/admin/news/create'); } $slug=$this->slugify($title); $image=null; if(isset($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){ $uploadPath=PUBLIC_PATH.'/uploads/news'; if(!is_dir($uploadPath)) mkdir($uploadPath,0755,true); $ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION)); $allowed=['jpg','jpeg','png','gif','webp']; $maxSize=$this->config['upload']['max_size']??5242880; if($_FILES['image']['size']>$maxSize){ $session->setFlashMessage('error','Image trop volumineuse'); return $this->redirect('/admin/news/create'); } if(in_array($ext,$allowed)){ $fn='news_'.time().'_'.rand(1000,9999).'.'.$ext; if(move_uploaded_file($_FILES['image']['tmp_name'],$uploadPath.'/'.$fn)) $image='/uploads/news/'.$fn; } } $publishedAt=$status==='published'?date('Y-m-d H:i:s'):null; $db->execute("INSERT INTO news (title, slug, excerpt, content, category, image, author_id, status, published_at, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())",[$title,$slug,$excerpt,$content,$category,$image,$userId,$status,$publishedAt]); $session->setFlashMessage('success','Actualité créée'); return $this->redirect('/admin/news'); }
    public function editNews($id){ $db=$this->getDb(); $newsItem=$this->safeFetchOne($db,"SELECT * FROM news WHERE id = ?",[$id],null); if(!$newsItem){ $this->getSession()->setFlashMessage('error','Actualité introuvable'); return $this->redirect('/admin/news'); } return $this->view('admin/news-form', ['newsItem'=>$newsItem,'action'=>'/admin/news/'.$id.'/edit','method'=>'POST']); }
    public function updateNews($id){ $request=$this->getRequest(); $db=$this->getDb(); $session=$this->getSession(); if(!$request->isPost()) return $this->redirect('/admin/news'); $newsItem=$this->safeFetchOne($db,"SELECT * FROM news WHERE id = ?",[$id],null); if(!$newsItem){ $session->setFlashMessage('error','Actualité introuvable'); return $this->redirect('/admin/news'); } $title=trim($request->getBodyParam('title')); $excerpt=trim($request->getBodyParam('excerpt')); $content=trim($request->getBodyParam('content')); $category=trim($request->getBodyParam('category')); $status=trim($request->getBodyParam('status'))?:'draft'; if(empty($title)||empty($category)){ $session->setFlashMessage('error','Champs requis'); return $this->redirect('/admin/news/'.$id.'/edit'); } $slug=$this->slugify($title); $image=$newsItem['image']; if(isset($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){ $uploadPath=PUBLIC_PATH.'/uploads/news'; if(!is_dir($uploadPath)) mkdir($uploadPath,0755,true); $ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION)); $allowed=['jpg','jpeg','png','gif','webp']; $maxSize=$this->config['upload']['max_size']??5242880; if($_FILES['image']['size']>$maxSize){ $session->setFlashMessage('error','Image trop volumineuse'); return $this->redirect('/admin/news/'.$id.'/edit'); } if(in_array($ext,$allowed)){ $fn='news_'.time().'_'.rand(1000,9999).'.'.$ext; if(move_uploaded_file($_FILES['image']['tmp_name'],$uploadPath.'/'.$fn)){ if(!empty($newsItem['image'])) $this->deleteImageFile($newsItem['image']); $image='/uploads/news/'.$fn; } } } $publishedAt=$status==='published'?date('Y-m-d H:i:s'):null; $db->execute("UPDATE news SET title=?, slug=?, excerpt=?, content=?, category=?, image=?, status=?, published_at=?, updated_at=NOW() WHERE id=?",[$title,$slug,$excerpt,$content,$category,$image,$status,$publishedAt,$id]); $session->setFlashMessage('success','Actualité mise à jour'); return $this->redirect('/admin/news'); }
    public function deleteNews($id){ $db=$this->getDb(); $session=$this->getSession(); $newsItem=$this->safeFetchOne($db,"SELECT * FROM news WHERE id = ?",[$id],null); if(!$newsItem){ $session->setFlashMessage('error','Actualité introuvable'); return $this->redirect('/admin/news'); } $db->execute("UPDATE news SET deleted_at = NOW() WHERE id = ?",[$id]); if(!empty($newsItem['image'])) $this->deleteImageFile($newsItem['image']); $session->setFlashMessage('success','Actualité supprimée'); return $this->redirect('/admin/news'); }
    private function deleteImageFile($publicPath){ $relative=ltrim($publicPath,'/'); $full=BASE_PATH.'/'.$relative; if(file_exists($full)&&is_file($full)) @unlink($full); }
    private function slugify($text){ $text=preg_replace('~[^\pL\d]+~u','-',$text); $text=trim($text,'-'); $text=iconv('utf-8','us-ascii//TRANSLIT',$text); $text=strtolower($text); $text=preg_replace('~[^-_\w]+~','',$text); if(empty($text)) return 'item-'.time(); return $text.'-'.time(); }
}
