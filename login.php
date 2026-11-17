<?php require 'config.php'; require 'layout.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $u=row("SELECT * FROM users WHERE email=?",[trim($_POST['email'])]);
 $p=$_POST['password'];
 if($u&&$u['password']==='RESET'&&$p==='123456'){$u['password']=password_hash($p,PASSWORD_DEFAULT);q("UPDATE users SET password=? WHERE id=?",[$u['password'],$u['id']]);}
 if($u&&password_verify($p,$u['password'])){session_regenerate_id(true);$_SESSION['u']=$u;header('Location: '.$u['role'].'.php');exit;}
 $err='Почта ё парол нодуруст аст.';}
head('Ворид');?>
<div class="login card"><h1>Хуш омадед 👋</h1><p class="mut" style="margin-bottom:18px">Ба ҳисоби худ ворид шавед</p>
<?php if($err):?><div class="alert err"><?=e($err)?></div><?php endif;?>
<form method="post"><?=csrf()?><?=inp('email','Почтаи электронӣ','email')?><?=inp('password','Парол','password')?>
<button class="btn">Ворид шудан</button></form>
<p class="mut" style="margin-top:16px;font-size:12px">Намуна: admin@drive.tj · ins1@drive.tj · st1@drive.tj / 123456</p></div>
<?php foot();
