<?php if(!rss_feeds_folder_is_root()) { ?>
<div>
	<a href="#"  onclick="_tgl(<?php echo rss_feeds_folder_id(); ?>,'category'); return false;">
		<img src="<?php echo rss_theme_path(); ?>/media/folder.gif" alt="<?php echo rss_feeds_folder_name(); ?>" />
	</a>
	<a href="<?php echo rss_feeds_folder_link(); ?>"><?php echo rss_feeds_folder_name(); ?></a>
	<?php echo rss_feeds_folders_unread_count(); ?>
	
</div>
<?php } ?>
<ul id="fc<?php echo rss_feeds_folder_id();?>" class="<?php echo rss_feeds_ul_class(); ?>" style="display:<?php echo rss_feeds_ul_style(); ?>">
<?php rss_feeds_folder_feeds(); ?>
</ul>
