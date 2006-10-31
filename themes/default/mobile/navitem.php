<?php 
if (!isset($GLOBALS['__ak__'])) {
	$GLOBALS['__ak__']=0;
}
$ak = ++$GLOBALS['__ak__'];
?>
<p><span>[<?= $ak; ?>] </span><a accesskey="<?= $ak; ?>" href="<?php echo rss_nav_item_href(); ?>"><?php echo rss_nav_item_label(); ?></a></p>
