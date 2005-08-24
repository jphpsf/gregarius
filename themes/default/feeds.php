<h2><?= rss_feeds_column_title() ?></h2>
<?php list($total, $unread, $channelcount) = rss_feeds_stats(); ?>

<p class="stats"><?= rss_feeds_stats() ?></p>
<?php rss_feeds_folders(); ?>

