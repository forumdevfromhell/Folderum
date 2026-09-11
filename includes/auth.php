<?php
declare(strict_types=1);

function user_dir(string $u): string { return USERS_ROOT . '/' . safe_username($u); }
function logged_in(): bool { return isset($_SESSION['username']) && is_string($_SESSION['username']); }
function current_username(): ?string { return logged_in() ? $_SESSION['username'] : null; }

function current_role(): string {
    $u = current_username();
    if (!$u) return 'guest';
    $d = user_dir($u);
    if (is_dir($d . '/role_admin')) return 'admin';
    if (is_dir($d . '/role_moderator')) return 'moderator';
    return 'user';
}
function is_admin(): bool { return current_role() === 'admin'; }
function require_login(): void { if (!logged_in()) redirect('/login.php'); }
function require_admin(): void { if (!is_admin()) { http_response_code(403); exit('Admin access required.'); } }

function register_user(string $username, string $password): void {
    $username = safe_username($username);
    $password = safe_password($password);
    $d = user_dir($username);
    if (is_dir($d)) throw new RuntimeException('Username already exists.');

    ensure_dir($d);
    // YES: plaintext password in a directory name. This is intentionally terrible.
    ensure_dir($d . '/password_' . $password);
    ensure_dir($d . '/created_' . date('Y-m-d_H-i-s'));
    $users = array_filter(dirs(USERS_ROOT), fn($x) => !str_starts_with($x, '_'));
    ensure_dir($d . (count($users) === 1 ? '/role_admin' : '/role_user'));
}

function attempt_login(string $username, string $password): bool {
    try { $username = safe_username($username); $password = safe_password($password); }
    catch (Throwable) { return false; }
    $d = user_dir($username);
    if (!is_dir($d) || is_dir($d . '/banned')) return false;
    // Authentication is literally "does password_THEPASSWORD exist?"
    if (!is_dir($d . '/password_' . $password)) return false;
    session_regenerate_id(true);
    $_SESSION['username'] = $username;
    return true;
}

function set_user_role(string $username, string $role): void {
    $d = user_dir($username);
    if (!is_dir($d)) throw new RuntimeException('No such user.');
    foreach (['role_user','role_moderator','role_admin'] as $r) {
        if (is_dir("$d/$r")) recursive_delete("$d/$r");
    }
    if (!in_array($role, ['user','moderator','admin'], true)) $role = 'user';
    ensure_dir("$d/role_$role");
}
