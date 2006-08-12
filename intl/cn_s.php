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

/// Language: chinese simplified
define ('LOCALE_WINDOWS','chinese');
define ('LOCALE_LINUX','cn_zh');

define ('LBL_ITEM','条目');
define ('LBL_ITEMS','条目');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d 匹配为 %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d 匹配为 %s");
define ('LBL_H2_SEARCH', '搜过 %d 条目');
define ('LBL_SEARCH_SEARCH_QUERY','搜索:');
define ('LBL_SEARCH_MATCH_OR', '部分匹配 (或)');
define ('LBL_SEARCH_MATCH_AND', '全部匹配 (和)');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', '精确匹配');
define ('LBL_SEARCH_CHANNELS', 'Feed:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','日期, feed排序');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','feed, 日期排序');
define ('LBL_SEARCH_RESULTS_PER_PAGE','每页结果:');
define ('LBL_SEARCH_RESULTS','结果: ');
define ('LBL_H2_UNREAD_ITEMS','未读条目 (<strong id="ucnt">%d</strong>)');
define ('LBL_H2_RECENT_ITEMS', "最近条目");
define ('LBL_H2_CHANNELS','Feeds');
define ('LBL_H5_READ_UNREAD_STATS','%d 条, %d 未读');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> 条 (<strong id="fucnt">%d</strong> 未读) 共 <strong>%d</strong> feeds');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> tagged items, 共 <strong>%d</strong> tags');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d 未读)</strong>');
define ('LBL_UNREAD','未读');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','所有');
define ('LBL_NAV_HOME','<span>首页</span>');
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
define ('LBL_UPDATE_STATUS','状态');
define ('LBL_UPDATE_UNREAD','新条目');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','更新 %d Feeds中...');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('LBL_ADMIN_EDIT', '编辑');
define ('LBL_ADMIN_DELETE', '删除');
define ('LBL_ADMIN_DELETE2', '删除');
define ('LBL_ADMIN_RENAME', '重命名为...');
define ('LBL_ADMIN_CREATE', '创建');
define ('LBL_ADMIN_IMPORT','导入');
define ('LBL_ADMIN_EXPORT','导出');
define ('LBL_ADMIN_DEFAULT','默认');
define ('LBL_ADMIN_ADD','添加');
define ('LBL_ADMIN_YES', '是');
define ('LBL_ADMIN_NO', '否');
define ('LBL_ADMIN_FOLDERS','组:');
define ('LBL_ADMIN_CHANNELS','Feeds:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','Items:');
define ('LBL_ADMIN_CONFIG','配置:');
define ('LBL_ADMIN_OK','确定');
define ('LBL_ADMIN_CANCEL','取消');
define ('LBL_ADMIN_LOGOUT','注销');

define ('LBL_ADMIN_OPML_IMPORT','导入');
define ('LBL_ADMIN_OPML_EXPORT','导出');
define ('LBL_ADMIN_OPML_IMPORT_OPML','导入 OPML:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','导出 OPML:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','... 从 URL:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... 从 文件:');
define ('LBL_ADMIN_FILE_IMPORT','导入 文件');

define ('LBL_ADMIN_IN_FOLDER','到组:');
define ('LBL_ADMIN_SUBMIT_CHANGES', '提交修改');
define ('LBL_ADMIN_PREVIEW_CHANGES','预览');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','标题');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','组');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','描述');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','移动');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','行为');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','Flags');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','关键词');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','值');
define ('LBL_ADMIN_CHANNELS_ADD','添加 feed:');
define ('LBL_ADMIN_FOLDERS_ADD','添加组');
define ('LBL_ADMIN_CHANNEL_ICON','favicon.icon:');
define ('LBL_CLEAR_FOR_NONE','(留空为没有favicon.icon)');

define ('LBL_ADMIN_CONFIG_VALUE','Value for');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','名字');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','作者');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','版本');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','描述');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','动作');
define ('LBL_ADMIN_PLUGINS_HEADING_OPTIONS','选项');
define ('LBL_ADMIN_PLUGINS_OPTIONS','扩展 选项');
define ('LBL_ADMIN_THEME_OPTIONS','风格 选项');

define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','编辑 feed ');
define ('LBL_ADMIN_CHANNEL_NAME','标题:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','站点 URL:');
define ('LBL_ADMIN_CHANNEL_FOLDER','所在组:');
define ('LBL_ADMIN_CHANNEL_DESCR','描述:');
define ('LBL_ADMIN_FOLDER_NAME','组名:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','这个 feed 是 <strong>被保护的</strong>, 只允许管理员察看.');
define ('LBL_ADMIN_CHANNEL_DELETED','这个 feed 是 <strong>被限制的</strong>, 不允许更新及察看.');

