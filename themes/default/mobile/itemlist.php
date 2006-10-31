<?php if ($title = rss_itemlist_title()) { ?>
<h2><?php echo $title; ?></h2>
<?php if(hidePrivate()) { ?>
	<p>(<a href="<?php echo getPath(); ?>?mobilelogin&amp;media=mobile">Login</a> to mark items read)</p>
<?php } ?>
<form method="POST" style="display: inline;">
<?php } rss_itemlist_feeds(); ?>
<?php if( !isMobileDevice() ) { ?>
<input type='hidden' name='mobile' />
<?php } ?>
<input type='submit' value='next' />
</form>
