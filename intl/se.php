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
# Swedish translation by David Hardne - dh at tx dot se
#
###############################################################################
# 
# $Log$
# Revision 1.2  2005/06/09 11:46:51  mbonetti
# That'd be Svenska!
#
# 
#
#
###############################################################################

/// Language: Svenska

define ('ITEM','inl&auml;gg');
define ('ITEMS','inl&auml;gg');
define ('H2_SEARCH_RESULTS_FOR', "%d tr&auml;ffar f&ouml;r %s");
define ('H2_SEARCH_RESULT_FOR',"%d tr&auml;ff f&ouml;r %s");
define ('H2_SEARCH', 'S&ouml;k bland %d inl&auml;gg');
define ('SEARCH_SEARCH_QUERY','S&ouml;kord:');
define ('SEARCH_MATCH_OR', 'N&aring;got av orden (OR)');
define ('SEARCH_MATCH_AND', 'Alla orden (AND)');                                                                 
define ('SEARCH_MATCH_EXACT', 'Den exakta frasen');
define ('SEARCH_CHANNELS', 'Feed:');
define ('SEARCH_ORDER_DATE_CHANNEL','Ordna efter datum, feed');
define ('SEARCH_ORDER_CHANNEL_DATE','Ordna efter feed, datum');
define ('SEARCH_RESULTS_PER_PAGE','Resultat per sida:');
define ('SEARCH_RESULTS','Resultat: ');
define ('H2_UNREAD_ITEMS',"Ol&auml;sta inl&auml;gg (%d)");
define ('H2_RECENT_ITEMS', "Senast l&auml;sta inl&auml;gg");
define ('H2_CHANNELS','Feeds');
define ('H5_READ_UNREAD_STATS','%d inl&auml;gg, %d ol&auml;sta');
define ('ITEMCOUNT_PF', '<strong>%d</strong> inl&auml;gg (<strong>%d</strong> ol&auml;sta) i <strong>%d</strong> feeds');
define ('UNREAD_PF', '(<strong>%d ol&auml;sta</strong>)');
define ('UNREAD','ol&auml;sta');

define ('FTR_POWERED_BY', ", drivs med hj&auml;lp av ");
define ('ALL','Alla');
define ('NAV_HOME','Hem');
define ('NAV_UPDATE', 'Uppdatera');
define ('NAV_CHANNEL_ADMIN', 'Admin');
define ('NAV_SEARCH', "S&ouml;k");
define ('NAV_DEVLOG', "Devlog");
define ('SEARCH_GO', 'S&ouml;k');

define ('POSTED', 'Postad: ');
define ('FETCHED','H&auml;mtad: ');

define ('LBL_AND','och');

define ('TITLE_UPDATING','Uppdaterar');
define ('TITLE_SEARCH','S&ouml;k');
define ('TITLE_ADMIN','Admin');


define ('HOME_FOLDER','Root');
define ('VISIT', '(&ouml;ppna)');
define ('COLLAPSE','[-] Kollapsa');
define ('EXPAND','[+] Expandera');
define ('PL_FOR','Permalink till ');

define ('UPDATE_CHANNEL','Feed');
define ('UPDATE_STATUS','Status');
define ('UPDATE_UNDREAD','New Items');

