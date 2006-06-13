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
# Swedish translation by David Hardne - dh at tx dot se
#
###############################################################################


/// Language: Svenska
define ('LOCALE_WINDOWS','sve');
define ('LOCALE_LINUX','sv_SV');

define ('LBL_ITEM','inl&auml;gg');
define ('LBL_ITEMS','inl&auml;gg');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d tr&auml;ffar f&ouml;r %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d tr&auml;ff f&ouml;r %s");
define ('LBL_H2_SEARCH', 'S&ouml;k bland %d inl&auml;gg');
define ('LBL_SEARCH_SEARCH_QUERY','S&ouml;kord:');
define ('LBL_SEARCH_MATCH_OR', 'N&aring;got av orden (OR)');
define ('LBL_SEARCH_MATCH_AND', 'Alla orden (AND)');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', 'Den exakta frasen');
define ('LBL_SEARCH_CHANNELS', 'Fl&ouml;de:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Ordna efter datum, fl&ouml;de');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Ordna efter fl&ouml;de, datum');
define ('LBL_SEARCH_RESULTS_PER_PAGE','Resultat per sida:');
define ('LBL_SEARCH_RESULTS','Resultat: ');
define ('LBL_H2_UNREAD_ITEMS','Ol&auml;sta inl&auml;gg (<strong id="ucnt">%d</strong>)');
define ('LBL_H2_RECENT_ITEMS', "Senast l&auml;sta inl&auml;gg");
define ('LBL_H2_CHANNELS','Fl&ouml;den');
define ('LBL_H5_READ_UNREAD_STATS','%d inl&auml;gg, %d ol&auml;sta');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> inl&auml;gg (<strong id="fucnt">%d</strong> ol&auml;sta) i <strong>%d</strong> fl&ouml;den');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> taggade inl&auml;gg, <strong>%d</strong> taggar');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d ol&auml;sta)</strong>');
define ('LBL_UNREAD','ol&auml;sta');

define ('LBL_FTR_POWERED_BY', " drivs med hj&auml;lp av ");
define ('LBL_ALL','Alla');
define ('LBL_NAV_HOME','<span>H</span>em');
define ('LBL_NAV_UPDATE', '<span>U</span>ppdatera');
define ('LBL_NAV_CHANNEL_ADMIN', 'A<span>d</span>min');
define ('LBL_NAV_SEARCH', "<span>S</span>&ouml;k");
define ('LBL_SEARCH_GO', 'S&ouml;k');

define ('LBL_POSTED', 'Postad: ');
define ('LBL_FETCHED','H&auml;mtad: ');
define ('LBL_BY', ' af ');

define ('LBL_AND','och');

define ('LBL_TITLE_UPDATING','Uppdaterar');
define ('LBL_TITLE_SEARCH','S&ouml;k');


define ('LBL_HOME_FOLDER','Root');
define ('LBL_VISIT', '(&ouml;ppna)');
define ('LBL_COLLAPSE','[-] Kollapsa');
define ('LBL_EXPAND','[+] Expandera');
define ('LBL_PL_FOR','Permalink till ');

define ('LBL_UPDATE_CHANNEL','Fl&ouml;de');
define ('LBL_UPDATE_STATUS','Status');
define ('LBL_UPDATE_UNREAD','New Items');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Lokal cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','Uppdaterar %d fl&ouml;den...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Lokal cache)');
// admin
define ('LBL_ADMIN_EDIT', 'editera');
define ('LBL_ADMIN_DELETE', 'radera');
define ('LBL_ADMIN_DELETE2', 'Radera');
define ('LBL_ADMIN_RENAME', 'Byt namn till...');
define ('LBL_ADMIN_CREATE', 'Skapa');
define ('LBL_ADMIN_IMPORT','Importera');
define ('LBL_ADMIN_EXPORT','Exportera');
define ('LBL_ADMIN_DEFAULT','default');
define ('LBL_ADMIN_ADD','L&auml;gg till');
define ('LBL_ADMIN_YES', 'Ja');
define ('LBL_ADMIN_NO', 'Nej');
define ('LBL_ADMIN_FOLDERS','Foldrar:');
define ('LBL_ADMIN_CHANNELS','Fl&ouml;den:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','Inl&auml;gg:');
define ('LBL_ADMIN_CONFIG','Konfiguration:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','Avbryt');
define ('LBL_ADMIN_LOGOUT','Logga ut');


define ('LBL_ADMIN_OPML_IMPORT','Importera');
define ('LBL_ADMIN_OPML_EXPORT','Export');
define ('LBL_ADMIN_OPML_IMPORT_OPML','Importera OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','Exportera till OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... fr&aring;n URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... fr&aring;n fil:');


