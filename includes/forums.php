<?php
declare(strict_types=1);

function category_children(string $relative=''): array {
    $base = forum_path($relative);
    $out = [];
    foreach (dirs($base) as $n) {
        if (!str_starts_with($n, '_')) $out[] = $n;
    }
    return $out;
}

function create_category(string $parent, string $name): void {
    $name = safe_slug($name);
    $base = forum_path($parent);
    if (!is_dir($base)) throw new RuntimeException('Parent category missing.');
    $d = "$base/$name";
    if (is_dir($d)) throw new RuntimeException('Category exists.');
    ensure_dir($d . '/_threads');
    ensure_dir($d . '/_meta');
    set_dir_text($d . '/_meta', 'description', '');
}

function thread_dir(string $category, string $id): string {
    return forum_path($category) . '/_threads/' . safe_id($id);
}

function create_thread(string $category, string $title, string $body, string $author): string {
    require_login();
    $cat = forum_path($category);
    if (!is_dir($cat)) throw new RuntimeException('Category missing.');
    ensure_dir($cat . '/_threads');
    $id = new_id();
    $d = $cat . '/_threads/' . $id;
    ensure_dir($d . '/posts');
    ensure_dir($d . '/votes/up');
    ensure_dir($d . '/votes/down');
    set_dir_text($d, 'title', trim($title));
    set_dir_value($d, 'author', $author);
    set_dir_value($d, 'created', date(DATE_ATOM));
    create_post($d, $body, $author);
    return $id;
}

function create_post(string $threadDir, string $body, string $author): string {
    if (has_flag($threadDir, 'locked')) throw new RuntimeException('Thread is locked.');
    $id = new_id();
    $p = $threadDir . '/posts/' . $id;
    ensure_dir($p . '/votes/up');
    ensure_dir($p . '/votes/down');
    set_dir_value($p, 'author', $author);
    set_dir_value($p, 'created', date(DATE_ATOM));
    set_dir_text($p, 'body', trim($body));
    return $id;
}

function list_threads(string $category): array {
    $base = forum_path($category) . '/_threads';
    $out = [];
    foreach (dirs($base) as $id) {
        if (!preg_match('/^[a-f0-9]{32}$/', $id)) continue;
        $d = "$base/$id";
        $out[] = ['id'=>$id, 'title'=>dir_text($d,'title','Untitled'),
                  'author'=>dir_value($d,'author','?'), 'created'=>dir_value($d,'created','')];
    }
    usort($out, fn($a,$b)=>strcmp($b['created'],$a['created']));
    return $out;
}

function list_posts(string $threadDir): array {
    $out = [];
    foreach (dirs($threadDir . '/posts') as $id) {
        if (!preg_match('/^[a-f0-9]{32}$/', $id)) continue;
        $p = $threadDir . '/posts/' . $id;
        $out[] = ['id'=>$id, 'author'=>dir_value($p,'author','?'),
                  'created'=>dir_value($p,'created',''), 'body'=>dir_text($p,'body','')];
    }
    usort($out, fn($a,$b)=>strcmp($a['created'],$b['created']));
    return $out;
}

function vote_post(string $threadDir, string $postId, string $user, string $vote): void {
    $postId = safe_id($postId);
    $p = $threadDir . '/posts/' . $postId . '/votes';
    if (!is_dir($p)) throw new RuntimeException('Post missing.');
    foreach (['up','down'] as $v) {
        $x = "$p/$v/$user";
        if (is_dir($x)) recursive_delete($x);
    }
    if (in_array($vote,['up','down'],true)) ensure_dir("$p/$vote/$user");
}
function post_score(string $threadDir, string $postId): int {
    $p = $threadDir . '/posts/' . safe_id($postId) . '/votes';
    return count(dirs("$p/up")) - count(dirs("$p/down"));
}
