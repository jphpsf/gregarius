<h2><?= LBL_H2_CHANNELS ?></h2>
<?php list($total, $unread, $channelcount) = rss_feeds_stats(); ?>

<p class="stats"><?php printf(LBL_ITEMCOUNT_PF, $total, $unread, $channelcount) ?></p>
<?php rss_feeds_folders(); ?>

