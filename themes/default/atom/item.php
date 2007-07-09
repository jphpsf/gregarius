<entry>
		<id><?php echo rss_item_url(); ?></id>
		<author><name><?php echo $GLOBALS['rss']->currentItem->author; ?></name></author>
		<title><?php echo rss_htmlspecialchars(rss_feed_title()); ?>: <?php echo rss_htmlspecialchars(rss_item_title()); ?></title>
                <link rel="alternate" type="text/html" href="<?php echo rss_item_url(); ?>"/>		
		<updated><?php echo rss_item_date_with_format('c'); ?></updated>
		<published><?php echo rss_item_date_with_format('c'); ?></published>
		<content type="html"><![CDATA[	<?php echo rss_item_content(); ?> ]]></content>
<?php foreach ($GLOBALS['rss']->currentItem->tags as $tag_) { ?>
 		<category term="<?php echo $tag_ ?>" />
<?php } ?>
</entry>
