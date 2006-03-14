<?php

// stores constants for overrides' defaults
require_once('mobileconstants.php');

// load our optional overrides
rss_theme_config_override_option('rss.output.frontpage.numitems', DEFAULT_MOBILE_FRONTPAGE_NUMITEMS);
rss_theme_config_override_option('rss.output.itemsinchannelview', DEFAULT_MOBILE_FRONTPAGE_ITEMSINCHANNELVIEW);

if (getProperty('rss.prop.theme.default.mobile','rss.output.maxlength') === NULL) {
	setProperty('rss.prop.theme.default.mobile','rss.output.maxlength','theme',0);
}

if (getProperty('rss.prop.theme.default.mobile','rss.content.strip.images') === NULL) {
	setProperty('rss.prop.theme.default.mobile','rss.content.strip.images','theme',false);
}


// but why does it sort in reverse order unless we set this?
rss_config_override('rss.output.frontpage.mixeditems', false);

// force turning off of profiling
$GLOBALS['rss'] -> profiler = null;

//handle form data...
if (!hidePrivate()) {
	foreach($_POST as $varName => $value) {
		switch( $value ) {
			case 'mobile_sticky':
				rss_plugins_set_item_state( $varName, RSS_MODE_STICKY_STATE, true );
			//intentional fallthrough
			case 'mobile_read':
				rss_plugins_set_item_state( $varName, RSS_MODE_UNREAD_STATE, false );
		}
	}
}

?>
