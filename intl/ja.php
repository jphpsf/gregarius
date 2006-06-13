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
#
# 	Planning to translate this into your own language? Please read this:
#	http://wiki.gregarius.net/index.php/Internationalization
#
###############################################################################

/// Language: 日本語 
define ('LOCALE_WINDOWS','Japanese');
define ('LOCALE_LINUX','ja_JP');

define ('LBL_ITEM','アイテム');
define ('LBL_ITEMS','アイテム');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d件ヒット %s");
define ('LBL_H2_SEARCH_RESULT_FOR',"%d件ヒット %s");
define ('LBL_H2_SEARCH', '現在の記事 %d件');
define ('LBL_SEARCH_SEARCH_QUERY','検索語:');
define ('LBL_SEARCH_MATCH_OR', 'OR');
define ('LBL_SEARCH_MATCH_AND', 'AND');                                                                 
define ('LBL_SEARCH_MATCH_EXACT', '完全一致');
define ('LBL_SEARCH_CHANNELS', 'フィード:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','日付、フィードの順に並べる');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','フィード、日付の順に並べる');
define ('LBL_SEARCH_RESULTS_PER_PAGE','表示件数:');
define ('LBL_SEARCH_RESULTS','検索結果: ');
define ('LBL_H2_UNREAD_ITEMS','未読記事 (<strong id="ucnt">%d</strong>)');
define ('LBL_H2_RECENT_ITEMS', "最近の記事");
define ('LBL_H2_CHANNELS','フィード');
define ('LBL_H5_READ_UNREAD_STATS','%d 件, %d 未読');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong>件の記事 (未読<strong id="fucnt">%d</strong>件) <strong>%d</strong>フィード');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> tagged items, in <strong>%d</strong> tags');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">(%d)</strong>');
define ('LBL_UNREAD','unread');

define ('LBL_FTR_POWERED_BY', " powered by ");
define ('LBL_ALL','All');
define ('LBL_NAV_HOME','<span>ホーム</span>');
define ('LBL_NAV_UPDATE', '<span>更新</span>');
define ('LBL_NAV_CHANNEL_ADMIN', '<span>管理</span>');
define ('LBL_NAV_SEARCH', "<span>検索</span>");
define ('LBL_SEARCH_GO', '検索');

define ('LBL_POSTED', 'Posted: ');
define ('LBL_FETCHED','Fetched: ');
define ('LBL_BY', ' by ');

define ('LBL_AND','and');

define ('LBL_TITLE_UPDATING','更新中');
define ('LBL_TITLE_SEARCH','検索');


define ('LBL_HOME_FOLDER','ルート');
define ('LBL_VISIT', '(visit)');
define ('LBL_COLLAPSE','[-] 折りたたみ');
define ('LBL_EXPAND','[+] 展開');
define ('LBL_PL_FOR','Permalink for ');

define ('LBL_UPDATE_CHANNEL','フィード');
define ('LBL_UPDATE_STATUS','ステータス');
define ('LBL_UPDATE_UNREAD','新しい記事');

define ('LBL_UPDATE_STATUS_OK','OK (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'OK (Local cache)');
define ('LBL_UPDATE_STATUS_ERROR','ERROR');
define ('LBL_UPDATE_H2','%d 個のフィードを更新中');
define ('LBL_UPDATE_CACHE_TIMEOUT','HTTP Timeout (Local cache)');
define ('LBL_UPDATE_NOT_MODIFIED','OK (304 Not modified)');
define ('LBL_UPDATE_NOT_FOUND','404 Not Found (Local cache)');
// admin
define ('LBL_ADMIN_EDIT', '編集');
define ('LBL_ADMIN_DELETE', '削除');
define ('LBL_ADMIN_DELETE2', '削除');
define ('LBL_ADMIN_RENAME', '名前変更');
define ('LBL_ADMIN_CREATE', '作成');
define ('LBL_ADMIN_IMPORT','インポート');
define ('LBL_ADMIN_EXPORT','エクスポート');
define ('LBL_ADMIN_DEFAULT','デフォルト');
define ('LBL_ADMIN_ADD','追加');
define ('LBL_ADMIN_YES', 'はい');
define ('LBL_ADMIN_NO', 'いいえ');
define ('LBL_ADMIN_FOLDERS','フォルダ:');
define ('LBL_ADMIN_CHANNELS','フィード:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','記事:');
define ('LBL_ADMIN_CONFIG','設定:');
define ('LBL_ADMIN_OK','OK');
define ('LBL_ADMIN_CANCEL','キャンセル');
define ('LBL_ADMIN_LOGOUT','ログアウト');
define ('LBL_ADMIN_CONFIGURE','設定');

define ('LBL_ADMIN_OPML_IMPORT','インポート');
define ('LBL_ADMIN_OPML_EXPORT','エクスポート');
define ('LBL_ADMIN_OPML_IMPORT_OPML','OPMLをインポート:');
define ('LBL_ADMIN_OPML_EXPORT_OPML','OPMLをエクスポート:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','URL指定:');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','ファイル指定:');
define ('LBL_ADMIN_FILE_IMPORT','ファイルをインポート');

define ('LBL_ADMIN_IN_FOLDER','次のフォルダへ:');
define ('LBL_ADMIN_SUBMIT_CHANGES', '変更を適応');
define ('LBL_ADMIN_PREVIEW_CHANGES','プレビュー');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','タイトル');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','フォルダ');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','詳細');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','移動');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','アクション');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','フラグ');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','キー');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','値');
define ('LBL_ADMIN_CHANNELS_ADD','フィード追加:');
define ('LBL_ADMIN_FOLDERS_ADD','フォルダ追加:');
define ('LBL_ADMIN_CHANNEL_ICON','favicon:');
define ('LBL_CLEAR_FOR_NONE','(空白にするとアイコン無し)');

