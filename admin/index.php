<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2006 Marco Bonetti
#
###############################################################################
# This program is free software and open source software; you can redistribute
# it and/or modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of the License,
# or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at gmail dot com
# Web page:	   http://gregarius.net/
#
###############################################################################

define ('RSS_FILE_LOCATION','/admin');
define ('THEME_OVERRIDE','default');

require_once('../init.php');
require_once('../opml.php');
require_once('ds.php');

require_once('channels.php');
require_once('items.php');
require_once('folders.php');
require_once('opml.php');
require_once('config.php');
require_once('dashboard.php');
require_once('users.php');
require_once('plugins.php');
require_once('themes.php');
require_once('tags.php');
require_once('../cls/wrappers/toolkit.php');

define ('CST_ADMIN_DOMAIN','domain');
define ('CST_ADMIN_DOMAIN_NONE','none');
define ('CST_ADMIN_DELETE_ACTION','delete');
define ('CST_ADMIN_DEFAULT_ACTION','default');
define ('CST_ADMIN_EDIT_ACTION','edit');
define ('CST_ADMIN_MOVE_UP_ACTION','up');
define ('CST_ADMIN_MOVE_DOWN_ACTION','down');
define ('CST_ADMIN_SUBMIT_EDIT','submit_edit');
define ('CST_ADMIN_VIEW','view');
define ('CST_ADMIN_CONFIRMED','confirmed');
define ('CST_ADMIN_PRUNE','prune');
define ('CST_ADMIN_DOMAIN_SYSINFO','sysinfo');
define ('CST_ADMIN_METAACTION','metaaction');

define ('CST_ADMIN_DOMAIN_DASHBOARD','dashboard');
define ('CST_ADMIN_DOMAIN_FOLDER','folders');
define ('CST_ADMIN_DOMAIN_CHANNEL','feeds');
define ('CST_ADMIN_DOMAIN_ITEM','items');
define ('CST_ADMIN_DOMAIN_CONFIG','config');
define ('CST_ADMIN_DOMAIN_OPML','opml');
define ('CST_ADMIN_DOMAIN_PLUGINS','plugins');
define ('CST_ADMIN_DOMAIN_PLUGIN_OPTIONS','plugin_options');
define ('CST_ADMIN_DOMAIN_THEMES','themes');
define ('CST_ADMIN_DOMAIN_THEME_OPTIONS','theme_options');
define ('CST_ADMIN_DOMAIN_TAGS','tags');
// OPML import target
define ('CST_ADMIN_OPML_IMPORT_WIPE',1);
define ('CST_ADMIN_OPML_IMPORT_FOLDER',2);
define ('CST_ADMIN_OPML_IMPORT_MERGE',3);


$auth=rss_user_check_user_level(RSS_USER_LEVEL_ADMIN);
if (! $auth) {
    // check whether the admin password has been set.
    $sql = "select uname,password from " . getTable('users') . " where ulevel=99";
    list($dummy, $__pw__) = rss_fetch_row(rss_query($sql));
    if ($__pw__ == '') {
        $admin_uname = null;
        $admin_pass = null;
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $admin_uname = $_POST['username'];
            $admin_pass = $_POST['password'];
        }
        set_admin_pass($admin_uname,$admin_pass);
    } else {
    	rss_login_form();
      exit();
    }
}

admin_header();
admin_main($auth);
admin_footer();


///////////////////////////////////////////////////////////////////////////////////////////

/**
 * main function. checks for authorization and renders the
 * required admin section.
 */
