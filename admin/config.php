<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
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
# E-mail:	   mbonetti at users dot sourceforge dot net
# Web page:	   http://sourceforge.net/projects/gregarius
#
###############################################################################

/*************** Config management ************/

function config() {
	echo "<h2 class=\"trigger\">".LBL_ADMIN_CONFIG."</h2>\n"
	  ."<div id=\"admin_config\" class=\"trigger\">\n";

	echo "<table id=\"configtable\">\n"
	  ."<tr>\n"
	  ."\t<th>". LBL_ADMIN_CHANNELS_HEADING_KEY ."</th>\n"
	  ."\t<th>". LBL_ADMIN_CHANNELS_HEADING_VALUE ."</th>\n"
	  ."\t<th>". LBL_ADMIN_CHANNELS_HEADING_DESCR ."</th>\n"
	  ."\t<th class=\"cntr\">". LBL_ADMIN_CHANNELS_HEADING_ACTION ."</th>\n"
	  ."</tr>\n";

	$sql = "select * from " .getTable("config") ." order by key_ asc";

	$res = rss_query($sql);
	$cntr = 0;
	while ($row = rss_fetch_assoc($res)) {
		$value =  real_strip_slashes($row['value_']);
		$class_ = (($cntr++ % 2 == 0)?"even":"odd");
	
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
		 	$arr = unserialize($value);
		 	echo admin_plugins_mgmnt($arr);
		 	break;
		
		 case 'rss.output.lang':
		 	$arr = getLanguages();
            echo $arr[getConfig('rss.output.lang')];
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
		  ."<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_CONFIG
		  ."&amp;action=". CST_ADMIN_EDIT_ACTION. "&amp;key=".$row['key_']."\">" . LBL_ADMIN_EDIT
		  ."</a>";
	
		if ($row['value_'] != $row['default_'] && $row['key_'] != 'rss.config.plugins') {
			echo "|"
	
			  ."<a href=\"".$_SERVER['PHP_SELF']. "?".CST_ADMIN_DOMAIN."=". CST_ADMIN_DOMAIN_CONFIG
			  ."&amp;action=". CST_ADMIN_DEFAULT_ACTION. "&amp;key=".$row['key_']."\">" . LBL_ADMIN_DEFAULT
			  ."</a>";
		}
	
		echo "</td>\n"
		  ."</tr>\n";
	
	}
	echo "</table>";
	echo "</div>\n";
}

