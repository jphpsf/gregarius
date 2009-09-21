<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd"> 
<html xml:lang="en">
<head>
<?php rss_main_header(); ?>
</head>
<body id="top">
<?php 
if( array_key_exists('feeds',$_REQUEST) ) {
	$GLOBALS['rss']->sideMenu->activeElement = 'FeedList';
	rss_main_feeds();
}
else if( array_key_exists('cats',$_REQUEST) ) {
	$GLOBALS['rss']->sideMenu->activeElement = 'CatList';
	rss_main_feeds();
}
else { ?>
<div <?php echo rss_main_div_id(); ?>>
 	<?php rss_main_object(); ?>
</div>
<?php } ?>
<?php rss_main_footer(); ?>
</body>
</html>
