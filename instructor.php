<?php require 'config.php'; require 'layout.php'; need('instructor');
$iid=val("SELECT id FROM instructors WHERE user_id=?",[me()['id']]);
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['a'];
 if($a==='slot'){$s=strtotime($_POST['start']);$e=$s+(int)$_POST['dur']*60;$car=(int)$_POST['car_id'];
  if($s<time())flash('Вақти гузашта интихоб нашавад','err');
  elseif(conflict($iid,$car,date('Y-m-d H:i:s',$s),date('Y-m-d H:i:s',$e)))flash('Инструктор ё мошин дар ин вақт аллакай банд аст!','err');
  else{q("INSERT INTO driving_sessions(instructor_id,car_id,start_time,end_time) VALUES(?,?,?,?)",[$iid,$car,date('Y-m-d H:i:s',$s),date('Y-m-d H:i:s',$e)]);flash('Вақти холӣ илова шуд ✓');}}
 if($a==='done'){q("UPDATE driving_sessions SET status='done',comment=? WHERE id=? AND instructor_id=? AND status='booked'",[$_POST['comment'],$_POST['id'],$iid]);flash('Машқ қайд шуд ✓');}
 if($a==='cancel'){q("UPDATE driving_sessions SET status='cancelled' WHERE id=? AND instructor_id=? AND status IN('free','booked')",[$_POST['id'],$iid]);flash('Бекор шуд');}
 header('Location: instructor.php');exit;}
head('Инструктор',['x'=>'🗓️ Ҷадвали ман']);
$cars=array_column(rows("SELECT id,CONCAT(model,' · ',plate) n FROM cars"),'n','id');?>
<h1>Ҷадвали кории ман</h1><p class="mut" style="margin-bottom:20px">Вақти холӣ илова кунед — хонандагон худашон сабт мешаванд.</p>
<form method="post" class="card grid" style="align-items:end"><?=csrf()?><input type="hidden" name="a" value="slot">
<?=inp('start','Оғоз','datetime-local')?><?=sel('dur','Давомнокӣ',['60'=>'60 дақиқа','90'=>'90 дақиқа','120'=>'120 дақиқа'])?><?=sel('car_id','Мошин',$cars)?><button class="btn">＋ Вақти холӣ</button></form>
<div class="card tw"><h2>Машқҳо</h2><table><tr><th>Вақт</th><th>Мошин</th><th>Хонанда</th><th>Ҳолат</th><th>Амал</th></tr>
<?php foreach(rows("SELECT d.*,c.model,u.full_name sn FROM driving_sessions d JOIN cars c ON c.id=d.car_id LEFT JOIN students s ON s.id=d.student_id LEFT JOIN users u ON u.id=s.user_id WHERE d.instructor_id=? AND d.status<>'cancelled' ORDER BY d.start_time DESC LIMIT 50",[$iid]) as $r):?>
<tr><td><?=dt($r['start_time'])?></td><td><?=e($r['model'])?></td><td><?=e($r['sn']?:'—')?></td><td><?=badge($r['status'])?></td><td>
<?php if($r['status']==='booked'):?><form method="post" style="display:flex;gap:6px"><?=csrf()?><input type="hidden" name="a" value="done"><input type="hidden" name="id" value="<?=$r['id']?>"><input name="comment" placeholder="Шарҳи инструктор" required style="min-width:170px"><button class="btn sm">✓ Гузашт</button></form>
<?php elseif($r['status']==='done'):?><span class="mut"><?=e($r['comment'])?></span><?php endif;
if(in_array($r['status'],['free','booked'])):?><form method="post" style="display:inline"><?=csrf()?><input type="hidden" name="a" value="cancel"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn sm danger">Бекор</button></form><?php endif;?></td></tr><?php endforeach;?></table></div>
<?php foot();
