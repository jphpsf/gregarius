<?php if(!rss_feeds_folder_is_root()) { ?>
<div>
	<a href="#"  onclick="_tgl(<?= rss_feeds_folder_id() ?>); return false;">
		<img src="<?= rss_theme_path() ?>/media/folder.gif" alt="<?= rss_feeds_folder_name() ?>" />
	</a>
	<a href="<?= rss_feeds_folder_link() ?>"><?= rss_feeds_folder_name() ?></a>
	<?= rss_feeds_folders_unread_count(); ?>
	
</div>
<?php } ?>
<ul id="fc<?= rss_feeds_folder_id() ?>" class="<?= rss_feeds_ul_class(); ?>" style="display:<?= rss_feeds_ul_style() ?>">
<?php rss_feeds_folder_feeds() ?>
</ul>
