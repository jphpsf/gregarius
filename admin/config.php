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
# Web page:	 http://gregarius.net/
#
###############################################################################

/*************** Config management ************/

function config() {

    echo "<h2 class=\"trigger\">".__('Configuration:')."</h2>\n"
    ."<div id=\"admin_config\" class=\"trigger\">\n";

    config_table_header();

    $sql = "select * from " .getTable("config") ." where key_ like
           'rss.%' order by key_ asc";

    $res = rss_query($sql);
    $cntr = 0;
    while ($row = rss_fetch_assoc($res)) {
        // Don't show old/moved config keys in the main config list
        if (in_array($row['key_'], array(
		  'rss.config.plugins',
		  'rss.output.theme',
		  'rss.output.barefrontpage',
		  'rss.output.noreaditems',
		  'rss.output.cachedir',
		  'rss.output.showdevloglink',
		  'rss.output.numitemsonpage'))) {
            continue;
        }

        $class_ = (($cntr++ % 2 == 0)?"even":"odd");
        config_table_row($row, $class_, CST_ADMIN_DOMAIN_CONFIG);
    }

    config_table_footer();
    echo "</div>\n";
}

function config_table_header($caption=null) {
    echo "<table id=\"configtable\">\n";
    if ($caption !== null) {
        echo "<caption>$caption</caption>\n";
    }
    echo "<tr>\n"
    ."\t<th>". __('Key') ."</th>\n"
    ."\t<th>". __('Value') ."</th>\n"
    ."\t<th>". __('Description') ."</th>\n"
    ."\t<th class=\"cntr\">". __('Action') ."</th>\n"
    ."</tr>\n";
}

function config_table_footer() {
    echo "</table>";
}

function config_table_row($row, $class_, $adminDomain, $extraLinkText='') {
    $value =  real_strip_slashes($row['value_']);

    echo "<tr class=\"$class_\">\n"
    ."\t<td>".$row['key_']."</td>\n";

    echo "\t<td>";

    switch($row['key_']) {

        //specific handling per key
    case 'rss.config.dateformat':
        echo $value
        . " ("
        . preg_replace('/ /','&nbsp;',date($value))
        .")";
        break;
    case 'rss.input.allowed':

        $arr = unserialize($value);
        echo admin_kses_to_html($arr);

        break;
    case 'rss.config.plugins':
    case 'rss.output.theme':
        continue;
        break;

    case 'rss.output.lang':
        $arr = getLanguages();
        if (isset($arr[getConfig('rss.output.lang')]['language'])) {
        	echo $arr[getConfig('rss.output.lang')]['language'];
        } else {
        	echo getConfig('rss.output.lang');
        }
        break;
    case 'rss.config.tzoffset':
        echo $value
        . " (your local time: "
        . preg_replace('/ /','&nbsp;',date("g:i A",mktime()+$value*3600))
        .")";
        break;
    default:

        // generic handling per type:
        switch ($row['type_']) {
        case 'string':
        case 'num':
        case 'boolean':
        default:
            echo $value;
            break;
        case 'enum':
            $arr = explode(',',$value);

            echo admin_enum_to_html($arr);

            break;
        case 'array':
            $arr = unserialize($value);
            echo "<ul>\n";
            foreach($arr as $av) {
                echo "\t<li>$av</li>\n";
            }
            echo "</ul>\n";
        }
        break;
    }

    echo "</td>\n";

    echo "\t<td>" .
    // source: http://ch2.php.net/manual/en/function.preg-replace.php
    preg_replace('/\s(\w+:\/\/)(\S+)/',
                 ' <a href="\\1\\2">\\1\\2</a>',
                 $row['desc_'])
    . "</td>\n";

    echo "\t<td class=\"cntr\">"
    ."<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". $adminDomain
    ."&amp;action=". CST_ADMIN_EDIT_ACTION. "&amp;key=".$row['key_']
    ."&amp;".CST_ADMIN_VIEW."=". $adminDomain
    ."$extraLinkText\">" . __('edit')
    ."</a>";

    if ($row['value_'] != $row['default_'] && $row['key_'] != 'rss.config.plugins') {
        echo "|"

        ."<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". $adminDomain
        ."&amp;action=". CST_ADMIN_DEFAULT_ACTION. "&amp;key=".$row['key_']."$extraLinkText\">" . __('default')
        ."</a>";
    }

    echo "</td>\n"
    ."</tr>\n";
}

