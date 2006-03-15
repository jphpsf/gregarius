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
#
###############################################################################

/// Language: German
define ('LOCALE_WINDOWS','deu');
define ('LOCALE_LINUX','de_DE');

define ('LBL_ITEM','Artikel');
define ('LBL_ITEMS','Artikel');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d Artikel gefunden f&uuml;r %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d passt f&uuml;r %s");
define ('LBL_H2_SEARCH', 'Suche %d Artikel');
define ('LBL_SEARCH_SEARCH_QUERY','Suchbegriffe:');
define ('LBL_SEARCH_MATCH_OR', 'Einige Begriffe (ODER)');
define ('LBL_SEARCH_MATCH_AND', 'Alle Begriffe (UND)');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', 'Exakte &Uuml;bereinstimmung');
define ('LBL_SEARCH_CHANNELS', 'Feed:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Sortiert nach Datum und Feed');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Sortiert nach Feed und Datum');
define ('LBL_SEARCH_RESULTS_PER_PAGE','Resultate pro Seite:');
define ('LBL_SEARCH_RESULTS','Resultate: ');
define ('LBL_H2_UNREAD_ITEMS','Ungelesene Artikel (<span id="ucnt">%d</span>)');
define ('LBL_H2_RECENT_ITEMS', "K&uuml;rzliche Artikel");
define ('LBL_H2_CHANNELS','Feeds');
define ('LBL_H5_READ_UNREAD_STATS','%d Artikel, %d ungelesen');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> Artikel (<strong>%d</strong> ungelesen) in <strong>%d</strong> Feeds');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> markierte Artikel, in <strong>%d</strong> Tags');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d ungelesen)</strong>');
define ('LBL_UNREAD','ungelesen');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','Alle');
define ('LBL_NAV_HOME','<span>H</span>ome');
define ('LBL_NAV_UPDATE', '<span>E</span>rneuern');
define ('LBL_NAV_CHANNEL_ADMIN', 'A<span>d</span>min');
define ('LBL_NAV_SEARCH', "<span>S</span>uchen");
define ('LBL_SEARCH_GO', 'Suchen');

define ('LBL_POSTED', 'Ver&ouml;ffentlicht: ');
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
define ('LBL_PL_FOR','Permalink f&uuml;r ');

define ('LBL_UPDATE_CHANNEL','Feed');
define ('LBL_UPDATE_STATUS','Status');
define ('LBL_UPDATE_UNREAD','Neue Begriffe');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','Aktualisiere %d Feeds...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Zeit&uuml;berschreitung (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('LBL_ADMIN_EDIT', 'bearbeiten');
define ('LBL_ADMIN_DELETE', 'l&ouml;schen');
define ('LBL_ADMIN_DELETE2', 'L&ouml;schen');
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
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','Schl&uuml;ssel');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','Wert');
define ('LBL_ADMIN_CHANNELS_ADD','Feed:');
define ('LBL_ADMIN_FOLDERS_ADD','Ordner hinzuf&uuml;gen:');
define ('LBL_ADMIN_CHANNEL_ICON','Gezeigte Favoriten-Zeichen:');
define ('LBL_CLEAR_FOR_NONE','(Leer lassen f&uuml;r kein Icon)');

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

define ('LBL_ADMIN_ARE_YOU_SURE', "Wollen sie den Ordner '%s' wirklich l&ouml;schen?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','Bist du sicher, dass du den Wert f&uuml;r %s auf den Standard zur&uuml;cksetzen willst \'%s\'?');
define ('LBL_ADMIN_TRUE','Wahr');
define ('LBL_ADMIN_FALSE','Falsch');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Die URL von einem RSS Feed eintragen oder die Webseite, welche den gew&uuml;nschten Feed enth&auml;lt )');
define ('LBL_ADMIN_FEEDS','Die folgenden Feeds wurden in <a href="%s">%s</a> gefunden, welchen wollen sie abonnieren?');

