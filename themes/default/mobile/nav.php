<div class="navlist">
<?php 
$GLOBALS['rss']->nav->appendNavItem(getPath()."?feeds",LBL_H2_CHANNELS);

foreach ($GLOBALS['rss']->nav->items as $item) {
    if( !eregi( 'update.php', $item -> href ) && !eregi( 'admin', $item -> href ) ) {
	$item -> render();
    }
}

?>
</div>
