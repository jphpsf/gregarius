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
# E-mail:      skorobogatov at gmail dot com
# Web page:    http://skorobogatov.ru
#
###############################################################################

/// Language: Русский
define ('LOCALE_WINDOWS','rus'); // ?
define ('LOCALE_LINUX','ru_RU');


define ('LBL_ITEM','лента');
define ('LBL_ITEMS','ленты');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d результатов по запросу %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d результат по запросу %s");
define ('LBL_H2_SEARCH', 'Поиск %d');
define ('LBL_SEARCH_SEARCH_QUERY','Искать:');
define ('LBL_SEARCH_MATCH_OR', 'Любое слово (OR)');
define ('LBL_SEARCH_MATCH_AND', 'Все слова (AND)');
define ('LBL_SEARCH_MATCH_EXACT', 'Точное совпадение');
define ('LBL_SEARCH_CHANNELS', 'Канал:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','Сортировать по дате, имени');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','Сортировать по имени, дате');
define ('LBL_SEARCH_RESULTS_PER_PAGE','Результатов на страницу:');
define ('LBL_SEARCH_RESULTS','Результаты: ');
define ('LBL_H2_UNREAD_ITEMS','Непрочтенные сообщения (<span id="ucnt">%d</span>)');
define ('LBL_H2_RECENT_ITEMS', "Последние сообщения");
define ('LBL_H2_CHANNELS','Каналы');
define ('LBL_H5_READ_UNREAD_STATS','%d сообщений, %d непрочтенных');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> сообщений (<strong>%d</strong> непрочтенных) в <strong>%d</strong> feeds');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> помеченных сообщений, в <strong>%d</strong> тегах');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d непрочитано)</strong>');
define ('LBL_UNREAD','непрочитано');

define ('LBL_FTR_POWERED_BY', " работает на ");
define ('LBL_ALL','Все');
define ('LBL_NAV_HOME','<span>Г</span>лавная');
define ('LBL_NAV_UPDATE', '<span>О</span>бновить');
define ('LBL_NAV_CHANNEL_ADMIN', 'У<span>п</span>равление');
define ('LBL_NAV_SEARCH', "<span>П</span>оиск");
define ('LBL_NAV_DEVLOG', "Дев<span>л</span>ог");
define ('LBL_SEARCH_GO', 'Искать');

define ('LBL_POSTED', 'Опубликовано: ');
define ('LBL_FETCHED','Обработано: ');
define ('LBL_BY', ' ');

define ('LBL_AND','и');

define ('LBL_TITLE_UPDATING','Обновление');
define ('LBL_TITLE_SEARCH','Поиск');
define ('LBL_TITLE_ADMIN','Панель управления');


define ('LBL_HOME_FOLDER','Корень');
define ('LBL_VISIT', '(visit)');
define ('LBL_COLLAPSE','[-] скрыть');
define ('LBL_EXPAND','[+] раскрыть');
define ('LBL_PL_FOR','Permalink for ');

define ('LBL_UPDATE_CHANNEL','Лента');
define ('LBL_UPDATE_STATUS','Статус');
define ('LBL_UPDATE_UNREAD','Новые элементы');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','Одновление фидов (%d)...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('LBL_ADMIN_EDIT', 'изменить');
define ('LBL_ADMIN_DELETE', 'удалить');
define ('LBL_ADMIN_DELETE2', 'Удалить');
define ('LBL_ADMIN_RENAME', 'Переименовать...');
define ('LBL_ADMIN_CREATE', 'Создать');
define ('LBL_ADMIN_IMPORT','Импорт');
define ('LBL_ADMIN_EXPORT','Экспорт');
define ('LBL_ADMIN_DEFAULT','по умолчанию');
define ('LBL_ADMIN_ADD','Добавить');
define ('LBL_ADMIN_YES', 'Да');
define ('LBL_ADMIN_NO', 'Нет');
define ('LBL_ADMIN_FOLDERS','Папки:');
define ('LBL_ADMIN_CHANNELS','Ленты:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','Записи:');
define ('LBL_ADMIN_CONFIG','Настройка:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','Отмена');
define ('LBL_ADMIN_LOGOUT','Выйти');

define ('LBL_ADMIN_OPML_IMPORT','Импорт');
define ('LBL_ADMIN_OPML_EXPORT','Экспорт');
define ('LBL_ADMIN_OPML_IMPORT_OPML','Импорт OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','Экспорт OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... из URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... из файла:');
define ('LBL_ADMIN_FILE_IMPORT','Импорт файла');

define ('LBL_ADMIN_IN_FOLDER','в папку:');
define ('LBL_ADMIN_SUBMIT_CHANGES', 'Сохранить изменения');
define ('LBL_ADMIN_PREVIEW_CHANGES','Предпросмотр');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','Заголовок');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','Папка');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','Описание');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','Переместить');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','Action');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Метки');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','Ключ');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','Значение');
define ('LBL_ADMIN_CHANNELS_ADD','Добавить ленту:');
define ('LBL_ADMIN_FOLDERS_ADD','Добавить папку:');
define ('LBL_ADMIN_CHANNEL_ICON','Показать иконку:');
define ('LBL_CLEAR_FOR_NONE','(Оставьте пустым, чтобы удалить иконку)');

define ('LBL_ADMIN_CONFIG_VALUE','Значение');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','Название');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','Автор');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','Версия');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','Описание');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','Активно');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','Изменение ленты ');
define ('LBL_ADMIN_CHANNEL_NAME','Название:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','Адрес сайта:');
define ('LBL_ADMIN_CHANNEL_FOLDER','В папке:');
define ('LBL_ADMIN_CHANNEL_DESCR','Описание:');
define ('LBL_ADMIN_FOLDER_NAME','Имя папки:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','Это лента <strong>личная</strong>, только администратор видит ее.');
define ('LBL_ADMIN_CHANNEL_DELETED','Эта лента <strong>исключена</strong>, ее не надо обновлять и показывать.');

