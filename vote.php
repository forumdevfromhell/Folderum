<?php
require __DIR__.'/includes/bootstrap.php'; require_login(); require_csrf();
$p=(string)($_POST['p']??''); $thread=safe_id((string)($_POST['thread']??'')); $post=safe_id((string)($_POST['post']??''));
vote_post(thread_dir($p,$thread),$post,current_username()??'',(string)($_POST['vote']??''));
redirect('/thread.php?p='.rawurlencode($p).'&id='.$thread);
