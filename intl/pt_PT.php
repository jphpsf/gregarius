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
# E-mail:      humaneasy at gmail dot com                
# Web page:    http://www.humaneasy.com/
#
##############################################################################
#
# 	Planning to translate this into your own language? Please read this:
#	http://wiki.gregarius.net/index.php/Internationalization
#
################################################################################

/// Language: Portugu&ecirc;s
define ('LOCALE_WINDOWS','pt');
define ('LOCALE_LINUX','pt_PT');

define ('LBL_ITEM','&Iacute;tem');
define ('LBL_ITEMS','&Iacute;tems');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d resultados para %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d resultado para %s");
define ('LBL_H2_SEARCH', 'Pesquisar %d &iacute;tems');
define ('LBL_SEARCH_SEARCH_QUERY','Pesquisar termos:');
define ('LBL_SEARCH_MATCH_OR', 'Alguns termos (OU)');
define ('LBL_SEARCH_MATCH_AND', 'Todos os termos (E)');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', 'Termo exacto');
define ('LBL_SEARCH_CHANNELS', 'Feed:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Ordenar por data, feed');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Ordenar por feed, data');
define ('LBL_SEARCH_RESULTS_PER_PAGE','Resultados por p&aacute;gina:');
define ('LBL_SEARCH_RESULTS','Resultados: ');
define ('LBL_H2_UNREAD_ITEMS','&Iacute;tems n&atilde;o lidos(<strong id="ucnt">%d</strong>)');
define ('LBL_H2_RECENT_ITEMS', "&Iacute;tems recentes");
define ('LBL_H2_CHANNELS','Feeds');
define ('LBL_H5_READ_UNREAD_STATS','%d &iacute;tems, %d n&atilde;o lidos');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> &iacute;tems (<strong id="fucnt">%d</strong> n&atilde;o lidos) em <strong>%d</strong> feeds');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> &iacute;tems etiquetados items, em <strong>%d</strong> etiquetas');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d n&atilde;o lido)</strong>');
define ('LBL_UNREAD','n&atilde;o lido');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','Todos');
define ('LBL_NAV_HOME','<span>A</span>bertura');
define ('LBL_NAV_UPDATE', 'Actuali<span>z</span>ar');
define ('LBL_NAV_CHANNEL_ADMIN', 'A<span>d</span>min');
define ('LBL_NAV_SEARCH', "<span>P</span>esquisa");
define ('LBL_NAV_DEVLOG', "Dev<span>l</span>og");
define ('LBL_SEARCH_GO', 'Pesquisa');

define ('LBL_POSTED', 'Artigo em: ');
define ('LBL_FETCHED','Pesquisado: ');
define ('LBL_BY', ' por ');

define ('LBL_AND','e');

define ('LBL_TITLE_UPDATING','Actualizado');
define ('LBL_TITLE_SEARCH','Pesquisar');
define ('LBL_TITLE_ADMIN','Administra&ccedil;&atilde;o de Feeds');


define ('LBL_HOME_FOLDER','Ra&iacute;z');
define ('LBL_VISIT', '(visite)');
define ('LBL_COLLAPSE','[-] contrair');
define ('LBL_EXPAND','[+] expandir');
define ('LBL_PL_FOR','Link permanente para ');

define ('LBL_UPDATE_CHANNEL','Feed');
define ('LBL_UPDATE_STATUS','Estado');
define ('LBL_UPDATE_UNREAD','Novos &iacute;tems');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Cache local)');
define ('LBL_UPDATE_STATUS_ERROR','ERRO');
define ('LBL_UPDATE_H2','Actualizando %d Feeds...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Cache local)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 N&atilde;o modificado)');
define ('LBL_UPDATE_NOT_FOUND','404 N&atilde;o encontrado (Cache local)');
// admin
define ('LBL_ADMIN_EDIT', 'editar');
define ('LBL_ADMIN_DELETE', 'excluir');
define ('LBL_ADMIN_DELETE2', 'Excluir');
define ('LBL_ADMIN_RENAME', 'Renomear para...');
define ('LBL_ADMIN_CREATE', 'Criar');
define ('LBL_ADMIN_IMPORT','Importar');
define ('LBL_ADMIN_EXPORT','Exportar');
define ('LBL_ADMIN_DEFAULT','padr&atilde;o');
define ('LBL_ADMIN_ADD','Adicionar');
define ('LBL_ADMIN_YES', 'Sim');
define ('LBL_ADMIN_NO', 'N&atilde;o');
define ('LBL_ADMIN_FOLDERS','Categorias:');
define ('LBL_ADMIN_CHANNELS','Feeds:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','&iacute;tems:');
define ('LBL_ADMIN_CONFIG','Configura&ccedil;&atilde;o:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','Cancelar');
define ('LBL_ADMIN_LOGOUT','Sair');
define ('LBL_ADMIN_CONFIGURE','Configure');