define ('LBL_ADMIN_ARE_YOU_SURE', "Ты уверен, что хочешь удалить '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','Ты уверен, что хочешь назначить значение по умолчанию \'%s\' для %s?');
define ('LBL_ADMIN_TRUE','Да');
define ('LBL_ADMIN_FALSE','Нет');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(Введи адрес ленты или сайта, с которого хотите получать ленту.)');
define ('LBL_ADMIN_FEEDS','Следующие ленты найдены на сайте <a href="%s">%s</a>. Выберите, на какие ты хочешь подписаться?');

define ('LBL_ADMIN_PRUNE_OLDER','Удалить записи старше, чем ');
define ('LBL_ADMIN_PRUNE_DAYS','дней');
define ('LBL_ADMIN_PRUNE_MONTHS','месяцев');
define ('LBL_ADMIN_PRUNE_YEARS','лет');
define ('LBL_ADMIN_PRUNE_KEEP','Оставить только последние записи: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Удалить приклеенные записи: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','Не удалять записи с тегами... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(Введи <strong>*</strong>, чтобы оставить все записи с тегами)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Предупреждение: ты собираешься удалить %s записей (из %s)');
define ('LBL_ADMIN_PRUNING','Очистка');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','папки');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','ленты');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','записи');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','настройки');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','Закладка для подписки [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Добавить в Gregarius!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Авторизация!</h1>\nВы не авторизованы и не можете использовать панель управления.\n"
		."Пройдите по <a href=\"%s\">ссылке</a> на главную страницу.\n"
		."Счастливого дня!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Неправильный ');
define ('LBL_ADMIN_ERROR_NO_PERIOD','опа, не указан период очистки');
define ('LBL_ADMIN_BAD_RSS_URL',"Мне жаль, не думаю, что смогу обработать этот адрес: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"Ты не можешь удалить папку " . LBL_HOME_FOLDER);
define ('LBL_ADMIN_CANT_RENAME',"ТЫ не можешь переименовать папку '%s', потому что уде есть папка с таким именем.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"Похоже, что папка с именем '%s' уже есть!");

define ('LBL_TAG_TAGS','Теги');
define ('LBL_TAG_EDIT','изменить');
define ('LBL_TAG_SUBMIT','отправить');
define ('LBL_TAG_CANCEL','отмена');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Опа! Нет записей помеченных тегом &laquo;%s&raquo;.");
define ('LBL_TAG_ALL_TAGS','Все теги');
define ('LBL_TAG_TAGGED','помеченные');
define ('LBL_TAG_TAGGEDP','помеченные');
define ('LBL_TAG_SUGGESTIONS','варианты');
define ('LBL_TAG_SUGGESTIONS_NONE','вариантов нет');
define ('LBL_TAG_RELATED','Связанные теги: ');

define ('LBL_MARK_READ', "Пометить все как прочтенные");
define ('LBL_MARK_CHANNEL_READ', "Пометить эту ленту как прочтенную");
define ('LBL_MARK_FOLDER_READ',"Пометить эту папку как прочтенную");
define ('LBL_SHOW_UNREAD_ALL_SHOW','Показать записи: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','Только непрочтенные');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','Прочтенные и непрочтенные');

define ('LBL_STATE_UNREAD','Непрочтенная');
define ('LBL_STATE_STICKY','Приклеенная (не удаляются при очистке)');
define ('LBL_STATE_PRIVATE','Личная (только администраторы видят личные записи)');
define ('LBL_STICKY','Приклеенная');
define ('LBL_DEPRECATED','Исключенная');
define ('LBL_PRIVATE','Личная');
define ('LBL_ADMIN_TOGGLE_STATE','Поменять состояние:');
define ('LBL_ADMIN_TOGGLE_SET','Установить');
define ('LBL_ADMIN_IM_SURE','Я уверен!');


// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','Ты вошел как <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','Не вошел');
define ('LBL_LOG_OUT','Выйти');
define ('LBL_LOG_IN','Войти');


define ('LBL_ADMIN_OPML_IMPORT_AND','Импортировать ленты и:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... заменить существующие ленты и записи.');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... добавить их в папку:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... совместить с существующими.');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','Добавление %s в %s... ');

define ('LBL_TAG_FOLDERS','Рубрики');
define ('LBL_SIDE_ITEMS','(%d записей)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d непрочтенных %d лентах)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> лент в <strong>%d</strong> рубриках');

define ('LBL_RATING','Rating:');
// New in 0.5.3:
// TRANSLATION NEEDED! Please join gregarius-i18n: http://sinless.org/mailman/listinfo/gregarius-i18n
define('LBL_ENCLOSURE', 'Enclosure:');
define('LBL_DOWNLOAD', 'download');
define('LBL_PLAY', 'play');

define('LBL_FOOTER_LAST_MODIF_NEVER', 'Never');
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

define ('LBL_LAST_UPDATE','Last update');						
define ('LBL_ADMIN_DOMAIN_THEMES_LBL','themes');
define ('LBL_ADMIN_THEMES','Themes');

define ('LBL_ADMIN_THEMES_GET_MORE', '<p style="font-size:small">'
.'Themes are made of a set of template files which specify how your Gregarius installation looks.<br />'
.'More themes can be downloaded at the <a style="text-decoration:underline" '
.' href="http://themes.gregarius.net/">Themes Repository</a>.</p>');
				
?>
