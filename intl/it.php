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

/// Language: Italiano

// Nota: tradotto in cinque minuti, non riletto e mai testato :)
// Se trovi degli errori o pensi di poter fare meglio, grazie di
// mandare correzioni a mbonetti@gmail.com

define ('LOCALE_WINDOWS','ita'); //??
define ('LOCALE_LINUX','it_IT');


define ('LBL_ITEM','elemento');
define ('LBL_ITEMS','elementi');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d risultati per %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d risultato per %s");
define ('LBL_H2_SEARCH', 'Cerca tra %d elementi');
define ('LBL_SEARCH_SEARCH_QUERY','Termini di ricerca:');
define ('LBL_SEARCH_MATCH_OR', 'Alcuni termini (OR)');
define ('LBL_SEARCH_MATCH_AND', 'Tutti i termini (AND)');
define ('LBL_SEARCH_MATCH_EXACT', 'Frase esatta');
define ('LBL_SEARCH_CHANNELS', 'Feed:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Ordina per data, feed');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Ordina per feed, data');
define ('LBL_SEARCH_RESULTS_PER_PAGE','Risultati per pagina:');
define ('LBL_SEARCH_RESULTS','Risultati: ');
define ('LBL_H2_UNREAD_ITEMS','<span id="ucnt">%d</span> elementi non letti');
define ('LBL_H2_RECENT_ITEMS', "Elementi recenti");
define ('LBL_H2_CHANNELS','Feeds');
define ('LBL_H5_READ_UNREAD_STATS','%d elementi, %d non letti');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> elementi (<strong>%d</strong> non letti) in <strong>%d</strong> feed');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> elementi taggati, <strong>%d</strong> tags');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d non letti)</strong>');
define ('LBL_UNREAD','non letti');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','Tutti');
define ('LBL_NAV_HOME','<span>H</span>ome');
define ('LBL_NAV_UPDATE', 'A<span>g</span>giorna');
define ('LBL_NAV_CHANNEL_ADMIN', '<span>A</span>mministrazione');
define ('LBL_NAV_SEARCH', "<span>R</span>icerca");
define ('LBL_NAV_DEVLOG', "Dev<span>l</span>og");
define ('LBL_SEARCH_GO', 'Cerca');

define ('LBL_POSTED', 'Pubblicato: ');
define ('LBL_FETCHED','Scaricato: ');
define ('LBL_BY', ' da ');

define ('LBL_AND','e');

define ('LBL_TITLE_UPDATING','Aggiornando');
define ('LBL_TITLE_SEARCH','Ricerca');
define ('LBL_TITLE_ADMIN','Amministrazione');


define ('LBL_HOME_FOLDER','Radice');
define ('LBL_VISIT', '(visita)');
define ('LBL_COLLAPSE','[-] collassa');
define ('LBL_EXPAND','[+] espandi');
define ('LBL_PL_FOR','Permalink per ');

define ('LBL_UPDATE_CHANNEL','Feed');
define ('LBL_UPDATE_STATUS','Statuto');
define ('LBL_UPDATE_UNREAD','Nuovi elementi');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Cache locale)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','Aggiorno %d Feeds...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Cache locale)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Cache locale)');
// admin
define ('LBL_ADMIN_EDIT', 'modifica');
define ('LBL_ADMIN_DELETE', 'cancella');
define ('LBL_ADMIN_DELETE2', 'Cancella');
define ('LBL_ADMIN_RENAME', 'Rinomina in...');
define ('LBL_ADMIN_CREATE', 'Crea');
define ('LBL_ADMIN_IMPORT','Importa');
define ('LBL_ADMIN_EXPORT','Esporta');
define ('LBL_ADMIN_DEFAULT','resetta');
define ('LBL_ADMIN_ADD','Aggiungi');
define ('LBL_ADMIN_YES', 'Si');
define ('LBL_ADMIN_NO', 'No');
define ('LBL_ADMIN_FOLDERS','Cartelle:');
define ('LBL_ADMIN_CHANNELS','Feeds:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','Elementi:');
define ('LBL_ADMIN_CONFIG','Configurazione:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','Cancella');
define ('LBL_ADMIN_LOGOUT','Logout');

