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

define ('ITEM','&eacute;l&eacute;ment');
define ('ITEMS','&eacute;l&eacute;ments');
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
define ('H2_UNREAD_ITEMS',"&Eacute;l&eacute;ments non lus (%d)");
define ('H2_RECENT_ITEMS', "&Eacute;l&eacute;ments r&eacute;cents");
define ('H2_CHANNELS','Canaux');
define ('H5_READ_UNREAD_STATS','%d &eacute;l&eacute;ments , %d non lus');
define ('ITEMCOUNT_PF', '<strong>%d</strong> &eacute;l&eacute;ments (<strong>%d</strong> non lus) dans <strong>%d</strong> canaux');
define ('UNREAD_PF', '(<strong>%d non lus</strong>)');

define ('FTR_POWERED_BY', "est propuls&eacute; par ");
define ('ALL','Tous');
define ('NAV_HOME','Accueil');
define ('NAV_UPDATE', 'Mise &agrave; jour');
define ('NAV_CHANNEL_ADMIN', 'Administration');
define ('NAV_SEARCH', "Recherche");
define ('NAV_DEVLOG', "Journal du d&eacute;veloppeur");
define ('SEARCH_GO', 'Rechercher');

define ('POSTED', 'Publi&eacute;: ');
define ('FETCHED', 'R&eacute;cup&eacute;r&eacute;: ');

define ('LBL_AND','et');

define ('TITLE_UPDATING','Mise &agrave; jour');
define ('TITLE_SEARCH','Recherche');
define ('TITLE_ADMIN','Administration des canaux');

define ('HOME_FOLDER','Racine');
define ('VISIT', '(visiter)');
define ('COLLAPSE','[-] replier');
define ('EXPAND','[+] d&eacute;plier');
define ('PL_FOR','Lien pour ');

define ('UPDATE_CHANNEL','Canal');
define ('UPDATE_STATUS','Etat');
define ('UPDATE_UNDREAD','Nouveaux &eacute;l&eacute;ments');

define ('UPDATE_STATUS_OK','OK (HTTP 200)');
define ('UPDATE_STATUS_CACHED', 'OK (Depuis cache)');
define ('UPDATE_STATUS_ERROR','ERREUR');
define ('UPDATE_H2','Mise &agrave; jour de %d Canaux...');
define ('UPDATE_CACHE_TIMEOUT','Cache (HTTP Timeout)');
define ('UPDATE_NOT_MODIFIED','OK (304 Pas de modifications)'); 
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
define ('ADMIN_ITEM','El&eacute;ments:');
define ('ADMIN_CONFIG','Configuration:');

define ('ADMIN_IN_FOLDER','au r&eacute;pertoire:');
define ('ADMIN_SUBMIT_CHANGES', 'Poster les modifications');
define ('ADMIN_PREVIEW_CHANGES','Pr&eacute;visualiser');
define ('ADMIN_CANCEL','Annuler');
define ('ADMIN_CHANNELS_HEADING_TITLE','Titre');
define ('ADMIN_CHANNELS_HEADING_FOLDER','R&eacute;pertoire');
define ('ADMIN_CHANNELS_HEADING_DESCR','Description');
define ('ADMIN_CHANNELS_HEADING_MOVE','D&eacute;placer');
define ('ADMIN_CHANNELS_HEADING_PRIVATE','Priv&eacute;');
define ('ADMIN_CHANNELS_HEADING_ACTION','Action');
define ('ADMIN_CHANNELS_HEADING_KEY','Clef');
define ('ADMIN_CHANNELS_ADD','Ajouter un canal:');
define ('ADMIN_CHANNELS_HEADING_VALUE','Valeur');  
define ('ADMIN_FOLDERS_ADD','Ajouter un r&eacute;pertoire:');
define ('ADMIN_CHANNEL_ICON','Icone affich&eacute;e:');
define ('CLEAR_FOR_NONE','(Laisser vide pour ne pas afficher d\icone)');
define ('ADMIN_OPML_EXPORT','Exporter OPML:');
define ('ADMIN_OPML_IMPORT','Importer OPML:');
define ('ADMIN_CONFIG_VALUE','Valeur');

