<?php ob_start();?>
<div class="page-header"><div><h1>Pipeline CRM — Kanban</h1><div class="breadcrumb">Admin / CRM / Pipeline • Drag & Drop historisé • Valeur pondérée: <?=number_format($weightedTotal??0,0,'',' ')?> $</div></div><a href="/admin/crm/opportunities" class="btn btn-white btn-sm">Liste</a></div>
<div style="display:flex;gap:10px;overflow-x:auto;padding-bottom:10px">
<?php foreach(($stages??[]) as $stage): $opps=$columns[$stage]??[]; ?>
<div style="min-width:260px;flex:0 0 260px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:10px">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px"><b style="font-size:12px;text-transform:uppercase;color:#0f172a"><?=htmlspecialchars(str_replace('_',' ', $stage))?></b><span class="pill" style="background:white"><?=count($opps)?></span></div>
<div style="display:flex;flex-direction:column;gap:8px;min-height:200px" data-stage="<?=htmlspecialchars($stage)?>" ondrop="drop(event)" ondragover="allowDrop(event)">
<?php foreach($opps as $o): $weighted= $o['amount']*$o['probability']/100; ?>
<div draggable="true" ondragstart="drag(event)" data-id="<?=$o['id']?>" style="background:white;border:1px solid #e2e8f0;border-radius:10px;padding:10px;cursor:grab">
<div style="font-size:13px;font-weight:800;color:#0f172a"><?=htmlspecialchars($o['name'])?></div>
<div style="font-size:12px" class="muted"><?=htmlspecialchars($o['client']??'')?> • <?=htmlspecialchars($o['project_title']??'')?></div>
<div style="font-size:12px;margin-top:6px"><b><?=number_format($o['amount'],0,'',' ')?> $</b> • <span class="muted"><?=$o['probability']?>%</span> • <b style="color:#0ea5e9"><?=number_format($weighted,0,'',' ')?> $</b></div>
<div style="font-size:11px" class="muted">Resp: <?=htmlspecialchars($o['resp']??'—')?> • <?=htmlspecialchars(substr($o['updated_at']??'',0,10))?></div>
<div style="margin-top:6px;display:flex;gap:4px">
<select onchange="moveStage(<?=$o['id']?>, this.value)" style="flex:1;padding:4px;border:1px solid #e2e8f0;border-radius:6px;font-size:11px">
<option value="">Déplacer…</option><?php foreach(($stages??[]) as $s): ?><option value="<?=$s?>"><?=htmlspecialchars($s)?></option><?php endforeach; ?><option value="perdu">Perdu</option><option value="suspendu">Suspendu</option>
</select>
</div>
</div>
<?php endforeach; if(empty($opps)) echo '<div class="muted" style="font-size:12px;padding:20px;text-align:center;border:1px dashed #e2e8f0;border-radius:8px">Aucune</div>'; ?>
</div>
</div>
<?php endforeach; ?>
</div>
<div style="display:flex;gap:10px;margin-top:10px;overflow-x:auto">
<?php foreach(($secondary??[]) as $s): $opps=$columns[$s]??[]; ?>
<div style="min-width:260px;flex:0 0 260px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:10px">
<b style="font-size:12px;text-transform:uppercase;color:#991b1b"><?=htmlspecialchars($s)?> (<?=count($opps)?>)</b>
<?php foreach($opps as $o): ?><div style="background:white;border:1px solid #fecaca;border-radius:8px;padding:8px;margin-top:6px;font-size:12px"><?=htmlspecialchars($o['name'])?> — <?=number_format($o['amount'],0,'',' ')?> $</div><?php endforeach; ?>
</div>
<?php endforeach; ?>
</div>
<script>
function allowDrop(ev){ ev.preventDefault(); }
function drag(ev){ ev.dataTransfer.setData("text", ev.target.dataset.id); }
function drop(ev){ ev.preventDefault(); const id=ev.dataTransfer.getData("text"); const stage=ev.currentTarget.dataset.stage; if(id&&stage) moveStage(id, stage); }
function moveStage(id, stage){ if(!stage) return; fetch('/admin/crm/opportunities/'+id+'/move?stage='+encodeURIComponent(stage)).then(()=>location.reload()); }
</script>
<?php $content=ob_get_clean(); require APP_PATH.'/Views/layouts/admin-layout.php'; ?>
