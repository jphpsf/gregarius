<item>
		<title><?php echo rss_htmlspecialchars(rss_feed_title()); ?>: <?php echo rss_htmlspecialchars(rss_item_title()); ?></title>
		<link><?php echo rss_item_url(); ?></link>
		<pubDate><?php echo rss_item_date_with_format('r'); ?></pubDate>
		<guid><?php echo rss_item_url(); ?></guid>
	    <?php if (rss_item_has_enclosure()) { ?>
		<enclosure url="<?php echo rss_item_enclosure(); ?>" length="" type="" />
	    <?php } ?>
		<content:encoded><![CDATA[	<?php echo rss_item_content(); ?> ]]></content:encoded>
</item>
