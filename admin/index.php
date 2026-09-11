<?php require dirname(__DIR__).'/includes/bootstrap.php'; require_admin(); page_top('Admin'); ?>
<div class="card"><a href="<?=BASE_URL?>/admin/users.php">Users</a></div><div class="card"><a href="<?=BASE_URL?>/admin/forums.php">Forums</a></div><div class="card"><a href="<?=BASE_URL?>/admin/settings.php">Settings</a></div>
<?php page_bottom(); ?>
