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

define ('MARK_READ', "Tous marquer comme lus");
define ('MARK_CHANNEL_READ', "Marquer ce canal comme lu");
define ('H2_SEARCH_RESULTS_FOR', "%d r&eacute;sultats pour %s");
define ('H2_SEARCH_RESULT_FOR',"%d r&eacute;sultat pour %s");
define ('H2_SEARCH', 'Rechercher parmi %d &eacute;l&eacute;ments');
define ('SEARCH_SEARCH_QUERY','Termes de recherche:');
define ('SEARCH_MATCH_OR', 'Certains termes (OU)');
define ('SEARCH_MATCH_AND', 'Tous les termes (ET)');
define ('SEARCH_MATCH_EXACT', 'Phrase');
define ('SEARCH_CHANNELS', 'Canal:');
define ('SEARCH_ORDER_DATE_CHANNEL','Trier par date, canal');
define ('SEARCH_ORDER_CHANNEL_DATE','Trier par canal, date');
define ('SEARCH_RESULTS','R&eacute;sultats: ');
define ('SEARCH_RESULTS_PER_PAGE','R&eacute;sultats par page:'); 
define ('H2_UNREAD_ITEMS',"Elements non lus (%d)");
define ('H2_RECENT_ITEMS', "Elements recents");
define ('H2_CHANNELS','Canaux');
define ('H5_READ_UNREAD_STATS','%d &eacute;l&eacute;ments , %d non lus');
define ('ITEMCOUNT_PF', '<strong>%d</strong> &eacute;l&eacute;ments (<strong>%d</strong> non lus) dans <strong>%d</strong> canaux');
define ('UNREAD_PF', '(<strong>%d non lus</strong>)');

define ('FTR_POWERED_BY', "is powered by ");
define ('ALL','Tous');
define ('NAV_HOME','D&eacute;part');
define ('NAV_UPDATE', 'Rafra&icirc;chir');
define ('NAV_CHANNEL_ADMIN', 'Administration');
define ('NAV_SEARCH', "Chercher");
define ('NAV_DEVLOG', "Devlog");
define ('SEARCH_GO', 'Rechercher');

define ('POSTED', 'Publi&eacute;: ');
define ('FETCHED', 'R&eacute;cup&eacute;r&eacute;: ');

define ('TITLE_UPDATING','Mise &agrave; jour');
define ('TITLE_SEARCH','Recherche');
define ('TITLE_ADMIN','Administration des canaux');

define ('HOME_FOLDER','Racine');
define ('VISIT', '(visiter)');
define ('COLLAPSE','[-] collapser');
define ('EXPAND','[+] &eacute;tendre');
define ('PL_FOR','Lien pour ');

define ('UPDATE_CHANNEL','Canal');
define ('UPDATE_STATUS','Etat');
define ('UPDATE_UNDREAD','Nouveaux &eacute;l&eacute;ments');

define ('UPDATE_STATUS_OK','OK (HTTP 200)');
define ('UPDATE_STATUS_CACHED', 'OK (Depuis cache)');
define ('UPDATE_STATUS_ERROR','ERREUR');
define ('UPDATE_H2','Mise &agrave; jour de %d Canaux...');
define ('UPDATE_CACHE_TIMEOUT','Cache (HTTP Timeout)');
define ('UPDATE_NOT_MODIFIED','OK (304 Not modified)'); 
define ('UPDATE_NOT_FOUND','404 Pas Trouv&eacute; (Depuis cache)');


// admin
define ('ADMIN_EDIT', '&eacute;diter');
define ('ADMIN_DELETE', 'effacer');
define ('ADMIN_DELETE2', 'Effacer');
define ('ADMIN_RENAME', 'Renommer en...');
define ('ADMIN_CREATE', 'Cr&eacute;er');
define ('ADMIN_IMPORT','Importer');
define ('ADMIN_EXPORT','Exporter');
define ('ADMIN_DEFAULT','d&eacute;faut');
define ('ADMIN_ADD','Ajouter');
define ('ADMIN_YES', 'Oui');
define ('ADMIN_NO', 'Non');
define ('ADMIN_FOLDERS','R&eacute;pertoires:');
define ('ADMIN_CHANNELS','Canaux:');
define ('ADMIN_OPML','OPML:');
define ('ADMIN_CONFIG','Configuration');                                                                                                         
define ('ADMIN_IN_FOLDER','au r&eacute;pertoire:');
define ('ADMIN_SUBMIT_CHANGES', 'Poster les modifications');
define ('ADMIN_CHANNELS_HEADING_TITLE','Titre');
define ('ADMIN_CHANNELS_HEADING_FOLDER','R&eacute;pertoire');
define ('ADMIN_CHANNELS_HEADING_DESCR','Description');
define ('ADMIN_CHANNELS_HEADING_MOVE','D&eacute;placer');
define ('ADMIN_CHANNELS_HEADING_ACTION','Action');
define ('ADMIN_CHANNELS_ADD','Ajouter un canal:');
define ('ADMIN_CHANNELS_HEADING_KEY','Clef');
define ('ADMIN_CHANNELS_HEADING_VALUE','Valeur');  
define ('ADMIN_FOLDERS_ADD','Ajouter un r&eacute;pertoire:');
define ('ADMIN_CHANNEL_ICON','Icone affichee:');
define ('CLEAR_FOR_NONE','(Laisser vide pour ne pas afficher d\icone)');
define ('ADMIN_OPML_EXPORT','Exporter OPML:');
define ('ADMIN_OPML_IMPORT','Importer OPML:');

define ('ADMIN_CHANNEL_NAME','Titre:');
define ('ADMIN_CHANNEL_RSS_URL','URL RSS:');
define ('ADMIN_CHANNEL_SITE_URL','URL Site:');
define ('ADMIN_CHANNEL_FOLDER','Dans le r&eacute;pertoire:');
define ('ADMIN_CHANNEL_DESCR','Description:');
define ('ADMIN_FOLDER_NAME','Nom du r&eacute;pertoire:');
define ('ADMIN_ARE_YOU_SURE', "Êtes-vous sûr de vouloir &eacute;ffacer '%s'?");

define('ADMIN_MOVE_UP','&uarr;');
define('ADMIN_MOVE_DOWN','&darr;');
define('ADMIN_ADD_CHANNEL_EXPL','(Veuillez saisir l\'URL d\'un flux RSS ou d\'un site Internet)');
define('ADMIN_FEEDS','Les flux suivants ont &eacute;t&eacute; d&eacute;tect&eacute; dans <a href=\"%s\">%s</a>, lequel d&eacute;sirez-vous souscrir?');
?>
