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
# E-mail:      godsea at gmail dot com
# Web page:    http://godsea.dsland.org
#
###############################################################################
# $Log$
# Revision 1.1  2005/06/09 11:45:00  mbonetti
# Swedish and Spanish language packs
#
# Revision 0.9  2005/06/09 10:20:10  godsea
# CVS Log messages in the file header
#
#
###############################################################################

/// Language: Espa&ntilde;ol

define ('ITEM','tema');
define ('ITEMS','temas');
define ('H2_SEARCH_RESULTS_FOR', "%d encontrados de %s");
define ('H2_SEARCH_RESULT_FOR',"%d encontrado de %s");
define ('H2_SEARCH', 'Search %d temas');
define ('SEARCH_SEARCH_QUERY','Buscar palabras:');
define ('SEARCH_MATCH_OR', 'Alguna palabra');
define ('SEARCH_MATCH_AND', 'Todas las palabras');                                                                 
define ('SEARCH_MATCH_EXACT', 'Busqueda exacta');
define ('SEARCH_CHANNELS', 'Canal:');
define ('SEARCH_ORDER_DATE_CHANNEL','Ordenar por fecha, tema');
define ('SEARCH_ORDER_CHANNEL_DATE','Ordenar por tema, fecha');
define ('SEARCH_RESULTS_PER_PAGE','Resultados por pagina:');
define ('SEARCH_RESULTS','Resultados: ');
define ('H2_UNREAD_ITEMS',"temas sin leer (%d)");
define ('H2_RECENT_ITEMS', "temas recientes");
define ('H2_CHANNELS','Canales');
define ('H5_READ_UNREAD_STATS','%d temas, %d sin leer');
define ('ITEMCOUNT_PF', '<strong>%d</strong> temas (<strong>%d</strong> sin leer) en <strong>%d</strong> canales');
define ('UNREAD_PF', '(<strong>%d sin leer</strong>)');
define ('UNREAD','sin leer');

define ('FTR_POWERED_BY', ", desarrollado por ");
define ('ALL','Todos');
define ('NAV_HOME','Inicio');
define ('NAV_UPDATE', 'Actualizar');
define ('NAV_CHANNEL_ADMIN', 'Administrar');
define ('NAV_SEARCH', "Buscador");
define ('NAV_DEVLOG', "Blog desarrollo");
define ('SEARCH_GO', 'Buscar');

define ('POSTED', 'Archivado: ');
define ('FETCHED','Actualizado: ');

define ('LBL_AND','y');

define ('TITLE_UPDATING','Actualizando');
define ('TITLE_SEARCH','Buscador');
define ('TITLE_ADMIN','Fuentes Administrador');


define ('HOME_FOLDER','Raiz');
define ('VISIT', '(visitado)');
define ('COLLAPSE','[-] recoger');
define ('EXPAND','[+] expandir');
define ('PL_FOR','Enlace permanente para ');

define ('UPDATE_CHANNEL','Canal');
define ('UPDATE_STATUS','Estado');
define ('UPDATE_UNDREAD','Nuevos temas');

define ('UPDATE_STATUS_OK','OK');
define ('UPDATE_STATUS_CACHED', 'OK (Cache local)');
define ('UPDATE_STATUS_ERROR','ERROR');
define ('UPDATE_H2','Actualizando %d temas...');
define ('UPDATE_CACHE_TIMEOUT','No se pudo recuperar (Cache local)');
define ('UPDATE_NOT_MODIFIED','OK (Sin modificaciones)');
define ('UPDATE_NOT_FOUND','No encontrado (Cache local)');
// admin
define ('ADMIN_EDIT', 'editar');
define ('ADMIN_DELETE', 'eliminar');
define ('ADMIN_DELETE2', 'Eliminar');
define ('ADMIN_RENAME', 'Renombrar a...');
define ('ADMIN_CREATE', 'Crear');
define ('ADMIN_IMPORT','Importar');
define ('ADMIN_EXPORT','Exportar');
define ('ADMIN_DEFAULT','predeterminado');
define ('ADMIN_ADD','Enviar');
define ('ADMIN_YES', 'Si');
define ('ADMIN_NO', 'No');
define ('ADMIN_FOLDERS','Carpetas:');
define ('ADMIN_CHANNELS','Temas:');
define ('ADMIN_OPML','OPML:');  
define ('ADMIN_ITEM','Temas:');
define ('ADMIN_CONFIG','Configuracion:');
define ('ADMIN_OK','Aceptar');
define ('ADMIN_CANCEL','Cancelar');
define ('OPML_IMPORT','Importar');
define ('OPML_EXPORT','Exportar');

