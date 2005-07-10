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


/// Name: Extra Button
/// Author: Marco Bonetti
/// Description: Adds an extra "Mark Feed As Read" button at the bottom of each feed view
/// Version: $Revision: 1.8

function __extra_button_Button($in) {
	if (defined('MARK_READ_ALL_FORM') 		|| 
		 defined ('MARK_READ_FEED_FORM') 	|| 
		 defined ('MARK_READ_FOLDER_FORM')) {
        
        echo "<div style=\"text-align:right\">\n";
        
        if (defined('MARK_READ_ALL_FORM')) {
        		markAllReadForm();
        } elseif(defined('MARK_READ_FEED_FORM')) {
        		markReadForm(MARK_READ_FEED_FORM);
        } elseif(defined('MARK_READ_FOLDER_FORM')) {
        		markFolderReadForm(MARK_READ_FOLDER_FORM);
        }
        echo "</div>\n";
    }
	return null;
}

rss_set_hook('rss.plugins.items.afteritems','__extra_button_Button');
?>