function admin_main($authorised) {
    echo "\n<div id=\"channel_admin\" class=\"frame\">";
    if ($authorised) {
        admin_menu();
        if (array_key_exists(CST_ADMIN_DOMAIN,$_REQUEST)) {
            switch($_REQUEST[CST_ADMIN_DOMAIN]) {
            case CST_ADMIN_DOMAIN_FOLDER:
                    $show = folder_admin();
                break;
            case CST_ADMIN_DOMAIN_CHANNEL:
                $show = channel_admin();
                break;
            case CST_ADMIN_DOMAIN_CONFIG:
                $show = config_admin();
                break;
            case CST_ADMIN_DOMAIN_ITEM:
                $show = item_admin();
                break;
            case CST_ADMIN_DOMAIN_PLUGINS:
                $show = plugins_admin();
                break;
            case CST_ADMIN_DOMAIN_THEMES:
                $show = themes_admin();
                break;
			case CST_ADMIN_DOMAIN_TAGS:
				$show = tags_admin();
				break;
            case CST_ADMIN_DOMAIN_PLUGIN_OPTIONS:
                $show = plugin_options_admin();
                break;
            case CST_ADMIN_DOMAIN_THEME_OPTIONS:
                $show = theme_options_admin();
                break;
            default:
                break;
            }
        }

        if (array_key_exists(CST_ADMIN_VIEW,$_REQUEST) || isset($show)) {
            if (!isset($show)) {
                $show = $_REQUEST[CST_ADMIN_VIEW];
            }
            switch ($show) {
            case CST_ADMIN_DOMAIN_CONFIG:
                config();
                break;
            case CST_ADMIN_DOMAIN_CHANNEL:
                channels();
                break;
            case CST_ADMIN_DOMAIN_FOLDER:
                folders();
                break;
            case CST_ADMIN_DOMAIN_OPML:
                opml();
                break;
            case CST_ADMIN_DOMAIN_NONE:
                break;
            case CST_ADMIN_DOMAIN_ITEM:
                items();
                break;
            case CST_ADMIN_DOMAIN_SYSINFO:
                sysinfo();
                break;
            case CST_ADMIN_DOMAIN_DASHBOARD:
                dashboard();
                break;
            case CST_ADMIN_DOMAIN_PLUGINS:
                plugins();
                break;
            case CST_ADMIN_DOMAIN_THEMES:
                themes();
                break;
			case CST_ADMIN_DOMAIN_TAGS:
				tags();
				break;
            case CST_ADMIN_DOMAIN_PLUGIN_OPTIONS:
                plugin_options();
                break;
            case CST_ADMIN_DOMAIN_THEME_OPTIONS:
                theme_options();
                break;
            default:
            }
        } else {
            if(true == getConfig('rss.config.defaultdashboard')) {
                dashboard();
            } else {
                channels();
            }
        }

        echo "\n<div class=\"clearer\"></div>\n";

    } else {
        rss_error(sprintf(__('<h1>Not Authorized!</h1>
You are not authorized to access the administration interface.
Please follow <a href="%s">this link</a> back to the main page.
Have  a nice day!'),getPath()), RSS_ERROR_ERROR,true);
    }
    echo "</div>\n";
}

/////////

/**
 * Renders the admin sub-menu
 */
function admin_menu() {
    $active = array_key_exists(CST_ADMIN_VIEW, $_REQUEST) ? $_REQUEST[CST_ADMIN_VIEW] : null;
    $use_mod_rewrite = false;

    echo "\n<ul class=\"navlist\">\n";
    foreach (array (
                 /* url/id -- internationalized label, defined in intl/* */
                 array (CST_ADMIN_DOMAIN_DASHBOARD, __('Dashboard')),
                 array (CST_ADMIN_DOMAIN_CHANNEL, __('feeds')),
                 array (CST_ADMIN_DOMAIN_ITEM, __('items')),
                 array (CST_ADMIN_DOMAIN_CONFIG, __('config')),
                 array (CST_ADMIN_DOMAIN_PLUGINS, __('plugins')),
                 array (CST_ADMIN_DOMAIN_THEMES, __('themes')),
                 array (CST_ADMIN_DOMAIN_FOLDER, __('folders')),
                 array (CST_ADMIN_DOMAIN_OPML, __('opml')),
                 array (CST_ADMIN_DOMAIN_TAGS, __('Tags'))) as $item) {

        $link = "index.php?view=".$item[0];
        $lbl = $item[1];
        $cls = ($item[0] == $active ? " class=\"active\"" : "");
        echo "\t<li$cls><a href=\"".getPath()."admin/$link\">".ucfirst($lbl)."</a></li>\n";
    }
    echo "\t<li><a href=\"".getPath()."?logout\">".__('Logout')."</a></li>\n";
    echo "</ul>\n";
}

function admin_kses_to_html($arr) {
    $ret = "";
    foreach ($arr as $tag => $attr) {
        $ret .= "&lt;$tag";
        foreach ($attr as $nm => $val) {
            $ret .= "&nbsp;$nm=\"...\"&nbsp;";
        }
        $ret .= "&gt;\n";
    }
    return $ret;
}


function getLanguages() {

    $d = dir('../intl');
    $files = array();
    $ret = array();
    $activeIdx = "0";
	$ret['en_US']=array(
		'language'=>'English',
		'windows-locale'=>'english'
	);
    while (false !== ($entry = $d->read())) {
        if (preg_match('#^[a-z]{2}_[A-Z]{2}$#',$entry)) {
        	$ret[$entry]=getLanguageInfo($entry);
        } 
    }
    $d->close();
    return $ret;
}



function getLanguageInfo($dir) {
    $info = array();
    $path = "../intl/$dir/langinfo.txt";
    if (file_exists($path)) {
        $f = @fopen($path,'r');
        $contents = "";
        if ($f) {
            $contents .= fread($f, filesize($path));
            @fclose($f);
        } else {
            $contents = "";
        }

        if ($contents && preg_match_all("/([^:]+):(.*)/",$contents,$matches,PREG_SET_ORDER)) {
            foreach($matches as $match) {
                $key = trim(strtolower($match[1]));
                $val = trim($match[2]);
                $info[$key] = $val;
            }
        }
    }

    return $info;
}

function admin_enum_to_html($arr) {
    $idx = array_pop($arr);
    $ret = "";
    foreach ($arr as $i => $val) {
        if ($i == $idx)
            $ret .= "$val";
    }
    return $ret;
}

function admin_header() {

    header('Content-Type: text/html; charset='
           . (getConfig('rss.output.encoding') ? getConfig('rss.output.encoding') : DEFAULT_OUTPUT_ENCODING));
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
    echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n";
    echo "<head>";

    $header = new Header(admin_title(), LOCATION_ADMIN, null, '', (HDR_NONE | HDR_NO_CACHECONTROL | HDR_NO_OUPUTBUFFERING));
    $header -> render();

    echo "</head>";
    echo "<body>\n";

    echo ""
    ."<div id=\"nav\" class=\"frame\">"
    ."<h1 id=\"top\">" .rss_main_title() ."</h1>";
    $nav = new Navigation();
    $nav->render();
    echo "</div>";
}

function admin_footer() {
    echo "<div id=\"footer\" class=\"frame\">\n";
    rss_main_footer();
    echo "</div>\n\n</body>\n</html>\n";
}

function admin_title() {
	$title = array("Admin");
    if (array_key_exists(CST_ADMIN_VIEW,$_REQUEST)) {
    	$title[] = ucwords(
    		preg_replace('#[^a-zA-Z]#',' ',$_REQUEST[CST_ADMIN_VIEW])
    	) ;
    }
    return $title;
}
?>
