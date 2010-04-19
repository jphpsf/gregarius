<div class="feed frame">
	<div class="hd feed-title">
		<h2><a href="<?php echo rss_feed_url(); ?>" <?php echo rss_feed_anchor_name(); ?>><?php echo rss_feed_title(); ?></a></h2>
	</div>
	<div class="bd feed-body">
		<div class="feed-items">
			<?php rss_feed_items(); ?>
		</div>
	</div>
</div>

