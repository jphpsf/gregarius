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

require_once('init.php');
$__rss_hooks = array();

function loadActivePlugins() {
	 foreach(getConfig('rss.config.plugins') as $pf) {
		  if (file_exists('plugins/'.$pf)) {
				require_once("plugins/$pf");
		  }
	 }	 	 
}


function rss_set_hook($hook,$fnct) {
	 global $__rss_hooks;
	 if (array_key_exists($hook, $__rss_hooks)) {
		  $__rss_hooks[$hook][] = $fnct;
	 } else {
		  $__rss_hooks[$hook]=array($fnct);
	 }
}


function rss_plugin_hook($hook, $data) {
	 global $__rss_hooks;
	 if (array_key_exists($hook, $__rss_hooks)) {
		  foreach($__rss_hooks[$hook] as $fnct) {
				if (function_exists($fnct)) {
					 $data = call_user_func($fnct,$data);
				}
		  }
	 }
	 return $data;
}
loadActivePlugins();
?>