define ('ADMIN_CHANNEL_EDIT_CHANNEL','Editer le canal ');
define ('ADMIN_CHANNEL_NAME','Titre:');
define ('ADMIN_CHANNEL_RSS_URL','URL RSS:');
define ('ADMIN_CHANNEL_SITE_URL','URL Site:');
define ('ADMIN_CHANNEL_FOLDER','Dans le r&eacute;pertoire:');
define ('ADMIN_CHANNEL_DESCR','Description:');
define ('ADMIN_FOLDER_NAME','Nom du r&eacute;pertoire:');
define ('ADMIN_CHANNEL_PRIVATE','Ce canal est <strong>priv&eacute</strong>, les administrateurs uniquement peuvent le voir :');
define ('ADMIN_ARE_YOU_SURE', "Etes-vous sur de vouloir &eacute;ffacer '%s'?");
define ('ADMIN_ARE_YOU_SURE_DEFAULT',"Etes-vous sur de vouloir remettre la valeur de la cl&eacute '%s' &agrave; sa valeur par d&eacute;faut '%s'?");
define ('ADMIN_TRUE','Oui');
define ('ADMIN_FALSE','Non');
define ('ADMIN_MOVE_UP','&uarr;');
define ('ADMIN_MOVE_DOWN','&darr;');
define ('ADMIN_ADD_CHANNEL_EXPL','(Veuillez saisir l\'URL d\'un flux RSS ou d\'un site Internet)');
define ('ADMIN_FEEDS','Les flux suivants ont &eacute;t&eacute; d&eacute;tect&eacute;s dans <a href=\"%s\">%s</a>. Auquel d&eacute;sirez-vous souscrir?');

define ('ADMIN_PRUNE_OLDER','Effacer les &eacute;l&eacute;ments plus anciens que ');
define ('ADMIN_PRUNE_DAYS','jours');
define ('ADMIN_PRUNE_MONTHS','mois');
define ('ADMIN_PRUNE_YEARS','ans');
define ('PRUNE_KEEP','Ne garder que ce nombre d\'&eacute;l&eacute;ments: ');
define ('ADMIN_PRUNE_INCLUDE_STICKY','Effacer les &eacute;l&eacute;ments \'Sticky\' aussi: ');
define ('ADMIN_PRUNE_EXCLUDE_TAGS','Ne pas effacer les &eacute;l&eacute;ments tagg&eacute;s ...');
define ('ADMIN_ABOUT_TO_DELETE','Attention: vous allez effacer %s  &eacute;l&eacute;ments (sur %s)');
define ('ADMIN_PRUNING','Suppression');
define ('ADMIN_DOMAIN_FOLDER_LBL','r&eacute;pertoires');
define ('ADMIN_DOMAIN_CHANNEL_LBL','canaux');
define ('ADMIN_DOMAIN_ITEM_LBL','el&eacute;ments');
define ('ADMIN_DOMAIN_CONFIG_LBL','configuration');
define ('ADMIN_DOMAIN_OPML_LBL','opml');
define ('ADMIN_BOOKMARKET_LABEL','Bookmarklet de souscription [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('ADMIN_BOOKMARKLET_TITLE','Ajouter &agrave; Gregarius!');

define ('ADMIN_ERROR_NOT_AUTHORIZED', 
		"<h1>Non autoris&eacute;</h1>\nVous n'&ecirc;tes pas autoris&eacute; &agrave; acc&eacute;der la section d'administration."
		."Veuillez suivre <a href=\"%s\">ce lien</a> pour revenir &agrave; la page d'acceuil.\n"
		."Merci.");

define ('ADMIN_ERROR_PRUNING_PERIOD',"P&eacute; d'effacement non valide");
define ('ADMIN_ERROR_NO_PERIOD',"P&eacute; non sp&eacute;cifi&eacute;e");
define ('ADMIN_BAD_RSS_URL',"Je ne sais pas comment g&eacute;rer cette URL '%s'");
define ('ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Vous ne pouvez pas effacer le r&eacute;pertoire " . HOME_FOLDER ."!");
define ('ADMIN_CANT_RENAME',"Vous ne pouvez pas renommer cet &eacute;l&eacute;ment '%s' parce que un &eacute;l&eacute;ment du m&ecirc;me nom existe d&eacute;j&agrave;!");
define ('ADMIN_ERROR_CANT_CREATE',"Un r&eacute;pertoire du m&ecirc;me nom ('%s') existe d&eacute;j&agrave;!");

define ('TAG_TAGS','Tags');
define ('TAG_EDIT','modifier');
define ('TAG_SUBMIT','valider');
define ('TAG_CANCEL','annuler');
define ('TAG_SUBMITTING','...');
define ('TAG_ERROR_NO_TAG',"D&eacute;sol&eacute;, aucun &eacute;l&eacute;ment taggu&eacute; &laquo;%s&raquo; n'a pu &ecirc;tre trouv&eacute;.");
define ('TAG_ALL_TAGS','Tous les Tags');
define ('TAG_TAGGED','tagg&eacute;');
define ('TAG_TAGGEDP','tagg&eacute;s');
define ('TAG_SUGGESTIONS','suggestions');
define ('TAG_SUGGESTIONS_NONE','aucune suggestion');
define ('TAG_RELATED','Tags similaires: ');

define ('MARK_READ', "Tous marquer comme lus");
define ('MARK_CHANNEL_READ', "Marquer ce canal comme lu");
define ('SHOW_UNREAD_ALL_SHOW','Afficher les &eacute;l&eacute;ments: ');
define ('SHOW_UNREAD_ALL_UNREAD_ONLY','Non lus uniquement');
define ('SHOW_UNREAD_ALL_READ_AND_UNREAD','Lus et non lus');

?>
