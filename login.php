<?php
require __DIR__.'/includes/bootstrap.php'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){require_csrf(); if(attempt_login($_POST['username']??'',$_POST['password']??'')) redirect('/'); $error='Invalid login.';}
page_top('Login'); if($error) echo '<p class="error">'.e($error).'</p>'; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>">
<label>Username<input name="username" required></label><label>Password<input type="password" name="password" required></label>
<button>Compare against directory name</button></form><?php page_bottom(); ?>