define ('LBL_ADMIN_OPML_IMPORT','Importar');
define ('LBL_ADMIN_OPML_EXPORT','Exportar');
define ('LBL_ADMIN_OPML_IMPORT_OPML','Importar OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','Exportar OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... da URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... do Arquivo:');
define ('LBL_ADMIN_FILE_IMPORT','Importar arquivo');

define ('LBL_ADMIN_IN_FOLDER','para a Categoria:');
define ('LBL_ADMIN_SUBMIT_CHANGES', 'Enviar Mudan&ccedil;as');
define ('LBL_ADMIN_PREVIEW_CHANGES','Preview');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','T&iacute;tulo');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','Categoria');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','Descri&ccedil;&atilde;o');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','Mover');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','A&ccedil;&atilde;o');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Marcas');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','Chave');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','Valor');
define ('LBL_ADMIN_CHANNELS_ADD','Adicionar um feed:');
define ('LBL_ADMIN_FOLDERS_ADD','Adicionar uma Categoria:');
define ('LBL_ADMIN_CHANNEL_ICON','Mostrar favicon:');
define ('LBL_CLEAR_FOR_NONE','(Deixe em branco para n&atilde;o usar &iacute;cone)');

define ('LBL_ADMIN_CONFIG_VALUE','Valor');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','Nome');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','Autor');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','Vers&atilde;o');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','Descri&ccedil;&atilde;o');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','Ativo');
define ('LBL_ADMIN_PLUGINS_HEADING_OPTIONS','Op&ccedil;&otilde;es');
define ('LBL_ADMIN_PLUGINS_OPTIONS','Op&ccedil;&otilde;es do Plugin');
define ('LBL_ADMIN_THEME_OPTIONS','Op&ccedil;&otilde;es Tema');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','Editar a feed ');
define ('LBL_ADMIN_CHANNEL_NAME','T&iacute;tulo:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','Site URL:');
define ('LBL_ADMIN_CHANNEL_FOLDER','Na Categoria:');
define ('LBL_ADMIN_CHANNEL_DESCR','Descri&ccedil;&atilde;o:');
define ('LBL_ADMIN_FOLDER_NAME','Nome da Categoria:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','Este feed &eacute; <strong>privado</strong>, somente administradores podem v&ecirc;-lo.');
define ('LBL_ADMIN_CHANNEL_DELETED','Este feed est&aacute; <strong>desactivado</strong>, ele n&atilde;o deve ser mais actualizado e n&atilde;o deve ser vis&iacute;vel na coluna de feeds.');

define ('LBL_ADMIN_ARE_YOU_SURE', "Voc&ecirc; tem certeza que deseja excluir  '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','Voc&ecirc; tem certeza que deseja reiniciar o valor de %s para seu padr&atilde;o \'%s\'?');
define ('LBL_ADMIN_TRUE','Verdadeiro');
define ('LBL_ADMIN_FALSE','Falso');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Entre ou a URL de um RSS feed ou um Website cujo feed voc&ecirc; deseja assinar)');
define ('LBL_ADMIN_FEEDS','Os seguintes feeds foram encontrados em <a href="%s">%s</a>, qual deles voc&ecirc; deseja assinar?');

