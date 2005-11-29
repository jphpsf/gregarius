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
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################
#
# 	Planning to translate this into your own language? Please read this:
#	http://wiki.gregarius.net/index.php/Internationalization
#
###############################################################################

/// Language: English
define ('LOCALE_WINDOWS','english');
define ('LOCALE_LINUX','en_EN');

define ('LBL_ITEM','item');
define ('LBL_ITEMS','items');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d matches for %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d match for %s");
define ('LBL_H2_SEARCH', 'Search %d items');
define ('LBL_SEARCH_SEARCH_QUERY','Search terms:');
define ('LBL_SEARCH_MATCH_OR', 'Some terms (OR)');
define ('LBL_SEARCH_MATCH_AND', 'All terms (AND)');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', 'Exact match');
define ('LBL_SEARCH_CHANNELS', 'Feed:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Order by date, feed');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Order by feed, date');
define ('LBL_SEARCH_RESULTS_PER_PAGE','Results per page:');
define ('LBL_SEARCH_RESULTS','Results: ');
define ('LBL_H2_UNREAD_ITEMS','Unread items (<span id="ucnt">%d</span>)');
define ('LBL_H2_RECENT_ITEMS', "Recent items");
define ('LBL_H2_CHANNELS','Feeds');
define ('LBL_H5_READ_UNREAD_STATS','%d items, %d unread');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> items (<strong>%d</strong> unread) in <strong>%d</strong> feeds');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> tagged items, in <strong>%d</strong> tags');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d unread)</strong>');
define ('LBL_UNREAD','unread');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','All');
define ('LBL_NAV_HOME','<span>H</span>ome');
define ('LBL_NAV_UPDATE', '<span>R</span>efresh');
define ('LBL_NAV_CHANNEL_ADMIN', 'A<span>d</span>min');
define ('LBL_NAV_SEARCH', "<span>S</span>earch");
define ('LBL_NAV_DEVLOG', "Dev<span>l</span>og");
define ('LBL_SEARCH_GO', 'Search');

define ('LBL_POSTED', 'Posted: ');
define ('LBL_FETCHED','Fetched: ');
define ('LBL_BY', ' by ');

define ('LBL_AND','and');

define ('LBL_TITLE_UPDATING','Updating');
define ('LBL_TITLE_SEARCH','Search');
define ('LBL_TITLE_ADMIN','Feeds Admin');


define ('LBL_HOME_FOLDER','Root');
define ('LBL_VISIT', '(visit)');
define ('LBL_COLLAPSE','[-] collapse');
define ('LBL_EXPAND','[+] expand');
define ('LBL_PL_FOR','Permalink for ');

define ('LBL_UPDATE_CHANNEL','Feed');
define ('LBL_UPDATE_STATUS','Status');
define ('LBL_UPDATE_UNREAD','New Items');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','Updating %d Feeds...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('LBL_ADMIN_EDIT', 'edit');
define ('LBL_ADMIN_DELETE', 'delete');
define ('LBL_ADMIN_DELETE2', 'Delete');
define ('LBL_ADMIN_RENAME', 'Rename to...');
define ('LBL_ADMIN_CREATE', 'Create');
define ('LBL_ADMIN_IMPORT','Import');
define ('LBL_ADMIN_EXPORT','Export');
define ('LBL_ADMIN_DEFAULT','default');
define ('LBL_ADMIN_ADD','Add');
define ('LBL_ADMIN_YES', 'Yes');
define ('LBL_ADMIN_NO', 'No');
define ('LBL_ADMIN_FOLDERS','Folders:');
define ('LBL_ADMIN_CHANNELS','Feeds:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','Items:');
define ('LBL_ADMIN_CONFIG','Configuration:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','Cancel');
define ('LBL_ADMIN_LOGOUT','Logout');

define ('LBL_ADMIN_OPML_IMPORT','Import');
define ('LBL_ADMIN_OPML_EXPORT','Export');
define ('LBL_ADMIN_OPML_IMPORT_OPML','Import OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','Export OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... from URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... from File:');
define ('LBL_ADMIN_FILE_IMPORT','Import file');

define ('LBL_ADMIN_IN_FOLDER','to folder:');
define ('LBL_ADMIN_SUBMIT_CHANGES', 'Submit Changes');
define ('LBL_ADMIN_PREVIEW_CHANGES','Preview');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','Title');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','Folder');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','Description');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','Move');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','Action');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Flags');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','Key');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','Value');
define ('LBL_ADMIN_CHANNELS_ADD','Add a feed:');
define ('LBL_ADMIN_FOLDERS_ADD','Add a folder:');
define ('LBL_ADMIN_CHANNEL_ICON','Shown favicon:');
define ('LBL_CLEAR_FOR_NONE','(Leave blank for no icon)');

define ('LBL_ADMIN_CONFIG_VALUE','Value for');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','Name');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','Author');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','Version');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','Description');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','Active');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','Edit the feed ');
define ('LBL_ADMIN_CHANNEL_NAME','Title:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','Site URL:');
define ('LBL_ADMIN_CHANNEL_FOLDER','In folder:');
define ('LBL_ADMIN_CHANNEL_DESCR','Description:');
define ('LBL_ADMIN_FOLDER_NAME','Folder name:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','This feed is <strong>private</strong>, only admins see it.');
define ('LBL_ADMIN_CHANNEL_DELETED','This feed is <strong>deprecated</strong>, it won\'t be updated anymore and won\'t be visible in the feeds column.');

