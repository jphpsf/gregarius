<?php

$displayFolder=FALSE;

// use rss_uri to convert current channel name or feed name to compare to the channel in argument
// note that channel in argument might be channel folder or a feed
if (isset($_GET['channel']) && rss_uri($_GET['channel'])===rss_uri(rss_feeds_folder_name())) {

	$displayFolder=TRUE;

// as a fallback, if user tried to mark a folder as read, then we should show that same folder open
} else if (isset($_POST['metaaction']) && $_POST['metaaction']=='ACT_MARK_FOLDER_READ' && $_POST["folder"]===rss_feeds_folder_id()) {

	$displayFolder=TRUE;

} else  {

	if (isset($_GET['channel'])) {
		foreach ($GLOBALS['rss']->currentFeedsFolder->feeds as $feed) {
			if (rss_uri($feed->title)===rss_uri($_GET['channel'])) {
				$displayFolder=TRUE;
				break;
			}
		}
	} else if (isset($_POST['metaaction']) && $_POST['metaaction']=='ACT_MARK_CHANNEL_READ') {
		foreach ($GLOBALS['rss']->currentFeedsFolder->feeds as $feed) {
			if ($feed->id===$_POST['channel']) {
				$displayFolder=TRUE;
				break;
			}
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
		<ul <?php if (!$displayFolder) { ?>style="display:none"<?php } ?>>
			<?php rss_feeds_folder_feeds(); ?>
		</ul>
	</div>
</div>
<?php } ?>
