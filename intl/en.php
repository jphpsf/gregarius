<?php
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





define ('MARK_READ', "Mark all as read");
define ('MARK_CHANNEL_READ', "Mark this feed as read");
define ('SEE_ALL_UNREAD', "See all %d unread items");
define ('SEE_ONLY_UNREAD', "Display only unread items (%d)");
define ('SEE_ALL', "See all %d items for this feed");
define ('H2_SEARCH_RESULTS_FOR', "%d matches for %s");
define ('H2_SEARCH_RESULT_FOR',"%d match for %s");
define ('H2_SEARCH', 'Search %d items');
define ('SEARCH_SEARCH_QUERY','Search terms:');
define ('SEARCH_MATCH_OR', 'Some terms (OR)');
define ('SEARCH_MATCH_AND', 'All terms (AND)');                                                                 
define ('SEARCH_MATCH_EXACT', 'Exact match');
define ('SEARCH_CHANNELS', 'Feed:');
define ('SEARCH_ORDER_DATE_CHANNEL','Order by date, feed');
define ('SEARCH_ORDER_CHANNEL_DATE','Order by feed, date');

define ('H2_UNREAD_ITEMS',"Unread items (%d)");
define ('H2_RECENT_ITEMS', "Recent items");
define ('H2_CHANNELS','Feeds');
define ('H5_READ_UNREAD_STATS','%d items, %d unread');
define ('ITEMCOUNT_PF', '<strong>%d</strong> items (<strong>%d</strong> unread) in <strong>%d</strong> feeds');
define ('UNREAD_PF', '(<strong>%d unread</strong>)');

define ('FTR_POWERED_BY', ", powered by ");
define ('ALL','All');
define ('NAV_HOME','Home');
define ('NAV_UPDATE', 'Refresh');
define ('NAV_CHANNEL_ADMIN', 'Admin');
define ('NAV_SEARCH', "Search");
define ('SEARCH_GO', 'Search');

define ('POSTED', 'Posted: ');
define ('FETCHED','Fetched: ');

define ('TITLE_UPDATING','Updating');
define ('TITLE_SEARCH','Search');
define ('TITLE_ADMIN','Feeds Admin');


define ('HOME_FOLDER','Root');
define ('VISIT', '(visit)');
define ('COLLAPSE','collapse');
define ('EXPAND','expand');
define ('PL_FOR','Permalink for ');

define ('UPDATE_CHANNEL','Feed');
define ('UPDATE_STATUS','Status');
define ('UPDATE_UNDREAD','New Items');

define ('UPDATE_STATUS_OK','OK (HTTP 200)');
define ('UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('UPDATE_STATUS_ERROR','ERROR');
define ('UPDATE_H2','Updating %d Feeds...');
define ('UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('UPDATE_NOT_MODIFIED','OK (304 Not modified)');

// admin
define ('ADMIN_EDIT', 'edit');
define ('ADMIN_DELETE', 'delete');
define ('ADMIN_DELETE2', 'Delete');
define ('ADMIN_RENAME', 'Rename to...');
define ('ADMIN_CREATE', 'Create');
define ('ADMIN_IMPORT','Import');
define ('ADMIN_EXPORT','Export');
define ('ADMIN_ADD','Add');
define ('ADMIN_YES', 'Yes');
define ('ADMIN_NO', 'No');
define ('ADMIN_FOLDERS','Folders:');
define ('ADMIN_CHANNELS','Feeds:');
define ('ADMIN_OPML','OPML:');
define ('ADMIN_IN_FOLDER','to folder:');
define ('ADMIN_SUBMIT_CHANGES', 'Submit Changes');
define ('ADMIN_CHANNELS_HEADING_TITLE','Title');
define ('ADMIN_CHANNELS_HEADING_FOLDER','Folder');
define ('ADMIN_CHANNELS_HEADING_DESCR','Description');
define ('ADMIN_CHANNELS_HEADING_MOVE','Move');
define ('ADMIN_CHANNELS_HEADING_ACTION','Action');
define ('ADMIN_CHANNELS_ADD','Add a feed:');
define ('ADMIN_FOLDERS_ADD','Add a folder:');
define ('ADMIN_CHANNEL_ICON','Shown favicon:');
define ('CLEAR_FOR_NONE','(Leave blank for no icon)');
define ('ADMIN_OPML_EXPORT','Export OPML:');
define ('ADMIN_OPML_IMPORT','Import OPML:');

define ('ADMIN_CHANNEL_NAME','Title:');
define ('ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('ADMIN_CHANNEL_SITE_URL','Site URL:');
define ('ADMIN_CHANNEL_FOLDER','In folder:');
define ('ADMIN_CHANNEL_DESCR','Description:');
define ('ADMIN_FOLDER_NAME','Folder name:');
define ('ADMIN_ARE_YOU_SURE', "Are you sure you wish to delete '%s'?");

define('ADMIN_MOVE_UP','&uarr;');
define('ADMIN_MOVE_DOWN','&darr;');
define('ADMIN_ADD_CHANNEL_EXPL','(Enter either the URL of an RSS feed or of a Website whose feed you wish to subscribe)');
define('ADMIN_FEEDS','The following feeds were found in <a href="%s">%s</a>, which one would you like to subscribe?');
?>
