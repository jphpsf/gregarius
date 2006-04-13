<div class="frame" style="display:inline;float:right;font-size:small;padding: 0.4em !important;background-color: #f4f4f4;">
<?php foreach (rss_itemlist_navigation_pages() as $i => $page) { 
		list($url,$iscurrent,$gap) = $page;
		if ($gap) { ?>
		...
		<?php } elseif ($iscurrent) { ?>
			<strong><?php echo $i+1; ?></strong>
		<?php } else { ?>
			<a href="<? echo $url; ?>"><?php echo $i+1; ?></a>
		<?php } ?>
<?php } ?>
</div>
<div style="clear:right;height:0px"></div>