define ('LBL_ADMIN_PRUNE_OLDER','L&ouml;sche Artikel &auml;lter als ');
define ('LBL_ADMIN_PRUNE_DAYS','Tage');
define ('LBL_ADMIN_PRUNE_MONTHS','Monate');
define ('LBL_ADMIN_PRUNE_YEARS','Jahre');
define ('LBL_ADMIN_PRUNE_KEEP','Behalte die neuesten Artikel: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','L&ouml;sche auch Sticky Artikel: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','Markierte Artikel nicht l&ouml;schen... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Trage <strong>*</strong> ein, um alle markierten Artikel zu behalten)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Achtung: es werden %s Artikel (von %s) gel&ouml;scht');
define ('LBL_ADMIN_PRUNING','Bereinigen');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','Ordner');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','Feeds');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','Artikel');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','Konfiguration');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Bookmarklet indizieren [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Gregarius abonnieren!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Nicht berechtigt!</h1>\nDu bist nicht berechtigt, auf die Admin Umgebung zuzugreifen.\n"
		."Bitte folge <a href=\"%s\">diesem Link</a> zur&uuml;ck zur Hauptseite.\n"
		."Sch&ouml;nen Tag noch!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Ung&uuml;ltige Bereinigungsperiode');
define ('LBL_ADMIN_ERROR_NO_PERIOD','Oops, kein Zeitraum angegeben');
define ('LBL_ADMIN_BAD_RSS_URL',"Entschuldige, ich kann diese URL nicht verarbeiten: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Du kannst den " . LBL_HOME_FOLDER . " Ordner nicht l&ouml;schen");
define ('LBL_ADMIN_CANT_RENAME',"Du kannst den Ordner '%s' nicht umbenennen, da ein solcher schon existiert.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"Der Ordner '%s' existiert schon!");

define ('LBL_TAG_TAGS','Markierungen');
define ('LBL_TAG_EDIT','Bearbeiten');
define ('LBL_TAG_SUBMIT','Ausl&ouml;sen');
define ('LBL_TAG_CANCEL','Abbrechen');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Oops! Markierte Artikel wurden nicht gefunden &laquo;%s&raquo.");
define ('LBL_TAG_ALL_TAGS','Alle Markierungen');
define ('LBL_TAG_TAGGED','markiert');
define ('LBL_TAG_TAGGEDP','markiert');
define ('LBL_TAG_SUGGESTIONS','Vorschl&auml;ge');
define ('LBL_TAG_SUGGESTIONS_NONE','keine Vorschl&auml;ge');
define ('LBL_TAG_RELATED','&Auml;hnliche Markierungen: ');

define ('LBL_SHOW_UNREAD_ALL_SHOW','Artikel anzeigen: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','Nur Ungelesene');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','Gelesene und Ungelesene');

define ('LBL_STATE_UNREAD','Ungelesen (Setzt den Status des Artikels auf gelesen/ungelesen)');
define ('LBL_STATE_STICKY','Sticky (Wird nicht gel&ouml;scht beim Bereinigen von Artikeln)');
define ('LBL_STATE_PRIVATE','Privat (Nur Administratoren k&ouml;nnen private Eintr&auml;ge sehen)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','Veraltet');
define ('LBL_PRIVATE','Privat');
define ('LBL_ADMIN_STATE','Status:');
define ('LBL_ADMIN_STATE_SET','&Auml;ndern');
define ('LBL_ADMIN_IM_SURE','Ich bin sicher!');
// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','Angemeldet als <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','Nicht angemeldet');
define ('LBL_LOG_OUT','Abmelden');
define ('LBL_LOG_IN','Anmelden');

define ('LBL_ADMIN_OPML_IMPORT_AND','Import neuer Feeds und:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... ersetzen aller existierender Feeds und Artikel.');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... zum Ordner hinzuf&uuml;gen:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... verbinde diese mit den existierenden Ordnern.');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','F&uuml;ge %s zu %s... ');

define ('LBL_TAG_FOLDERS','Kategorien');
define ('LBL_SIDE_ITEMS','(%d Artikel)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d ungelesene in %d Feeds)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> Feeds in <strong>%d</strong> Kategorien');

define ('LBL_RATING','Bewertung:');

define('LBL_ENCLOSURE', 'Enclosure (Einschl&uuml;sse):');
define('LBL_DOWNLOAD', 'download');
define('LBL_PLAY', 'abspielen');

define('LBL_FOOTER_LAST_MODIF_NEVER', 'noch nicht');
define ('LBL_ADMIN_DASHBOARD','Dashboard');


define ('LBL_ADMIN_MUST_SET_PASS','<p>Es wurde noch kein Administrator bestimmt!</p>'
		.'<p>Bitte bestimme einen Administrator-Benutzernamen und -Passwort!</p>');
define ('LBL_USERNAME','Benutzername');		
define ('LBL_PASSWORD','Passwort');
define ('LBL_PASSWORD2','Passwort (nochmals)');
define ('LBL_ADMIN_LOGIN','Bitte anmelden');
define ('LBL_ADMIN_PASS_NO_MATCH','Die Passworte sind falsch!');

define ('LBL_ADMIN_PLUGINS','Plugins');
define ('LBL_ADMIN_PLUGINS_HEADING_OPTIONS','Optionen');
define ('LBL_ADMIN_PLUGINS_OPTIONS','Plugin Optionen');
define ('LBL_ADMIN_DOMAIN_PLUGINS_LBL','Plugins');
define ('LBL_ADMIN_CHECK_FOR_UPDATES','Pr&uuml;fe auf Updates');
define ('LBL_ADMIN_LOGIN_BAD_LOGIN','<strong>Oops!</strong> Falsche(s) Anmeldung/Passwort');
define ('LBL_ADMIN_LOGIN_NO_ADMIN','<strong>Oops!</strong> Erfolgreich '
			.'angemeldet als %s, aber du hast keine Administratorenrechte. Bitte nochmals '
			.'mit Administratorenrechten anmelden oder dann zur&uuml;ck <a href="..">home</a>');


define ('LBL_ADMIN_PLUGINS_GET_MORE', '<p style="font-size:small">'
.'Plugins sind kleine Programme, welche von Dritten geschrieben worden f&uuml;r Erweiterungen von Gregarius. '
.'Mehr Plugins k&ouml;nnen unter folgendem Link heruntergeladen werden <a style="text-decoration:underline" '
.' href="http://plugins.gregarius.net/"></a>.</p>');

define ('LBL_LAST_UPDATE','Letzte Aktualisierung');						
define ('LBL_ADMIN_DOMAIN_THEMES_LBL','Themen');
define ('LBL_ADMIN_THEMES','Themen');
define('LBL_ADMIN_ACTIVE_THEME','Aktuelles Thema');
define('LBL_ADMIN_USE_THIS_THEME','Dieses Thema verwenden');
define('LBL_ADMIN_THEME_OPTIONS','Themenoptionen');
define('LBL_ADMIN_CONFIGURE','Konfigurieren');

define ('LBL_ADMIN_THEMES_GET_MORE', '<p style="font-size:small">'
.'Themen sind mit mehreren Vorlagen erstellt, welche das Aussehen von Gregarius bestimmen.<br />'
.'Mehr Themen k&ouml;nnen unter folgendem Link heruntergeladen werden <a style="text-decoration:underline" '
.' href="http://themes.gregarius.net/"></a>.</p>');

define ('LBL_STATE_FLAG','Markiere (markiert einen Artikel zum sp&auml;teren Lesen)');
define ('LBL_FLAG','Markiert');

define ('LBL_MARK_READ', "Alle Artikel als gelesen markieren");
define ('LBL_MARK_CHANNEL_READ', "Den Feed als gelesen markieren");
define ('LBL_MARK_FOLDER_READ',"Den Ordner als gelesen markieren");

define ('LBL_MARK_CHANNEL_READ_ALL', "Markiere diesen Feed als gelesen");
define ('LBL_MARK_FOLDER_READ_ALL',"Markiere diesen Ordner als gelesen");
define ('LBL_MARK_CATEGORY_READ_ALL',"Markiere diese Kategorie als gelesen");
?>
