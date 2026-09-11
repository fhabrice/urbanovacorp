<?php

namespace App\Controllers;

use App\Helpers\AdminHelper;

class CrmController extends Controller
{
    private function safeFetchOne($db,$sql,$params=[],$default=[]){ try{ $r=$db->fetchOne($sql,$params); return $r?:$default;}catch(\Exception $e){return $default;}}
    private function safeFetchAll($db,$sql,$params=[]){ try{return $db->fetchAll($sql,$params);}catch(\Exception $e){return [];} }
    private function tableExists($db,$table){ try{ $db->fetchOne("SELECT 1 FROM `$table` LIMIT 1"); return true;}catch(\Exception $e){return false;}}

    // ========== PROSPECTS (LEADS) ==========
    public function leads()
    {
        $db=$this->getDb(); $req=$this->getRequest();
        $q=$req->getParam('q'); $status=$req->getParam('status'); $source=$req->getParam('source'); $type=$req->getParam('type');
        $sql="SELECT l.*, p.title as project_title, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as responsible_name FROM leads l LEFT JOIN projects p ON l.project_id=p.id LEFT JOIN users u ON l.assigned_to=u.id WHERE l.deleted_at IS NULL";
        $params=[];
        if($q){ $sql.=" AND (l.last_name LIKE ? OR l.first_name LIKE ? OR l.email LIKE ? OR l.phone LIKE ? OR l.reference LIKE ?)"; $like="%$q%"; $params=array_merge($params,[$like,$like,$like,$like,$like]); }
        if($status){ $sql.=" AND l.status=?"; $params[]=$status;}
        if($source){ $sql.=" AND l.source=?"; $params[]=$source;}
        if($type){ $sql.=" AND l.type=?"; $params[]=$type;}
        $sql.=" ORDER BY l.created_at DESC LIMIT 100";
        try{ $leads=$db->fetchAll($sql,$params);}catch(\Exception $e){ $leads=$this->safeFetchAll($db,"SELECT * FROM leads ORDER BY created_at DESC LIMIT 50");}
        $projects=$this->safeFetchAll($db,"SELECT id, title FROM projects ORDER BY title LIMIT 100");
        $users=$this->safeFetchAll($db,"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name FROM users ORDER BY name LIMIT 100");
        return $this->view('admin/crm/leads', ['leads'=>$leads,'projects'=>$projects,'users'=>$users,'pageTitle'=>'Prospects']);
    }

    public function createLead()
    {
        $projects=$this->safeFetchAll($this->getDb(),"SELECT id, title FROM projects ORDER BY title LIMIT 100");
        $users=$this->safeFetchAll($this->getDb(),"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name FROM users ORDER BY name LIMIT 100");
        $companies=$this->safeFetchAll($this->getDb(),"SELECT id, name FROM companies ORDER BY name LIMIT 100");
        return $this->view('admin/crm/lead-form', ['lead'=>null,'projects'=>$projects,'users'=>$users,'companies'=>$companies,'pageTitle'=>'Nouveau prospect']);
    }

