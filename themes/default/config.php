<?php
require_once ('mobile/mobileconstants.php');

rss_theme_options_configure_overrides( 'default', 'web', 'rss.output.theme.scheme' );

rss_theme_config_override_option('rss.output.maxlength', DEFAULT_MOBILE_OUTPUT_MAXLENGTH);
rss_theme_config_override_option('rss.content.strip.images', DEFAULT_MOBILE_CONTENT_STRIP_IMAGES);

rss_theme_options_configure_overrides( /*theme name*/ 'default', /*media (optional)*/ 
'mobile', 
        array ( /* items for the configuration list */
                array( 'key_' => 'rss.output.frontpage.numitems', 'default_' => DEFAULT_MOBILE_FRONTPAGE_NUMITEMS ),
                array( 'key_' => 'rss.output.itemsinchannelview', 'default_' => DEFAULT_MOBILE_FRONTPAGE_ITEMSINCHANNELVIEW ),
                array( 'key_' => 'rss.output.maxlength', 'default_' => DEFAULT_MOBILE_OUTPUT_MAXLENGTH, 'type_' => 'num', 
			'desc_' => 'Truncate long posts to this many words. Set to 0 (default) to disable this.', 'export_' => NULL ),
                array( 'key_' => 'rss.content.strip.images', 'default_' => DEFAULT_MOBILE_CONTENT_STRIP_IMAGES, 'type_' => 'boolean', 
			'desc_' => "When true, Gregarius won't display any image in items shown on mobile devices.", 'export_' => NULL )
        )
);



?>
