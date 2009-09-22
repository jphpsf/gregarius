<li>
	<a class="<?php echo rss_feeds_feed_class(); ?>" title="<?php echo rss_feeds_feed_description_entities(); ?>" href="<?php echo rss_feeds_feed_link();  ?>">
		<?php echo rss_feeds_feed_title(); ?>
		<?php if (rss_feeds_feed_unread_count()>0): ?>
		 (<strong><?php echo rss_feeds_feed_unread_count(); ?></strong>)
		<?php endif; ?>
	</a>
</li>
