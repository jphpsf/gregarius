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

/// Language: German
define ('LOCALE_WINDOWS','deu');
define ('LOCALE_LINUX','de_DE');

define ('LBL_ITEM','Artikel');
define ('LBL_ITEMS','Artikel');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d Artikel gefunden fuer %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d passt fuer %s");
define ('LBL_H2_SEARCH', 'Suche %d Artikel');
define ('LBL_SEARCH_SEARCH_QUERY','Suchbegriffe:');
define ('LBL_SEARCH_MATCH_OR', 'Einige Begriffe (ODER)');
define ('LBL_SEARCH_MATCH_AND', 'Alle Begriffe (UND)');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', 'Exakte Uebereinstimmung');
define ('LBL_SEARCH_CHANNELS', 'Feed:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Sortiert nach Datum und Feed');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Sortiert nach Feed und Datum');
define ('LBL_SEARCH_RESULTS_PER_PAGE','Resultate pro Seite:');
define ('LBL_SEARCH_RESULTS','Resultate: ');
define ('LBL_H2_UNREAD_ITEMS','Ungelesene Artikel (<span id="ucnt">%d</span>)');
define ('LBL_H2_RECENT_ITEMS', "Kuerzliche Artikel");
define ('LBL_H2_CHANNELS','Feeds');
define ('LBL_H5_READ_UNREAD_STATS','%d Artikel, %d ungelesen');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> Begriffe (<strong>%d</strong> ungelesen) in <strong>%d</strong> Feeds');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> markierte Artikel, in <strong>%d</strong> Markierungen');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d ungelesen)</strong>');
define ('LBL_UNREAD','ungelesen');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','Alle');
define ('LBL_NAV_HOME','<span>H</span>ome');
define ('LBL_NAV_UPDATE', '<span>R</span>efresh');
define ('LBL_NAV_CHANNEL_ADMIN', 'A<span>d</span>min');
define ('LBL_NAV_SEARCH', "<span>S</span>earch");
define ('LBL_NAV_DEVLOG', "Dev<span>l</span>og");
define ('LBL_SEARCH_GO', 'Suchen');

define ('LBL_POSTED', 'Veroeffentlicht: ');
define ('LBL_FETCHED','geholt: ');
define ('LBL_BY', ' by ');

define ('LBL_AND','und');

define ('LBL_TITLE_UPDATING','Aktualisiere');
define ('LBL_TITLE_SEARCH','Suchen');
define ('LBL_TITLE_ADMIN','Feeds Admin');


define ('LBL_HOME_FOLDER','Root');
define ('LBL_VISIT', '(besuchen)');
define ('LBL_COLLAPSE','[-] verringern');
define ('LBL_EXPAND','[+] erweitern');
define ('LBL_PL_FOR','Permalink fuer ');

define ('LBL_UPDATE_CHANNEL','Feed');
define ('LBL_UPDATE_STATUS','Status');
define ('LBL_UPDATE_UNREAD','Neue Begriffe');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','Aktualisiere %d Feeds...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Zeitueberschreitung (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('LBL_ADMIN_EDIT', 'bearbeiten');
define ('LBL_ADMIN_DELETE', 'loeschen');
define ('LBL_ADMIN_DELETE2', 'Loeschen');
define ('LBL_ADMIN_RENAME', 'Umbenennen zu...');
define ('LBL_ADMIN_CREATE', 'Erstellen');
define ('LBL_ADMIN_IMPORT','Import');
define ('LBL_ADMIN_EXPORT','Export');
define ('LBL_ADMIN_DEFAULT','Standard');
define ('LBL_ADMIN_ADD','Hinzuf&uuml;gen');
define ('LBL_ADMIN_YES', 'Ja');
define ('LBL_ADMIN_NO', 'Nein');
define ('LBL_ADMIN_FOLDERS','Ordner:');
define ('LBL_ADMIN_CHANNELS','Feeds:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','Artikel:');
define ('LBL_ADMIN_CONFIG','Konfiguration:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','Abbrechen');
define ('LBL_ADMIN_LOGOUT','Abmelden');

define ('LBL_ADMIN_OPML_IMPORT','Import');
define ('LBL_ADMIN_OPML_EXPORT','Export');
define ('LBL_ADMIN_OPML_IMPORT_OPML','Import OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','Export OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... aus URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... vom File:');
define ('LBL_ADMIN_FILE_IMPORT','Import file');

define ('LBL_ADMIN_IN_FOLDER','zu Ordner:');
//define ('LBL_ADMIN_SUBMIT_CHANGES', 'Aenderungen aktivieren');
define ('LBL_ADMIN_SUBMIT_CHANGES', '&Auml;nderungen aktivieren');
define ('LBL_ADMIN_PREVIEW_CHANGES','Vorschau');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','Titel');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','Ordner');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','Beschreibung');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','Bewegen');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','Aktion');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Flags');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','Schluessel');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','Wert');
define ('LBL_ADMIN_CHANNELS_ADD','Feed:');
define ('LBL_ADMIN_FOLDERS_ADD','Ordner hinzuf&uuml;gen:');
define ('LBL_ADMIN_CHANNEL_ICON','Gezeigte Favoriten-Zeichen:');
define ('LBL_CLEAR_FOR_NONE','(Leer lassen fuer kein Icon)');

