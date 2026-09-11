<?php
require __DIR__.'/includes/bootstrap.php'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){ try{require_csrf(); register_user($_POST['username']??'',$_POST['password']??''); redirect('/login.php');}catch(Throwable $e){$error=$e->getMessage();}}
page_top('Register'); if($error) echo '<p class="error">'.e($error).'</p>'; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>">
<label>Username<input name="username" required></label>
<label>Password<input name="password" type="password" required></label>
<p><small>Intentionally awful: password must be 6–64 letters/numbers/_/- because it is stored literally in a directory name.</small></p>
<button>Create folder-person</button></form><?php page_bottom(); ?>
