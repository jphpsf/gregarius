<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
#
###############################################################################
# File: $Id$ $Name:  $
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
# $Log: fr.php,v $
# Revision 1.59  2005/06/14 12:55:59  mbonetti
# use javascript to collapse folders
#
# Revision 1.58  2005/06/11 16:59:12  mbonetti
# prefixed all labels
#
# Revision 1.57  2005/06/09 11:45:01  mbonetti
# Swedish and Spanish language packs
#
# Revision 1.56  2005/06/05 06:27:29  mbonetti
# option: display unread count (feed,folder,total) in the document title
#
# Revision 1.55  2005/05/20 07:42:21  mbonetti
# CVS Log messages in the file header
#
#
###############################################################################


/// Language: Fran&ccedil;ais

define ('LBL_ITEM','&eacute;l&eacute;ment');
define ('LBL_ITEMS','&eacute;l&eacute;ments');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d r&eacute;sultats pour %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d r&eacute;sultat pour %s");
define ('LBL_H2_SEARCH', 'Rechercher parmi %d &eacute;l&eacute;ments');
define ('LBL_SEARCH_SEARCH_QUERY','Termes de recherche:');
define ('LBL_SEARCH_MATCH_OR', 'Certains termes (OU)');
define ('LBL_SEARCH_MATCH_AND', 'Tous les termes (ET)');
define ('LBL_SEARCH_MATCH_EXACT', 'Phrase');
define ('LBL_SEARCH_CHANNELS', 'Canal:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Trier par date, canal');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Trier par canal, date');
define ('LBL_SEARCH_RESULTS','R&eacute;sultats: ');
define ('LBL_SEARCH_RESULTS_PER_PAGE','R&eacute;sultats par page:'); 
define ('LBL_H2_UNREAD_ITEMS',"&Eacute;l&eacute;ments non lus (%d)");
define ('LBL_H2_RECENT_ITEMS', "&Eacute;l&eacute;ments r&eacute;cents");
define ('LBL_H2_CHANNELS','Canaux');
define ('LBL_H5_READ_UNREAD_STATS','%d &eacute;l&eacute;ments , %d non lus');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> &eacute;l&eacute;ments (<strong>%d</strong> non lus) dans <strong>%d</strong> canaux');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d non lus)</strong>');
define ('LBL_UNREAD','non lus');

define ('LBL_FTR_POWERED_BY', "est propuls&eacute; par ");
define ('LBL_ALL','Tous');
define ('LBL_NAV_HOME','Accueil');
define ('LBL_NAV_UPDATE', 'Mise &agrave; jour');
define ('LBL_NAV_CHANNEL_ADMIN', 'Administration');
define ('LBL_NAV_SEARCH', "Recherche");
define ('LBL_NAV_DEVLOG', "Journal du d&eacute;veloppeur");
define ('LBL_SEARCH_GO', 'Rechercher');

define ('LBL_POSTED', 'Publi&eacute;: ');
define ('LBL_FETCHED', 'R&eacute;cup&eacute;r&eacute;: ');

define ('LBL_AND','et');

define ('LBL_TITLE_UPDATING','Mise &agrave; jour');
define ('LBL_TITLE_SEARCH','Recherche');
define ('LBL_TITLE_ADMIN','Administration des canaux');

define ('LBL_HOME_FOLDER','Racine');
define ('LBL_VISIT', '(visiter)');
define ('LBL_COLLAPSE','[-] replier');
define ('LBL_EXPAND','[+] d&eacute;plier');
define ('LBL_PL_FOR','Lien pour ');

