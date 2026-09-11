<?php
require dirname(__DIR__).'/includes/bootstrap.php'; require_admin(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){require_csrf();$u=safe_username($_POST['username']??'');$d=user_dir($u);if(isset($_POST['role']))set_user_role($u,$_POST['role']);if(isset($_POST['ban']))set_flag($d,'banned',$_POST['ban']==='1');$msg='Directories changed.';}
page_top('Admin — Users'); if($msg)echo '<p class="notice">'.e($msg).'</p>';
foreach(dirs(USERS_ROOT) as $u): $d=user_dir($u); ?>
<div class="card"><b><?=e($u)?></b><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="username" value="<?=e($u)?>">
<select name="role"><option value="user">user</option><option value="moderator">moderator</option><option value="admin">admin</option></select><button>Rename role folder</button></form>
<form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="username" value="<?=e($u)?>"><button name="ban" value="<?=is_dir("$d/banned")?'0':'1'?>"><?=is_dir("$d/banned")?'Unban (rmdir)':'Ban (mkdir)'?></button></form></div>
<?php endforeach; page_bottom(); ?>
