<?php

$displayFolder=(isset($_GET['channel']) && $_GET['channel']===rss_feeds_folder_name());
if (isset($_GET['channel']) && !$displayFolder)
{

	foreach ($GLOBALS['rss']->currentFeedsFolder->feeds as $feed)
	{
		if (rss_uri($feed->title)===$_GET['channel'])
		{
			$displayFolder=TRUE;
			break;
		}
	}
}

$folderIcon=($displayFolder?'folderhide':'foldershow');

?>
<?php if(!rss_feeds_folder_is_root()) { ?>
<div class="block folder">
	<div class="bd">
		<h3>
			<a href="#" class="<?php echo $folderIcon ?>">&nbsp;</a>
			<a href="<?php echo rss_feeds_folder_link(); ?>"><?php echo rss_feeds_folder_name(); ?>
				<?php if (rss_feeds_folders_unread_count(NULL,TRUE)>0): ?>
				(<strong><?php echo rss_feeds_folders_unread_count(NULL,TRUE); ?></strong>)
				<?php endif; ?>
			</a>
		</h3>
		<ul class="biglist" <?php if (!$displayFolder) { ?>style="display:none"<?php } ?>>
			<?php rss_feeds_folder_feeds(); ?>
		</ul>
	</div>
</div>
<?php } ?>