define ('ADMIN_IN_FOLDER','en la carpeta:');
define ('ADMIN_SUBMIT_CHANGES', 'Guardar cambios');
define ('ADMIN_PREVIEW_CHANGES','Previsualizar');
define ('ADMIN_CHANNELS_HEADING_TITLE','Titulo');
define ('ADMIN_CHANNELS_HEADING_FOLDER','Carpeta');
define ('ADMIN_CHANNELS_HEADING_DESCR','Descripcion');
define ('ADMIN_CHANNELS_HEADING_MOVE','Mover');
define ('ADMIN_CHANNELS_HEADING_ACTION','Accion');
define ('ADMIN_CHANNELS_HEADING_FLAGS','Destacado');
define ('ADMIN_CHANNELS_HEADING_KEY','Clave');
define ('ADMIN_CHANNELS_HEADING_VALUE','Value');
define ('ADMIN_CHANNELS_ADD','Nuevo canal:');
define ('ADMIN_FOLDERS_ADD','Nueva carpeta:');
define ('ADMIN_CHANNEL_ICON','Mostrar icono:');
define ('CLEAR_FOR_NONE','(Dejar en blanco para anular el icono)');
define ('ADMIN_OPML_EXPORT','Exportar OPML:');

define ('ADMIN_OPML_IMPORT','Importar OPML:');
define ('ADMIN_OPML_IMPORT_FROM_URL','... desde URL:');
define ('ADMIN_OPML_IMPORT_FROM_FILE','... desde Archivo:');

define ('ADMIN_CONFIG_VALUE','Valor');

define ('ADMIN_OPML_FILE_IMPORT','Importar OPML de archivo local:');
define ('ADMIN_FILE_IMPORT','Importar archivo');

define ('ADMIN_PLUGINS_HEADING_NAME','Nombre');
define ('ADMIN_PLUGINS_HEADING_AUTHOR','Autor');
define ('ADMIN_PLUGINS_HEADING_VERSION','Version');
define ('ADMIN_PLUGINS_HEADING_DESCRIPTION','Descripcion');
define ('ADMIN_PLUGINS_HEADING_ACTION','Activo');




define ('ADMIN_CHANNEL_EDIT_CHANNEL','Editar canal ');
define ('ADMIN_CHANNEL_NAME','Titulo:');
define ('ADMIN_CHANNEL_RSS_URL','URL RSS:');
define ('ADMIN_CHANNEL_SITE_URL','URL Sitio:');
define ('ADMIN_CHANNEL_FOLDER','En carpeta:');
define ('ADMIN_CHANNEL_DESCR','Descripcion:');
define ('ADMIN_FOLDER_NAME','Nombre carpeta:');
define ('ADMIN_CHANNEL_PRIVATE','El canal es <strong>privado</strong>, solo lo ven los administradores');
define ('ADMIN_CHANNEL_DELETED','El tema <strong>no esta aprovado</strong>, no ser&aacute; puesto al d&iacute;a m&aacute;s y no ser&aacute; visible en la columna de las canales.');

define ('ADMIN_ARE_YOU_SURE', "Est&aacute; seguro de querer eliminar '%s'?");
define ('ADMIN_ARE_YOU_SURE_DEFAULT','Est&aacute; seguro que quiere establecer el valor de %s al predeterminado \'%s\'?');
define ('ADMIN_TRUE','Si');
define ('ADMIN_FALSE','No');
define ('ADMIN_MOVE_UP','&uarr;');
define ('ADMIN_MOVE_DOWN','&darr;');
define ('ADMIN_ADD_CHANNEL_EXPL','(Introduce la URL del canal RSS o web en la que te desees suscribir)');
define ('ADMIN_FEEDS','Los canales siguientes fueron encontrados dentro de <a href="%s">%s</a>, quieres suscribirte a alguno?');

