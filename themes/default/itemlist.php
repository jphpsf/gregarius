<?php if (rss_itemlist_has_extractions()) { ?>
<div id="feedaction" class="withmargin">
	<?php rss_itemlist_prerender_callback() ?>		
</div>
<?php } ?>

<?php if ($title = rss_itemlist_title()) { ?>
<h2<?= rss_itemlist_anchor(); ?>>
<?php if ($icon = rss_itemlist_icon()) { ?>
	<img src="<?= $icon ?>" class="favicon" alt="<?= rss_itemlist_title() ?>" />
<?php } ?>
	<?= $title ?> 
</h2>
<?php } else { ?>
<a <?= rss_itemlist_anchor(); ?>></a>
<?php } ?>

<?= rss_itemlist_before_list(); ?>
<?php rss_itemlist_feeds(); ?>
<?= rss_itemlist_after_list(); ?>