<?php if(!rss_feeds_folder_is_root()) { ?>
<div>
	<img src="<?php echo getExternalThemeFile('media/folder.gif'); ?>" alt="<?php echo rss_feeds_folder_name();  ?>" />
	<a href="<?php echo rss_feeds_folder_link(); ?>"><?php echo rss_feeds_folder_name(); ?></a>
	<?php echo rss_feeds_folders_unread_count(); ?>
	
</div>
<?php } ?>
<ul>
<?php rss_feeds_folder_feeds(); ?>
</ul>