    public function storeLead()
    {
        $db=$this->getDb(); $req=$this->getRequest(); $session=$this->getSession();
        if(!$req->isPost()) return $this->redirect('/admin/crm/leads');
        $lastName=trim($req->getBodyParam('last_name')); $firstName=trim($req->getBodyParam('first_name'));
        $phone=trim($req->getBodyParam('phone')); $email=trim($req->getBodyParam('email'));
        $type=$req->getBodyParam('type','investisseur'); $source=$req->getBodyParam('source','site_web');
        $responsible=$req->getBodyParam('assigned_to'); $status=$req->getBodyParam('status','nouveau');
        if(empty($lastName) || (empty($phone) && empty($email)) || empty($type) || empty($source) || empty($responsible)){
            $session->setFlashMessage('error','Champs obligatoires: Nom, Téléphone ou Email, Type, Source, Responsable');
            return $this->redirect('/admin/crm/leads/create');
        }
        // duplication check
        if($email && AdminHelper::checkDuplicate($db,'leads','email',$email)){ $session->setFlashMessage('error','Un contact similaire existe déjà (email).'); return $this->redirect('/admin/crm/leads/create');}
        if($phone && AdminHelper::checkDuplicate($db,'leads','phone',$phone)){ $session->setFlashMessage('error','Un contact similaire existe déjà (téléphone).'); return $this->redirect('/admin/crm/leads/create');}
        $reference=AdminHelper::generateReference($db,'LEAD');
        $sql="INSERT INTO leads (reference, first_name, last_name, company_name, phone, email, type, source, project_id, amount_potential, status, country, city, function_title, sector, assigned_to, comment, next_action_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $params=[$reference,$firstName,$lastName,$req->getBodyParam('company_name'),$phone,$email,$type,$source,$req->getBodyParam('project_id')?:null,$req->getBodyParam('amount_potential')?:null,$status,$req->getBodyParam('country'),$req->getBodyParam('city'),$req->getBodyParam('function_title'),$req->getBodyParam('sector'),$responsible,$req->getBodyParam('comment'),$req->getBodyParam('next_action_date')?:null];
        try{ $db->execute($sql,$params); $id=$db->lastInsertId(); AdminHelper::auditLog($db,$session->get('user_id'),'creation','leads',$id,null,['reference'=>$reference],"Lead créé $reference"); // automation A1
            try{ $db->execute("INSERT INTO notifications (user_id, type, title, message, priority) VALUES (?,?,?, ?,?)",[$responsible,'prospect','Nouveau prospect','Prospect '.$lastName.' assigné.','action_requise']); }catch(\Exception $e){}
            $session->setFlashMessage('success','Prospect créé: '.$reference);
        }catch(\Exception $e){ $session->setFlashMessage('error','Erreur: '.$e->getMessage()); return $this->redirect('/admin/crm/leads/create');}
        return $this->redirect('/admin/crm/leads');
    }

    public function editLead($id)
    {
        $db=$this->getDb(); $lead=$this->safeFetchOne($db,"SELECT * FROM leads WHERE id=?",[$id],null);
        if(!$lead){ $this->getSession()->setFlashMessage('error','Prospect introuvable'); return $this->redirect('/admin/crm/leads');}
        $projects=$this->safeFetchAll($db,"SELECT id, title FROM projects ORDER BY title LIMIT 100");
        $users=$this->safeFetchAll($db,"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name FROM users ORDER BY name LIMIT 100");
        $companies=$this->safeFetchAll($db,"SELECT id, name FROM companies ORDER BY name LIMIT 100");
        return $this->view('admin/crm/lead-form', ['lead'=>$lead,'projects'=>$projects,'users'=>$users,'companies'=>$companies,'pageTitle'=>'Modifier prospect']);
    }

    public function updateLead($id)
    {
        $db=$this->getDb(); $req=$this->getRequest(); $session=$this->getSession();
        $lead=$this->safeFetchOne($db,"SELECT * FROM leads WHERE id=?",[$id],null);
        if(!$lead){ $session->setFlashMessage('error','Prospect introuvable'); return $this->redirect('/admin/crm/leads');}
        $old=$lead;
        $fields=['first_name','last_name','company_name','phone','email','type','source','project_id','amount_potential','status','country','city','function_title','sector','assigned_to','comment','next_action_date'];
        $sets=[]; $params=[];
        foreach($fields as $f){ $v=$req->getBodyParam($f); if($v!==null){ $sets[]="$f=?"; $params[]=$v?:null; } }
        if(empty($sets)){ $session->setFlashMessage('error','Aucune modification'); return $this->redirect('/admin/crm/leads/'.$id.'/edit');}
        $params[]=$id;
        $db->execute("UPDATE leads SET ".implode(',',$sets).", updated_at=NOW() WHERE id=?",$params);
        $new=$this->safeFetchOne($db,"SELECT * FROM leads WHERE id=?",[$id],[]);
        AdminHelper::auditLog($db,$session->get('user_id'),'modification','leads',$id,$old,$new,"Lead modifié #$id");
        $session->setFlashMessage('success','Prospect mis à jour');
        return $this->redirect('/admin/crm/leads');
    }

