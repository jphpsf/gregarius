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
# Chinese_s by http://www.sluke.cn
###############################################################################
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################
#
# 	Planning to translate this into your own language? Please read this:
#	http://wiki.gregarius.net/index.php/Internationalization
#
###############################################################################

/// Language: chinese traditional
define ('LOCALE_WINDOWS','chinese');
define ('LOCALE_LINUX','cn_zh');

define ('LBL_ITEM','條目');
define ('LBL_ITEMS','條目');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d 匹配為 %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d 匹配為 %s");
define ('LBL_H2_SEARCH', '搜過 %d 條目');
define ('LBL_SEARCH_SEARCH_QUERY','搜索:');
define ('LBL_SEARCH_MATCH_OR', '部分匹配 (或)');
define ('LBL_SEARCH_MATCH_AND', '全部匹配 (和)');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', '精確匹配');
define ('LBL_SEARCH_CHANNELS', 'Feed:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','日期, feed排序');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','feed, 日期排序');
define ('LBL_SEARCH_RESULTS_PER_PAGE','每頁結果:');
define ('LBL_SEARCH_RESULTS','結果: ');
define ('LBL_H2_UNREAD_ITEMS','未讀條目 (<strong id="ucnt">%d</strong>)');
define ('LBL_H2_RECENT_ITEMS', "最近條目");
define ('LBL_H2_CHANNELS','Feeds');
define ('LBL_H5_READ_UNREAD_STATS','%d 條, %d 未讀');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> 條 (<strong id="fucnt">%d</strong> 未讀) 共 <strong>%d</strong> feeds');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> tagged items, 共 <strong>%d</strong> tags');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d 未讀)</strong>');
define ('LBL_UNREAD','未讀');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','所有');
define ('LBL_NAV_HOME','<span>首頁</span>');
define ('LBL_NAV_UPDATE', '<span>刷新</span>');
define ('LBL_NAV_CHANNEL_ADMIN', '<span>管理</span>');
define ('LBL_NAV_SEARCH', "<span>搜索</span>");
define ('LBL_NAV_DEVLOG', "<span>程序</span>");
define ('LBL_SEARCH_GO', '搜索');

define ('LBL_POSTED', '提交: ');
define ('LBL_FETCHED','采集: ');
define ('LBL_BY', ' 由 ');

define ('LBL_AND','和');

define ('LBL_TITLE_UPDATING','更新');
define ('LBL_TITLE_SEARCH','搜索');
define ('LBL_TITLE_ADMIN','Feeds 管理');


define ('LBL_HOME_FOLDER','Root');
define ('LBL_VISIT', '(visit)');
define ('LBL_COLLAPSE','[-] collapse');
define ('LBL_EXPAND','[+] expand');
define ('LBL_PL_FOR','Permalink for ');

define ('LBL_UPDATE_CHANNEL','Feed');
define ('LBL_UPDATE_STATUS','狀態');
define ('LBL_UPDATE_UNREAD','新條目');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','更新 %d Feeds中...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('LBL_ADMIN_EDIT', '編輯');
define ('LBL_ADMIN_DELETE', '刪除');
define ('LBL_ADMIN_DELETE2', '刪除');
define ('LBL_ADMIN_RENAME', '重命名為...');
define ('LBL_ADMIN_CREATE', '創建');
define ('LBL_ADMIN_IMPORT','導入');
define ('LBL_ADMIN_EXPORT','導出');
define ('LBL_ADMIN_DEFAULT','默認');
define ('LBL_ADMIN_ADD','添加');
define ('LBL_ADMIN_YES', '是');
define ('LBL_ADMIN_NO', '否');
define ('LBL_ADMIN_FOLDERS','組:');
define ('LBL_ADMIN_CHANNELS','Feeds:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','Items:');
define ('LBL_ADMIN_CONFIG','配置:');
define ('LBL_ADMIN_OK','確定');
define ('LBL_ADMIN_CANCEL','取消');
define ('LBL_ADMIN_LOGOUT','注銷');

define ('LBL_ADMIN_OPML_IMPORT','導入');
define ('LBL_ADMIN_OPML_EXPORT','導出');
define ('LBL_ADMIN_OPML_IMPORT_OPML','導入 OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','導出 OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... 從 URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... 從 文件:');
define ('LBL_ADMIN_FILE_IMPORT','導入 文件');

