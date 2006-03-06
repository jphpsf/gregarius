<?php
require_once ('mobile/mobileconstants.php');

rss_theme_options_configure_overrides( 'default', 'web', 'rss.output.theme.scheme' );

rss_theme_options_configure_overrides( /*theme name*/ 'default', /*media (optional)*/ 'mobile', 
        array ( /* items for the configuration list */
                array( 'key_' => 'rss.output.frontpage.numitems', 'default_' => DEFAULT_MOBILE_FRONTPAGE_NUMITEMS ),
                array( 'key_' => 'rss.output.itemsinchannelview', 'default_' => DEFAULT_MOBILE_FRONTPAGE_ITEMSINCHANNELVIEW )
        )
);
?>
