<?php if ($title = rss_itemlist_title()) { ?>
<h2><?php echo $title; ?></h2>
<?php if(hidePrivate()) { ?>
	<p>(<a href="<?php echo getPath(); ?>?mobilelogin&amp;media=mobile">Login</a> to mark items read)</p>
<?php } ?>
<form method="post" action="<?= getPath(); ?>">
<?php } rss_itemlist_feeds(); ?>
<?php if( !isMobileDevice() ) { ?>
<input type='hidden' name='mobile' />
<?php } ?>
<p id="nextitems"><input type='submit' value='Next  &raquo;&raquo;' /></p>
</form>
