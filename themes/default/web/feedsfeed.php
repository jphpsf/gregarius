<li<?php echo rss_feeds_feed_li_class(); ?>>
<?php if($icon = rss_feeds_feed_icon()) { ?>
	<img src="<?php echo $icon; ?>" class="favicon" alt="" />
<?php } ?>
<?php if(!getConfig('rss.config.restrictrefresh')) { ?>
<?php echo "<a href=\"" . getPath() . "update.php?cid=" . rss_feeds_feed_id() . "\"><img src=\"" . getExternalThemeFile("media/arrow_refresh.png") . "\" width=\"16\" height=\"16\"></a>"; ?>
<?php } ?>
	<a class="<?php echo rss_feeds_feed_class(); ?>" title="<?php echo rss_feeds_feed_description_entities(); ?>" href="<?php echo rss_feeds_feed_link();  ?>"><?php echo rss_feeds_feed_title(); ?></a>
<?php if (($rdLbl = rss_feeds_feed_read_label()) != "") { ?>
	<?php echo $rdLbl; ?>
<?php } ?>
<?php echo rss_feeds_feed_meta(); ?>
</li>
