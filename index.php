<?php require 'config.php'; require 'layout.php';
if(me()){header('Location: '.me()['role'].'.php');exit;}
head('Сомонаи асосӣ');?>
<section class="hero"><div class="car">🚗</div>
<h1>Мактаби ронандагӣ <em>дар як ҷо</em></h1>
<p>Гурӯҳҳо, дарсҳои назариявӣ, машқи амалӣ ва имтиҳонҳо — бо санҷиши ҳушманди вақт ва ҳисоби соатҳо.</p>
<div><a class="btn" href="login.php">Ворид шудан →</a></div></section>
<div class="grid">
<div class="card"><h2>🛡️ Маъмур</h2><p class="mut">Идораи гурӯҳҳо, инструкторҳо, мошинҳо ва қабули хонандагон.</p></div>
<div class="card"><h2>🧭 Инструктор</h2><p class="mut">Ҷадвали кории худ ва қайди машқҳои гузаронидашуда.</p></div>
<div class="card"><h2>🎓 Хонанда</h2><p class="mut">Сабт ба машқ, ҷадвал, соатҳо ва натиҷаҳои имтиҳон.</p></div></div>
<?php foot();