define ('LBL_ADMIN_CONFIG_VALUE','Wert f&uuml;r');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','Name');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','Autor');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','Version');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','Beschreibung');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','Aktive');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','Den Feed bearbeiten ');
define ('LBL_ADMIN_CHANNEL_NAME','Titel:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','Seiten URL:');
define ('LBL_ADMIN_CHANNEL_FOLDER','In Ordner:');
define ('LBL_ADMIN_CHANNEL_DESCR','Beschreibung:');
define ('LBL_ADMIN_FOLDER_NAME','Ordnername:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','Dieser Feed ist <strong>privat</strong>, nur Admins sehen ihn.');
define ('LBL_ADMIN_CHANNEL_DELETED','Dieser Feed ist <strong>veraltet</strong>, er wird nicht mehr aktualisiert und wird nicht mehr in der Feedspalte sichtbar sein.');

define ('LBL_ADMIN_ARE_YOU_SURE', "Wollen sie den Ordner '%s' wirklich loeschen?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','Bist du sicher, dass du den Wert fuer %s auf den Standard zuruecksetzen willst \'%s\'?');
define ('LBL_ADMIN_TRUE','Wahr');
define ('LBL_ADMIN_FALSE','Falsch');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Die URL von einem RSS Feed eintragen oder die Webseite, welche den gewuenschten Feed enthaelt )');
define ('LBL_ADMIN_FEEDS','Die folgenden Feeds wurden in <a href="%s">%s</a> gefunden, welchen wollen sie abonnieren?');

define ('LBL_ADMIN_PRUNE_OLDER','Loesche Artikel aelter als ');
define ('LBL_ADMIN_PRUNE_DAYS','Tage');
define ('LBL_ADMIN_PRUNE_MONTHS','Monate');
define ('LBL_ADMIN_PRUNE_YEARS','Jahre');
define ('LBL_ADMIN_PRUNE_KEEP','Behalte die neuesten Artikel: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Loesche auch Sticky Artikel: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','Markierte Artikel nicht loeschen... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Trage <strong>*</strong> ein, um alle markierten Artikel zu behalten)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Achtung: es werden %s Artikel (von %s) geloescht');
define ('LBL_ADMIN_PRUNING','Bereinigen');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','Ordner');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','Feeds');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','Artikel');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','Konfiguration');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Bookmarklet indizieren [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Gregarius abonnieren!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Not Authorized!</h1>\nYou are not authorized to access the administration interface.\n"
		."Please follow <a href=\"%s\">this link</a> back to the main page.\n"
		."Have a nice day!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Ungueltige Bereinigungsperiode');
define ('LBL_ADMIN_ERROR_NO_PERIOD','oops, kein Zeitraum angegeben');
define ('LBL_ADMIN_BAD_RSS_URL',"Entschuldige, Ich kann diese URL nicht verarbeiten: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Du kannst den " . LBL_HOME_FOLDER . " folder nicht loeschen");
define ('LBL_ADMIN_CANT_RENAME',"Du kannst den Ordner '%s' nicht umbenennen, da ein solcher schon existiert.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"Der Ordner '%s' existiert schon!");

define ('LBL_TAG_TAGS','Markierungen');
define ('LBL_TAG_EDIT','bearbeiten');
define ('LBL_TAG_SUBMIT','Ausloesen');
define ('LBL_TAG_CANCEL','Abbrechen');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Oops! Markierte Artikel wurden nicht gefunden &laquo;%s&raquo.");
define ('LBL_TAG_ALL_TAGS','Alle Markierungen');
define ('LBL_TAG_TAGGED','markiert');
define ('LBL_TAG_TAGGEDP','markiert');
define ('LBL_TAG_SUGGESTIONS','Vorschlaege');
define ('LBL_TAG_SUGGESTIONS_NONE','keine Vorschlaege');
define ('LBL_TAG_RELATED','Aehnliche Markierungen: ');

define ('LBL_MARK_READ', "Alle Begriffe als gelesen markieren");
define ('LBL_MARK_CHANNEL_READ', "Den Feed als gelesen markieren");
define ('LBL_MARK_FOLDER_READ',"Den Ordner als gelesen markieren");
define ('LBL_SHOW_UNREAD_ALL_SHOW','Begriffe anzeigen: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','Nur Ungelesene');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','Gelesene und Ungelesene');

define ('LBL_STATE_UNREAD','Ungelesen (Set this item\\\'s read/unread state)');
define ('LBL_STATE_STICKY','Sticky (Won\\\'t be deleted when you prune items)');
define ('LBL_STATE_PRIVATE','Privat (Nur Administratoren koennen private Eintraege sehen)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','Veraltet');
define ('LBL_PRIVATE','Privat');
define ('LBL_ADMIN_TOGGLE_STATE','Status aendern:');
define ('LBL_ADMIN_TOGGLE_SET','Aendern');
define ('LBL_ADMIN_IM_SURE','Ich bin sicher!');
// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','Angemeldet als <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','Nicht angemeldet');
define ('LBL_LOG_OUT','Abmelden');
define ('LBL_LOG_IN','Anmelden');

define ('LBL_ADMIN_OPML_IMPORT_AND','Import neuer Feeds und:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... ersetzen aller existierender Feeds und Begriffe.');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... zum Ordner hinzufuegen:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... verbinde diese mit den existierenden Ordnern.');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','Fuege %s zu %s... ');

define ('LBL_TAG_FOLDERS','Kategorien');
define ('LBL_SIDE_ITEMS','(%d Artikel)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d ungelesene in %d Feeds)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> Feeds in <strong>%d</strong> Kategorien');

define ('LBL_RATING','Bewertung:');
// New in 0.5.3:
// TRANSLATION NEEDED! Please join gregarius-i18n: http://sinless.org/mailman/listinfo/gregarius-i18n
define('LBL_ENCLOSURE', 'Enclosure:');
define('LBL_DOWNLOAD', 'download');
define('LBL_PLAY', 'play');

define('LBL_FOOTER_LAST_MODIF_NEVER', 'noch nicht');

?>
