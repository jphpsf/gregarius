<?php

require_once ('mobile/mobileconstants.php');

rss_theme_options_configure_overrides( /*theme name*/ 'default', /*media (optional)*/ 'mobile', 
        array ( /* items for the configuration list */
                array( 'key_' => 'rss.output.frontpage.numitems', 'default_' => DEFAULT_MOBILE_FRONTPAGE_NUMITEMS ),
                array( 'key_' => 'rss.output.itemsinchannelview', 'default_' => DEFAULT_MOBILE_FRONTPAGE_ITEMSINCHANNELVIEW )
        )
);

// we take the array that's input, and return an array as if it had been selected 
// from the config table and dumped out using rss_fetch_assoc.  This means the 
// theme author does not have to pass anything more than key_ in his input array.
// If $key is not null we query and return only that item, otherwise we fill
// an array to match the entire input array
function theme_options_fill_override_array($theme, $media, $array_input, $key=null) {
    $ret = array();
    foreach( $array_input as $inp ) {
        if( !is_array( $inp ) && isset( $inp ) ) {
            $inp = array( 'key_' => $inp );
        }
        if( isset( $inp['key_'] ) ) {
            $thisret = array();
            if( $key === null || $key === $inp['key_'] ) {
                $sql = "select * from " .getTable("config") ." where key_ like
                       '" . $inp['key_'] . "'";

                $res = rss_query($sql);
                if ($row = rss_fetch_assoc($res)) {
                    foreach( $row as $rowkey => $rowval ) {
                        if( $rowkey !== 'value_' ) {
                            if( !isset( $inp[$rowkey] ) ) {
                                $thisret[$rowkey] = $rowval;
                            } else {
                                $thisret[$rowkey] = $inp[$rowkey];
                            }
                        }
                    }
                }
                
                $thisret['value_'] = rss_plugin_config_override_option($thisret['key_'], $thisret['default_'], $theme, $media);

                if( $key === null )
                    $ret[] = $thisret;
                else
                    $ret = $thisret;
            }
        } else {
            rss_error('rss_theme_options_configure_overrides was passed an item with no key_', RSS_ERROR_ERROR,true);
        }
    }
    
    return $ret;
}

// Display a configuration form similar to the form in the admin->config section
// except we will get the theme's overrides via rss_themes_get_option.  You will
// need to pass in the theme, the media (optional - pass null for no media) and
// an array.
// The array is where it gets complex.  Each member of the array can be a string
// representing the key of a configuration item, or it can be an array that specifies
// the various properties of the configuration item.  These properties are the
// same as the fields in the config table (key_, default_, descr_) and any that
// are missing will be loaded from that table.  The keys of the configuration
// items may match an entry in the config table, or you can create a custom one.
// In the later case, you *must* use the second form of the $config_items array
// and you must pass all the fields that this function uses (key_, default_, type_
// and desc_)  Note that there is no point to passing value_ as this is loaded
// via a call to rss_themes_get_option.
function rss_theme_options_configure_overrides($theme, $media, $config_items) {
    $action = null;
    if (isset($_REQUEST[CST_ADMIN_METAACTION])) {
        $action = $_REQUEST[CST_ADMIN_METAACTION];
    } else if (isset($_REQUEST['action'])) {
        $action = $_REQUEST['action'];
    }

    if (array_key_exists(CST_ADMIN_CONFIRMED,$_POST) && $_POST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_YES) {
        if (!array_key_exists('key',$_REQUEST)) {
            rss_error('Invalid config key specified.', RSS_ERROR_ERROR,true);
        } else {
            $key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
            rss_plugin_delete_config_override_option($key,$theme,$media);
        }
        $action = null; //redirect to our theme's admin page
    } else if( rss_theme_options_is_submit() ) {
        switch ($action) {
        case LBL_ADMIN_SUBMIT_CHANGES:
        case 'LBL_ADMIN_SUBMIT_CHANGES':
            if (!array_key_exists('key',$_REQUEST)) {
                rss_error('Invalid config key specified.', RSS_ERROR_ERROR,true);
                break;
            }
            if (!array_key_exists('type',$_REQUEST)) {
                rss_error('Invalid config type specified.', RSS_ERROR_ERROR,true);
                break;
            }
            if (!array_key_exists('value',$_REQUEST)) {
                rss_error('Invalid config value specified.', RSS_ERROR_ERROR,true);
                break;
            }
            
            $key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
            $type = sanitize($_POST['type'],RSS_SANITIZER_CHARACTERS);
            $value = sanitize($_POST['value'], RSS_SANITIZER_SIMPLE_SQL);
            rss_plugin_set_config_override_option($key, $value, $type, $theme, $media);

            break;
            
        default:
            rss_error('Invalid config action specified.', RSS_ERROR_ERROR,true);
            break;
        }
        $action = null; //redirect to our theme's admin page
    }
    
    switch ($action) {
    case CST_ADMIN_DEFAULT_ACTION:
    case 'CST_ADMIN_DEFAULT_ACTION':
        if (!array_key_exists('key',$_REQUEST)) {
            rss_error('Invalid config key specified.', RSS_ERROR_ERROR,true);
            break;
        }
        $key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
        $item = theme_options_fill_override_array($theme,$media,$config_items,$key);
        extract( $item );
        config_default_form($key_, $type_, $default_, CST_ADMIN_DOMAIN_THEME_OPTIONS);
        rss_theme_options_form_class('box');
        rss_theme_options_rendered_buttons(true);
        break;
        
    case CST_ADMIN_EDIT_ACTION:
    case 'CST_ADMIN_EDIT_ACTION':
        if (!array_key_exists('key',$_REQUEST)) {
            rss_error('Invalid config key specified.', RSS_ERROR_ERROR,true);
            break;
        }
        $key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
        $item = theme_options_fill_override_array($theme,$media,$config_items,$key);
        extract( $item );
        config_edit_form($key_,$value_,$default_,$type_,$desc_,$export_);
        break;
        
    default:
        $caption = "Configuration overrides";
        if( isset( $media ) ) {
            $caption .= " for $media media";
        }
        config_table_header($caption);

        $cntr = 0;
        $items = theme_options_fill_override_array($theme,$media,$config_items);
        foreach( $items as $item ) {
            config_table_row( $item, (($cntr++ % 2 == 0)?"even":"odd"), CST_ADMIN_DOMAIN_THEME_OPTIONS, "&theme=$theme" );
        }

        config_table_footer();
        
        //no buttons here
        rss_theme_options_rendered_buttons(true);

        break;
    }
}
?>
