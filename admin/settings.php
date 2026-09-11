<?php
require dirname(__DIR__).'/includes/bootstrap.php'; require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){require_csrf();set_flag(SETTINGS_ROOT,'registration_disabled',isset($_POST['registration_disabled']));set_flag(SETTINGS_ROOT,'maintenance_mode',isset($_POST['maintenance_mode']));}
page_top('Admin — Settings'); ?>
<form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>">
<label><input type="checkbox" name="registration_disabled" <?=has_flag(SETTINGS_ROOT,'registration_disabled')?'checked':''?>> registration_disabled/ exists</label>
<label><input type="checkbox" name="maintenance_mode" <?=has_flag(SETTINGS_ROOT,'maintenance_mode')?'checked':''?>> maintenance_mode/ exists</label>
<button>mkdir/rmdir settings</button></form><?php page_bottom(); ?>