define ('LBL_ADMIN_CONFIG_VALUE','Value for');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','名前');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','著者');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','バージョン');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','詳細');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','アクティブ');
define ('LBL_ADMIN_PLUGINS_HEADING_OPTIONS','オプション');
define ('LBL_ADMIN_PLUGINS_OPTIONS','プラグインオプション');
define ('LBL_ADMIN_THEME_OPTIONS','テーマオプション');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','フィードを編集');
define ('LBL_ADMIN_CHANNEL_NAME','タイトル:');
define ('LBL_ADMIN_CHANNEL_RSS_URL','RSS URL:');
define ('LBL_ADMIN_CHANNEL_SITE_URL','サイト URL:');
define ('LBL_ADMIN_CHANNEL_FOLDER','フォルダ:');
define ('LBL_ADMIN_CHANNEL_DESCR','詳細:');
define ('LBL_ADMIN_FOLDER_NAME','フォルダ名:');
define ('LBL_ADMIN_CHANNEL_PRIVATE','このフィードは <strong>プライベート</strong>です。管理者のみ閲覧可能です。');
define ('LBL_ADMIN_CHANNEL_DELETED','このフィードは <strong>無効</strong>です。RSS取得は行われません。また、フィード一覧にも表示されません。');

define ('LBL_ADMIN_ARE_YOU_SURE', "本当に削除してもよろしいですか '%s'?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','本当にリセットしてもよろしいですか？ %s デフォルト値は \'%s\'です。');
define ('LBL_ADMIN_TRUE','True');
define ('LBL_ADMIN_FALSE','False');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','RSSフィードのURLまたは、ウェブサイトのURLを入力してください。');
define ('LBL_ADMIN_FEEDS','いかのフィードがみつかりました。 <a href="%s">%s</a> どのフィードを追加しますか？');

define ('LBL_ADMIN_PRUNE_OLDER','以下より古い記事を削除： ');
define ('LBL_ADMIN_PRUNE_DAYS','日');
define ('LBL_ADMIN_PRUNE_MONTHS','月');
define ('LBL_ADMIN_PRUNE_YEARS','年');
define ('LBL_ADMIN_PRUNE_KEEP','一番最近の記事は保存: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','Sticky記事も削除: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','以下のタグの付いた記事は削除しない... ');
define ('LBL_ADMIN_ALLTAGS_EXPL','(<strong>*</strong> を入力すると全てのタグの付いた記事が保存されます。)');

define ('LBL_ADMIN_ABOUT_TO_DELETE','Warning: %s個の記事が削除されます (トータル %s記事)');
define ('LBL_ADMIN_PRUNING','Pruning');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','フォルダ');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','フィード');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','記事');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','設定');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','ブックマークレット： [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','Gregariusに追加！！');


define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED', 
 		"<h1>Not Authorized!</h1>\nYou are not authorized to access the administration interface.\n"
		."Please follow <a href=\"%s\">this link</a> back to the main page.\n"
		."Have  a nice day!");
		
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','日付が無効です');
define ('LBL_ADMIN_ERROR_NO_PERIOD','日付が指定されていません');
define ('LBL_ADMIN_BAD_RSS_URL',"I'm sorry, I don't think I can handle this URL: '%s'");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"You can't delete the " . LBL_HOME_FOLDER . " folder");
define ('LBL_ADMIN_CANT_RENAME',"You can't rename this folder '%s' because such a folder already exists.");
define('LBL_ADMIN_ERROR_CANT_CREATE',"Looks like you already have a folder called '%s'!");

define ('LBL_TAG_TAGS','Tags');
define ('LBL_TAG_EDIT','編集');
define ('LBL_TAG_SUBMIT','OK');
define ('LBL_TAG_CANCEL','キャンセル');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"Oops! No items tagged &laquo;%s&raquo; were found.");
define ('LBL_TAG_ALL_TAGS','All Tags');
define ('LBL_TAG_TAGGED','tagged');
define ('LBL_TAG_TAGGEDP','tagged');
define ('LBL_TAG_SUGGESTIONS','おすすめタグ');
define ('LBL_TAG_SUGGESTIONS_NONE','おすすめタグはありません');
define ('LBL_TAG_RELATED','関連タグ: ');