    public function deleteLead($id)
    {
        $db=$this->getDb(); $session=$this->getSession();
        $lead=$this->safeFetchOne($db,"SELECT * FROM leads WHERE id=?",[$id],null);
        if(!$lead){ $session->setFlashMessage('error','Prospect introuvable'); return $this->redirect('/admin/crm/leads');}
        try{ $db->execute("UPDATE leads SET deleted_at=NOW() WHERE id=?",[$id]); AdminHelper::auditLog($db,$session->get('user_id'),'suppression','leads',$id,$lead,null,"Suppression lead #$id"); $session->setFlashMessage('success','Prospect supprimé (Soft Delete)'); }catch(\Exception $e){ $session->setFlashMessage('error','Erreur suppression');}
        return $this->redirect('/admin/crm/leads');
    }

    public function convertLead($id)
    {
        $db=$this->getDb(); $session=$this->getSession();
        $lead=$this->safeFetchOne($db,"SELECT * FROM leads WHERE id=?",[$id],null);
        if(!$lead){ $session->setFlashMessage('error','Prospect introuvable'); return $this->redirect('/admin/crm/leads');}
        $choose=$this->getRequest()->getParam('to','contact');
        // Create contact
        $refC=AdminHelper::generateReference($db,'CONT');
        try{
            if($this->tableExists($db,'crm_contacts')){
                $db->execute("INSERT INTO crm_contacts (reference, first_name, last_name, email, phone, function_title, country, city, type, responsible_id, notes) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                    [$refC,$lead['first_name']?:'', $lead['last_name'], $lead['email'], $lead['phone'], $lead['function_title'], $lead['country'], $lead['city'], $lead['type'], $lead['assigned_to'], $lead['comment']]);
            }
            // Create opportunity if requested
            if(in_array($choose,['opportunity','all'])){
                $refO=AdminHelper::generateReference($db,'OPP');
                $db->execute("INSERT INTO opportunities (reference, name, company_id, project_id, type, amount, currency, probability, stage, responsible_id, opened_at) VALUES (?,?,?,?,?,?,?,?,?,?,CURDATE())",
                    [$refO, "Opportunité ".$lead['last_name'], $lead['company_id']??null, $lead['project_id']??null, 'investissement', $lead['amount_potential']??0, 'USD', 30, 'nouveau', $lead['assigned_to']]);
            }
        }catch(\Exception $e){}
        $db->execute("UPDATE leads SET status='converti' WHERE id=?",[$id]);
        $session->setFlashMessage('success','Prospect converti (statut: Converti, non supprimé)');
        return $this->redirect('/admin/crm/leads');
    }

    // ========== CONTACTS ==========
    public function contacts()
    {
        $db=$this->getDb(); $q=$this->getRequest()->getParam('q');
        $sql="SELECT c.*, comp.name as company_name, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as resp_name FROM crm_contacts c LEFT JOIN companies comp ON c.company_id=comp.id LEFT JOIN users u ON c.responsible_id=u.id WHERE c.deleted_at IS NULL";
        $params=[];
        if($q){ $sql.=" AND (c.last_name LIKE ? OR c.first_name LIKE ? OR c.email LIKE ?)"; $like="%$q%"; $params=[$like,$like,$like];}
        $sql.=" ORDER BY c.created_at DESC LIMIT 100";
        try{ $contacts=$db->fetchAll($sql,$params);}catch(\Exception $e){ $contacts=$this->safeFetchAll($db,"SELECT * FROM contacts ORDER BY created_at DESC LIMIT 100");}
        return $this->view('admin/crm/contacts', ['contacts'=>$contacts,'pageTitle'=>'Contacts']);
    }
    public function contactShow($id){
        $db=$this->getDb(); $c=$this->safeFetchOne($db,"SELECT c.*, comp.name as company_name FROM crm_contacts c LEFT JOIN companies comp ON c.company_id=comp.id WHERE c.id=?",[$id],null);
        if(!$c) $c=$this->safeFetchOne($db,"SELECT * FROM contacts WHERE id=?",[$id],null);
        if(!$c){ $this->getSession()->setFlashMessage('error','Contact introuvable'); return $this->redirect('/admin/crm/contacts');}
        $opps=$this->safeFetchAll($db,"SELECT * FROM opportunities WHERE contact_id=? LIMIT 20",[$id]);
        $acts=$this->safeFetchAll($db,"SELECT * FROM activities WHERE contact_id=? ORDER BY activity_date DESC LIMIT 20",[$id]);
        return $this->view('admin/crm/contact-show', ['contact'=>$c,'opportunities'=>$opps,'activities'=>$acts,'pageTitle'=>$c['first_name'].' '.$c['last_name']]);
    }

    // ========== COMPANIES ==========
    public function companies()
    {
        $db=$this->getDb(); $q=$this->getRequest()->getParam('q');
        $sql="SELECT comp.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as resp_name FROM companies comp LEFT JOIN users u ON comp.responsible_id=u.id WHERE comp.deleted_at IS NULL";
        $params=[];
        if($q){ $sql.=" AND (comp.name LIKE ? OR comp.rccm LIKE ? OR comp.nif LIKE ?)"; $like="%$q%"; $params=[$like,$like,$like];}
        $sql.=" ORDER BY comp.created_at DESC LIMIT 100";
        $companies=$this->safeFetchAll($db,$sql,$params);
        return $this->view('admin/crm/companies', ['companies'=>$companies,'pageTitle'=>'Entreprises']);
    }
    public function createCompany(){
        $users=$this->safeFetchAll($this->getDb(),"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name FROM users ORDER BY name LIMIT 100");
        return $this->view('admin/crm/company-form', ['company'=>null,'users'=>$users,'pageTitle'=>'Nouvelle entreprise']);
    }
    public function storeCompany(){
        $db=$this->getDb(); $req=$this->getRequest(); $session=$this->getSession();
        $name=trim($req->getBodyParam('name'));
        if(!$name){ $session->setFlashMessage('error','Nom entreprise requis'); return $this->redirect('/admin/crm/companies/create');}
        $rccm=$req->getBodyParam('rccm'); $nif=$req->getBodyParam('nif');
        if($rccm && AdminHelper::checkDuplicate($db,'companies','rccm',$rccm)){$session->setFlashMessage('error','RCCM déjà utilisé'); return $this->redirect('/admin/crm/companies/create');}
        if($nif && AdminHelper::checkDuplicate($db,'companies','nif',$nif)){$session->setFlashMessage('error','NIF déjà utilisé'); return $this->redirect('/admin/crm/companies/create');}
        $ref=AdminHelper::generateReference($db,'COMP');
        $db->execute("INSERT INTO companies (reference, name, legal_form, rccm, nif, id_national, sector, address, city, country, website, phone, email, responsible_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$ref,$name,$req->getBodyParam('legal_form'),$rccm?:null,$nif?:null,$req->getBodyParam('id_national'),$req->getBodyParam('sector'),$req->getBodyParam('address'),$req->getBodyParam('city'),$req->getBodyParam('country'),$req->getBodyParam('website'),$req->getBodyParam('phone'),$req->getBodyParam('email'),$req->getBodyParam('responsible_id')?:null]);
        $session->setFlashMessage('success','Entreprise créée: '.$ref);
        return $this->redirect('/admin/crm/companies');
    }

    // ========== OPPORTUNITIES ==========
    public function opportunities()
    {
        $db=$this->getDb(); $req=$this->getRequest();
        $q=$req->getParam('q'); $stage=$req->getParam('stage'); $resp=$req->getParam('responsible');
        $sql="SELECT o.*, COALESCE(CONCAT(cc.first_name,' ',cc.last_name),cc.name) as contact_name, comp.name as company_name, p.title as project_title, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as resp_name FROM opportunities o LEFT JOIN crm_contacts cc ON o.contact_id=cc.id LEFT JOIN companies comp ON o.company_id=comp.id LEFT JOIN projects p ON o.project_id=p.id LEFT JOIN users u ON o.responsible_id=u.id WHERE o.deleted_at IS NULL";
        $params=[];
        if($q){ $sql.=" AND (o.name LIKE ? OR o.reference LIKE ?)"; $like="%$q%"; $params=array_merge($params,[$like,$like]);}
        if($stage){ $sql.=" AND o.stage=?"; $params[]=$stage;}
        if($resp){ $sql.=" AND o.responsible_id=?"; $params[]=$resp;}
        $sql.=" ORDER BY o.created_at DESC LIMIT 100";
        $opps=$this->safeFetchAll($db,$sql,$params);
        $users=$this->safeFetchAll($db,"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name FROM users LIMIT 100");
        return $this->view('admin/crm/opportunities', ['opportunities'=>$opps,'users'=>$users,'pageTitle'=>'Opportunités']);
    }
    public function createOpportunity(){
        $db=$this->getDb();
        $contacts=$this->safeFetchAll($db,"SELECT id, CONCAT(first_name,' ',last_name) as name FROM crm_contacts WHERE deleted_at IS NULL LIMIT 100");
        $companies=$this->safeFetchAll($db,"SELECT id, name FROM companies WHERE deleted_at IS NULL LIMIT 100");
        $projects=$this->safeFetchAll($db,"SELECT id, title FROM projects ORDER BY title LIMIT 100");
        $users=$this->safeFetchAll($db,"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name FROM users LIMIT 100");
        return $this->view('admin/crm/opportunity-form', ['opportunity'=>null,'contacts'=>$contacts,'companies'=>$companies,'projects'=>$projects,'users'=>$users,'pageTitle'=>'Nouvelle opportunité']);
    }
    public function storeOpportunity(){
        $db=$this->getDb(); $req=$this->getRequest(); $session=$this->getSession();
        $name=trim($req->getBodyParam('name')); $amount=(float)$req->getBodyParam('amount',0); $prob=(int)$req->getBodyParam('probability',50);
        if(!$name){ $session->setFlashMessage('error','Nom opportunité requis'); return $this->redirect('/admin/crm/opportunities/create');}
        if($prob<0||$prob>100){ $session->setFlashMessage('error','Probabilité doit être entre 0 et 100'); return $this->redirect('/admin/crm/opportunities/create');}
        if($amount<0){ $session->setFlashMessage('error','Montant ne peut être négatif'); return $this->redirect('/admin/crm/opportunities/create');}
        $ref=AdminHelper::generateReference($db,'OPP');
        $db->execute("INSERT INTO opportunities (reference, name, contact_id, company_id, project_id, type, amount, currency, probability, stage, responsible_id, opened_at, expected_close, next_action, notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$ref,$name,$req->getBodyParam('contact_id')?:null,$req->getBodyParam('company_id')?:null,$req->getBodyParam('project_id')?:null,$req->getBodyParam('type','investissement'),$amount,$req->getBodyParam('currency','USD'),$prob,$req->getBodyParam('stage','nouveau'),$req->getBodyParam('responsible_id')?:null,$req->getBodyParam('opened_at')?:date('Y-m-d'),$req->getBodyParam('expected_close')?:null,$req->getBodyParam('next_action')?:null,$req->getBodyParam('notes')]);
        $session->setFlashMessage('success','Opportunité créée: '.$ref.' — valeur pondérée: '.AdminHelper::weightedValue($amount,$prob).' USD');
        return $this->redirect('/admin/crm/opportunities');
    }
    public function moveOpportunityStage($id){
        $db=$this->getDb(); $req=$this->getRequest(); $session=$this->getSession();
        $newStage=$req->getParam('stage') ?? $req->getBodyParam('stage');
        $opp=$this->safeFetchOne($db,"SELECT * FROM opportunities WHERE id=?",[$id],null);
        if(!$opp || !$newStage){ $session->setFlashMessage('error','Mouvement invalide'); return $this->redirect('/admin/crm/pipeline');}
        $old=$opp['stage'];
        $db->execute("UPDATE opportunities SET stage=? WHERE id=?",[$newStage,$id]);
        AdminHelper::auditLog($db,$session->get('user_id'),'deplacement','opportunities',$id,['stage'=>$old],['stage'=>$newStage],"Pipeline: $old → $newStage");
        // Return json if ajax
        if($req->isAjax() || strpos($_SERVER['HTTP_ACCEPT']??'','json')!==false){
            header('Content-Type: application/json'); echo json_encode(['success'=>true,'weighted'=>AdminHelper::weightedValue($opp['amount'],$opp['probability'])]); exit;
        }
        $session->setFlashMessage('success','Opportunité déplacée: '.$old.' → '.$newStage);
        return $this->redirect('/admin/crm/pipeline');
    }

    // ========== PIPELINE ==========
    public function pipeline()
    {
        $db=$this->getDb();
        $stages=['nouveau','qualifie','contact_etabli','rendez_vous','proposition','due_diligence','negociation','engagement','gagne'];
        $secondary=['perdu','suspendu'];
        $columns=[];
        foreach(array_merge($stages,$secondary) as $s){
            $opps=$this->safeFetchAll($db,"SELECT o.*, COALESCE(CONCAT(cc.first_name,' ',cc.last_name),'') as client, p.title as project_title, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as resp FROM opportunities o LEFT JOIN crm_contacts cc ON o.contact_id=cc.id LEFT JOIN projects p ON o.project_id=p.id LEFT JOIN users u ON o.responsible_id=u.id WHERE o.stage=? AND o.deleted_at IS NULL ORDER BY o.updated_at DESC",[$s]);
            $columns[$s]=$opps;
        }
        $weightedTotal=$this->safeFetchOne($db,"SELECT COALESCE(SUM(amount*probability/100),0) as t FROM opportunities WHERE deleted_at IS NULL AND stage NOT IN ('perdu','suspendu')",[],['t'=>0])['t'];
        return $this->view('admin/crm/pipeline', ['columns'=>$columns,'stages'=>$stages,'secondary'=>$secondary,'weightedTotal'=>$weightedTotal,'pageTitle'=>'Pipeline CRM']);
    }

    // ========== ACTIVITIES ==========
    public function activities()
    {
        $db=$this->getDb();
        $list=$this->safeFetchAll($db,"SELECT a.*, COALESCE(CONCAT(cc.first_name,' ',cc.last_name),'') as contact_name, o.name as opp_name, p.title as project_title, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as resp_name FROM activities a LEFT JOIN crm_contacts cc ON a.contact_id=cc.id LEFT JOIN opportunities o ON a.opportunity_id=o.id LEFT JOIN projects p ON a.project_id=p.id LEFT JOIN users u ON a.responsible_id=u.id ORDER BY a.activity_date DESC, a.activity_time DESC LIMIT 100");
        return $this->view('admin/crm/activities', ['activities'=>$list,'pageTitle'=>'Activités CRM']);
    }
    public function storeActivity(){
        $db=$this->getDb(); $req=$this->getRequest(); $session=$this->getSession();
        if(!$req->isPost()) return $this->redirect('/admin/crm/activities');
        $db->execute("INSERT INTO activities (type, contact_id, opportunity_id, project_id, responsible_id, activity_date, activity_time, summary, result, next_action) VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$req->getBodyParam('type','appel'),$req->getBodyParam('contact_id')?:null,$req->getBodyParam('opportunity_id')?:null,$req->getBodyParam('project_id')?:null,$req->getBodyParam('responsible_id')?:$session->get('user_id'),$req->getBodyParam('activity_date')?:date('Y-m-d'),$req->getBodyParam('activity_time')?:null,$req->getBodyParam('summary'),$req->getBodyParam('result'),$req->getBodyParam('next_action')]);
        $session->setFlashMessage('success','Activité enregistrée');
        return $this->redirect('/admin/crm/activities');
    }

    // ========== TASKS ==========
    public function tasks()
    {
        $db=$this->getDb(); $req=$this->getRequest();
        $status=$req->getParam('status'); $priority=$req->getParam('priority');
        $sql="SELECT t.*, COALESCE(CONCAT(u.first_name,' ',u.last_name),u.name) as resp_name, o.name as opp_name, p.title as project_title FROM tasks t LEFT JOIN users u ON t.responsible_id=u.id LEFT JOIN opportunities o ON t.opportunity_id=o.id LEFT JOIN projects p ON t.project_id=p.id WHERE t.deleted_at IS NULL";
        $params=[];
        if($status){ $sql.=" AND t.status=?"; $params[]=$status;}
        if($priority){ $sql.=" AND t.priority=?"; $params[]=$priority;}
        $sql.=" ORDER BY t.due_date ASC, FIELD(t.priority,'urgente','haute','normale','faible') LIMIT 100";
        $tasks=$this->safeFetchAll($db,$sql,$params);
        $users=$this->safeFetchAll($db,"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name FROM users LIMIT 100");
        return $this->view('admin/crm/tasks', ['tasks'=>$tasks,'users'=>$users,'pageTitle'=>'Tâches & Relances']);
    }
    public function createTask(){
        $db=$this->getDb();
        $users=$this->safeFetchAll($db,"SELECT id, COALESCE(CONCAT(first_name,' ',last_name),name) as name FROM users LIMIT 100");
        $contacts=$this->safeFetchAll($db,"SELECT id, CONCAT(first_name,' ',last_name) as name FROM crm_contacts LIMIT 100");
        $projects=$this->safeFetchAll($db,"SELECT id, title FROM projects LIMIT 100");
        $opps=$this->safeFetchAll($db,"SELECT id, name FROM opportunities LIMIT 100");
        return $this->view('admin/crm/task-form', ['task'=>null,'users'=>$users,'contacts'=>$contacts,'projects'=>$projects,'opportunities'=>$opps,'pageTitle'=>'Nouvelle tâche']);
    }
    public function storeTask(){
        $db=$this->getDb(); $req=$this->getRequest(); $session=$this->getSession();
        $title=trim($req->getBodyParam('title'));
        if(!$title){ $session->setFlashMessage('error','Titre requis'); return $this->redirect('/admin/crm/tasks/create');}
        $ref=AdminHelper::generateReference($db,'TASK');
        $db->execute("INSERT INTO tasks (reference, title, description, responsible_id, contact_id, project_id, opportunity_id, priority, due_date, due_time, status) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            [$ref,$title,$req->getBodyParam('description'),$req->getBodyParam('responsible_id')?:$session->get('user_id'),$req->getBodyParam('contact_id')?:null,$req->getBodyParam('project_id')?:null,$req->getBodyParam('opportunity_id')?:null,$req->getBodyParam('priority','normale'),$req->getBodyParam('due_date')?:date('Y-m-d'),$req->getBodyParam('due_time'),$req->getBodyParam('status','a_faire')]);
        $session->setFlashMessage('success','Tâche créée: '.$ref);
        return $this->redirect('/admin/crm/tasks');
    }
    public function updateTaskStatus($id){
        $db=$this->getDb(); $status=$this->getRequest()->getParam('status');
        if($status) $db->execute("UPDATE tasks SET status=? WHERE id=?",[$status,$id]);
        $this->getSession()->setFlashMessage('success','Statut tâche mis à jour');
        return $this->redirect('/admin/crm/tasks');
    }
}
