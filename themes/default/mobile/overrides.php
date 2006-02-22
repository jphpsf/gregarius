<?php

// force at most 10 items on the front page
rss_config_override('rss.output.frontpage.numitems', 10);
// and on feed.php as well
rss_config_override('rss.output.itemsinchannelview', 10);
// but why does it sort in reverse order unless we set this?
rss_config_override('rss.output.frontpage.mixeditems', false);

// force turning off of profiling
$GLOBALS['rss'] -> profiler = null;

//handle form data...
foreach($_POST as $varName => $value)
{
	switch( $value )
	{
		case 'mobile_sticky':
			rss_plugins_set_item_state( $varName, RSS_MODE_STICKY_STATE, true );
		//intentional fallthrough
		case 'mobile_read':
			rss_plugins_set_item_state( $varName, RSS_MODE_UNREAD_STATE, false );
	}
};

?>