define ('LBL_ADMIN_PRUNE_OLDER','Excluir &iacute;tems mais velhos que ');
define ('LBL_ADMIN_PRUNE_DAYS','dias');
define ('LBL_ADMIN_PRUNE_MONTHS','meses');
define ('LBL_ADMIN_PRUNE_YEARS','anos');
define ('LBL_ADMIN_PRUNE_KEEP','Mantenha os &iacute;tems mais recentes: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Excluir &iacute;tems Fixos também: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','N&atilde;o excluir &iacute;tems etiquetados... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Entre <strong>*</strong> para manter todos os &iacute;tems etiquetados)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Alerta: voc&ecirc; est&aacute; prestes a excluir %s &iacute;tems (de %s)');
define ('LBL_ADMIN_PRUNING','Limpando');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','Categorias');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','feeds');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','&iacute;tems');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','configura&ccedil;&atilde;o');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Bookmarklet de assinatura[<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Subscrever no Gregarius!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>N&atilde;o autorizado!</h1>\nVoc&ecirc; n&atilde;o est&aacute; autorizado a acessar a interface de administra&ccedil;&atilde;o.\n"
		."Por favor siga <a href=\"%s\">este link</a> para voltar a p&aacute;gina principal.\n"
		."Tenha um bom dia!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Per&iacute;odo de exclus&atilde;o inv&aacute;lido');
define ('LBL_ADMIN_ERROR_NO_PERIOD','oops, n&atilde;o foi especificado um per&iacute;odo');
define ('LBL_ADMIN_BAD_RSS_URL',"Sinto muito, eu acho que n&atilde;o posso tratar esta URL: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Voc&ecirc; n&atilde;o pode excluir a Categoria " . LBL_HOME_FOLDER . "");
define ('LBL_ADMIN_CANT_RENAME',"Voc&ecirc; n&atilde;o pode renomear a Categoria '%s' porque tal Categoria j&aacute; existe.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"Parece que voc&ecirc; j&aacute; tem uma Categoria chamada '%s'!");

define ('LBL_TAG_TAGS','Etiquetas');
define ('LBL_TAG_EDIT','editar');
define ('LBL_TAG_SUBMIT','submeter');
define ('LBL_TAG_CANCEL','cancelar');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Oops! Nenhum &iacute;tem etiquetado &laquo;%s&raquo; foi encontrado.");
define ('LBL_TAG_ALL_TAGS','Todas as etiquetas');
define ('LBL_TAG_TAGGED','etiquetado');
define ('LBL_TAG_TAGGEDP','etiquetado');
define ('LBL_TAG_SUGGESTIONS','sugest&otilde;es');
define ('LBL_TAG_SUGGESTIONS_NONE','sem sugest&otilde;es');
define ('LBL_TAG_RELATED','Etiquetas relacionadas: ');

define ('LBL_MARK_READ', "Marcar todos os &iacute;tems como lidos");
define ('LBL_MARK_CHANNEL_READ', "Marcar este feed como lido");
define ('LBL_MARK_FOLDER_READ',"Marcar esta Categoria como lida");
define ('LBL_SHOW_UNREAD_ALL_SHOW','Mostrar &iacute;tems: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','N&atilde;o lidos apenas');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','Lidos e n&atilde;o lidos');

define ('LBL_STATE_UNREAD','N&atilde;o lido (Altere o estado deste &iacute;tem para lido/n&atilde;o lido)');
define ('LBL_STATE_STICKY','Fixo (N&atilde;o excluir quando limpar &iacute;tems)');
define ('LBL_STATE_PRIVATE','Privado (Apenas administradores podem l&ecirc;r &iacute;tems privados)');
define ('LBL_STICKY','Fixo');
define ('LBL_DEPRECATED','Desactivado');
define ('LBL_PRIVATE','Privado');
define ('LBL_ADMIN_TOGGLE_STATE','Mudar estado:');
define ('LBL_ADMIN_TOGGLE_SET','Mudar');
define ('LBL_ADMIN_STATE','State:');
define ('LBL_ADMIN_STATE_SET','Set');
define ('LBL_ADMIN_IM_SURE','Eu tenho certeza!');

// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','Conectado como <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','N&atilde;o conectado');
define ('LBL_LOG_OUT','Sair');
define ('LBL_LOG_IN','Entrar');


define ('LBL_ADMIN_OPML_IMPORT_AND','Importar novas not&iacute;cias e:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... substituir todas as not&iacute;cias e &iacute;tems.');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... adicion&aacute;-las a Categoria:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... mescl&aacute;-las com as existentes.');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','Adicionando %s para %s... ');

define ('LBL_TAG_FOLDERS','Categorias');
define ('LBL_SIDE_ITEMS','(%d &iacute;tems)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d n&atilde;o lidos em %d not&iacute;cias)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> not&iacute;cias em <strong>%d</strong> categorias');

define ('LBL_RATING','Avalia&ccedil;&atilde;o:');


// New in 0.5.3:
define ('LBL_ENCLOSURE', 'Enclosure:');
define ('LBL_DOWNLOAD', 'descarregar');
define ('LBL_PLAY', 'tocar');

define ('LBL_MARK_READ', "Mark These Items as Read");
define ('LBL_MARK_CHANNEL_READ', "Mark These Items as Read");
define ('LBL_MARK_FOLDER_READ',"Mark These Items as Read");

define ('LBL_MARK_CHANNEL_READ_ALL', "Marcar Este Feed como Lido");
define ('LBL_MARK_FOLDER_READ_ALL',"Marcar Esta Pasta como Lida");
define ('LBL_MARK_CATEGORY_READ_ALL',"Marcar Esta Categoria como Lida");

// New in 0.5.x:
define ('LBL_FOOTER_LAST_MODIF_NEVER', 'Nunca');
define ('LBL_ADMIN_DASHBOARD','Painel'); 

define ('LBL_ADMIN_MUST_SET_PASS','<p>Ainda n&atilde;o foi definido nenhum Administrador!</p>'
		.'<p>Forne&ccedil;a agora um nome de utilizador e senha para o Administrator!</p>');
define ('LBL_USERNAME','Utilizador');		
define ('LBL_PASSWORD','Senha');
define ('LBL_PASSWORD2','Senha (novamente)');
define ('LBL_ADMIN_LOGIN','Por favor, concete-se');
define ('LBL_ADMIN_PASS_NO_MATCH','Senhas s&atilde; o diferentes!');

define ('LBL_ADMIN_PLUGINS','Plugins');
define ('LBL_ADMIN_DOMAIN_PLUGINS_LBL','plugins');
define ('LBL_ADMIN_PLUGINS_HEADING_UPDATES','Actualiza&ccedil;&atilde;o Dispon&iacute;vel');
define ('LBL_ADMIN_CHECK_FOR_UPDATES','Verificar Actualiza&ccedil;&otilde;es');
define ('LBL_ADMIN_LOGIN_BAD_LOGIN','<strong>Oops!</strong> Utilizador e/ou Senha errados');
define ('LBL_ADMIN_LOGIN_NO_ADMIN','<strong>Oops!</strong> Conectado com sucesso '
			.'como %s, mas voc&Ecirc; n&atilde;o tem poderes de adimistra&ccedil;&atilde;o. Conecte-se novamente '
			.'como um utilizador com poderes administrativos ou siga para a  <a href="..">abertura</a>');

define('LBL_ADMIN_ACTIVE_THEME','Tema Activo');
define('LBL_ADMIN_USE_THIS_THEME','Use este Tema');

define ('LBL_ADMIN_PLUGINS_GET_MORE', '<p style="font-size:small">'
.'Plugins s&atilde;o programas desenvolvidos por terceiros e que oferecem extens&atilde;o das functionalidades. '
.'Mais plugins podem ser recolhidos no <a style="text-decoration:underline" '
.' href="http://plugins.gregarius.net/">Dep&oacute;sito de Plugins</a>.</p>');

define ('LBL_LAST_UPDATE','&Uacute;ltima actualiza&ccedil;&atilde;o');
define ('LBL_ADMIN_DOMAIN_THEMES_LBL','temas');
define ('LBL_ADMIN_THEMES','Temas');

define ('LBL_ADMIN_THEMES_GET_MORE', '<p style="font-size:small">'
.'Temas s&atilde;o um conjunto de ficheiros que definem o aspecto da sua instala&ccedil;&atilde;o do Gregarius.<br />'
.'Mais temas podem ser recolhidos no <a style="text-decoration:underline" '
.' href="http://themes.gregarius.net/">Dep&oacute;sito de Temas</a>.</p>');

define ('LBL_STATE_FLAG','Marcar (Marca um item para leitura posterior)');
define ('LBL_FLAG','Marcado');

?>
