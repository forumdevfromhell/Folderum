<?php
declare(strict_types=1);
function page_top(string $title): void { ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width">
<title><?=e($title)?> — Folderum</title><link rel="stylesheet" href="<?=BASE_URL?>/assets/style.css"></head><body>
<header><a href="<?=BASE_URL?>/"><b>📁 Folderum</b></a><nav>
<?php if (logged_in()): ?>
<a href="<?=BASE_URL?>/profile.php?u=<?=urlencode(current_username()??'')?>"><?=e(current_username()??'')?></a>
<?php if (is_admin()): ?><a href="<?=BASE_URL?>/admin/">Admin</a><?php endif; ?>
<a href="<?=BASE_URL?>/logout.php">Logout</a>
<?php else: ?><a href="<?=BASE_URL?>/login.php">Login</a><a href="<?=BASE_URL?>/register.php">Register</a><?php endif; ?>
</nav></header><main><h1><?=e($title)?></h1>
<?php }
function page_bottom(): void { ?></main><footer>There is no database. There are barely files.</footer></body></html><?php }
