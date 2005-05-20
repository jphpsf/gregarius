<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
#
###############################################################################
# File: $Id$ $Name$
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
# E-mail:      mbonetti at users dot sourceforge dot net
# Web page:    http://sourceforge.net/projects/gregarius
#
###############################################################################
# $Log$
# Revision 1.53  2005/05/20 07:33:17  mbonetti
# Do not redefine exported variables when recursing on config reading
#
###############################################################################

rss_require('util.php');

function getConfig($key,$allowRecursion = true) {
   static $config;
	if ($config == null) {
		$cfgQry = "select key_,value_,default_,type_,desc_,export_ "
		  ." from " .getTable("config");
		
		$res = rss_query($cfgQry);		
		
		$config = array();
		while (list($key_,$value_,$default_,$type_,$description_,$export_) = rss_fetch_row($res)) {
			$value_ = real_strip_slashes($value_);
			$real_value = configRealValue($value_,$type_);
			$config[$key_] =
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
    
    if (array_key_exists($key,$config)) {
    	return $config[$key]['value'];
    } elseif($allowRecursion) {
    	rss_require('schema.php');
    	$config = null;
		setDefaults($key);
		return getConfig($key,false);
    }
    
    return null;
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
?>
