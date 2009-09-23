<?php if (rss_itemlist_has_extractions()) { ?>
<div class="feedaction">
	<?php rss_itemlist_prerender_callback(); ?>
</div>
<?php } ?>
<?php if ($title = rss_itemlist_title()) { ?>
<div class="feedtitle">
	<h2<?php echo rss_itemlist_anchor(); ?>>
		<?php echo $title; ?>
	</h2>
	<?php } else { ?>
	<a <?php echo rss_itemlist_anchor(); ?>></a>
	<?php } ?>
	<?php echo rss_itemlist_before_list(); ?>
	<?php rss_itemlist_navigation(); ?>
</div>
<?php rss_itemlist_feeds(); ?>
<?php rss_itemlist_navigation(); ?>
<?php echo rss_itemlist_after_list(); ?>
