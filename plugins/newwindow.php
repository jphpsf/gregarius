<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
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
# Revision 1.4  2005/05/20 07:42:21  mbonetti
# CVS Log messages in the file header
#
#
###############################################################################


/// Name: New window
/// Author: Marco Bonetti
/// Description: When active, this plugin will open off-site links in a new window
/// Version: $Revision$


function __new_window_js($dummy) {

  echo "<script type=\"text/javascript\">\n"
	 ."// <!--\n"
	 ."if (document.getElementsByTagName) {\n"
	 ."var anchors = document.getElementById('items').getElementsByTagName('a');\n"
	 ."for (var i=0; i<anchors.length; i++) {\n"
	 ."var anchor = anchors[i];\n"
	 ."if (anchor.href &&\n"
	 ."(anchor.href.indexOf('".$_SERVER['HTTP_HOST']."') < 0)\n"
	 .") {\n"
	 ."anchor.target = '_blank';\n"
	 ."}\n"
	 ."}\n"
	 ."}\n"
	 ."// -->\n"
	 ."</script>\n";
			 
	 return null;
}

rss_set_hook('rss.plugins.bodyend','__new_window_js');

?>