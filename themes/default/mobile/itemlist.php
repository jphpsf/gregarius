<?php if(hidePrivate()) { ?>
	<p>(<a href="<?php echo getPath(); ?>?mobilelogin&media=mobile">Login</a> to mark items read)</p>
<?php } ?>
<?php if ($title = rss_itemlist_title()) { ?>
<h2><?php echo $title; ?></h2>
<form method="POST" style="display: inline;">
<?php } rss_itemlist_feeds(); ?>
<input type='submit' value='next'>
</form>
