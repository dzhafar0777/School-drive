<?php
session_start();
date_default_timezone_set('Asia/Dushanbe');
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
try{$db=new PDO('mysql:host=localhost;dbname=driving_school;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
catch(Exception $x){die('Хатои пойгоҳи додаҳо: '.e($x->getMessage()));}
function q($sql,$p=[]){global $db;$s=$db->prepare($sql);$s->execute($p);return $s;}
function rows($sql,$p=[]){return q($sql,$p)->fetchAll();}
function row($sql,$p=[]){return q($sql,$p)->fetch();}
function val($sql,$p=[]){return q($sql,$p)->fetchColumn();}
function me(){return $_SESSION['u']??null;}
function need($r){if(!me()||me()['role']!==$r){header('Location: login.php');exit;}}
function flash($m,$t='ok'){$_SESSION['f']=[$m,$t];}
if(empty($_SESSION['t']))$_SESSION['t']=bin2hex(random_bytes(16));
function csrf(){return '<input type="hidden" name="t" value="'.$_SESSION['t'].'">';}
if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals($_SESSION['t'],$_POST['t']??'')){http_response_code(419);die('CSRF');}

/* Санҷиши вақт: инструктор ва мошин дар як вақт ду машқ дошта наметавонанд */
function conflict($ins,$car,$s,$e,$ex=0){
 return val("SELECT COUNT(*) FROM driving_sessions WHERE status<>'cancelled' AND id<>? AND (instructor_id=? OR car_id=?) AND start_time<? AND end_time>?",[$ex,$ins,$car,$e,$s])>0;}
function student_conflict($sid,$s,$e){
 return val("SELECT COUNT(*) FROM driving_sessions WHERE student_id=? AND status IN('booked','done') AND start_time<? AND end_time>?",[$sid,$e,$s])>0;}

/* Ҳисоби соатҳо */
function hours($sid,$st){return round((float)val("SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE,start_time,end_time)),0)/60 FROM driving_sessions WHERE student_id=? AND status=?",[$sid,$st]),1);}
function alerts($sid){
 $g=row("SELECT g.* FROM students s JOIN `groups` g ON g.id=s.group_id WHERE s.id=?",[$sid]);
 if(!$g)return [];
 $done=hours($sid,'done');$plan=hours($sid,'booked');$need=$g['required_hours']-$done;
 $days=(int)ceil((strtotime($g['exam_date'])-time())/86400);$o=[];
 if($need<=0)$o[]=['ok','Меъёри соатҳо пурра шуд. Шумо ба имтиҳон омодаед!'];
 elseif($days<0)$o[]=['err','Санаи имтиҳон гузашт, аммо '.$need.' соат намерасад.'];
 elseif($need-$plan>0&&$days<=14)$o[]=['warn','Диққат! То имтиҳон '.$days.' рӯз монд. Ба шумо ҳанӯз '.($need-$plan).' соат намерасад (гузашт: '.$done.', сабтшуда: '.$plan.'). Зудтар сабт шавед.'];
 return $o;}
function newuser($role){
 return (function()use($role){global $db;
  q("INSERT INTO users(full_name,email,password,role,phone) VALUES(?,?,?,?,?)",[$_POST['full_name'],$_POST['email'],password_hash('123456',PASSWORD_DEFAULT),$role,$_POST['phone']??'']);
  return $db->lastInsertId();})();}
