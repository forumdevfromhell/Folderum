<?php
require __DIR__.'/includes/bootstrap.php'; $p=(string)($_GET['p']??''); $d=forum_path($p); if(!is_dir($d)){http_response_code(404);exit('Forum missing.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){require_login(); try{require_csrf(); create_thread($p,$_POST['title']??'',$_POST['body']??'',current_username()??''); redirect('/forum.php?p='.rawurlencode($p));}catch(Throwable $e){$error=$e->getMessage();}}
page_top($p?:'Forum');
foreach(category_children($p) as $c){$child=trim($p.'/'.$c,'/'); echo '<div class="card">📁 <a href="'.BASE_URL.'/forum.php?p='.urlencode($child).'">'.e($c).'</a></div>';}
echo '<h2>Threads</h2>'; foreach(list_threads($p) as $t){echo '<div class="card">📂 <a href="'.BASE_URL.'/thread.php?p='.urlencode($p).'&id='.$t['id'].'">'.e($t['title']).'</a><small> by '.e($t['author']).'</small></div>';}
if(logged_in()): ?><h2>New thread</h2><?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><label>Title<input name="title" required></label><label>Post<textarea name="body" required></textarea></label><button>mkdir thread</button></form>
<?php endif; page_bottom(); ?>