define ('LBL_ADMIN_ARE_YOU_SURE', "您确定删除 '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','您确定设定 %s 为默认t \'%s\'?');
define ('LBL_ADMIN_TRUE','确定');
define ('LBL_ADMIN_FALSE','失败');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(输入目标RSS的URL[以http://开头])');
define ('LBL_ADMIN_FEEDS','找到以下feeds<a href="%s">%s</a>, 您会订阅哪一个?');

define ('LBL_ADMIN_PRUNE_OLDER','删除比此日期早的条目:');
define ('LBL_ADMIN_PRUNE_DAYS','天');
define ('LBL_ADMIN_PRUNE_MONTHS','月');
define ('LBL_ADMIN_PRUNE_YEARS','年');
define ('LBL_ADMIN_PRUNE_KEEP','保留最近的条目: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Sticky条目一起删除: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','不删除条目TAG... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(输入 <strong>*</strong> 为所有标记条目)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','注意: 您将要删除 %s 条 (of %s)');
define ('LBL_ADMIN_PRUNING','修剪');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','组');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','feeds');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','条目');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','配置');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','捐助bookmarklet [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','订阅  Gregarius!');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>未批准进入管理!</h1>"
		."点击 <a href=\"%s\">链接</a> 返回首页.\n"
		."你好!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','Invalid pruning period');
define ('LBL_ADMIN_ERROR_NO_PERIOD','no period specified');
define ('LBL_ADMIN_BAD_RSS_URL',"对不起,不能处理这个URL: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"您不能删除 " . LBL_HOME_FOLDER . " 组");
define ('LBL_ADMIN_CANT_RENAME',"重命名失败,组名'%s'已经存在.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"组名 '%s'重复!");

define ('LBL_TAG_TAGS','Tags');
define ('LBL_TAG_EDIT','编辑');
define ('LBL_TAG_SUBMIT','提交');
define ('LBL_TAG_CANCEL','取消');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"嘎嘎~没有发现被标记的条目 &laquo;%s&raquo; ");
define ('LBL_TAG_ALL_TAGS','所有 Tags');
define ('LBL_TAG_TAGGED','已标记');
define ('LBL_TAG_TAGGEDP','已标记');
define ('LBL_TAG_SUGGESTIONS','建议');
define ('LBL_TAG_SUGGESTIONS_NONE','无建议');
define ('LBL_TAG_RELATED','最近 tags: ');

define ('LBL_SHOW_UNREAD_ALL_SHOW','显示条目: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','只显示未读');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','已读和未读');

define ('LBL_STATE_UNREAD','未读 (设置本条目 已读/未读 状态)');
define ('LBL_STATE_STICKY','Sticky (当修剪条目时不会被删除)');
define ('LBL_STATE_PRIVATE','保护 (只允许管理员察看)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','限制');
define ('LBL_PRIVATE','保护');
define ('LBL_ADMIN_TOGGLE_STATE','Toggle 状态:');
define ('LBL_ADMIN_TOGGLE_SET','设置');
define ('LBL_ADMIN_IM_SURE','我确定!');


// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','登录为 <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','未登录');
define ('LBL_LOG_OUT','注销');
define ('LBL_LOG_IN','登陆');

define ('LBL_ADMIN_OPML_IMPORT_AND','导入新的 feed 和:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... 替换所有现有的feed和条目.');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... 添加到组:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... 与现有的部分合并.');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','添加 %s 到 %s... ');

define ('LBL_TAG_FOLDERS','分类');
define ('LBL_SIDE_ITEMS','(%d 条)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d 未读, 共 %d feed)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> feed, 共 <strong>%d</strong> 分类');

define ('LBL_RATING','规定值:');

// New in 0.5.3:
define ('LBL_ENCLOSURE', '附录:');
define ('LBL_DOWNLOAD', '下载');
define ('LBL_PLAY', 'play');

define ('LBL_MARK_READ', "标记这些条目为已读");
define ('LBL_MARK_CHANNEL_READ', "标记这些条目为已读");
define ('LBL_MARK_FOLDER_READ',"标记这些条目为已读");

define ('LBL_MARK_CHANNEL_READ_ALL', "标记这些Feed为已读");
define ('LBL_MARK_FOLDER_READ_ALL',"标记这些目录为已读");
define ('LBL_MARK_CATEGORY_READ_ALL',"标记这些分类为已读");

// New in 0.5.x:
define ('LBL_FOOTER_LAST_MODIF_NEVER', '从未');
define ('LBL_ADMIN_DASHBOARD','控制面板'); 

define ('LBL_ADMIN_MUST_SET_PASS','<p>管理员未指定！</p>'
		.'<p>管理员请提供用户名和密码了!</p>');
define ('LBL_USERNAME','用户名');		
define ('LBL_PASSWORD','密码');
define ('LBL_PASSWORD2','验证密码');
define ('LBL_ADMIN_LOGIN','请登陆');
define ('LBL_ADMIN_PASS_NO_MATCH','密码不符!');

define ('LBL_ADMIN_PLUGINS','扩展');
define ('LBL_ADMIN_DOMAIN_PLUGINS_LBL','扩展');
define ('LBL_ADMIN_PLUGINS_HEADING_UPDATES','升级可用');
define ('LBL_ADMIN_CHECK_FOR_UPDATES','检查新版本');
define ('LBL_ADMIN_LOGIN_BAD_LOGIN','<strong>噢噢!</strong> 用户名或密码错误');
define ('LBL_ADMIN_LOGIN_NO_ADMIN','<strong>噢噢!</strong> 登陆成功 '
			.' %s 登陆成功, 但是您没有管理权。 请再登陆'
			.'以获得管理权限。 <a href="..">首页</a>');

define('LBL_ADMIN_ACTIVE_THEME','可用风格');
define('LBL_ADMIN_USE_THIS_THEME','使用此风格');

define ('LBL_ADMIN_PLUGINS_GET_MORE', '<p style="font-size:small">'
.'扩展是第三方脚本. '
.'更多扩展下载请到 <a style="text-decoration:underline" '
.' href="http://plugins.gregarius.net/">Plugin Repository</a>.</p>');

define ('LBL_LAST_UPDATE','最后升级');
define ('LBL_ADMIN_DOMAIN_THEMES_LBL','风格');
define ('LBL_ADMIN_THEMES','风格');

define ('LBL_ADMIN_THEMES_GET_MORE', '<p style="font-size:small">'
.'风格是一系列的模板文件,具体如何看你安装Gregarius.<br />'
.'更多风格请到 <a style="text-decoration:underline" '
.' href="http://themes.gregarius.net/">Themes Repository</a>.</p>');

define ('LBL_STATE_FLAG','标记 (为最后阅读的条目做)');
define ('LBL_FLAG','已标记');
define ('LBL_ADDED', 'Added');
?>