define ('LBL_ADMIN_OPML_IMPORT','Importa');
define ('LBL_ADMIN_OPML_EXPORT','Esporta');
define ('LBL_ADMIN_OPML_IMPORT_OPML','Importa OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','Esporta OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... da un\' URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... da un File:');
define ('LBL_ADMIN_FILE_IMPORT','Importa file');

define ('LBL_ADMIN_IN_FOLDER','alla cartella:');
define ('LBL_ADMIN_SUBMIT_CHANGES', 'Invia Modifiche');
define ('LBL_ADMIN_PREVIEW_CHANGES','Preview');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','Titolo');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','Cartella');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','Descrizione');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','Sposta');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','Azione');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Flags');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','Chiave');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','Valore');
define ('LBL_ADMIN_CHANNELS_ADD','Aggiungi un Feed:');
define ('LBL_ADMIN_FOLDERS_ADD','Aggiungi una Cartella:');
define ('LBL_ADMIN_CHANNEL_ICON','Favicon');
define ('LBL_CLEAR_FOR_NONE','(Lascia vuoto per nessuna favicon)');

define ('LBL_ADMIN_CONFIG_VALUE','Valore di');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','Nome');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','Autore');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','Versione');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','Descrizione');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','Attivo');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','Edita il feed ');
define ('LBL_ADMIN_CHANNEL_NAME','Titolo:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','URL RSS:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','URL del Sito:');
define ('LBL_ADMIN_CHANNEL_FOLDER','Nella Cartella:');
define ('LBL_ADMIN_CHANNEL_DESCR','Descrizione:');
define ('LBL_ADMIN_FOLDER_NAME','Nome cartella:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','Questo feed &egrave; <strong>privato</strong>, Solo amministratori possono vederlo.');
define ('LBL_ADMIN_CHANNEL_DELETED','Questo feed &egrave; <strong>in disuso</strong>, Non sar&agrave; pi&ugrave; aggiornato e sar&agrave; invisibile nella colonna dei feeds.');

define ('LBL_ADMIN_ARE_YOU_SURE', "Sei sicuro di voler cancellare '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','Sei sicuro di voler ripristinare %s al suo valore d\'origine \'%s\'?');
define ('LBL_ADMIN_TRUE','Vero');
define ('LBL_ADMIN_FALSE','Falso');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Digita l\'URL del feed RSS o del sito al quale vorresti abbonarti)');
define ('LBL_ADMIN_FEEDS','I feed seguenti sono stati rinvenuti in <a href="%s">%s</a>, a quale vorresti abbonarti?');

define ('LBL_ADMIN_PRUNE_OLDER','Cancella elementi pi&ugrave; vecchi che...');
define ('LBL_ADMIN_PRUNE_DAYS','giorni');
define ('LBL_ADMIN_PRUNE_MONTHS','mesi');
define ('LBL_ADMIN_PRUNE_YEARS','anni');
define ('LBL_ADMIN_PRUNE_KEEP','Tieni soltanto gli elementi pi&ugrave; recenti: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Elimina anche gli elementi \'Sticky\': ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','Non eliminare gli elementi taggati... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Digita <strong>*</strong> per tenere tutti gli elementi taggati)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Attenzione: stai per cancellare %s elementi (su %s)');
define ('LBL_ADMIN_PRUNING','Eliminare');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','cartelle');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','feeds');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','elementi');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','configurazione');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Bookmarklet d\'abbonamento [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Abbona in Gregarius!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Non Autorizzato!</h1>\nNon sei autorizzato ad accedere all\'interfaccia d\'amministrazione.\n"
		."Segui <a href=\"%s\">questo link</a> per tornare alla pagina principale.\n"
		."Buona giornata :)");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Periodo invalido');
