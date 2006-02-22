<?php if (rss_itemlist_has_extractions()) { ?>
<div class="feedaction withmargin">
	<?php rss_itemlist_prerender_callback(); ?>
</div>
<?php } ?>
<?php if ($title = rss_itemlist_title()) { ?>
<h2<?php echo rss_itemlist_anchor(); ?>>
<?php if ($icon = rss_itemlist_icon()) { ?>
	<img src="<?php echo $icon; ?>" class="favicon" alt="" />
<?php } ?>
	<?php echo $title; ?> 
</h2>
<?php } else { ?>
<a <?php echo rss_itemlist_anchor(); ?>></a>
<?php } ?>
<?php echo rss_itemlist_before_list(); ?>
<?php rss_itemlist_feeds(); ?>
<?php echo rss_itemlist_after_list(); ?>
