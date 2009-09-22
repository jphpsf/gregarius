<div id="sad<?php echo rss_item_id(); ?>" style="display:none"></div>
<?php if (rss_item_has_enclosure()) { ?>
		<h5><?php echo __('Enclosure:'); ?>&nbsp;[<a href="<?php echo rss_item_enclosure(); ?>"><?php echo __('download'); ?></a><?php rss_plugin_hook("rss.plugins.items.enclosure", null); ?>]</h5>
<?php } ?>
<div class="content" id="c<?php echo rss_item_id(); ?>">
	<?php echo rss_item_content(); ?>
</div>