define ('ADMIN_PRUNE_OLDER','Eliminar temas m&aacute;s viejos de ');
define ('ADMIN_PRUNE_DAYS','dias');
define ('ADMIN_PRUNE_MONTHS','meses');
define ('ADMIN_PRUNE_YEARS','a 	&ntilde;os');
define ('PRUNE_KEEP','Proteger temas m&aacute;s recientes: ');
define ('ADMIN_PRUNE_INCLUDE_STICKY','Eliminar temas destacados: ');
define ('ADMIN_PRUNE_EXCLUDE_TAGS','No eliminar temas con tag... ');
define ('ADMIN_ALLTAGS_EXPL','(Ponga <strong>*</strong> para proteger todos los temas con tag)');

define ('ADMIN_ABOUT_TO_DELETE','Atenci&oacute;n: est&aacute; apunto de eliminar %s temas (de %s)');
define ('ADMIN_PRUNING','Limpieza');
define ('ADMIN_DOMAIN_FOLDER_LBL','carpetas');
define ('ADMIN_DOMAIN_CHANNEL_LBL','canales');
define ('ADMIN_DOMAIN_ITEM_LBL','temas');
define ('ADMIN_DOMAIN_CONFIG_LBL','config');
define ('ADMIN_DOMAIN_OPML_LBL','opml');
define ('ADMIN_BOOKMARKET_LABEL','Suscripcion bookmarklet [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('ADMIN_BOOKMARKLET_TITLE','Suscribir en Gregarius!');


define ('ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>No autorizado!</h1>\nNo est&aacute; autorizado para acceder al area de administraci&oacute;n.\n"
		."Por favor siga <a href=\"%s\">este enlace</a> para volver atras.\n"
		."Le deseamos un buen d&iacute;a!");
		
define ('ADMIN_ERROR_PRUNING_PERIOD','Periodo de limpieza no valido');
define ('ADMIN_ERROR_NO_PERIOD','Oops, no ha especificado un periodo');
define ('ADMIN_BAD_RSS_URL',"Lo siento, creo que no podemos hacer nada con esta URL: '%s'");
define ('ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"No puede eliminar la carpeta " . HOME_FOLDER);
define ('ADMIN_CANT_RENAME',"No puede renombrar esta carpeta '%s' porque la otra ya existe.");
define ('ADMIN_ERROR_CANT_CREATE',"Ahora tiene una carpeta llamada '%s'!");

define ('TAG_TAGS','Tags');
define ('TAG_EDIT','editar');
define ('TAG_SUBMIT','enviar');
define ('TAG_CANCEL','cancelar');
define ('TAG_SUBMITTING','...');
define ('TAG_ERROR_NO_TAG',"Oops! No existen temas con tag &laquo;%s&raquo;");
define ('TAG_ALL_TAGS','Todos los tags');
define ('TAG_TAGGED','tagged');
define ('TAG_TAGGEDP','tagged');
define ('TAG_SUGGESTIONS','sugerencias');
define ('TAG_SUGGESTIONS_NONE','sin sugerencias');
define ('TAG_RELATED','Tags relacionados: ');

define ('MARK_READ', "Marcar todos como leidos");
define ('MARK_CHANNEL_READ', "Marcar este como leido");
define ('MARK_FOLDER_READ',"Marcar esta carpeta como leida");
define ('SHOW_UNREAD_ALL_SHOW','Mostrar temas: ');
define ('SHOW_UNREAD_ALL_UNREAD_ONLY','Sin leer');
define ('SHOW_UNREAD_ALL_READ_AND_UNREAD','Sin leer y leidos');

define ('STATE_UNREAD','Sin leer (Cambia el estado de lectura)');
define ('STATE_STICKY','Destacados (No ser&aacute;n eliminados por las tareas de limpieza)');
define ('STATE_PRIVATE','Privados (Solo los administradores pueden ver estos temas)');
?>