define ('UPDATE_STATUS_OK','OK (HTTP 200)');
define ('UPDATE_STATUS_CACHED', 'OK (Lokal cache)');
define ('UPDATE_STATUS_ERROR','ERROR');
define ('UPDATE_H2','Uppdaterar %d feeds...');
define ('UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('UPDATE_NOT_FOUND','404 Not Found (Lokal cache)');
// admin
define ('ADMIN_EDIT', 'editera');
define ('ADMIN_DELETE', 'radera');
define ('ADMIN_DELETE2', 'Radera');
define ('ADMIN_RENAME', 'Byt namn till...');
define ('ADMIN_CREATE', 'Skapa');
define ('ADMIN_IMPORT','Importera');
define ('ADMIN_EXPORT','Exportera');
define ('ADMIN_DEFAULT','default');
define ('ADMIN_ADD','L&auml;gg till');
define ('ADMIN_YES', 'Ja');
define ('ADMIN_NO', 'Nej');
define ('ADMIN_FOLDERS','Foldrar:');
define ('ADMIN_CHANNELS','Feeds:');
define ('ADMIN_OPML','OPML:');  
define ('ADMIN_ITEM','Inl&auml;gg:');
define ('ADMIN_CONFIG','Konfiguration:');
define ('ADMIN_OK','OK');
define ('ADMIN_CANCEL','Avbryt');
define ('OPML_IMPORT','Importera');
define ('OPML_EXPORT','Export');

define ('ADMIN_IN_FOLDER','till foldern:');
define ('ADMIN_SUBMIT_CHANGES', 'Uppdatera');
define ('ADMIN_PREVIEW_CHANGES','F&ouml;rhandsgranska');
define ('ADMIN_CHANNELS_HEADING_TITLE','Rubrik');
define ('ADMIN_CHANNELS_HEADING_FOLDER','Folder');
define ('ADMIN_CHANNELS_HEADING_DESCR','Beskrivning');
define ('ADMIN_CHANNELS_HEADING_MOVE','Ordning');
define ('ADMIN_CHANNELS_HEADING_ACTION','');
define ('ADMIN_CHANNELS_HEADING_FLAGS','Flaggor');
define ('ADMIN_CHANNELS_HEADING_KEY','Key');
define ('ADMIN_CHANNELS_HEADING_VALUE','Value');
define ('ADMIN_CHANNELS_ADD','L&auml;gg till en feed:');
define ('ADMIN_FOLDERS_ADD','Skapa en folder:');
define ('ADMIN_CHANNEL_ICON','Visa favicon:');
define ('CLEAR_FOR_NONE','(L&auml;mna tom f&ouml;r att inte visa n&aring;n ikon)');
define ('ADMIN_OPML_EXPORT','Exportera till OPML:');

define ('ADMIN_OPML_IMPORT','Importera OPML:');
define ('ADMIN_OPML_IMPORT_FROM_URL','... fr&aring;n URL:');
define ('ADMIN_OPML_IMPORT_FROM_FILE','... fr&aring;n fil:');

define ('ADMIN_CONFIG_VALUE','Value');

define ('ADMIN_OPML_FILE_IMPORT','Importera lokal OPML-fil:');
define ('ADMIN_FILE_IMPORT','Importera');

define ('ADMIN_PLUGINS_HEADING_NAME','Namn');
define ('ADMIN_PLUGINS_HEADING_AUTHOR','Postad av');
define ('ADMIN_PLUGINS_HEADING_VERSION','Version');
define ('ADMIN_PLUGINS_HEADING_DESCRIPTION','Beskrivning');
define ('ADMIN_PLUGINS_HEADING_ACTION','Aktiv');




define ('ADMIN_CHANNEL_EDIT_CHANNEL','Redigera feed ');
define ('ADMIN_CHANNEL_NAME','Rubrik:');
define ('ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('ADMIN_CHANNEL_SITE_URL','Sidans URL:');
define ('ADMIN_CHANNEL_FOLDER','I foldern:');
define ('ADMIN_CHANNEL_DESCR','Beskrivning:');
define ('ADMIN_FOLDER_NAME','Foldernamn:');
define ('ADMIN_CHANNEL_PRIVATE','Denna feed &auml;r <strong>privat</strong> och kan endast ses av admin.');
define ('ADMIN_CHANNEL_DELETED','Denna feed &auml;r <strong>ur bruk</strong>. Den kommer inte uppdateras, och visas inte l&auml;ngre i listan &ouml;ver feeds.');

define ('ADMIN_ARE_YOU_SURE', "&Auml;r du s&auml;ker p&aring; att du vill ta bort '%s'?");
define ('ADMIN_ARE_YOU_SURE_DEFAULT','&Auml;r du s&auml;ker p&aring; att du vill &aring;terst&auml;lla %s till standardv&auml;rdet \'%s\'?');
define ('ADMIN_TRUE','Sant');
define ('ADMIN_FALSE','Falskt');
define ('ADMIN_MOVE_UP','&uarr;');
define ('ADMIN_MOVE_DOWN','&darr;');
define ('ADMIN_ADD_CHANNEL_EXPL','(Ange URL:en direkt till en RSS-feed eller till den webbsida du vill prenumerera p&aring;)');
define ('ADMIN_FEEDS','F&ouml;ljande feeds hittades p&aring; <a href="%s">%s</a>, vilken vill du prenumerera p&aring;?');

define ('ADMIN_PRUNE_OLDER','Radera inl&auml;gg &auml;ldre &auml;n ');
define ('ADMIN_PRUNE_DAYS','dagar');
define ('ADMIN_PRUNE_MONTHS','m&aring;nader');
define ('ADMIN_PRUNE_YEARS','&aring;r');
define ('PRUNE_KEEP','Keep the most recent items: ');
define ('ADMIN_PRUNE_INCLUDE_STICKY','Radera &auml;ven inl&auml;gg markerade som Sticky: ');
define ('ADMIN_PRUNE_EXCLUDE_TAGS','Radera inte inl&auml;gg taggade med: ');
define ('ADMIN_ALLTAGS_EXPL','(Ange <strong>*</strong> f&ouml;r att spara alla inl&auml;gg som har taggar)');

define ('ADMIN_ABOUT_TO_DELETE','Warning: you are about to delete %s items (of %s)');
define ('ADMIN_PRUNING','St&auml;da');
define ('ADMIN_DOMAIN_FOLDER_LBL','foldrar');
define ('ADMIN_DOMAIN_CHANNEL_LBL','feeds');
define ('ADMIN_DOMAIN_ITEM_LBL','inl&auml;gg');
define ('ADMIN_DOMAIN_CONFIG_LBL','konfiguration');
define ('ADMIN_DOMAIN_OPML_LBL','opml');
define ('ADMIN_BOOKMARKET_LABEL','Bookmarklet f&ouml;r att l&auml;gga till en sida i Gregarius [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('ADMIN_BOOKMARKLET_TITLE','L&auml;gg till i Gregarius!');


define ('ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Stopp och bel&auml;gg!</h1>\Du har inte beh&ouml;righet att anv&auml;nda adminsidorna.\n"
		."Klicka <a href=\"%s\">h&auml;r</a> f&ouml;r att komma tillbaka till startsidan.\n"
		."Ha en bra dag!!");
		
define ('ADMIN_ERROR_PRUNING_PERIOD','Felaktig period');
define ('ADMIN_ERROR_NO_PERIOD','du angav ingen period');
define ('ADMIN_BAD_RSS_URL',"Tyv&auml;rr kunde inte f&ouml;ljande URL anv&auml;ndas: '%s'");
define ('ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Du kan inte radera foldern " . HOME_FOLDER . "!");
define ('ADMIN_CANT_RENAME',"Du kan inte &auml;ndra folderns namn till '%s', namnet &auml;r redan upptaget.");
define('ADMIN_ERROR_CANT_CREATE',"Det finns redan en folder som heter '%s'!");

define ('TAG_TAGS','Taggar');
define ('TAG_EDIT','&auml;ndra');
define ('TAG_SUBMIT','skicka');
define ('TAG_CANCEL','avbryt');
define ('TAG_SUBMITTING','...');
define ('TAG_ERROR_NO_TAG',"Ojd&aring;! Hittade inga inl&auml;gg med taggen &laquo;%s&raquo;.");
define ('TAG_ALL_TAGS','Alla Taggar');
define ('TAG_TAGGED','taggad');
define ('TAG_TAGGEDP','taggad');
define ('TAG_SUGGESTIONS','f&ouml;rslag');
define ('TAG_SUGGESTIONS_NONE','inga f&ouml;rslag');
define ('TAG_RELATED','Relaterade taggar: ');

define ('MARK_READ', "Markera alla inl&auml;gg som l&auml;sta");
define ('MARK_CHANNEL_READ', "Markera denna feed som l&auml;st");
define ('MARK_FOLDER_READ',"Markera foldern som l&auml;st");
define ('SHOW_UNREAD_ALL_SHOW','Visa: ');
define ('SHOW_UNREAD_ALL_UNREAD_ONLY','Ol&auml;sta');
define ('SHOW_UNREAD_ALL_READ_AND_UNREAD','L&auml;sta och ol&auml;sta');

define ('STATE_UNREAD','Ol&auml;st (Markera inl&auml;gget som l&auml;st/ol&auml;st)');
define ('STATE_STICKY','Sticky (Spara inl&auml;gget n&auml;r du st&auml;dar bland inl&auml;ggen)');
define ('STATE_PRIVATE','Privat (Visa endast f&ouml;r admin)');
?>