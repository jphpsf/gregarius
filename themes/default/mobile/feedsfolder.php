<?php if(!rss_feeds_folder_is_root()) { ?>
<div>
	<a href="#"  onclick="<?php echo rss_feeds_onclickaction(); ?>">
		<img src="<?php echo getExternalThemeFile('media/folder.gif'); ?>" alt="<?php echo rss_feeds_folder_name();  ?>" />
	</a>
	<a href="<?php echo rss_feeds_folder_link(); ?>"><?php echo rss_feeds_folder_name(); ?></a>
	<?php echo rss_feeds_folders_unread_count(); ?>
	
</div>
<?php } ?>
<ul id="fc<?php echo rss_feeds_folder_id(); ?>" class="<?php echo rss_feeds_ul_class(); ?>" style="display:<?php echo rss_feeds_ul_style(); ?>">
<?php rss_feeds_folder_feeds(); ?>
</ul>