define ('LBL_UPDATE_CHANNEL','Canal');
define ('LBL_UPDATE_STATUS','Etat');
define ('LBL_UPDATE_UNDREAD','Nouveaux &eacute;l&eacute;ments');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Depuis cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERREUR');
define ('LBL_UPDATE_H2','Mise &agrave; jour de %d Canaux...');
define ('LBL_UPDATE_CACHE_TIMEOUT','Cache (HTTP Timeout)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Pas de modifications)'); 
define ('LBL_UPDATE_NOT_FOUND','404 Non Trouv&eacute; (Depuis cache)');
// admin
define ('LBL_ADMIN_EDIT', '&eacute;diter');
define ('LBL_ADMIN_DELETE', 'effacer');
define ('LBL_ADMIN_DELETE2', 'Effacer');
define ('LBL_ADMIN_RENAME', 'Renommer en...');
define ('LBL_ADMIN_CREATE', 'Cr&eacute;er');
define ('LBL_ADMIN_IMPORT','Importer');
define ('LBL_ADMIN_EXPORT','Exporter');
define ('LBL_ADMIN_DEFAULT','d&eacute;faut');
define ('LBL_ADMIN_ADD','Ajouter');
define ('LBL_ADMIN_YES', 'Oui');
define ('LBL_ADMIN_NO', 'Non');
define ('LBL_ADMIN_FOLDERS','R&eacute;pertoires:');
define ('LBL_ADMIN_CHANNELS','Canaux:');
define ('LBL_ADMIN_OPML','OPML:');
define ('LBL_ADMIN_ITEM','El&eacute;ments:');
define ('LBL_ADMIN_CONFIG','Configuration:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','Annuler');

define ('LBL_ADMIN_IN_FOLDER','au r&eacute;pertoire:');
define ('LBL_ADMIN_SUBMIT_CHANGES', 'Poster les modifications');
define ('LBL_ADMIN_PREVIEW_CHANGES','Pr&eacute;visualiser');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','Titre');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','R&eacute;pertoire');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','Description');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','D&eacute;placer');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Etat');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','Action');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','Clef');
define ('LBL_ADMIN_CHANNELS_ADD','Ajouter un canal:');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','Valeur');  
define ('LBL_ADMIN_FOLDERS_ADD','Ajouter un r&eacute;pertoire:');
define ('LBL_ADMIN_CHANNEL_ICON','Icone affich&eacute;e:');
define ('LBL_CLEAR_FOR_NONE','(Laisser vide pour ne pas afficher d\'ic&ocirc;ne)');


define ('LBL_ADMIN_OPML_IMPORT','Importer');
define ('LBL_ADMIN_OPML_EXPORT','Exporter');
define ('LBL_ADMIN_OPML_IMPORT_OPML','Importer OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','Exporter OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... depuis une URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... depuis un fichier:');
define ('LBL_ADMIN_FILE_IMPORT','Importer le fichier');


define ('LBL_ADMIN_CONFIG_VALUE','Valeur');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','Nom');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','Auteur');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','Version');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','Description');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','Actif');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','Editer le canal ');
define ('LBL_ADMIN_CHANNEL_NAME','Titre:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','URL RSS:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','URL Site:');
define ('LBL_ADMIN_CHANNEL_FOLDER','Dans le r&eacute;pertoire:');
define ('LBL_ADMIN_CHANNEL_DESCR','Description:');
define ('LBL_ADMIN_FOLDER_NAME','Nom du r&eacute;pertoire:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','Ce canal est <strong>priv&eacute;</strong>, Seul l\'administrateur peut le visionner');
define ('LBL_ADMIN_CHANNEL_DELETED','Ce canal est <strong>d&eacute;sactiv&eacute;</strong>, il ne sera plus mis &agrave; jour et n\'est pas visible dans la colonne des canaux.');
define ('LBL_ADMIN_ARE_YOU_SURE', "Etes-vous sur de vouloir &eacute;ffacer '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT',"Etes-vous sur de vouloir remettre la valeur de la cl&eacute '%s' &agrave; sa valeur par d&eacute;faut '%s'?");
define ('LBL_ADMIN_TRUE','Oui');
define ('LBL_ADMIN_FALSE','Non');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Veuillez saisir l\'URL d\'un flux RSS ou d\'un site Internet)');
define ('LBL_ADMIN_FEEDS','Les flux suivants ont &eacute;t&eacute; d&eacute;tect&eacute;s dans <a href=\"%s\">%s</a>. Auquel d&eacute;sirez-vous souscrir ?');

