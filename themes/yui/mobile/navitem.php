<?php 
if (!isset($GLOBALS['__ak__'])) {
	$GLOBALS['__ak__']=0;
}
$ak = ++$GLOBALS['__ak__'];
?>
<p><span>[<?php print($ak); ?>] </span><a accesskey="<?php print($ak); ?>" href="<?php echo rss_nav_item_href(); ?>"><?php echo rss_nav_item_label(); ?></a></p>
