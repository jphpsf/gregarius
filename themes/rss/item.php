<item>
		<title><?= rss_feed_title()?>: <?= rss_item_title(); ?></title>
		<link><?= rss_item_url(); ?></link>
		<pubDate><?= rss_item_date_with_format('r'); ?></pubDate>
		<guid><?= rss_item_url(); ?></guid>
		<content:encoded><![CDATA[	<?= rss_item_content(); ?> ]]></content:encoded>
</item>
