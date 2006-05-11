<div class="navlist">
<?php 
$GLOBALS['rss']->nav->appendNavItem(getPath()."?feeds",LBL_H2_CHANNELS);
$GLOBALS['rss']->nav->appendNavItem(getPath()."?cats",LBL_TAG_FOLDERS);
$GLOBALS['rss']->nav->appendNavItem(getPath()."update.php?mobile", LBL_NAV_UPDATE);

foreach ($GLOBALS['rss']->nav->items as $item) {
    if( !eregi( 'update.php$', $item -> href ) && !eregi( 'admin', $item -> href ) ) {
			$item -> render();
    }
}

?>
</div>
