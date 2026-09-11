<?php
require __DIR__.'/includes/bootstrap.php'; $p=(string)($_GET['p']??''); $id=safe_id((string)($_GET['id']??'')); $d=thread_dir($p,$id); if(!is_dir($d)){http_response_code(404);exit('Thread missing.');}
if($_SERVER['REQUEST_METHOD']==='POST'){require_login();require_csrf();create_post($d,$_POST['body']??'',current_username()??'');redirect('/thread.php?p='.rawurlencode($p).'&id='.$id);}
page_top(dir_text($d,'title','Thread'));
foreach(list_posts($d) as $post): ?><article class="post"><b><?=e($post['author'])?></b> <small><?=e($post['created'])?></small><p><?=nl2br(e($post['body']))?></p>
<div>Score: <?=post_score($d,$post['id'])?> <?php if(logged_in()): ?>
<form class="inline" method="post" action="<?=BASE_URL?>/vote.php"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="p" value="<?=e($p)?>"><input type="hidden" name="thread" value="<?=$id?>"><input type="hidden" name="post" value="<?=$post['id']?>"><button name="vote" value="up">▲</button><button name="vote" value="down">▼</button></form><?php endif;?></div></article><?php endforeach;
if(logged_in()&&!has_flag($d,'locked')): ?><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><label>Reply<textarea name="body" required></textarea></label><button>mkdir reply</button></form><?php endif; page_bottom(); ?>
