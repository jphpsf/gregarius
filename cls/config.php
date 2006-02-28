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
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################

rss_require('util.php');
rss_require('cls/wrappers/config.php');

$GLOBALS['rss'] -> config = new Configuration();

class Configuration {

    var $_config = null;
    var $_properties = null;
	var $_propCahceHits = null;
	var $_propCacheMisses = null;
	
    function Configuration() {
        $this -> _populate();
        $this -> _populateProperties();
    }


    function _populate() {
        $this -> _config = null;
        $cfgQry = "select key_,value_,default_,type_,desc_,export_ "
                  ." from " .getTable("config");

        $res = rss_query($cfgQry);

        $this -> _config = array();
        while (list($key_,$value_,$default_,$type_,$description_,$export_) = rss_fetch_row($res)) {
        $value_ = real_strip_slashes($value_);
            $real_value = $this -> configRealValue($value_,$type_);
            $this -> _config[$key_] =
                array(
                    'value' => $real_value,
                    'default' => $default_,
                    'type' => $type_,
                    'description' => $description_
                );
            if ($export_ != '' && !defined($export_)) {
                define ($export_,(string)$real_value);
            }
        }
    }
    
    function _populateProperties() {
    	_pf('Populating properties');
    	$this -> _properties = array();
    	$qry = "select fk_ref_object_id, proptype, property, value from "
    		.getTable('properties');
    	$rs = rss_query($qry);
    	while (list($ref_obj, $ptype,$prop, $pval)=rss_fetch_row($rs)) {
    		if (!isset($this -> _properties[$ptype])) {
    			$this -> _properties[$ptype] = array();
    		}
    		$val = @unserialize($pval);
    		$this -> _properties[$ptype][$ref_obj] = array(
    			'ref_obj' => $ref_obj,
    			'property' => $prop,
    			'value' => $val
    		);
    	}
		_pf('Done: populating properties');
    }
    
    function getProperties($prop,$type) {
    	$ret = array();
    	if (!isset($this -> _properties[$type])) {
    		return $ret;
    	}
    	
    	foreach($this -> _properties[$type] as $ref_obj => $_prop) {
    		if ($_prop['property'] == $prop) {
    			$ret[$ref_obj] = $_prop['value'];
    		}
    	}
    	return $ret;
    }
    
    function getProperty($ref_obj, $prop) {

    	foreach($this -> _properties as $type => $props) {
    		if (!isset($this -> _properties[$type][$ref_obj])) {
    			continue;
    		}
    		foreach($props as $ref => $_props) {
    			if ($ref == $ref_obj && $_props['property'] == $prop) {
    				return $_props['value'];
    			}
    		}
    	}
    	return null;
    }
	
	function getObjectsHavingProperty($prop, $type, $value) {
		if (!isset($this -> _properties[$type])) {
			return array();
		}
	
		$ret = array();
		foreach ($this -> _properties[$type] as $ref_obj => $_data) {
			if ($_data['property'] == $prop && $value == $_data['value']) {
				$ret[] = $ref_obj;
			}
		}
		return $ret;
	}

	function deleteProperty($ref_obj, $prop) {
		rss_query( "delete from " . getTable('properties') 
			. " where fk_ref_object_id = '$ref_obj'"
			." and property='$prop'" );
		$this -> _populateProperties();
		rss_invalidate_cache();
    }

		function setProperty($ref_obj, $prop, $type, $value) {
			$val = @serialize($value);
			if (!$val) {
				return false;
			}
			$val = rss_real_escape_string($val);

		   $res = rss_query('SELECT count(fk_ref_object_id) FROM ' 
		   .getTable('properties')
		   ." WHERE fk_ref_object_id = '$ref_obj' AND proptype = '$type'"
		   ." AND property = '$prop'");
			list ($cnt_rows) = rss_fetch_row($res);

			if ($cnt_rows) {
				   rss_query('UPDATE ' 
				   .getTable('properties') 
				   ." SET value = '$val' WHERE fk_ref_object_id = '$ref_obj' AND proptype = '$type'"
				   ." AND property = '$prop'");
		   	} else {
				   rss_query('insert into ' 
				   .getTable('properties') 
				   .'(fk_ref_object_id, proptype, property, value) values ('
				   ."'$ref_obj','$type','$prop','$val'"
				   .')');
		   	}
		   	
		   	
			$this -> _populateProperties();
			rss_invalidate_cache();
			return true;
		}

    function getConfig($key,$allowRecursion = true, $invalidateCache = false) {
        if (defined('RSS_CONFIG_OVERRIDE_' . strtoupper(preg_replace('/\./','_',$key)))) {
            return constant('RSS_CONFIG_OVERRIDE_' . strtoupper(preg_replace('/\./','_',$key)));
        }

        if (array_key_exists($key,$this -> _config)) {
            return $this -> _config[$key]['value'];
        }
        elseif($allowRecursion) {
            rss_require('schema.php');
            $this -> _config = null;
            setDefaults($key);
            $this -> _populate();
            return $this -> getConfig($key,false);
        }

        return null;
    }


    function configInvalidate() {
        getConfig('dummy',true,true);
    }



    function configRealValue($value_,$type_) {
        $real_value = null;
        switch ($type_) {
        case 'boolean':
                $real_value = ($value_ == 'true');
            break;

        case 'array':
            $real_value=unserialize($value_);
            break;

        case 'enum':
            $tmp = explode(',',$value_);
            $idx = array_pop($tmp);
            $real_value = $tmp[$idx];
            break;

        case 'num':
        case 'string':
        default:
            $real_value = $value_;
            break;
        }
        return $real_value;
    }

    /**
    * Theme wrapper function to override config options
      Returns true if the config value was overridden. (otherwise it returns false)
    **/
    function rss_config_override($key, $value) {
        $confKey = 'RSS_CONFIG_OVERRIDE_' . strtoupper(preg_replace('/\./','_',$key));
        $retValue = false;
        if (!defined($confKey)) {
            define($confKey, $value);
            $retValue = true;
        }
        return $retValue;
    }

}

?>
