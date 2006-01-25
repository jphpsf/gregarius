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

function getConfig($key,$allowRecursion = true, $invalidateCache = false) {
	return $GLOBALS['rss'] -> config -> getConfig($key,$allowRecursion, $invalidateCache);
}

function configInvalidate() {
	getConfig('dummy',true,true);
}

function configRealValue($value_,$type_) {
	return $GLOBALS['rss'] -> config ->configRealValue($value_,$type_);
}

/**
* Theme wrapper function to override config options
  Returns true if the config value was overridden. (otherwise it returns false)
**/
function rss_config_override($key, $value) {
	return $GLOBALS['rss'] -> config -> rss_config_override($key, $value);
}

/////////// Properties //////////////////////////////////////////////////

/**
 * Returns the property 'prop' of the object 'ref_obj'
 */
function getProperty($ref_obj, $prop) {
	return $GLOBALS['rss'] -> config ->getProperty($ref_obj, $prop);
}

/**
 * Returns an array of the ID's (and their value) of all objects having 
 * the property 'prop' of type 'type'
 */
function getProperties($prop,$type) {
	return $GLOBALS['rss'] -> config -> getProperties($prop,$type);
}
    
/**
 * Sets the property 'prop' of type 'type' of the object having an id 'ref_obj'
 * to 'value', replaces the property if it already exists
 */
function setProperty($ref_obj, $prop, $type, $value) {
	return $GLOBALS['rss'] -> config -> setProperty($ref_obj, $prop, $type, $value);
}

/**
 * Returns an array of ID's of the objects having their property 'prop' of type
 * 'type' set to 'value'
 */
function getObjectsHavingProperty($prop, $type, $value) {
	return $GLOBALS['rss'] -> config ->getObjectsHavingProperty($prop, $type, $value);
}

/**
 * Deletes the property 'prop' of the object 'ref_obj'
 */
function deleteProperty($ref_obj, $prop) {
	return $GLOBALS['rss'] -> config ->deleteProperty($ref_obj, $prop);
}

?>