define ('LBL_ADMIN_ERROR_NO_PERIOD','Non avete specificato nessun periodo');
define ('LBL_ADMIN_BAD_RSS_URL',"Spiacente, non so cosa fare con questa URL: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Non potete cancellare la cartella " . LBL_HOME_FOLDER);
define ('LBL_ADMIN_CANT_RENAME',"Non puoi rinominare la cartella in '%s' Perch&eacute; una cartella con quel nome esiste gia.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"Una cartella chiamata '%s' esiste gia!");

define ('LBL_TAG_TAGS','Tags');
define ('LBL_TAG_EDIT','edita');
define ('LBL_TAG_SUBMIT','invia');
define ('LBL_TAG_CANCEL','cancella');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Nessun tag &laquo;%s&raquo; &egrave; stato trovato.");
define ('LBL_TAG_ALL_TAGS','Tutti i Tags');
define ('LBL_TAG_TAGGED','taggato');
define ('LBL_TAG_TAGGEDP','taggati');
define ('LBL_TAG_SUGGESTIONS','suggerimenti');
define ('LBL_TAG_SUGGESTIONS_NONE','nessun suggerimento');
define ('LBL_TAG_RELATED','Tags vicini: ');

define ('LBL_MARK_READ', "Marca tutti gli elementi come letti");
define ('LBL_MARK_CHANNEL_READ', "Marca questo feed come letto");
define ('LBL_MARK_FOLDER_READ',"Marca questa cartella come letta");
define ('LBL_SHOW_UNREAD_ALL_SHOW','Mostra gli elementi: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','Non letti unicamente');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','Letti e non letti');

define ('LBL_STATE_UNREAD','Non letto (Definisce lo stato letto/non letto di questo elemento)');
define ('LBL_STATE_STICKY','Sticky (Non sar&agrave; eliminato quando eliminerai degli elementi)');
define ('LBL_STATE_PRIVATE','Privato (Unicamente gli amministratori vedono degli elementi privati)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','In disuso');
define ('LBL_PRIVATE','Privato');
define ('LBL_ADMIN_TOGGLE_STATE','Stato:');
define ('LBL_ADMIN_TOGGLE_SET','Cambia');
define ('LBL_ADMIN_IM_SURE','Sono sicuro!');

// new in 0.5.1:

// Requires translation!
define ('LBL_LOGGED_IN_AS','Logged in as <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','Not logged in');
define ('LBL_LOG_OUT','Logout');
define ('LBL_LOG_IN','Login');

define ('LBL_ADMIN_OPML_IMPORT_AND','Importa i nuovi feeds e:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... rimpiazza tutti i feeds ed elementi esistenti');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... aggiungili alla cartella:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... aggiungili ai feeds esistenti');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','Importo %s in %s... ');

define ('LBL_TAG_FOLDERS','Categorie');
define ('LBL_SIDE_ITEMS','(%d elementi)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d non letti in %d feeds)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> feeds in <strong>%d</strong> categorie');

define ('LBL_RATING','Rating:');
// New in 0.5.3:
// TRANSLATION NEEDED! Please join gregarius-i18n: http://sinless.org/mailman/listinfo/gregarius-i18n
define('LBL_ENCLOSURE', 'Enclosure:');
define('LBL_DOWNLOAD', 'scarica');
define('LBL_PLAY', 'riproduci');

define('LBL_FOOTER_LAST_MODIF_NEVER', 'nessuna');
define ('LBL_ADMIN_DASHBOARD','Dashboard');


define ('LBL_ADMIN_MUST_SET_PASS','<p>No Administrator has been specified yet!</p>'
		.'<p>Please provide an Administrator username and password now!</p>');
define ('LBL_USERNAME','Username');		
define ('LBL_PASSWORD','Password');
define ('LBL_ADMIN_LOGIN','Please log in');


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

?>