define ('LBL_ADMIN_ARE_YOU_SURE', "Are you sure you wish to delete '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','Are you sure you wish to reset the value for %s to its default \'%s\'?');
define ('LBL_ADMIN_TRUE','True');
define ('LBL_ADMIN_FALSE','False');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Enter either the URL of an RSS feed or of a Website whose feed you wish to subscribe to)');
define ('LBL_ADMIN_FEEDS','The following feeds were found in <a href="%s">%s</a>, which one would you like to subscribe?');

define ('LBL_ADMIN_PRUNE_OLDER','Delete items older than ');
define ('LBL_ADMIN_PRUNE_DAYS','days');
define ('LBL_ADMIN_PRUNE_MONTHS','months');
define ('LBL_ADMIN_PRUNE_YEARS','years');
define ('LBL_ADMIN_PRUNE_KEEP','Keep the most recent items: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Delete Sticky items too: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','Do not delete items tagged... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Enter <strong>*</strong> to keep all tagged items)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Warning: you are about to delete %s items (of %s)');
define ('LBL_ADMIN_PRUNING','Pruning');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','folders');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','feeds');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','items');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','config');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Subscription bookmarklet [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Subscribe in Gregarius!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Not Authorized!</h1>\nYou are not authorized to access the administration interface.\n"
		."Please follow <a href=\"%s\">this link</a> back to the main page.\n"
		."Have  a nice day!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Invalid pruning period');
define ('LBL_ADMIN_ERROR_NO_PERIOD','oops, no period specified');
define ('LBL_ADMIN_BAD_RSS_URL',"I'm sorry, I don't think I can handle this URL: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"You can't delete the " . LBL_HOME_FOLDER . " folder");
define ('LBL_ADMIN_CANT_RENAME',"You can't rename this folder '%s' because such a folder already exists.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"Looks like you already have a folder called '%s'!");

define ('LBL_TAG_TAGS','Tags');
define ('LBL_TAG_EDIT','edit');
define ('LBL_TAG_SUBMIT','submit');
define ('LBL_TAG_CANCEL','cancel');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Oops! No items tagged &laquo;%s&raquo; were found.");
define ('LBL_TAG_ALL_TAGS','All Tags');
define ('LBL_TAG_TAGGED','tagged');
define ('LBL_TAG_TAGGEDP','tagged');
define ('LBL_TAG_SUGGESTIONS','suggestions');
define ('LBL_TAG_SUGGESTIONS_NONE','no suggestions');
define ('LBL_TAG_RELATED','Related tags: ');

define ('LBL_MARK_READ', "Mark all items as read");
define ('LBL_MARK_CHANNEL_READ', "Mark this feed as read");
define ('LBL_MARK_FOLDER_READ',"Mark this folder as read");
define ('LBL_SHOW_UNREAD_ALL_SHOW','Show items: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','Unread only');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','Read and unread');

define ('LBL_STATE_UNREAD','Unread (Set this item\\\'s read/unread state)');
define ('LBL_STATE_STICKY','Sticky (Won\\\'t be deleted when you prune items)');
define ('LBL_STATE_PRIVATE','Private (Only administrators can see private items)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','Deprecated');
define ('LBL_PRIVATE','Private');
define ('LBL_ADMIN_TOGGLE_STATE','Toggle State:');
define ('LBL_ADMIN_TOGGLE_SET','Set');
define ('LBL_ADMIN_IM_SURE','I\'m sure!');


// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','Logged in as <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','Not logged in');
define ('LBL_LOG_OUT','Logout');
define ('LBL_LOG_IN','Login');

define ('LBL_ADMIN_OPML_IMPORT_AND','Import new feeds and:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... replace all existing feeds and items.');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... add them to the folder:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... merge them with the existing ones.');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','Adding %s to %s... ');

define ('LBL_TAG_FOLDERS','Categories');
define ('LBL_SIDE_ITEMS','(%d items)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d unread in %d feeds)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> feeds in <strong>%d</strong> categories');

define ('LBL_RATING','Rating:');


// New in 0.5.3:
define ('LBL_ENCLOSURE', 'Enclosure:');
define ('LBL_DOWNLOAD', 'download');
define ('LBL_PLAY', 'play');

// New in 0.5.x:
define ('LBL_FOOTER_LAST_MODIF_NEVER', 'Never');
define ('LBL_ADMIN_DASHBOARD','Dashboard'); 

define ('LBL_ADMIN_MUST_SET_PASS','<p>No Administrator has been specified yet!</p>'
		.'<p>Please provide an Administrator username and password now!</p>');
define ('LBL_USERNAME','Username');		
define ('LBL_PASSWORD','Password');
define ('LBL_ADMIN_LOGIN','Please log in');

define ('LBL_ADMIN_PLUGINS','Plugins');
define ('LBL_ADMIN_DOMAIN_PLUGINS_LBL','plugins');
define ('LBL_ADMIN_PLUGINS_HEADING_UPDATES','Update Available');
define ('LBL_ADMIN_CHECK_FOR_UPDATES','Check for Updates');
define ('LBL_ADMIN_LOGIN_BAD_LOGIN','<strong>Oops!</strong> Bad login/password');
define ('LBL_ADMIN_LOGIN_NO_ADMIN','<strong>Oops!</strong> You are successfully '
			.'logged in as %s, but you don\\\'t have administration privileges. Log in again '
			.'with administration privileges or follow your way <a href="..">home</a>');



define ('LBL_ADMIN_PLUGINS_GET_MORE', '<p style="font-size:small">'
.'Plugins are third-party scripts that offer extended functionalities. '
.'More plugins can be downloaded at the <a style="text-decoration:underline" '
.' href="http://plugins.gregarius.net/">Plugin Repository</a>.</p>');

						
?>