define ('LBL_SHOW_UNREAD_ALL_SHOW','表示する記事: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','未読のみ');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','すべて');

define ('LBL_STATE_UNREAD','未読 (チェックすると未読)');
define ('LBL_STATE_STICKY','Sticky (自動削除の対象にしない)');
define ('LBL_STATE_PRIVATE','プライベート (管理者のみ閲覧可)');
define ('LBL_STICKY','Sticky');
define ('LBL_DEPRECATED','無効');
define ('LBL_PRIVATE','プライベート');
define ('LBL_ADMIN_STATE','状態:');
define ('LBL_ADMIN_STATE_SET','適応');
define ('LBL_ADMIN_IM_SURE','確認');


// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','<strong>%s</strong> でログイン中');
define ('LBL_NOT_LOGGED_IN','ログインしていません');
define ('LBL_LOG_OUT','ログアウト');
define ('LBL_LOG_IN','ログイン');

define ('LBL_ADMIN_OPML_IMPORT_AND','インポートするフィードは:');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','全てのフィードを一旦削除しインポートする。');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','以下のフォルダに入れる:');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','既存のフィードとマージする');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','追加 %s to %s... ');

define ('LBL_TAG_FOLDERS','カテゴリー');
define ('LBL_SIDE_ITEMS','(%d 記事)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d unread in %d feeds)');
define ('LBL_CATCNT_PF', '<strong>%d</strong>個のフィードが<strong>%d</strong>カテゴリーにあります。');

define ('LBL_RATING','レーティング:');


// New in 0.5.3:
define ('LBL_ENCLOSURE', 'Enclosure:');
define ('LBL_DOWNLOAD', 'ダウンロード');
define ('LBL_PLAY', 'プレイ');

define ('LBL_MARK_READ', "記事を既読にする");
define ('LBL_MARK_CHANNEL_READ', "記事を既読にする");
define ('LBL_MARK_FOLDER_READ',"記事を既読にする");

define ('LBL_MARK_CHANNEL_READ_ALL', "このフィードを既読にする");
define ('LBL_MARK_FOLDER_READ_ALL',"このフォルダを既読に");
define ('LBL_MARK_CATEGORY_READ_ALL',"このカテゴリを既読に");

// New in 0.5.x:
define ('LBL_FOOTER_LAST_MODIF_NEVER', 'Never');
define ('LBL_ADMIN_DASHBOARD','ダッシュボード'); 

define ('LBL_ADMIN_MUST_SET_PASS','<p>No Administrator has been specified yet!</p>'
		.'<p>Please provide an Administrator username and password now!</p>');
define ('LBL_USERNAME','ユーザー名');
define ('LBL_PASSWORD','パスワード');
define ('LBL_PASSWORD2','パスワード(再入力)');
define ('LBL_ADMIN_LOGIN','ログインしてください');
define ('LBL_ADMIN_PASS_NO_MATCH','パスワードが一致しません');

define ('LBL_ADMIN_PLUGINS','プラグイン');
define ('LBL_ADMIN_DOMAIN_PLUGINS_LBL','プラグイン');
define ('LBL_ADMIN_PLUGINS_HEADING_UPDATES','アップデート情報');
define ('LBL_ADMIN_CHECK_FOR_UPDATES','アップデートを確認');
define ('LBL_ADMIN_LOGIN_BAD_LOGIN','<strong>Oops!</strong> Bad login/password');
define ('LBL_ADMIN_LOGIN_NO_ADMIN','<strong>Oops!</strong> You are successfully '
			.'logged in as %s, but you don\\\'t have administration privileges. Log in again '
			.'with administration privileges or follow your way <a href="..">home</a>');

define('LBL_ADMIN_ACTIVE_THEME','現在のテーマ');
define('LBL_ADMIN_USE_THIS_THEME','このテーマにする');

define ('LBL_ADMIN_PLUGINS_GET_MORE', '<p style="font-size:small">'
.'プラグインはサードパーティーよって提供される機能拡張です。'
.'<a style="text-decoration:underline" '
.' href="http://plugins.gregarius.net/">プラグインレィポジトリ</a>から他の機能拡張をダウンロードできます。');

define ('LBL_LAST_UPDATE','最終更新日');
define ('LBL_ADMIN_DOMAIN_THEMES_LBL','テーマ');
define ('LBL_ADMIN_THEMES','テーマ');

define ('LBL_ADMIN_THEMES_GET_MORE', '<p style="font-size:small">'
.'テーマを変更することによってGregariusの機能や見た目を変更できます。テンプレートをカスタマイズすることによって自由にテーマを作成できます。<br />'
.'<a style="text-decoration:underline" '
.' href="http://themes.gregarius.net/">テーマレポジトリ</a>で他のテーマをダウンロードできます。');

define ('LBL_STATE_FLAG','フラグ(後で読むために記事にフラグをつける)');
define ('LBL_FLAG','フラグがついています');

?>
