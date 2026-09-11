<?php require __DIR__.'/includes/bootstrap.php'; page_top('Folderum'); ?>
<p class="notice">Everything below is backed by directory names. No application data files.</p>
<h2>Forums</h2>
<?php $cats=category_children(); if(!$cats): ?><p>No categories yet. <?php if(is_admin()): ?><a href="<?=BASE_URL?>/admin/forums.php">Create one.</a><?php endif; ?></p><?php endif; ?>
<?php foreach($cats as $c): ?><div class="card">📁 <a href="<?=BASE_URL?>/forum.php?p=<?=urlencode($c)?>"><?=e($c)?></a></div><?php endforeach; ?>
<?php page_bottom(); ?>
