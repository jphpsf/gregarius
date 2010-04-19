<div id="pagination">
<?php print(__('Skip to page: ')); ?>
<?php foreach (rss_itemlist_navigation_pages() as $i => $page) {
		list($url,$iscurrent,$gap) = $page;
		if ($gap) { ?>
		...
		<?php } elseif ($iscurrent) { ?>
			<strong class="current"><?php echo $i+1; ?></strong>
		<?php } else { ?>
			<a href="<?php echo $url; ?>" class="awesome"><?php echo $i+1; ?></a>
		<?php } ?>
<?php } ?>
</div>
