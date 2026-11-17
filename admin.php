<?php require 'config.php'; require 'layout.php'; need('admin');
$L=['dash'=>'📊 Назорат','groups'=>'👥 Гурӯҳҳо','students'=>'🎓 Хонандагон','instructors'=>'🧭 Инструкторҳо','cars'=>'🚘 Мошинҳо','lessons'=>'📚 Назария','sessions'=>'🛣️ Машқҳо','exams'=>'📝 Имтиҳонҳо'];
$p=$_GET['p']??'dash';
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['a']??'';
 try{switch($a){
  case 'group':q("INSERT INTO `groups`(name,category,start_date,exam_date,required_hours) VALUES(?,?,?,?,?)",[$_POST['name'],$_POST['category'],$_POST['start_date'],$_POST['exam_date'],$_POST['required_hours']]);break;
  case 'student':$id=newuser('student');q("INSERT INTO students(user_id,group_id,birth_date) VALUES(?,?,?)",[$id,$_POST['group_id'],$_POST['birth_date']]);break;
  case 'instructor':$id=newuser('instructor');q("INSERT INTO instructors(user_id,experience_years) VALUES(?,?)",[$id,$_POST['exp']]);break;
  case 'car':q("INSERT INTO cars(plate,model,category) VALUES(?,?,?)",[$_POST['plate'],$_POST['model'],$_POST['category']]);break;
  case 'lesson':q("INSERT INTO lessons(group_id,topic,lesson_date,start_time,end_time,room,teacher) VALUES(?,?,?,?,?,?,?)",[$_POST['group_id'],$_POST['topic'],$_POST['lesson_date'],$_POST['start_time'],$_POST['end_time'],$_POST['room'],$_POST['teacher']]);break;
  case 'exam':q("INSERT INTO exams(student_id,exam_type,exam_date,score,passed,notes) VALUES(?,?,?,?,?,?)",[$_POST['student_id'],$_POST['exam_type'],$_POST['exam_date'],$_POST['score'],$_POST['score']>=60?1:0,$_POST['notes']]);break;
  case 'del':$m=['groups'=>"DELETE FROM `groups` WHERE id=?",'cars'=>"DELETE FROM cars WHERE id=?",'lessons'=>"DELETE FROM lessons WHERE id=?",'exams'=>"DELETE FROM exams WHERE id=?",'driving_sessions'=>"DELETE FROM driving_sessions WHERE id=?",
   'students'=>"DELETE u FROM users u JOIN students s ON s.user_id=u.id WHERE s.id=?",'instructors'=>"DELETE u FROM users u JOIN instructors i ON i.user_id=u.id WHERE i.id=?"];
   if(isset($m[$_POST['t2']]))q($m[$_POST['t2']],[$_POST['id']]);break;}
  flash('Анҷом шуд ✓');}catch(Exception $x){flash('Хато: '.$x->getMessage(),'err');}
 header("Location: admin.php?p=$p");exit;}
