<?php
function inp($n,$l,$t='text',$v='',$ex=''){return '<label class="f"><span>'.$l.'</span><input name="'.$n.'" type="'.$t.'" value="'.e($v).'" '.$ex.' required></label>';}
function sel($n,$l,$o){$h='<label class="f"><span>'.$l.'</span><select name="'.$n.'" required>';foreach($o as $k=>$v)$h.='<option value="'.e($k).'">'.e($v).'</option>';return $h.'</select></label>';}
function dt($s){return date('d.m.Y H:i',strtotime($s));}
function badge($s){$m=['free'=>['Холӣ','b-ok'],'booked'=>['Сабтшуда','b-a'],'done'=>['Гузашт','b-b'],'cancelled'=>['Бекор','b-err']];return '<span class="badge '.$m[$s][1].'">'.$m[$s][0].'</span>';}
function delbtn($t,$id){return '<form method="post" onsubmit="return confirm(\'Нест кунем?\')" style="display:inline">'.csrf().'<input type="hidden" name="a" value="del"><input type="hidden" name="t2" value="'.$t.'"><input type="hidden" name="id" value="'.$id.'"><button class="btn sm danger">✕</button></form>';}
function head($title,$links=[]){
 $u=me();$f=$_SESSION['f']??null;unset($_SESSION['f']);$cur=$_GET['p']??array_key_first($links);$file=basename($_SERVER['PHP_SELF']);
 $icons=['admin'=>'🛡️','instructor'=>'🧭','student'=>'🎓'];?>
<!doctype html><html lang="tg"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> · DriveSchool</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="aurora"><i></i><i></i><i></i></div>
<?php if($u):?><header class="top"><a class="logo" href="index.php"><span>🚗</span>Drive<b>School</b></a>
<nav class="nav"><?php foreach($links as $k=>$v):?><a class="<?=$cur===$k?'on':''?>" href="<?=$file?>?p=<?=$k?>"><?=$v?></a><?php endforeach;?></nav>
<div class="user"><span><?=$icons[$u['role']]?> <?=e($u['full_name'])?></span><a class="btn sm ghost" href="logout.php">Баромад</a></div></header><?php endif;?>
<main class="wrap"><?php if($f):?><div class="toast <?=$f[1]?>"><?=e($f[0])?></div><?php endif;
}
function foot(){echo '</main><script src="assets/app.js"></script></body></html>';}