define ('LBL_ADMIN_PRUNE_OLDER','Effacer les &eacute;l&eacute;ments plus anciens que ');
define ('LBL_ADMIN_PRUNE_DAYS','jours');
define ('LBL_ADMIN_PRUNE_MONTHS','mois');
define ('LBL_ADMIN_PRUNE_YEARS','ans');
define ('LBL_ADMIN_PRUNE_KEEP','Ne garder que ce nombre d\'&eacute;l&eacute;ments: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Effacer les &eacute;l&eacute;ments Persistants aussi: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','Ne pas effacer les &eacute;l&eacute;ments tagg&eacute;s ...');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Entrez <strong>*</strong> pour garder tous les &eacute;l&eacute;ments tagg&eacute;s)');
define ('LBL_ADMIN_ABOUT_TO_DELETE','Attention: vous allez effacer %s  &eacute;l&eacute;ments (sur %s)');
define ('LBL_ADMIN_PRUNING','Suppression');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','r&eacute;pertoires');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','canaux');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','el&eacute;ments');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','configuration');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Bookmarklet de souscription [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Ajouter &agrave; Gregarius!');

define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
		"<h1>Non autoris&eacute;</h1>\nVous n'&ecirc;tes pas autoris&eacute; &agrave; acc&eacute;der &agrave; la section d'administration."
		."Veuillez suivre <a href=\"%s\">ce lien</a> pour revenir &agrave; la page d'acceuil.\n"
		."Merci.");

define ('LBL_ADMIN_ERROR_PRUNING_PERIOD',"P&eacute;riode d'effacement non valide");
define ('LBL_ADMIN_ERROR_NO_PERIOD',"Aucune p&eacute;riode sp&eacute;cifi&eacute;e!");
define ('LBL_ADMIN_BAD_RSS_URL',"Je ne sais pas comment g&eacute;rer cette URL '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Vous ne pouvez pas effacer le r&eacute;pertoire " . LBL_HOME_FOLDER ."!");
define ('LBL_ADMIN_CANT_RENAME',"Vous ne pouvez pas renommer cet &eacute;l&eacute;ment '%s' parce que un &eacute;l&eacute;ment du m&ecirc;me nom existe d&eacute;j&agrave;!");
define ('LBL_ADMIN_ERROR_CANT_CREATE',"Un r&eacute;pertoire du m&ecirc;me nom ('%s') existe d&eacute;j&agrave;!");

define ('LBL_TAG_TAGS','Tags');
define ('LBL_TAG_EDIT','modifier');
define ('LBL_TAG_SUBMIT','valider');
define ('LBL_TAG_CANCEL','annuler');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"D&eacute;sol&eacute;, aucun &eacute;l&eacute;ment taggu&eacute; &laquo;%s&raquo; n'a pu &ecirc;tre trouv&eacute;.");
define ('LBL_TAG_ALL_TAGS','Tous les Tags');
define ('LBL_TAG_TAGGED','tagg&eacute;');
define ('LBL_TAG_TAGGEDP','tagg&eacute;s');
define ('LBL_TAG_SUGGESTIONS','suggestions');
define ('LBL_TAG_SUGGESTIONS_NONE','aucune suggestion');
define ('LBL_TAG_RELATED','Tags similaires: ');

define ('LBL_MARK_READ', "Tous marquer comme lus");
define ('LBL_MARK_CHANNEL_READ', "Marquer ce canal comme lu");
define ('LBL_MARK_FOLDER_READ', "Marquer ce r&eacute;pertoire comme lu");
define ('LBL_SHOW_UNREAD_ALL_SHOW','Afficher les &eacute;l&eacute;ments: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','Non lus uniquement');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','Lus et non lus');

define ('LBL_STATE_UNREAD','Non lu (Sp&eacute;cifie l\\\'&eacute;tat lu/non lu de cet &eacute;l&eacute;ment)');
define ('LBL_STATE_STICKY','Persistant (L\\\'&eacute;l&eacute;ment ne sera pas effac&eacute;)');
define ('LBL_STATE_PRIVATE','Priv&eacute; (Seuls les administrateurs peuvent voir les &eacute;l&eacute;ments priv&eacute;s)');

?>