define ('LBL_ADMIN_IN_FOLDER','到組:');
define ('LBL_ADMIN_SUBMIT_CHANGES', '提交修改');
define ('LBL_ADMIN_PREVIEW_CHANGES','預覽');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','標題');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','組');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','描述');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','移動');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','行為');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Flags');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','關鍵詞');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','值');
define ('LBL_ADMIN_CHANNELS_ADD','添加 feed:');
define ('LBL_ADMIN_FOLDERS_ADD','添加組');
define ('LBL_ADMIN_CHANNEL_ICON','favicon.icon:');
define ('LBL_CLEAR_FOR_NONE','(留空為沒有favicon.icon)');

define ('LBL_ADMIN_CONFIG_VALUE','Value for');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','名字');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','作者');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','版本');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','描述');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','動作');
define ('LBL_ADMIN_PLUGINS_HEADING_OPTIONS','選項');
define ('LBL_ADMIN_PLUGINS_OPTIONS','擴展 選項');
define ('LBL_ADMIN_THEME_OPTIONS','風格 選項');

define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','編輯 feed ');
define ('LBL_ADMIN_CHANNEL_NAME','標題:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','站點 URL:');
define ('LBL_ADMIN_CHANNEL_FOLDER','所在組:');
define ('LBL_ADMIN_CHANNEL_DESCR','描述:');
define ('LBL_ADMIN_FOLDER_NAME','組名:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','這個 feed 是 <strong>被保護的</strong>, 只允許管理員察看.');
define ('LBL_ADMIN_CHANNEL_DELETED','這個 feed 是 <strong>被限制的</strong>, 不允許更新及察看.');

