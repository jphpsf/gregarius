<?php
// capture the controls to repeat at bottom of page
ob_start();
?>
<div class="controls frame">
	<?php if (rss_itemlist_has_extractions()) { ?>
	<div class="feedaction">
		<?php rss_itemlist_prerender_callback(); ?>
	</div>
	<?php } ?>
	<?php echo rss_itemlist_before_list(); ?>
	<?php rss_itemlist_navigation(); // pagination ?>
	<?php /*echo rss_itemlist_after_list(); //next/prev link */?>
</div>
<?php
	// quick workaround to add a class to the all read button
	$controls=str_replace(
		array(
			'<input id="_markReadButton"', // really strange, the code is not the same on a feed
			'as Read "/>', // and on the main page for the mark all as read
			/*'<hr class="clearer hidden"/>',
			' class="fl">',
			' class="fr">',*/
		),array(
			'<input id="_markReadButton" class="awesome"',
			'as Read" class="awesome"/>',
			/*'',
			' class="fl awesome">',
			' class="fr awesome">',*/
		),ob_get_contents());
	ob_end_clean();
	echo $controls;
?>
<?php rss_itemlist_feeds(); // items ?>
<?php
	echo $controls;
?>
