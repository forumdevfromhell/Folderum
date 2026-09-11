<?php
require dirname(__DIR__).'/includes/bootstrap.php'; require_admin(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){require_csrf();try{create_category($_POST['parent']??'',$_POST['name']??'');$msg='Category directory created.';}catch(Throwable $e){$msg=$e->getMessage();}}
function render_tree(string $p='',int $depth=0):void{foreach(category_children($p) as $c){$x=trim("$p/$c",'/');echo '<div class="card" style="margin-left:'.($depth*20).'px">📁 '.e($x).'</div>';render_tree($x,$depth+1);}}
page_top('Admin — Forums'); if($msg)echo '<p class="notice">'.e($msg).'</p>'; render_tree(); ?>
<h2>Create category</h2><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><label>Parent path (blank = root)<input name="parent"></label><label>Folder name<input name="name" required></label><button>mkdir category</button></form><?php page_bottom(); ?>