define ('LBL_ADMIN_IN_FOLDER','till foldern:');
define ('LBL_ADMIN_SUBMIT_CHANGES', 'Uppdatera');
define ('LBL_ADMIN_PREVIEW_CHANGES','F&ouml;rhandsgranska');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','Rubrik');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','Folder');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','Beskrivning');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','Flytta');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Flaggor');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','Nyckel');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','V&auml;rde');
define ('LBL_ADMIN_CHANNELS_ADD','L&auml;gg till ett fl&ouml;de:');
define ('LBL_ADMIN_FOLDERS_ADD','Skapa en folder:');
define ('LBL_ADMIN_CHANNEL_ICON','Visa favicon:');
define ('LBL_CLEAR_FOR_NONE','(L&auml;mna tom f&ouml;r att inte visa n&aring;n ikon)');


define ('LBL_ADMIN_CONFIG_VALUE','V&auml;rde');

define ('LBL_ADMIN_OPML_FILE_IMPORT','Importera lokal OPML-fil:');
define ('LBL_ADMIN_FILE_IMPORT','Importera');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','Namn');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','Postad av');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','Version');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','Beskrivning');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','Aktiv');




define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','Redigera fl&ouml;de ');
define ('LBL_ADMIN_CHANNEL_NAME','Rubrik:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','Sidans URL:');
define ('LBL_ADMIN_CHANNEL_FOLDER','Till foldern:');
define ('LBL_ADMIN_CHANNEL_DESCR','Beskrivning:');
define ('LBL_ADMIN_FOLDER_NAME','Foldernamn:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','Detta fl&ouml;de &auml;r <strong>privat</strong> och kan endast ses av admin.');
define ('LBL_ADMIN_CHANNEL_DELETED','Detta fl&ouml;de &auml;r <strong>ur bruk</strong>. Den kommer inte 
uppdateras, och visas inte l&auml;ngre i listan &ouml;ver fl&ouml;den.');

define ('LBL_ADMIN_ARE_YOU_SURE', "&Auml;r du s&auml;ker p&aring; att du vill ta bort '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','&Auml;r du s&auml;ker p&aring; att du vill &aring;terst&auml;lla %s till standardv&auml;rdet \'%s\'?');
define ('LBL_ADMIN_TRUE','Sant');
define ('LBL_ADMIN_FALSE','Falskt');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Ange URL:en direkt till ett RSS-fl&ouml;de eller till den webbsida du vill prenumerera p&aring;)');
define ('LBL_ADMIN_FEEDS','F&ouml;ljande fl&ouml;den hittades p&aring; <a href="%s">%s</a>, vilken vill du prenumerera p&aring;?');

define ('LBL_ADMIN_PRUNE_OLDER','Radera inl&auml;gg &auml;ldre &auml;n ');
define ('LBL_ADMIN_PRUNE_DAYS','dagar');
define ('LBL_ADMIN_PRUNE_MONTHS','m&aring;nader');
define ('LBL_ADMIN_PRUNE_YEARS','&aring;r');
define ('LBL_ADMIN_PRUNE_KEEP','Keep the most recent items: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Radera &auml;ven inl&auml;gg markerade som Sticky: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','Radera inte inl&auml;gg taggade med: ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Ange <strong>*</strong> f&ouml;r att spara alla inl&auml;gg som har taggar)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Warning: you are about to delete %s items (of %s)');
define ('LBL_ADMIN_PRUNING','St&auml;da');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','foldrar');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','fl&ouml;den');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','inl&auml;gg');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','konfiguration');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Bookmarklet f&ouml;r att l&auml;gga till en sida i Gregarius [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','L&auml;gg till i Gregarius!');

// New in 0.5.x:

define ('LBL_ADMIN_CONFIGURE','Configure');
define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Stopp och bel&auml;gg!</h1>\Du har inte beh&ouml;righet att anv&auml;nda adminsidorna.\n"
		."Klicka <a href=\"%s\">h&auml;r</a> f&ouml;r att komma tillbaka till startsidan.\n"
		."Ha en bra dag!!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Felaktig period');
define ('LBL_ADMIN_ERROR_NO_PERIOD','du angav ingen period');
define ('LBL_ADMIN_BAD_RSS_URL',"Tyv&auml;rr kunde inte f&ouml;ljande URL anv&auml;ndas: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Du kan inte radera foldern " . LBL_HOME_FOLDER . "!");
define ('LBL_ADMIN_CANT_RENAME',"Du kan inte &auml;ndra folderns namn till '%s', namnet &auml;r redan upptaget.");
define ('LBL_ADMIN_ERROR_CANT_CREATE',"Det finns redan en folder som heter '%s'!");

define ('LBL_TAG_TAGS','Taggar');
define ('LBL_TAG_EDIT','aendra');
define ('LBL_TAG_SUBMIT','skicka');
define ('LBL_TAG_CANCEL','avbryt');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Ojd&aring;! Hittade inga inl&auml;gg med taggen &laquo;%s&raquo;.");
define ('LBL_TAG_ALL_TAGS','Alla Taggar');
define ('LBL_TAG_TAGGED','taggad');
define ('LBL_TAG_TAGGEDP','taggad');
define ('LBL_TAG_SUGGESTIONS','f&ouml;rslag');
define ('LBL_TAG_SUGGESTIONS_NONE','inga f&ouml;rslag');
define ('LBL_TAG_RELATED','Relaterade taggar: ');

define ('LBL_SHOW_UNREAD_ALL_SHOW','Visa: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','Ol&auml;sta');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','L&auml;sta och ol&auml;sta');

define ('LBL_STATE_UNREAD','Ol&auml;st (Markera inl&auml;gget som l&auml;st/ol&auml;st)');
define ('LBL_STATE_STICKY','Sticky (Spara inl&auml;gget n&auml;r du st&auml;dar bland inl&auml;ggen)');
define ('LBL_STATE_PRIVATE','Privat (Visa endast f&ouml;r admin)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','Ur bruk');
define ('LBL_PRIVATE','Privat');

define ('LBL_ADMIN_STATE','Status:');
define ('LBL_ADMIN_STATE_SET','Utf&ouml;r');
define ('LBL_ADMIN_IM_SURE','Bekr&auml;fta!');

// new in 0.5.1:

define ('LBL_LOGGED_IN_AS','Inloggad som <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','Du &auml;r inte inloggad');
define ('LBL_LOG_OUT','Logga ut');
define ('LBL_LOG_IN','Logga in');

define ('LBL_ADMIN_OPML_IMPORT_AND','Importera nya fl&ouml;den och:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... ers&auml;tt alla existerande fl&ouml;den och inl&auml;gg.');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... l&auml;gg dem i mappen:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... f&ouml;rena dem med nuvarande.');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','L&auml;gger %s till %s... ');

