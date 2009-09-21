<div class="block">
	<div class="hd feed-title">
		<h2><a href="<?php echo rss_feed_url(); ?>" <?php echo rss_feed_anchor_name(); ?>><?php echo rss_feed_title(); ?></a></h2>
	</div>
	<div class="bd feed-body">
		<?php rss_feed_items(); ?>
	</div>
</div>

