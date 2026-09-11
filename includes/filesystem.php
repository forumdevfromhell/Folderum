<?php
declare(strict_types=1);

/*
 * Folderum: Everything Is A Folder Edition
 *
 * Application data is represented ONLY by directories and directory names.
 * No .txt, JSON, SQLite, or other data files are created by Folderum.
 */

function ensure_dir(string $path): void {
    if (!is_dir($path) && !mkdir($path, 0750, true) && !is_dir($path)) {
        throw new RuntimeException("Could not create directory: $path");
    }
}

function safe_username(string $value): string {
    $value = trim($value);
    if (!preg_match('/^[A-Za-z0-9_]{3,32}$/', $value)) {
        throw new InvalidArgumentException('Username: 3-32 letters, numbers, underscores.');
    }
    return $value;
}

function safe_password(string $value): string {
    // Intentionally ridiculous: the plaintext password becomes part of a directory name.
    if (!preg_match('/^[A-Za-z0-9_-]{6,64}$/', $value)) {
        throw new InvalidArgumentException('Password: 6-64 letters, numbers, _ or - only.');
    }
    return $value;
}

function safe_slug(string $value): string {
    $value = trim($value);
    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9 _.-]{0,79}$/', $value) ||
        $value === '.' || $value === '..' || str_starts_with($value, '_')) {
        throw new InvalidArgumentException('Invalid folder/category name.');
    }
    return $value;
}

function safe_id(string $value): string {
    if (!preg_match('/^[a-f0-9]{32}$/', $value)) {
        throw new InvalidArgumentException('Invalid ID.');
    }
    return $value;
}

function b64u(string $s): string {
    return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}
function unb64u(string $s): string {
    $pad = strlen($s) % 4;
    if ($pad) $s .= str_repeat('=', 4 - $pad);
    $v = base64_decode(strtr($s, '-_', '+/'), true);
    return $v === false ? '' : $v;
}

function dirs(string $path): array {
    if (!is_dir($path)) return [];
    $out = [];
    foreach (new DirectoryIterator($path) as $e) {
        if (!$e->isDot() && $e->isDir() && !$e->isLink()) $out[] = $e->getFilename();
    }
    sort($out, SORT_NATURAL | SORT_FLAG_CASE);
    return $out;
}

function first_dir_with_prefix(string $path, string $prefix): ?string {
    foreach (dirs($path) as $name) if (str_starts_with($name, $prefix)) return $name;
    return null;
}

function dir_value(string $path, string $key, string $default=''): string {
    $n = first_dir_with_prefix($path, $key . '_');
    return $n === null ? $default : unb64u(substr($n, strlen($key) + 1));
}

function set_dir_value(string $path, string $key, string $value): void {
    ensure_dir($path);
    foreach (dirs($path) as $n) {
        if (str_starts_with($n, $key . '_')) recursive_delete($path . '/' . $n);
    }
    ensure_dir($path . '/' . $key . '_' . b64u($value));
}

function has_flag(string $path, string $flag): bool {
    return is_dir($path . '/' . $flag);
}
function set_flag(string $path, string $flag, bool $on): void {
    $p = $path . '/' . $flag;
    if ($on) ensure_dir($p);
    elseif (is_dir($p)) recursive_delete($p);
}

/* Long strings are split into ordered directory-name chunks. */
function set_dir_text(string $path, string $key, string $text): void {
    $base = $path . '/' . $key;
    if (is_dir($base)) recursive_delete($base);
    ensure_dir($base);
    $encoded = b64u($text);
    $chunks = str_split($encoded, 180);
    if (!$chunks) $chunks = [''];
    foreach ($chunks as $i => $chunk) {
        ensure_dir($base . '/' . sprintf('%06d_%s', $i, $chunk));
    }
}

function dir_text(string $path, string $key, string $default=''): string {
    $base = $path . '/' . $key;
    if (!is_dir($base)) return $default;
    $encoded = '';
    foreach (dirs($base) as $n) {
        $pos = strpos($n, '_');
        if ($pos !== false) $encoded .= substr($n, $pos + 1);
    }
    return unb64u($encoded);
}

function recursive_delete(string $path): void {
    if (!is_dir($path) || is_link($path)) return;
    foreach (new DirectoryIterator($path) as $e) {
        if ($e->isDot()) continue;
        if ($e->isDir() && !$e->isLink()) recursive_delete($e->getPathname());
    }
    rmdir($path);
}

function new_id(): string { return bin2hex(random_bytes(16)); }

function forum_parts(string $relative): array {
    $relative = trim(str_replace('\\', '/', $relative), '/');
    if ($relative === '') return [];
    $parts = explode('/', $relative);
    foreach ($parts as $p) safe_slug($p);
    return $parts;
}
function forum_path(string $relative=''): string {
    $p = FORUM_ROOT;
    foreach (forum_parts($relative) as $part) $p .= '/' . $part;
    return $p;
}