head('Маъмур',$L);
$G=array_column(rows("SELECT id,CONCAT(name,' (',category,')') n FROM `groups`"),'n','id');
function form($a,$fields){return '<form method="post" class="card grid" style="align-items:end">'.csrf().'<input type="hidden" name="a" value="'.$a.'">'.$fields.'<button class="btn">＋ Илова</button></form>';}
if($p==='dash'):?>
<h1>Панели назорат</h1><p class="mut" style="margin-bottom:20px">Ҳолати мактаби ронандагӣ имрӯз</p>
<div class="grid"><?php foreach(['Хонандагон'=>'students','Гурӯҳҳо'=>'`groups`','Инструкторҳо'=>'instructors','Мошинҳо'=>'cars'] as $l=>$t):?>
<div class="card stat"><div class="n" data-count="<?=val("SELECT COUNT(*) FROM $t")?>">0</div><div class="l"><?=$l?></div></div><?php endforeach;?></div>
<div class="card"><h2>Машқҳои наздик</h2><div class="tw"><table><tr><th>Вақт</th><th>Хонанда</th><th>Инструктор</th><th>Мошин</th></tr>
<?php foreach(rows("SELECT s.start_time,su.full_name sn,iu.full_name inn,c.model FROM driving_sessions d JOIN students s2 ON s2.id=d.student_id JOIN users su ON su.id=s2.user_id JOIN instructors i ON i.id=d.instructor_id JOIN users iu ON iu.id=i.user_id JOIN cars c ON c.id=d.car_id JOIN (SELECT id,start_time FROM driving_sessions) s ON s.id=d.id WHERE d.status='booked' AND d.start_time>NOW() ORDER BY d.start_time LIMIT 8") as $r):?>
<tr><td><?=dt($r['start_time'])?></td><td><?=e($r['sn'])?></td><td><?=e($r['inn'])?></td><td><?=e($r['model'])?></td></tr><?php endforeach;?></table></div></div>
<?php elseif($p==='groups'):?>
<h1>Гурӯҳҳо</h1><?=form('group',inp('name','Номи гурӯҳ').sel('category','Категория',['B'=>'B','C'=>'C']).inp('start_date','Оғози таълим','date').inp('exam_date','Санаи имтиҳон','date').inp('required_hours','Соати лозим','number','30','step="0.5"'))?>
<div class="card tw"><table><tr><th>Гурӯҳ</th><th>Кат.</th><th>Оғоз</th><th>Имтиҳон</th><th>Соат</th><th>Хонанда</th><th></th></tr>
<?php foreach(rows("SELECT g.*,(SELECT COUNT(*) FROM students WHERE group_id=g.id) n FROM `groups` g ORDER BY start_date DESC") as $r):?>
<tr><td><b><?=e($r['name'])?></b></td><td><span class="badge b-a"><?=$r['category']?></span></td><td><?=$r['start_date']?></td><td><?=$r['exam_date']?></td><td><?=$r['required_hours']?></td><td><?=$r['n']?></td><td><?=delbtn('groups',$r['id'])?></td></tr><?php endforeach;?></table></div>
<?php elseif($p==='students'):?>
<h1>Хонандагон</h1><p class="mut">Парол барои ҳисоби нав: 123456</p><?=form('student',inp('full_name','Ному насаб').inp('email','Почта','email').inp('phone','Телефон').sel('group_id','Гурӯҳ',$G).inp('birth_date','Санаи таваллуд','date'))?>
<div class="card tw"><table><tr><th>Ному насаб</th><th>Почта</th><th>Гурӯҳ</th><th>Соат</th><th></th></tr>
<?php foreach(rows("SELECT s.id,u.full_name,u.email,g.name,g.required_hours FROM students s JOIN users u ON u.id=s.user_id JOIN `groups` g ON g.id=s.group_id") as $r):?>
<tr><td><?=e($r['full_name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['name'])?></td><td><?=hours($r['id'],'done')?> / <?=$r['required_hours']?></td><td><?=delbtn('students',$r['id'])?></td></tr><?php endforeach;?></table></div>
<?php elseif($p==='instructors'):?>
<h1>Инструкторҳо</h1><?=form('instructor',inp('full_name','Ному насаб').inp('email','Почта','email').inp('phone','Телефон').inp('exp','Собиқаи корӣ (сол)','number','0'))?>
<div class="card tw"><table><tr><th>Ному насаб</th><th>Почта</th><th>Телефон</th><th>Собиқа</th><th></th></tr>
<?php foreach(rows("SELECT i.id,u.full_name,u.email,u.phone,i.experience_years FROM instructors i JOIN users u ON u.id=i.user_id") as $r):?>
<tr><td><?=e($r['full_name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['phone'])?></td><td><?=$r['experience_years']?> сол</td><td><?=delbtn('instructors',$r['id'])?></td></tr><?php endforeach;?></table></div>
<?php elseif($p==='cars'):?>
<h1>Мошинҳои таълимӣ</h1><?=form('car',inp('plate','Рақами давлатӣ').inp('model','Модел').sel('category','Категория',['B'=>'B','C'=>'C']))?>
<div class="card tw"><table><tr><th>Рақам</th><th>Модел</th><th>Кат.</th><th></th></tr>
<?php foreach(rows("SELECT * FROM cars") as $r):?><tr><td><?=e($r['plate'])?></td><td><?=e($r['model'])?></td><td><span class="badge b-b"><?=$r['category']?></span></td><td><?=delbtn('cars',$r['id'])?></td></tr><?php endforeach;?></table></div>
<?php elseif($p==='lessons'):?>
<h1>Ҷадвали дарсҳои назариявӣ</h1><?=form('lesson',sel('group_id','Гурӯҳ',$G).inp('topic','Мавзӯъ').inp('lesson_date','Сана','date').inp('start_time','Аз','time').inp('end_time','То','time').inp('room','Ҳуҷра').inp('teacher','Омӯзгор'))?>
<div class="card tw"><table><tr><th>Сана</th><th>Вақт</th><th>Гурӯҳ</th><th>Мавзӯъ</th><th>Ҳуҷра</th><th></th></tr>
<?php foreach(rows("SELECT l.*,g.name FROM lessons l JOIN `groups` g ON g.id=l.group_id ORDER BY lesson_date,start_time") as $r):?>
<tr><td><?=$r['lesson_date']?></td><td><?=substr($r['start_time'],0,5)?>–<?=substr($r['end_time'],0,5)?></td><td><?=e($r['name'])?></td><td><?=e($r['topic'])?></td><td><?=e($r['room'])?></td><td><?=delbtn('lessons',$r['id'])?></td></tr><?php endforeach;?></table></div>
<?php elseif($p==='sessions'):?>
<h1>Ҳамаи машқҳои ронандагӣ</h1>
<div class="card tw"><table><tr><th>Вақт</th><th>Инструктор</th><th>Мошин</th><th>Хонанда</th><th>Ҳолат</th><th></th></tr>
<?php foreach(rows("SELECT d.*,iu.full_name inn,c.model,su.full_name sn FROM driving_sessions d JOIN instructors i ON i.id=d.instructor_id JOIN users iu ON iu.id=i.user_id JOIN cars c ON c.id=d.car_id LEFT JOIN students s ON s.id=d.student_id LEFT JOIN users su ON su.id=s.user_id ORDER BY d.start_time DESC LIMIT 60") as $r):?>
<tr><td><?=dt($r['start_time'])?></td><td><?=e($r['inn'])?></td><td><?=e($r['model'])?></td><td><?=e($r['sn']?:'—')?></td><td><?=badge($r['status'])?></td><td><?=delbtn('driving_sessions',$r['id'])?></td></tr><?php endforeach;?></table></div>
<?php elseif($p==='exams'):?>
<h1>Имтиҳонҳои дохилӣ</h1><p class="mut">Ҳадди гузариш: 60 хол</p>
<?=form('exam',sel('student_id','Хонанда',array_column(rows("SELECT s.id,u.full_name n FROM students s JOIN users u ON u.id=s.user_id"),'n','id')).sel('exam_type','Намуд',['theory'=>'Назария','practice'=>'Амалия']).inp('exam_date','Сана','date').inp('score','Хол (0–100)','number','','min=0 max=100').inp('notes','Шарҳ'))?>
<div class="card tw"><table><tr><th>Хонанда</th><th>Намуд</th><th>Сана</th><th>Хол</th><th>Натиҷа</th><th></th></tr>
<?php foreach(rows("SELECT x.*,u.full_name FROM exams x JOIN students s ON s.id=x.student_id JOIN users u ON u.id=s.user_id ORDER BY exam_date DESC") as $r):?>
<tr><td><?=e($r['full_name'])?></td><td><?=$r['exam_type']==='theory'?'Назария':'Амалия'?></td><td><?=$r['exam_date']?></td><td><?=$r['score']?></td><td><?=$r['passed']?'<span class="badge b-ok">Гузашт</span>':'<span class="badge b-err">Нагузашт</span>'?></td><td><?=delbtn('exams',$r['id'])?></td></tr><?php endforeach;?></table></div>
<?php endif; foot();
