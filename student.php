<?php require 'config.php'; require 'layout.php'; need('student');
$st=row("SELECT s.id,s.group_id,g.name,g.category,g.exam_date,g.required_hours FROM students s JOIN `groups` g ON g.id=s.group_id WHERE s.user_id=?",[me()['id']]);$sid=$st['id'];
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['a'];$id=(int)$_POST['id'];
 if($a==='book'){$d=row("SELECT * FROM driving_sessions WHERE id=? AND status='free' AND start_time>NOW()",[$id]);
  if(!$d)flash('Ин вақт дигар холӣ нест','err');
  elseif(student_conflict($sid,$d['start_time'],$d['end_time']))flash('Шумо дар ин вақт машқи дигар доред!','err');
  else{$n=q("UPDATE driving_sessions SET student_id=?,status='booked' WHERE id=? AND status='free'",[$sid,$id])->rowCount();flash($n?'Шумо сабт шудед ✓':'Ин вақт ҳоло гирифта шуд',$n?'ok':'err');}}
 if($a==='unbook'){q("UPDATE driving_sessions SET student_id=NULL,status='free' WHERE id=? AND student_id=? AND status='booked' AND start_time>NOW()",[$id,$sid]);flash('Сабт бекор шуд');}
 header('Location: student.php?p='.($_GET['p']??'home'));exit;}
$L=['home'=>'🏠 Хонаи ман','book'=>'🛣️ Сабт ба машқ','theory'=>'📚 Назария','exams'=>'📝 Натиҷаҳо'];$p=$_GET['p']??'home';
head('Хонанда',$L);
if($p==='home'):$done=hours($sid,'done');$plan=hours($sid,'booked');$pct=min(1,$done/max(1,$st['required_hours']));?>
<h1>Салом, <?=e(explode(' ',me()['full_name'])[0])?>! 👋</h1><p class="mut" style="margin-bottom:20px">Гурӯҳ <?=e($st['name'])?> · категория <?=$st['category']?> · имтиҳон: <?=$st['exam_date']?></p>
<?php foreach(alerts($sid) as $m):?><div class="alert <?=$m[0]?>"><?=e($m[1])?></div><?php endforeach;?>
<div class="card split"><div class="ring" style="--o:<?=327*(1-$pct)?>"><svg width="140" height="140" viewBox="0 0 120 120"><defs><linearGradient id="g"><stop offset="0" stop-color="#7c5cff"/><stop offset="1" stop-color="#00d4ff"/></linearGradient></defs><circle class="bg" cx="60" cy="60" r="52"/><circle class="fg" cx="60" cy="60" r="52"/></svg><b><?=round($pct*100)?>%</b></div>
<div><h2>Соатҳои машқ</h2><p>Гузашт: <b><?=$done?></b> / <?=$st['required_hours']?> соат</p><p class="mut">Сабтшуда (оянда): <?=$plan?> соат</p></div></div>
<div class="card tw"><h2>Машқҳои ман</h2><table><tr><th>Вақт</th><th>Инструктор</th><th>Мошин</th><th>Ҳолат</th><th>Шарҳ</th><th></th></tr>
<?php foreach(rows("SELECT d.*,u.full_name inn,c.model FROM driving_sessions d JOIN instructors i ON i.id=d.instructor_id JOIN users u ON u.id=i.user_id JOIN cars c ON c.id=d.car_id WHERE d.student_id=? ORDER BY d.start_time DESC",[$sid]) as $r):?>
<tr><td><?=dt($r['start_time'])?></td><td><?=e($r['inn'])?></td><td><?=e($r['model'])?></td><td><?=badge($r['status'])?></td><td class="mut"><?=e($r['comment'])?></td>
<td><?php if($r['status']==='booked'&&strtotime($r['start_time'])>time()):?><form method="post"><?=csrf()?><input type="hidden" name="a" value="unbook"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn sm danger">Бекор</button></form><?php endif;?></td></tr><?php endforeach;?></table></div>
<?php elseif($p==='book'):?>
<h1>Вақтҳои холии инструкторҳо</h1><p class="mut" style="margin-bottom:20px">Як клик — ва шумо сабт шудед.</p>
<div class="grid"><?php foreach(rows("SELECT d.*,u.full_name inn,c.model,c.plate FROM driving_sessions d JOIN instructors i ON i.id=d.instructor_id JOIN users u ON u.id=i.user_id JOIN cars c ON c.id=d.car_id WHERE d.status='free' AND d.start_time>NOW() AND c.category=? ORDER BY d.start_time",[$st['category']]) as $r):?>
<div class="card"><h2>🕒 <?=dt($r['start_time'])?></h2><p>🧭 <?=e($r['inn'])?></p><p class="mut">🚘 <?=e($r['model'])?> · <?=e($r['plate'])?></p><p class="mut">Давом: <?=(strtotime($r['end_time'])-strtotime($r['start_time']))/60?> дақ.</p>
<form method="post" style="margin-top:12px"><?=csrf()?><input type="hidden" name="a" value="book"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn sm">Сабт шудан</button></form></div>
<?php endforeach;?></div>
<?php elseif($p==='theory'):?>
<h1>Ҷадвали дарсҳои назариявӣ</h1><div class="card tw"><table><tr><th>Сана</th><th>Вақт</th><th>Мавзӯъ</th><th>Ҳуҷра</th><th>Омӯзгор</th></tr>
<?php foreach(rows("SELECT * FROM lessons WHERE group_id=? AND lesson_date>=CURDATE() ORDER BY lesson_date,start_time",[$st['group_id']]) as $r):?>
<tr><td><?=$r['lesson_date']?></td><td><?=substr($r['start_time'],0,5)?>–<?=substr($r['end_time'],0,5)?></td><td><?=e($r['topic'])?></td><td><?=e($r['room'])?></td><td><?=e($r['teacher'])?></td></tr><?php endforeach;?></table></div>
<?php else:?>
<h1>Натиҷаҳои имтиҳон</h1><div class="card tw"><table><tr><th>Намуд</th><th>Сана</th><th>Хол</th><th>Натиҷа</th><th>Шарҳ</th></tr>
<?php foreach(rows("SELECT * FROM exams WHERE student_id=? ORDER BY exam_date DESC",[$sid]) as $r):?>
<tr><td><?=$r['exam_type']==='theory'?'Назария':'Амалия'?></td><td><?=$r['exam_date']?></td><td><b><?=$r['score']?></b></td><td><?=$r['passed']?'<span class="badge b-ok">Гузашт</span>':'<span class="badge b-err">Нагузашт</span>'?></td><td class="mut"><?=e($r['notes'])?></td></tr><?php endforeach;?></table></div>
<?php endif; foot();