define ('LBL_ADMIN_ARE_YOU_SURE', "您確定刪除 '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','您確定設定 %s 為默認t \'%s\'?');
define ('LBL_ADMIN_TRUE','確定');
define ('LBL_ADMIN_FALSE','失敗');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(輸入目標RSS的URL[以http://開頭])');
define ('LBL_ADMIN_FEEDS','找到以下feeds<a href="%s">%s</a>, 您會訂閱哪一個?');

define ('LBL_ADMIN_PRUNE_OLDER','刪除比此日期早的條目:');
define ('LBL_ADMIN_PRUNE_DAYS','天');
define ('LBL_ADMIN_PRUNE_MONTHS','月');
define ('LBL_ADMIN_PRUNE_YEARS','年');
define ('LBL_ADMIN_PRUNE_KEEP','保留最近的條目: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Sticky條目一起刪除: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','不刪除條目TAG... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(輸入 <strong>*</strong> 為所有標記條目)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','注意: 您將要刪除 %s 條 (of %s)');
define ('LBL_ADMIN_PRUNING','修剪');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','組');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','feeds');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','條目');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','配置');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','捐助bookmarklet [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','訂閱  Gregarius!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>未批准進入管理!</h1>"
		."點擊 <a href=\"%s\">鏈接</a> 返回首頁.\n"
		."你好!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Invalid pruning period');
define ('LBL_ADMIN_ERROR_NO_PERIOD','no period specified');
define ('LBL_ADMIN_BAD_RSS_URL',"對不起,不能處理這個URL: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"您不能刪除 " . LBL_HOME_FOLDER . " 組");
define ('LBL_ADMIN_CANT_RENAME',"重命名失敗,組名'%s'已經存在.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"組名 '%s'重復!");

define ('LBL_TAG_TAGS','Tags');
define ('LBL_TAG_EDIT','編輯');
define ('LBL_TAG_SUBMIT','提交');
define ('LBL_TAG_CANCEL','取消');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"嘎嘎~沒有發現被標記的條目 &laquo;%s&raquo; ");
define ('LBL_TAG_ALL_TAGS','所有 Tags');
define ('LBL_TAG_TAGGED','已標記');
define ('LBL_TAG_TAGGEDP','已標記');
define ('LBL_TAG_SUGGESTIONS','建議');
define ('LBL_TAG_SUGGESTIONS_NONE','無建議');
define ('LBL_TAG_RELATED','最近 tags: ');

define ('LBL_SHOW_UNREAD_ALL_SHOW','顯示條目: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','只顯示未讀');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','已讀和未讀');

define ('LBL_STATE_UNREAD','未讀 (設置本條目 已讀/未讀 狀態)');
define ('LBL_STATE_STICKY','Sticky (當修剪條目時不會被刪除)');
define ('LBL_STATE_PRIVATE','保護 (只允許管理員察看)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','限制');
define ('LBL_PRIVATE','保護');
define ('LBL_ADMIN_TOGGLE_STATE','Toggle 狀態:');
define ('LBL_ADMIN_TOGGLE_SET','設置');
define ('LBL_ADMIN_IM_SURE','我確定!');


// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','登錄為 <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','未登錄');
define ('LBL_LOG_OUT','注銷');
define ('LBL_LOG_IN','登陸');

define ('LBL_ADMIN_OPML_IMPORT_AND','導入新的 feed 和:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... 替換所有現有的feed和條目.');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... 添加到組:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... 與現有的部分合並.');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','添加 %s 到 %s... ');

define ('LBL_TAG_FOLDERS','分類');
define ('LBL_SIDE_ITEMS','(%d 條)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d 未讀, 共 %d feed)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> feed, 共 <strong>%d</strong> 分類');

define ('LBL_RATING','規定值:');

// New in 0.5.3:
define ('LBL_ENCLOSURE', '附錄:');
define ('LBL_DOWNLOAD', '下載');
define ('LBL_PLAY', 'play');

define ('LBL_MARK_READ', "標記這些條目為已讀");
define ('LBL_MARK_CHANNEL_READ', "標記這些條目為已讀");
define ('LBL_MARK_FOLDER_READ',"標記這些條目為已讀");

define ('LBL_MARK_CHANNEL_READ_ALL', "標記這些Feed為已讀");
define ('LBL_MARK_FOLDER_READ_ALL',"標記這些目錄為已讀");
define ('LBL_MARK_CATEGORY_READ_ALL',"標記這些分類為已讀");

// New in 0.5.x:
define ('LBL_FOOTER_LAST_MODIF_NEVER', '從未');
define ('LBL_ADMIN_DASHBOARD','控制面板'); 

define ('LBL_ADMIN_MUST_SET_PASS','<p>管理員未指定！</p>'
		.'<p>管理員請提供用戶名和密碼了!</p>');
define ('LBL_USERNAME','用戶名');		
define ('LBL_PASSWORD','密碼');
define ('LBL_PASSWORD2','驗證密碼');
define ('LBL_ADMIN_LOGIN','請登陸');
define ('LBL_ADMIN_PASS_NO_MATCH','密碼不符!');

define ('LBL_ADMIN_PLUGINS','擴展');
define ('LBL_ADMIN_DOMAIN_PLUGINS_LBL','擴展');
define ('LBL_ADMIN_PLUGINS_HEADING_UPDATES','昇級可用');
define ('LBL_ADMIN_CHECK_FOR_UPDATES','檢查新版本');
define ('LBL_ADMIN_LOGIN_BAD_LOGIN','<strong>噢噢!</strong> 用戶名或密碼錯誤');
define ('LBL_ADMIN_LOGIN_NO_ADMIN','<strong>噢噢!</strong> 登陸成功 '
			.' %s 登陸成功, 但是您沒有管理權。 請再登陸'
			.'以獲得管理權限。 <a href="..">首頁</a>');

define('LBL_ADMIN_ACTIVE_THEME','可用風格');
define('LBL_ADMIN_USE_THIS_THEME','使用此風格');

define ('LBL_ADMIN_PLUGINS_GET_MORE', '<p style="font-size:small">'
.'擴展是第三方腳本. '
.'更多擴展下載請到 <a style="text-decoration:underline" '
.' href="http://plugins.gregarius.net/">Plugin Repository</a>.</p>');

define ('LBL_LAST_UPDATE','最後昇級');
define ('LBL_ADMIN_DOMAIN_THEMES_LBL','風格');
define ('LBL_ADMIN_THEMES','風格');

define ('LBL_ADMIN_THEMES_GET_MORE', '<p style="font-size:small">'
.'風格是一系列的模板文件,具體如何看你安裝Gregarius.<br />'
.'更多風格請到 <a style="text-decoration:underline" '
.' href="http://themes.gregarius.net/">Themes Repository</a>.</p>');

define ('LBL_STATE_FLAG','標記 (為最後閱讀的條目做)');
define ('LBL_FLAG','已標記');

?>
