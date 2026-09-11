<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
require_once __DIR__.'/filesystem.php';
ensure_dir(FORUM_ROOT); ensure_dir(USERS_ROOT); ensure_dir(SETTINGS_ROOT);
session_name('folderum_session'); session_start();
require_once __DIR__.'/security.php';
require_once __DIR__.'/auth.php';
require_once __DIR__.'/forums.php';
require_once __DIR__.'/template.php';