function config_admin() {

	$ret__ = CST_ADMIN_DOMAIN_CONFIG;

	switch ($_REQUEST['action']) {

	 case CST_ADMIN_DEFAULT_ACTION:
		if (!array_key_exists('key',$_REQUEST)) {
			rss_error('Invalid config key specified.');
			break;
		}
		$key = $_REQUEST['key'];
		$res = rss_query("select value_,default_,type_ from " .getTable('config') . " where key_='$key'");
		list($value,$default,$type) = rss_fetch_row($res);
		$value = real_strip_slashes($value);
		$default = real_strip_slashes($default);
	
		if ($value == $default) {
			rss_error("The value for '$key' is the same as its default value!");
			break;
		}
	
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
	
		if (array_key_exists(CST_ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_YES) {
			rss_query("update " . getTable('config') ." set value_=default_ where key_='$key'" );
		} elseif (array_key_exists(CST_ADMIN_CONFIRMED,$_REQUEST) && $_REQUEST[CST_ADMIN_CONFIRMED] == LBL_ADMIN_NO) {
			//nop
		} else {
	
			echo "<form class=\"box\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
			  ."<p class=\"error\">"; printf(LBL_ADMIN_ARE_YOU_SURE_DEFAULT,$key,$html_default); echo "</p>\n"
			  ."<p><input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". LBL_ADMIN_NO ."\"/>\n"
			  ."<input type=\"submit\" name=\"".CST_ADMIN_CONFIRMED."\" value=\"". LBL_ADMIN_YES ."\"/>\n"
			  ."<input type=\"hidden\" name=\"key\" value=\"$key\"/>\n"
			  ."<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"".CST_ADMIN_DOMAIN_CONFIG."\"/>\n"
			  ."<input type=\"hidden\" name=\"action\" value=\"". CST_ADMIN_DEFAULT_ACTION ."\"/>\n"
			  ."</p>\n</form>\n";
	
			$ret =	CST_ADMIN_DOMAIN_NONE;
		}
		break;

	 case CST_ADMIN_EDIT_ACTION:
		$key_ = $_REQUEST['key'];
		$res = rss_query("select * from ". getTable('config') . " where key_ ='$key_'");
		list($key,$value,$default,$type,$desc,$export) =  rss_fetch_row($res);
		$value = real_strip_slashes($value);
	
		echo "<div>\n";
		echo "\n\n<h2>Edit '$key'</h2>\n";
		echo "<form id=\"cfg\" method=\"post\" action=\"" .$_SERVER['PHP_SELF'] ."\">\n"
		  ."<p>\n<input type=\"hidden\" name=\"".CST_ADMIN_DOMAIN."\" value=\"". CST_ADMIN_DOMAIN_CONFIG."\"/>\n"
		  ."<input type=\"hidden\" name=\"key\" value=\"$key\"/>\n"
		  ."<input type=\"hidden\" name=\"type\" value=\"$type\"/>\n"
	
		  .preg_replace('/\s(\w+:\/\/)(\S+)/',
				' <a href="\\1\\2">\\1\\2</a>',
				$desc)
	
			."\n</p>\n"
		  ."<p>\n";
	
		switch($key) {


         case 'rss.config.plugins':
         	echo "<input type=\"hidden\" name=\"value\" value=\"\" />\n";
         	echo "</p>\n<table id=\"plugintable\">\n<tr>\n"
					."<th>".LBL_ADMIN_PLUGINS_HEADING_ACTION."</th>\n"
         		."<th>".LBL_ADMIN_PLUGINS_HEADING_NAME."</th>\n"
					."<th>".LBL_ADMIN_PLUGINS_HEADING_VERSION."</th>\n"
					."<th>".LBL_ADMIN_PLUGINS_HEADING_AUTHOR."</th>\n"
					."<th>".LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION."</th>\n"
         		."</tr>\n";
         		
         	$active_plugins= getConfig('rss.config.plugins');
         	$cntr = 0;
            $d = dir('../plugins');
            $files = array();
            while (false !== ($entry = $d->read())) {
               if (
                $entry != "CVS" &&              
                substr($entry,0,1) != "."                
               ) {
               		$info = getPluginInfo($entry);
               		$active= in_array($entry,$active_plugins);

               		if (count($info)) {
               			echo "<tr class=\""
               				.(($cntr++ % 2 == 0)?"even":"odd")
               				.($active?" active":"")
               				."\">\n";               		
               			echo "<td class=\"cntr\">" 
          					."<input type=\"checkbox\" name=\"_gregarius_plugin_$entry\" "
          					." id=\"_gregarius_plugin_$entry\" value=\"1\" "
          					.($active?"checked=\"checked\"":"")." />\n"
          					."</td>\n";  
               			echo "<td><label for=\"_gregarius_plugin_$entry\">".(array_key_exists('name',$info)?$info['name']:"&nbsp"). "</label></td>\n";
               			echo "<td class=\"cntr\">"	.(array_key_exists('version',$info)?$info['version']:"&nbsp"). "</td>\n";
               			echo "<td>"	.(array_key_exists('author',$info)?$info['author']:"&nbsp"). "</td>\n";
               			echo "<td>"	.(array_key_exists('description',$info)?$info['description']:"&nbsp"). "</td>\n";
     			
               			echo "</tr>\n";
               		}
               }
            }
            $d->close();
            echo "</table>\n<p>";        
            
         break;
         
         case 'rss.output.theme':

        	$themes = getThemes();
        	echo "<input type=\"hidden\" name=\"value\" value=\"\" />\n";
         	echo "</p>\n<table id=\"plugintable\">\n<tr>\n"
					."<th>".LBL_ADMIN_PLUGINS_HEADING_ACTION."</th>\n"
         		."<th>".LBL_ADMIN_PLUGINS_HEADING_NAME."</th>\n"
					."<th>".LBL_ADMIN_PLUGINS_HEADING_VERSION."</th>\n"
					."<th>".LBL_ADMIN_PLUGINS_HEADING_AUTHOR."</th>\n"
					."<th>".LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION."</th>\n"
         		."</tr>\n";

         	$active_theme= getConfig('rss.output.theme');
         	$cntr = 0;
         	foreach ($themes as $entry => $theme) {

							extract($theme);
							if (!$name) {
								$name = $entry;
							}
							if ($url) {
								$author = "<a href=\"$url\">$author</a>";
							}
							$active = ($entry ==  $active_theme);
									echo "<tr class=\""
										.(($cntr++ % 2 == 0)?"even":"odd")
										.($active?" active":"")
										."\">\n";
									echo "<td class=\"cntr\">"
									."<input type=\"radio\" name=\"value\" "
									." id=\"_gregarius_theme_$entry\" value=\"$entry\" "
									.($active?" checked=\"checked\"":"")
								.(!$htmltheme?" disabled=\"disabled\"":"")
									." />\n"
									."</td>\n";
									echo "<td><label for=\"_gregarius_theme_$entry\">".($name?$name:"&nbsp"). "</label></td>\n";
									echo "<td class=\"cntr\">".($version?$version:"&nbsp"). "</td>\n";
									echo "<td>"	.($author?$author:"&nbsp") . "</td>\n";
									echo "<td>"	.($description?$description:"&nbsp"). "</td>\n";
			
									echo "</tr>\n";
						}
       	    echo "</table>\n<p>";


         break;
         
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
         	
         	
            echo "<label for=\"c_value\">". LBL_ADMIN_CONFIG_VALUE ." $key:</label>\n"
    		  ."\t\t<select name=\"value\" id=\"c_value\">\n";
         	$cntr = 0;
         	$value = "";
         	$langs = getLanguages();
         	foreach ($langs as $code => $name) {
       			echo "<option value=\"$code\"";
    			if ($code == $active_lang)   {
    			  echo " selected=\"selected\"";
                }
    			echo ">".$langs[$code]."</option>\n";
            }
    		echo "</select>\n";
		break;
         
	 default:

		// generic handling per type:
		switch ($type) {
		 case 'string':
		 case 'num':
		echo "<label for=\"c_value\">". LBL_ADMIN_CONFIG_VALUE ." for $key:</label>\n"
		  ."<input type=\"text\" id=\"c_value\" name=\"value\" value=\"$value\"/>";
		break;
		 case 'boolean':
		echo LBL_ADMIN_CONFIG_VALUE ." for $key:</p><p>";
		echo "<input type=\"radio\" id=\"c_value_true\" name=\"value\""
		  .($value == 'true' ? " checked=\"checked\"":"") .""
		  ." value=\"".LBL_ADMIN_TRUE."\" "
		  ."/>\n"
		  ."<label for=\"c_value_true\">" . LBL_ADMIN_TRUE . "</label>\n";

		echo "<input type=\"radio\" id=\"c_value_false\" name=\"value\""
		  .($value != 'true' ? " checked=\"checked\"":"") .""
		  ." value=\"".LBL_ADMIN_FALSE."\" "
		  ."/>\n"
		  ."<label for=\"c_value_false\">" . LBL_ADMIN_FALSE . "</label>\n";
		break;
		 case 'enum':
		echo "<label for=\"c_value\">". LBL_ADMIN_CONFIG_VALUE ." for $key:</label>\n"
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

	echo "</p><p>\n";
	echo (isset($preview)?"<input type=\"submit\" name=\"action\" value=\"". LBL_ADMIN_PREVIEW_CHANGES ."\""
		  .(isset($onclickaction)?" onclick=\"$onclickaction\"":"") ." />\n":"");

	echo "<input type=\"submit\" name=\"action\" value=\"". LBL_ADMIN_SUBMIT_CHANGES ."\""
	  .(isset($onclickaction)?" onclick=\"$onclickaction\"":"")
		." />\n";

	echo "<input type=\"submit\" name=\"action\" value=\"". LBL_ADMIN_CANCEL ."\"/>\n"
	  ."</p>\n"
	  ."</form>\n\n</div>\n";

	$ret__ = CST_ADMIN_DOMAIN_NONE;
	break;

	case LBL_ADMIN_PREVIEW_CHANGES:
		rss_error('fixme: preview not yet implemented');
		break;
	case LBL_ADMIN_SUBMIT_CHANGES:
		$key = $_REQUEST['key'];
		$type = $_REQUEST['type'];
		$value = rss_real_escape_string($_REQUEST['value']);

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
		
	 case 'rss.config.plugins':
	 	$active=array();
	 	foreach($_REQUEST as $rkey=>$rentry) {
	 		if (preg_match('/_gregarius_plugin.([a-zA-Z0-9_]+).php/',$rkey,$matches)) {
	 			$active[] = ($matches[1] .".php");
	 		}
	 	}
	 	$value = serialize($active);
	 	$sql = "update " . getTable('config') . " set value_='$value' where key_='$key'";
	 	
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
					rss_error("Oops, I was expecting a numeric value, got '$value' instead!");
					break;
				}
				$sql = "update " . getTable('config') . " set value_='$value' where key_='$key'";
				break;
			case 'boolean':
				if ($value != LBL_ADMIN_TRUE && $value != LBL_ADMIN_FALSE) {
				rss_error('Oops, invalid value for ' . $key .": " . $value);
				break;
		}
		$sql = "update " . getTable('config') . " set value_='"
		  .($value == LBL_ADMIN_TRUE ? 'true':'false') ."'"
		  ." where key_='$key'";
		break;
		 case 'enum':
		$res  = rss_query( "select value_ from " . getTable('config') . " where key_='$key'" );
		list($oldvalue) = rss_fetch_row($res);

		if (strstr($oldvalue,$value) === FALSE) {
			rss_error("Oops, invalid value '$value' for this config key");
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
			rss_error("Oops, invalid value '$value' for this config key");
		}
		break;
		 default:
		rss_error('Ooops, unknown config type: ' . $type);
		var_dump($_REQUEST);
		break;
		}
	}

	if (isset($sql)) {
		rss_query( $sql );
	}
	break;
	default: break;
	}
	return $ret__;
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
