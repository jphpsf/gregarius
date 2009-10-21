<?php if(!rss_feeds_folder_is_root()) { ?>
<div class="block folder">
	<div class="bd">
		<h3>
			<a href="<?php echo rss_feeds_folder_link(); ?>"><?php echo rss_feeds_folder_name(); ?>
				<?php if (rss_feeds_folders_unread_count(NULL,TRUE)>0): ?>
				(<strong><?php echo rss_feeds_folders_unread_count(NULL,TRUE); ?></strong>)
				<?php endif; ?>
			</a>
			<a href="#" class="foldershow">&nbsp;</a>
		</h3>
		<ul class="biglist" style="display:none">
			<?php rss_feeds_folder_feeds(); ?>
		</ul>
	</div>
</div>
<?php } ?>