define ('LBL_TAG_FOLDERS','Kategorier');
define ('LBL_SIDE_ITEMS','(%d inl&auml;gg)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d olästa i %d fl&ouml;den)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> fl&ouml;den i <strong>%d</strong> kategorier');

define ('LBL_RATING','V&auml;rdering:');
// New in 0.5.3:
define('LBL_ENCLOSURE', 'Bilaga:');
define('LBL_DOWNLOAD', 'ladda ned');
define('LBL_PLAY', 'spela upp');

define('LBL_FOOTER_LAST_MODIF_NEVER', 'Never');

define ('LBL_ADMIN_DASHBOARD','Dashboard');


define ('LBL_ADMIN_MUST_SET_PASS','<p>No Administrator has been specified yet!</p>'
		.'<p>Please provide an Administrator username and password now!</p>');
define ('LBL_USERNAME','Username');		
define ('LBL_PASSWORD','Password');
define ('LBL_PASSWORD2','Password (again)');
define ('LBL_ADMIN_LOGIN','Please log in');
define ('LBL_ADMIN_PASS_NO_MATCH','Passwords do not match!');

define ('LBL_ADMIN_PLUGINS','Plugins');
define ('LBL_ADMIN_DOMAIN_PLUGINS_LBL','plugins');
define ('LBL_ADMIN_PLUGINS_HEADING_OPTIONS','Options');
define ('LBL_ADMIN_PLUGINS_OPTIONS','Plugin Options');
define ('LBL_ADMIN_CHECK_FOR_UPDATES','Check for Updates');
define ('LBL_ADMIN_LOGIN_BAD_LOGIN','<strong>Oops!</strong> Bad login/password');
define ('LBL_ADMIN_LOGIN_NO_ADMIN','<strong>Oops!</strong> You are successfully '
			.'logged in as %s, but you don\\\'t have administration privileges. Log in again '
			.'with administration privileges or follow your way <a href="..">home</a>');


define ('LBL_ADMIN_PLUGINS_GET_MORE', '<p style="font-size:small">'
.'Plugins are third-party scripts that offer extended functionalities. '
.'More plugins can be downloaded at the <a style="text-decoration:underline" '
.' href="http://plugins.gregarius.net/">Plugin Repository</a>.</p>');

define ('LBL_LAST_UPDATE','Last update');						
define ('LBL_ADMIN_DOMAIN_THEMES_LBL','themes');
define ('LBL_ADMIN_THEMES','Themes');
define('LBL_ADMIN_ACTIVE_THEME','Active Theme');
define('LBL_ADMIN_USE_THIS_THEME','Use this Theme');
define('LBL_ADMIN_THEME_OPTIONS','Theme Options');


define ('LBL_ADMIN_THEMES_GET_MORE', '<p style="font-size:small">'
.'Themes are made of a set of template files which specify how your Gregarius installation looks.<br />'
.'More themes can be downloaded at the <a style="text-decoration:underline" '
.' href="http://themes.gregarius.net/">Themes Repository</a>.</p>');
			
define ('LBL_STATE_FLAG','Flag (Flags an item for later reading)');
define ('LBL_FLAG','Flagged');

define ('LBL_MARK_READ', "Markera alla inl&auml;gg som l&auml;sta");
define ('LBL_MARK_CHANNEL_READ', "Markera detta fl&ouml;de som l&auml;st");
define ('LBL_MARK_FOLDER_READ',"Markera foldern som l&auml;st");

define ('LBL_MARK_CHANNEL_READ_ALL', "Mark This Feed as Read");
define ('LBL_MARK_FOLDER_READ_ALL',"Mark This Folder as Read");
define ('LBL_MARK_CATEGORY_READ_ALL',"Mark This Category as Read");
?>
