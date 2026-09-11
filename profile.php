<?php
require __DIR__.'/includes/bootstrap.php'; $u=safe_username((string)($_GET['u']??current_username()??'')); $d=user_dir($u); if(!is_dir($d)){http_response_code(404);exit('User missing.');}
page_top($u); ?><div class="card"><p>Role: <?=e(is_dir("$d/role_admin")?'admin':(is_dir("$d/role_moderator")?'moderator':'user'))?></p><p>Banned: <?=is_dir("$d/banned")?'yes':'no'?></p><p>Password: <i>literally visible in the folder tree, because this edition has abandoned reason.</i></p></div><?php page_bottom(); ?>
