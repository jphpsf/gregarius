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

/// Language: Danish

define ('LBL_ITEM','item');
define ('LBL_ITEMS','items');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d resultater for %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d resultat for %s");
define ('LBL_H2_SEARCH', 'S&oslash;g igennem %d items');
define ('LBL_SEARCH_SEARCH_QUERY','S&oslash;geord:');
define ('LBL_SEARCH_MATCH_OR', 'Nogle s&oslash;geord (ELLER)');
define ('LBL_SEARCH_MATCH_AND', 'Alle s&oslash;geord (OG)');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', 'Pr&aelig;cis s&oslash;ges&aelig;tning');
define ('LBL_SEARCH_CHANNELS', 'Feed:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Sorter ved dato, feed');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Sorter ved feed, dato');
define ('LBL_SEARCH_RESULTS_PER_PAGE','Resultater per side:');
define ('LBL_SEARCH_RESULTS','Resultater: ');
define ('LBL_H2_UNREAD_ITEMS','Ul&aelig;ste items (<span id="ucnt">%d</span>)');
define ('LBL_H2_RECENT_ITEMS', "Seneste items");
define ('LBL_H2_CHANNELS','Feeds');
define ('LBL_H5_READ_UNREAD_STATS','%d items, %d ul&aelig;ste');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> items (<strong>%d</strong> unread) i <strong>%d</strong> feeds');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> tagged items, i <strong>%d</strong> tags');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d ul&aelig;ste)</strong>');
define ('LBL_UNREAD','ul&aelig;st');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','Alle');
define ('LBL_NAV_HOME','<span>H</span>jem');
define ('LBL_NAV_UPDATE', '<span>O</span>pdater');
define ('LBL_NAV_CHANNEL_ADMIN', 'A<span>d</span>min');
define ('LBL_NAV_SEARCH', "<span>S</span>&oslash;g");
define ('LBL_NAV_DEVLOG', "Dev<span>l</span>og");
define ('LBL_SEARCH_GO', 'S&oslash;g');

define ('LBL_POSTED', 'Skrevet: ');
define ('LBL_FETCHED','Hented: ');
define ('LBL_BY', ' af ');

define ('LBL_AND','og');

define ('LBL_TITLE_UPDATING','Updaterer');
define ('LBL_TITLE_SEARCH','S&oslash;g');
define ('LBL_TITLE_ADMIN','Feeds Admin');


define ('LBL_HOME_FOLDER','Root');
define ('LBL_VISIT', '(bes&oslash;g)');
define ('LBL_COLLAPSE','[-] kollaps');
define ('LBL_EXPAND','[+] udvid');
define ('LBL_PL_FOR','Permanent link for ');

define ('LBL_UPDATE_CHANNEL','Feed');
define ('LBL_UPDATE_STATUS','Status');
define ('LBL_UPDATE_UNREAD','Nye items');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('LBL_UPDATE_STATUS_ERROR','FEJL');
define ('LBL_UPDATE_H2','Updating %d Feeds...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('LBL_ADMIN_EDIT', 'ret');
define ('LBL_ADMIN_DELETE', 'slet');
define ('LBL_ADMIN_DELETE2', 'Slet');
define ('LBL_ADMIN_RENAME', 'Omd&oslash;b til...');
define ('LBL_ADMIN_CREATE', 'Lav');
define ('LBL_ADMIN_IMPORT','Importer');
define ('LBL_ADMIN_EXPORT','Exporter');
define ('LBL_ADMIN_DEFAULT','standard');
define ('LBL_ADMIN_ADD','Tilf&oslash;j');
define ('LBL_ADMIN_YES', 'Ja');
define ('LBL_ADMIN_NO', 'Nej');
define ('LBL_ADMIN_FOLDERS','Foldere:');
define ('LBL_ADMIN_CHANNELS','Feeds:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','Items:');
define ('LBL_ADMIN_CONFIG','Konfiguration:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','Anuller');
define ('LBL_ADMIN_LOGOUT','Log ud');

define ('LBL_ADMIN_OPML_IMPORT','Importer');
define ('LBL_ADMIN_OPML_EXPORT','Exporter');
define ('LBL_ADMIN_OPML_IMPORT_OPML','Importer OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','Exporter OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... fra URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... fra fil:');
define ('LBL_ADMIN_FILE_IMPORT','Importer fil');

define ('LBL_ADMIN_IN_FOLDER','til folder:');
define ('LBL_ADMIN_SUBMIT_CHANGES', 'Submit &aelig;ndringer');
define ('LBL_ADMIN_PREVIEW_CHANGES','Preview');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','Titel');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','Folder');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','Beskrivelse');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','Flyt');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','Aktion');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Flag');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','N&oslash;gle');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','V&aelig;rdi');
define ('LBL_ADMIN_CHANNELS_ADD','Tilf&oslash;j et feed:');
define ('LBL_ADMIN_FOLDERS_ADD','Tilf&oslash;j en folder:');
define ('LBL_ADMIN_CHANNEL_ICON','Vist favicon:');
define ('LBL_CLEAR_FOR_NONE','(Efterlad blank for intet ikon)');