function config_admin() {

    $ret__ = CST_ADMIN_DOMAIN_CONFIG;

    if (isset($_REQUEST[CST_ADMIN_METAACTION])) {
        $action = $_REQUEST[CST_ADMIN_METAACTION];
    } else {
        $action = $_REQUEST['action'];
    }

    switch ($action) {

    case CST_ADMIN_DEFAULT_ACTION:
    case 'CST_ADMIN_DEFAULT_ACTION':
        if (!array_key_exists('key',$_REQUEST)) {
            rss_error(__('Invalid config key specified.'), RSS_ERROR_ERROR,true);
            break;
        }
        $key = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
        $res = rss_query("select value_,default_,type_ from " .getTable('config') . " where key_='$key'");
        list($value,$default,$type) = rss_fetch_row($res);
        $value = real_strip_slashes($value);
        $default = real_strip_slashes($default);

        if ($value == $default) {
            rss_error(__("The value for '$key' is the same as its default value!"), RSS_ERROR_ERROR,true);
            break;
        }

        if (array_key_exists(CST_ADMIN_CONFIRMED,$_POST) && $_POST[CST_ADMIN_CONFIRMED] == __('Yes')) {
            rss_query("update " . getTable('config') ." set value_=default_ where key_='$key'" );
            rss_invalidate_cache();
        }
        elseif (array_key_exists(CST_ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[CST_ADMIN_CONFIRMED] == __('No')) {
            //nop
        }
        else {
            echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";
            config_default_form($key, $type, $default, CST_ADMIN_DOMAIN_CONFIG);
            echo "</form>\n";

            $ret =	CST_ADMIN_DOMAIN_NONE;
        }
        break;

    case CST_ADMIN_EDIT_ACTION:
    case 'CST_ADMIN_EDIT_ACTION':
        $key_ = sanitize($_REQUEST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
        $res = rss_query("select * from ". getTable('config') . " where key_ ='$key_'");
        list($key,$value,$default,$type,$desc,$export) =  rss_fetch_row($res);

        echo "<div>\n";
        echo "\n\n<h2>Edit '$key'</h2>\n";
        echo "<form style=\"display:inline\" id=\"cfg\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n";

        $onclickaction = null;
        config_edit_form($key,$value,$default,$type,$desc,$export,$onclickaction);

        echo "<p style=\"display:inline\">\n";
        echo (isset($preview)?"<input type=\"submit\" name=\"action\" value=\"". __('Preview') ."\""
      .($onclickaction?" onclick=\"$onclickaction\"":"") ." />\n":"");
        echo "<input type=\"hidden\" name=\"".CST_ADMIN_METAACTION."\" value=\"ACT_ADMIN_SUBMIT_CHANGES\" />";

        echo "<input type=\"submit\" name=\"action\" value=\"". __('Submit Changes') ."\""
        .($onclickaction?" onclick=\"$onclickaction\"":"")
        ." /><input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"". CST_ADMIN_DOMAIN_CONFIG ."\"/>\n</p></form>\n";


        echo "<form style=\"display:inline\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
        ."<p style=\"display:inline\">\n<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"". CST_ADMIN_DOMAIN_CONFIG ."\"/>\n"
        ."<input type=\"hidden\" name=\"".CST_ADMIN_METAACTION."\" value=\"ACT_ADMIN_SUBMIT_CANCEL\" />"
        ."<input type=\"submit\" name=\"action\" value=\"". __('Cancel') ."\"/></p></form>\n"
        ."\n\n</div>\n";

        $ret__ = CST_ADMIN_DOMAIN_NONE;
        break;

    case __('Preview'):
    case 'ACT_ADMIN_PREVIEW_CHANGES':
        rss_error('fixme: preview not yet implemented', RSS_ERROR_ERROR,true);
        break;

    case __('Submit Changes'):
    case 'ACT_ADMIN_SUBMIT_CHANGES':

        $key = sanitize($_POST['key'],RSS_SANITIZER_NO_SPACES|RSS_SANITIZER_SIMPLE_SQL);
        $type = sanitize($_POST['type'],RSS_SANITIZER_CHARACTERS);
        $value = sanitize($_POST['value'], RSS_SANITIZER_SIMPLE_SQL);

        // sanitizine routines for values
        switch ($key) {
        case 'rss.output.title':
            $value = strip_tags($value);
            break;

        case 'rss.config.robotsmeta':
            $value = preg_replace('#[^a-zA-Z,\s]#','',$value);
            break;
        }


        switch ($key) {


        case 'rss.input.allowed':
            $ret = array();
            $tmp = explode(' ',$value);
            foreach ($tmp as $key__) {
                if (preg_match('|^[a-zA-Z]+$|',$key__)) {
                    $ret[$key__] = array();
                } else {
                    $tmp2 = array();
                    $attrs = explode(',',$key__);
                    $key__ = array_shift($attrs);
                    foreach($attrs as $attr) {
                        $tmp2[$attr] = 1;
                    }
                    $ret[$key__] = $tmp2;
                }
            }

            $sql = "update " . getTable('config') . " set value_='"
                   .serialize($ret)
                   ."' where key_='$key'";

            break;

        case 'rss.output.lang':
            $langs = getLanguages();

            $codes = array_keys($langs);
            $out_val = implode(',',$codes);
            $cntr = 0;
            $idx = "0";
            foreach($codes as $code) {
                if ($code == $value) {
                    $idx = $cntr;
                }
                $cntr++;
            }
            $out_val .= ",$idx";
            $sql = "update " . getTable('config') . " set value_='$out_val' where key_='$key'";
            break;

        default:
            switch($type) {
            case 'string':
                $sql = "update " . getTable('config') . " set value_='$value' where key_='$key'";
                break;
            case 'num':
                if (!is_numeric($value)) {
                    rss_error(__("Oops, I was expecting a numeric value, got '$value' instead!"), RSS_ERROR_ERROR,true);
                    break;
                }
                $sql = "update " . getTable('config') . " set value_='$value' where key_='$key'";
                break;
            case 'boolean':
                if ($value != __('True') && $value != __('False')) {
                    rss_error(__("Oops, invalid value for $key : $value"), RSS_ERROR_ERROR,true);
                    break;
                }
                $sql = "update " . getTable('config') . " set value_='"
                       .($value == __('True') ? 'true':'false') ."'"
                       ." where key_='$key'";
                break;
            case 'enum':
                $res  = rss_query( "select value_ from " . getTable('config') . " where key_='$key'" );
                list($oldvalue) = rss_fetch_row($res);

                if (strstr($oldvalue,$value) === FALSE) {
                    rss_error(__("Oops, invalid value '$value' for this config key"), RSS_ERROR_ERROR,true);
                    break;
                }

                $arr = explode(',',$oldvalue);
                $idx = array_pop($arr);
                $newkey = -1;
                foreach ($arr as $i => $val) {
                    if ($val == $value) {
                        $newkey = $i;
                    }
                }
                reset($arr);
                if ($newkey > -1) {
                    array_push($arr, $newkey);
                    $sql =	"update " . getTable('config') . " set value_='"
                           .implode(',',$arr) ."'"
                           ." where key_='$key'";
                } else {
                    rss_error(__("Oops, invalid value '$value' for this config key"), RSS_ERROR_ERROR,true);
                }
                break;
            default:
                rss_error(__('Ooops, unknown config type: ') . $type, RSS_ERROR_ERROR,true);
                //var_dump($_REQUEST);
                break;
            }
        }

        if (isset($sql)) {
            rss_query( $sql );
            rss_invalidate_cache();
        }
        break;
    default:
        break;
    }
    return $ret__;
}

function config_edit_form($key,$value,$default,$type,$desc,$export, & $onclickaction) {
    $value = real_strip_slashes($value);

    echo "<p>\n"
    ."<input type=\"hidden\" name=\"key\" value=\"$key\"/>\n"
    ."<input type=\"hidden\" name=\"type\" value=\"$type\"/>\n"

    .preg_replace('/\s(\w+:\/\/)(\S+)/',
                  ' <a href="\\1\\2">\\1\\2</a>',
                  $desc)

    ."\n</p>\n";
    echo "<p>\n";

    switch($key) {

    case 'rss.input.allowed':

        $arr = unserialize($value);

        echo "</p>\n"
        ."<fieldset class=\"tags\">\n"
        ."<legend>Tags</legend>\n"
        ."<select size=\"8\" name=\"first\" onchange=\"populate2()\">\n"
        ."<option>Your browser doesn't support javascript</option>\n"
        ."</select>\n"
        ."<input type=\"text\" name=\"newtag\" id=\"newtag\" />\n"
        ."<input type=\"button\" onclick=\"add1(); return false;\" value=\"add tag\" />\n"
        ."<input type=\"button\" onclick=\"delete1(); return false;\" value=\"delete tag\" />\n"
        ."</fieldset><fieldset class=\"tags\">\n"
        ."<legend>Attributes</legend>\n"
        ."<select size=\"8\" name=\"second\">\n"
        ."<option>Your browser doesn't support javascript</option>\n"
        ."</select>\n"
        ."<input type=\"text\" name=\"newattr\" id=\"newattr\" />\n"
        ."<input type=\"button\" onclick=\"add2(); return false;\" value=\"add attr\" />"
        . "<input type=\"button\" onclick=\"delete2(); return false;\" value=\"delete attr\" />"
        ."</fieldset>\n"
        ."<p><input type=\"hidden\" name=\"value\" id=\"packed\" value=\"\" />\n"
        ;

        $onclickaction = "pack(); return true";
        //$preview = true;

        echo "<script type=\"text/javascript\">\n"
        ."<!--\n";
        jsCode($arr);
        echo "\n// -->\n";
        echo "</script>\n";

        break;


    case 'rss.output.lang':
        $active_lang = getConfig('rss.output.lang');


        echo "<label for=\"c_value\">". __('Value for') ." $key:</label>\n"
        ."\t\t<select name=\"value\" id=\"c_value\">\n";
        $cntr = 0;
        $value = "";
        $langs = getLanguages();
        foreach ($langs as $code => $info) {
        	if (isset($info['language'])) {
        		$l=$info['language'];
        	} else {
        		$l=$code;
        	}
            echo "<option value=\"$code\"";
            if ($code == $active_lang)   {
                echo " selected=\"selected\"";
            }
            echo ">$l</option>\n";
        }
        echo "</select>\n";
        break;
    default:

        // generic handling per type:
        switch ($type) {
        case 'string':
        case 'num':
            echo "<label for=\"c_value\">". __('Value for') ." $key:</label>\n"
            ."<input type=\"text\" id=\"c_value\" name=\"value\" value=\"$value\"/>";
            break;
        case 'boolean':
            echo __('Value for') ." $key:</p><p>";
            echo "<input type=\"radio\" id=\"c_value_true\" name=\"value\""
            .($value == 'true' ? " checked=\"checked\"":"") .""
            ." value=\"".__('True')."\" "
            ."/>\n"
            ."<label for=\"c_value_true\">" . __('True') . "</label>\n";

            echo "<input type=\"radio\" id=\"c_value_false\" name=\"value\""
            .($value != 'true' ? " checked=\"checked\"":"") .""
            ." value=\"".__('False')."\" "
            ."/>\n"
            ."<label for=\"c_value_false\">" . __('False') . "</label>\n";
            break;
        case 'enum':
            echo "<label for=\"c_value\">". __('Value for') ." $key:</label>\n"
            ."\t\t<select name=\"value\" id=\"c_value\">\n";
            $arr = explode(',',$value);
            $idx = array_pop($arr);
            foreach ($arr as $i => $val) {
                echo "<option value=\"$val\"";
                if ($i == $idx)
                    echo " selected=\"selected\"";
                echo ">$val</option>\n";
            }
            echo "</select>\n";
            break;
        }
    }

    echo "</p>\n";
}

function config_default_form($key, $type, $default, $adminDomain) {
    switch ($type) {
    case 'enum':
        $html_default = admin_enum_to_html(explode(',',$default));
        break;
    case 'array':
        $html_default = admin_kses_to_html(unserialize($default));
        break;
    default:
        $html_default = $default;
        break;
    }

    echo "<p class=\"error\">";
    printf(__("Are you sure you wish to reset the value for %s to its default '%s'?"),$key,$html_default);
    echo "</p>\n"
    ."<p><input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". __('No') ."\"/>\n"
    ."<input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". __('Yes') ."\"/>\n"
    ."<input type=\"hidden\" name=\"key\" value=\"$key\"/>\n"
    ."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".$adminDomain."\"/>\n"
    ."<input type=\"hidden\" name=\"action\" value=\"". CST_ADMIN_DEFAULT_ACTION ."\"/>\n"
    ."</p>\n";
}

function sysinfo() {
    echo "<pre>\n";
    echo "PHP version: ".phpversion()."\n\n";

    if (function_exists("php_uname")) {
        echo "System: "	. php_uname() ."\n\n";
    }

    echo "Loaded PHP extensions:\n";
    foreach (get_loaded_extensions() as $ext) {
        echo " - $ext: (".phpversion($ext).")\n";
    }

    echo "\nPHP Settings:\n";
    foreach (ini_get_all() as $key => $val) {
        echo " - $key:\n\tglobal:".$val['global_value']."\n\tlocal: ".$val['local_value']."\n\n";
    }

    if (function_exists("apache_get_modules")) {
        echo "\nApache modules:\n";
        foreach (apache_get_modules() as $mod) {
            echo " - $mod \n";
        }
    }
    echo "</pre>\n";
}

?>
