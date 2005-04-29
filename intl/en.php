<?php
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

define ('ITEM','item');
define ('ITEMS','items');
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
define ('SEARCH_RESULTS_PER_PAGE','Results per page:');
define ('SEARCH_RESULTS','Results: ');
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
define ('NAV_DEVLOG', "Devlog");
define ('SEARCH_GO', 'Search');

define ('POSTED', 'Posted: ');
define ('FETCHED','Fetched: ');

define ('LBL_AND','and');

define ('TITLE_UPDATING','Updating');
define ('TITLE_SEARCH','Search');
define ('TITLE_ADMIN','Feeds Admin');


define ('HOME_FOLDER','Root');
define ('VISIT', '(visit)');
define ('COLLAPSE','[-] collapse');
define ('EXPAND','[+] expand');
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
define ('UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('ADMIN_EDIT', 'edit');
define ('ADMIN_DELETE', 'delete');
define ('ADMIN_DELETE2', 'Delete');
define ('ADMIN_RENAME', 'Rename to...');
define ('ADMIN_CREATE', 'Create');
define ('ADMIN_IMPORT','Import');
define ('ADMIN_EXPORT','Export');
define ('ADMIN_DEFAULT','default');
define ('ADMIN_ADD','Add');
define ('ADMIN_YES', 'Yes');
define ('ADMIN_NO', 'No');
define ('ADMIN_FOLDERS','Folders:');
define ('ADMIN_CHANNELS','Feeds:');
define ('ADMIN_OPML','OPML:');  
define ('ADMIN_ITEM','Items:');
define ('ADMIN_CONFIG','Configuration:');

define ('ADMIN_IN_FOLDER','to folder:');
define ('ADMIN_SUBMIT_CHANGES', 'Submit Changes');
define ('ADMIN_PREVIEW_CHANGES','Preview');
define ('ADMIN_CANCEL','Cancel');
define ('ADMIN_CHANNELS_HEADING_TITLE','Title');
define ('ADMIN_CHANNELS_HEADING_FOLDER','Folder');
define ('ADMIN_CHANNELS_HEADING_DESCR','Description');
define ('ADMIN_CHANNELS_HEADING_MOVE','Move');
define ('ADMIN_CHANNELS_HEADING_ACTION','Action');
define ('ADMIN_CHANNELS_HEADING_KEY','Key');
define ('ADMIN_CHANNELS_HEADING_VALUE','Value');
define ('ADMIN_CHANNELS_ADD','Add a feed:');
define ('ADMIN_FOLDERS_ADD','Add a folder:');
define ('ADMIN_CHANNEL_ICON','Shown favicon:');
define ('CLEAR_FOR_NONE','(Leave blank for no icon)');
define ('ADMIN_OPML_EXPORT','Export OPML:');
define ('ADMIN_OPML_IMPORT','Import OPML:');
define ('ADMIN_CONFIG_VALUE','Value');

define ('ADMIN_CHANNEL_NAME','Title:');
define ('ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('ADMIN_CHANNEL_SITE_URL','Site URL:');
define ('ADMIN_CHANNEL_FOLDER','In folder:');
define ('ADMIN_CHANNEL_DESCR','Description:');
define ('ADMIN_FOLDER_NAME','Folder name:');
define ('ADMIN_ARE_YOU_SURE', "Are you sure you wish to delete '%s'?");
define ('ADMIN_ARE_YOU_SURE_DEFAULT','Are you sure you wish to reset the value for %s to its default \'%s\'?');
define ('ADMIN_TRUE','True');
define ('ADMIN_FALSE','False');
define ('ADMIN_MOVE_UP','&uarr;');
define ('ADMIN_MOVE_DOWN','&darr;');
define ('ADMIN_ADD_CHANNEL_EXPL','(Enter either the URL of an RSS feed or of a Website whose feed you wish to subscribe)');
define ('ADMIN_FEEDS','The following feeds were found in <a href="%s">%s</a>, which one would you like to subscribe?');

define ('ADMIN_PRUNE_OLDER','Delete items older than ');
define ('ADMIN_PRUNE_DAYS','days');
define ('ADMIN_PRUNE_MONTHS','months');
define ('ADMIN_PRUNE_YEARS','years');
define ('PRUNE_KEEP','Keep the most recent items: ');
define ('ADMIN_ABOUT_TO_DELETE','Warning: you are about to delete %s items (of %s)');
define ('ADMIN_PRUNING','Pruning');
define ('ADMIN_DOMAIN_FOLDER_LBL','folders');
define ('ADMIN_DOMAIN_CHANNEL_LBL','feeds');
define ('ADMIN_DOMAIN_ITEM_LBL','items');
define ('ADMIN_DOMAIN_CONFIG_LBL','config');
define ('ADMIN_DOMAIN_OPML_LBL','opml');
define ('ADMIN_BOOKMARKET_LABEL','Subscription bookmarklet [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('ADMIN_BOOKMARKLET_TITLE','Subscribe in Gregarius!');


define ('ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Not Authorized!</h1>\nYou are not authorized to access the administration interface.\n"
		."Please follow <a href=\"%s\">this link</a> back to the main page.\n"
		."Have  a nice day!");
		
define ('ADMIN_ERROR_PRUNING_PERIOD','Invalid pruning period');
define ('ADMIN_ERROR_NO_PERIOD','oops, no period specified');
define ('ADMIN_BAD_RSS_URL',"I'm sorry, I don't think I can handle this URL: '%s'");
define ('ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"You can't delete the " . HOME_FOLDER . " folder");
define ('ADMIN_CANT_RENAME',"You can't rename this folder '%s' because such a folder already exists.");
define('ADMIN_ERROR_CANT_CREATE',"Looks like you already have a folder called '%s'!");

define ('TAG_TAGS','Tags');
define ('TAG_EDIT','edit');
define ('TAG_SUBMIT','submit');
define ('TAG_CANCEL','cancel');
define ('TAG_SUBMITTING','...');
define ('TAG_ERROR_NO_TAG',"Oops! No items tagged &laquo;%s&raquo; were found.");
define ('TAG_ALL_TAGS','All Tags');
define ('TAG_TAGGED','tagged');
define ('TAG_TAGGEDP','tagged');
define ('TAG_SUGGESTIONS','suggestions');
define ('TAG_SUGGESTIONS_NONE','no suggestions');
define ('TAG_RELATED','Related tags: ');

define ('MARK_READ', "Mark all items as read");
define ('MARK_CHANNEL_READ', "Mark this feed as read");
define ('SHOW_UNREAD_ALL_SHOW','Show items: ');
define ('SHOW_UNREAD_ALL_UNREAD_ONLY','Unread only');
define ('SHOW_UNREAD_ALL_READ_AND_UNREAD','Read and unread');

?>