define ('LBL_ADMIN_CONFIG_VALUE','V&aelig;rdi');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','Navn');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','Forfatter');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','Version');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','Beskrivelse');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','Aktiv');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','Ret feedet ');
define ('LBL_ADMIN_CHANNEL_NAME','Titel:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','Side URL:');
define ('LBL_ADMIN_CHANNEL_FOLDER','I folder:');
define ('LBL_ADMIN_CHANNEL_DESCR','Beskrivelse:');
define ('LBL_ADMIN_FOLDER_NAME','Folder navn:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','Dette feed er <strong>privat</strong>, kun admins kan se det.');
define ('LBL_ADMIN_CHANNEL_DELETED','Dette feed Er <strong>for&aelig;ldet</strong>, det vil ikke blive opdateret mere eller vist i feeds kolonnen.');

define ('LBL_ADMIN_ARE_YOU_SURE', "Er du sikker p&aring; at du &oslash;nsker at slette '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','Er du sikker p&aring; at du &oslash;nsker at resette v&aelig;rdien for %s til dets standard \'%s\'?');
define ('LBL_ADMIN_TRUE','Sand');
define ('LBL_ADMIN_FALSE','Falskt');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Indtast enten URLen af et RSS feed eller en hjemmeside hvis feed du &oslash;nsker at tilmelde dig)');
define ('LBL_ADMIN_FEEDS','De f&oslash;lgende feeds var fundet i <a href="%s">%s</a>, hvilken en &oslash;nsker du at tilmelde dig til?');

define ('LBL_ADMIN_PRUNE_OLDER','Slet items &aelig;ldre end ');
define ('LBL_ADMIN_PRUNE_DAYS','dage');
define ('LBL_ADMIN_PRUNE_MONTHS','m&aring;neder');
define ('LBL_ADMIN_PRUNE_YEARS','&aring;r');
define ('LBL_ADMIN_PRUNE_KEEP','Behold de seneste items: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Slet ogs&aring; Sticky items: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','Slet ikke taggede items... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Indtast <strong>*</strong> for at beholde alle taggede items)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Advarsel: du er ved at slette %s items (af %s)');
define ('LBL_ADMIN_PRUNING','Pruning');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','foldere');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','feeds');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','items');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','konfiguration');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Tilmeld bookmarklet [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Tilmeld i Gregarius!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
	"<h1>Ikke Autoriseret!</h1>\nDu er ikke autoriseret til at bes&oslash;ge administration brugerflade.\n"
	."F&oslash;lg venligst <a href=\"%s\">dette link</a> tilbage til forsiden.\n"
	."Hav en fortsat god dag!");

define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Invalid prunings periode');
define ('LBL_ADMIN_ERROR_NO_PERIOD','oops, ingen periode er specificeret');
define ('LBL_ADMIN_BAD_RSS_URL',"Unskyld, jeg tror ikke at jeg kan h&aring;ndtere denne URL: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Du kan ikke slette " . LBL_HOME_FOLDER . " mappen");
define ('LBL_ADMIN_CANT_RENAME',"Du kan ikke rette navnet p&aring; mappen '%s' fordi den mappe allerede eksistere.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"det lader til du allerede har en mapper der hedder '%s'!");

define ('LBL_TAG_TAGS','Tags');
define ('LBL_TAG_EDIT','ret');
define ('LBL_TAG_SUBMIT','post');
define ('LBL_TAG_CANCEL','annuler');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Oops! Ingen items tagged &laquo;%s&raquo; blev fundet.");
define ('LBL_TAG_ALL_TAGS','Alle Tags');
define ('LBL_TAG_TAGGED','tagged');
define ('LBL_TAG_TAGGEDP','tagged');
define ('LBL_TAG_SUGGESTIONS','forslag');
define ('LBL_TAG_SUGGESTIONS_NONE','ingen forslag');
define ('LBL_TAG_RELATED','Relateret tags: ');

define ('LBL_MARK_READ', "Marker alle items som l&aelig;st");
define ('LBL_MARK_CHANNEL_READ', "Marker denne feed som l&aelig;st");
define ('LBL_MARK_FOLDER_READ',"Marker denne mappe som l&aelig;st");
define ('LBL_SHOW_UNREAD_ALL_SHOW','Vis items: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','Kun ul&aelig;st');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','L&aelig;st og ul&aelig;st');

define ('LBL_STATE_UNREAD','Ul&aelig;st (S&aelig;t dette items l&aelig;st/ul&aelig;st status)');
define ('LBL_STATE_STICKY','Sticky (Bliver ikke slettet n&aring;r du pruner)');
define ('LBL_STATE_PRIVATE','Privat (Kun administratore kan se privat items)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','For&aelig;ldet');
define ('LBL_PRIVATE','Privat');
define ('LBL_ADMIN_TOGGLE_STATE','Toggle State:');
define ('LBL_ADMIN_TOGGLE_SET','S&aelig;t');
define ('LBL_ADMIN_IM_SURE','Jeg er sikker!');

// new in 0.5.1:

// Requires translation!
define ('LBL_LOGGED_IN_AS','Logged in as <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','Not logged in');
define ('LBL_LOG_OUT','Logout');
define ('LBL_LOG_IN','Login');

?>
