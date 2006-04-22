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
# E-mail:      omry at firefang.net
# Web page:    http://firefang.net/blog
#
###############################################################################
#
# 	Planning to translate this into your own language? Please read this:
#	http://wiki.gregarius.net/index.php/Internationalization
#
###############################################################################

/// Language: עברית
define ('LOCALE_WINDOWS','hebrew');
define ('LOCALE_LINUX','he_IL');

define ('LBL_ITEM','רשומה');
define ('LBL_ITEMS','רשומות');
define ('LBL_H2_SEARCH_RESULTS_FOR', "%d תוצאות ל%s");
define ('LBL_H2_SEARCH_RESULT_FOR',"תוצאה %d ל%s");
define ('LBL_H2_SEARCH', 'חפש ב%d רשומות');
define ('LBL_SEARCH_SEARCH_QUERY','חפש ביטוי:');
define ('LBL_SEARCH_MATCH_OR', 'כמה ביטויים (או)');
define ('LBL_SEARCH_MATCH_AND', 'כל הביטויים (וגם)');
define ('LBL_SEARCH_MATCH_EXACT', 'התאמה מדוייקת');
define ('LBL_SEARCH_CHANNELS', 'ערוץ:');
define ('LBL_SEARCH_ORDER_DATE_CHANNEL','מיין לפי תאריך,ערוץ');
define ('LBL_SEARCH_ORDER_CHANNEL_DATE','מיין לפי ערוץ,תאריך');
define ('LBL_SEARCH_RESULTS_PER_PAGE','תוצאות לעמוד:');
define ('LBL_SEARCH_RESULTS','תוצאות: ');
define ('LBL_H2_UNREAD_ITEMS','רשומות שלא נקראו (<span id="ucnt">%d</span>)');
define ('LBL_H2_RECENT_ITEMS', "רשומות קודמות");
define ('LBL_H2_CHANNELS','ערוצים');
define ('LBL_H5_READ_UNREAD_STATS','%d רשומות, %d לא נקראו');
define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> רשומות (<strong>%d</strong> לא נקראו) ב<strong>%d</strong> ערוצים');
//define ('LBL_ITEMCOUNT_PF', '<strong>%d</strong> רשומות ב <strong>%d</strong> ערוצים');
define ('LBL_TAGCOUNT_PF', '<strong>%d</strong> רשומות מסומנות ב<strong>%d</strong> תוויות');
define ('LBL_UNREAD_PF', '<strong id="%s" style="%s">לא נקראו %d</strong>');
define ('LBL_UNREAD','לא נקראו');

define ('LBL_FTR_POWERED_BY', " פועל על ");
define ('LBL_ALL','הכל');
define ('LBL_NAV_HOME','בית');
define ('LBL_NAV_UPDATE', 'רענן');
define ('LBL_NAV_CHANNEL_ADMIN', 'ניהול');
define ('LBL_NAV_SEARCH', "חיפוש");
define ('LBL_SEARCH_GO', 'חפש');

define ('LBL_POSTED', 'פורסם: ');
define ('LBL_FETCHED','נוסף: ');
define ('LBL_BY', ' על ידי ');

define ('LBL_AND','ו');

define ('LBL_TITLE_UPDATING','מעדכן');
define ('LBL_TITLE_SEARCH','חיפוש');

define ('LBL_HOME_FOLDER','שורש');
define ('LBL_VISIT', '(בקר(');
define ('LBL_COLLAPSE','[-] הקטן');
define ('LBL_EXPAND','[+] הגדל');
define ('LBL_PL_FOR','לינקבוע ל');

define ('LBL_UPDATE_CHANNEL','ערוץ');
define ('LBL_UPDATE_STATUS','מצב');
define ('LBL_UPDATE_UNREAD','רשומות חדשות');

define ('LBL_UPDATE_STATUS_OK','אוקיי (HTTP 200)');
define ('LBL_UPDATE_STATUS_CACHED', 'אוקיי (מטמון מקומי)');
define ('LBL_UPDATE_STATUS_ERROR','שגיאה');
define ('LBL_UPDATE_H2','מרענן %d ערוצים');
define ('LBL_UPDATE_CACHE_TIMEOUT','הזמן אזל (מטמון מקומי)');
define ('LBL_UPDATE_NOT_MODIFIED','אוקיי (304 - לא השתנה)');
define ('LBL_UPDATE_NOT_FOUND','לא נמצא (404 - מטמון מקומי)');
// admin
define ('LBL_ADMIN_EDIT', 'ערוך');
define ('LBL_ADMIN_DELETE', 'מחק');
define ('LBL_ADMIN_DELETE2', 'מחק');
define ('LBL_ADMIN_RENAME', 'שנה שם ל...');
define ('LBL_ADMIN_CREATE', 'צור חדש');
define ('LBL_ADMIN_IMPORT','יבוא');
define ('LBL_ADMIN_EXPORT','יצוא');
define ('LBL_ADMIN_DEFAULT','ברירת מחדל');
define ('LBL_ADMIN_ADD','הוסף');
define ('LBL_ADMIN_YES', 'כן');
define ('LBL_ADMIN_NO', 'לא');
define ('LBL_ADMIN_FOLDERS','מחיצות:');
define ('LBL_ADMIN_CHANNELS','ערוצים:');
define ('LBL_ADMIN_OPML','OPML:');  
define ('LBL_ADMIN_ITEM','רשומות: ');
define ('LBL_ADMIN_CONFIG','הגדרות');
define ('LBL_ADMIN_OK','אוקיי');
define ('LBL_ADMIN_CANCEL','ביטול');
define ('LBL_ADMIN_LOGOUT','יציאה');
define ('LBL_ADMIN_CONFIGURE','הגדר');

define ('LBL_ADMIN_OPML_IMPORT','יבוא');
define ('LBL_ADMIN_OPML_EXPORT','יצוא');
define ('LBL_ADMIN_OPML_IMPORT_OPML','יבוא');
define ('LBL_ADMIN_OPML_EXPORT_OPML','יצוא OPML');
define ('LBL_ADMIN_OPML_IMPORT_FROM_URL','...מכתובת אינטרנט (URL):');
define ('LBL_ADMIN_OPML_IMPORT_FROM_FILE','... מקובץ:');
define ('LBL_ADMIN_FILE_IMPORT','יבא הגדרות');

define ('LBL_ADMIN_IN_FOLDER','אל מחיצה');
define ('LBL_ADMIN_SUBMIT_CHANGES', 'החל שינויים');
define ('LBL_ADMIN_PREVIEW_CHANGES','תצוגה מקדימה');
define ('LBL_ADMIN_CHANNELS_HEADING_TITLE','כותרת');
define ('LBL_ADMIN_CHANNELS_HEADING_FOLDER','מחיצה');
define ('LBL_ADMIN_CHANNELS_HEADING_DESCR','תיאור');
define ('LBL_ADMIN_CHANNELS_HEADING_MOVE','הזז');
define ('LBL_ADMIN_CHANNELS_HEADING_ACTION','פעולה');
define ('LBL_ADMIN_CHANNELS_HEADING_FLAGS','דגלים');
define ('LBL_ADMIN_CHANNELS_HEADING_KEY','מפתח');
define ('LBL_ADMIN_CHANNELS_HEADING_VALUE','ערך');
define ('LBL_ADMIN_CHANNELS_ADD','הוסף ערוץ: ');
define ('LBL_ADMIN_FOLDERS_ADD','הוסף מחיצה');
define ('LBL_ADMIN_CHANNEL_ICON','הראה צלמית אתר :');
define ('LBL_CLEAR_FOR_NONE',')השאר ריק כדי לא לקבל צלמית(');

define ('LBL_ADMIN_CONFIG_VALUE','ערך ל');

define ('LBL_ADMIN_PLUGINS_HEADING_NAME','שם');
define ('LBL_ADMIN_PLUGINS_HEADING_AUTHOR','מחבר');
define ('LBL_ADMIN_PLUGINS_HEADING_VERSION','גרסא');
define ('LBL_ADMIN_PLUGINS_HEADING_DESCRIPTION','תאור');
define ('LBL_ADMIN_PLUGINS_HEADING_ACTION','פעיל');
define ('LBL_ADMIN_PLUGINS_HEADING_OPTIONS','אפשרויות');
define ('LBL_ADMIN_PLUGINS_OPTIONS','אפשרויות תוסף');
define ('LBL_ADMIN_THEME_OPTIONS','אפשרויות ערכת נושא');


define ('LBL_ADMIN_CHANNEL_EDIT_CHANNEL','ערוך ערוץ ');
define ('LBL_ADMIN_CHANNEL_NAME','כותרת: ');
define ('LBL_ADMIN_CHANNEL_RSS_URL','כתובת ערוץ RSS');
define ('LBL_ADMIN_CHANNEL_SITE_URL','כתובת אתר');
define ('LBL_ADMIN_CHANNEL_FOLDER','במחיצה: ');
define ('LBL_ADMIN_CHANNEL_DESCR','תיאור: ');
define ('LBL_ADMIN_FOLDER_NAME','שם מחיצה: ');
define ('LBL_ADMIN_CHANNEL_PRIVATE','ערוץ פרטי, רק מנהלים יכולים לראות אותו');
define ('LBL_ADMIN_CHANNEL_DELETED','הערוץ בוטל, הוא לא יעודכן בהמשך ולא יהיה זמין בעמודת הערוצים');


define ('LBL_ADMIN_ARE_YOU_SURE', "האם אתה בטוח שאתה רוצה למחוק את %s?");
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','Are you sure you wish to reset the value for %s to its default \'%s\'?');
define ('LBL_ADMIN_ARE_YOU_SURE_DEFAULT','האם אתה בטוח שברצונך לאפס את הערך של %s לערך ברירת המחדל %s?');
define ('LBL_ADMIN_TRUE','אמת');
define ('LBL_ADMIN_FALSE','שקר');
define ('LBL_ADMIN_MOVE_UP','&uarr;');
define ('LBL_ADMIN_MOVE_DOWN','&darr;');
define ('LBL_ADMIN_ADD_CHANNEL_EXPL','(הכנס כתובת אינטרנט של ערוץ RSS או של אתר שלערוץ שלו אתה רוצה להרשם)');
define ('LBL_ADMIN_FEEDS','The following feeds were found in <a href="%s">%s</a>, which one would you like to subscribe?');
define ('LBL_ADMIN_FEEDS','הערוצים הבאים נמצאו ב <a href="%s">%s</a>לאיזה ערוץ תרצה להרשם?');

define ('LBL_ADMIN_PRUNE_OLDER','מחק רשומות ישנות יותר מ ');
define ('LBL_ADMIN_PRUNE_DAYS','ימים');
define ('LBL_ADMIN_PRUNE_MONTHS','חודשים');
define ('LBL_ADMIN_PRUNE_YEARS','שנים');
define ('LBL_ADMIN_PRUNE_KEEP','שמור את הרשומות החדשות ביותר: ');
define ('LBL_ADMIN_PRUNE_INCLUDE_STICKY','מחק גם רשומות דביקות: ');
define ('LBL_ADMIN_PRUNE_EXCLUDE_TAGS','אל תמחוק רשומות מתוייגות');
define ('LBL_ADMIN_ALLTAGS_EXPL','(הכנס <strong>*</strong> כדי לשמור על כל הרשומות המתוייגות)');
define ('LBL_ADMIN_ABOUT_TO_DELETE','אזהרה, אתה עומד למחוק %s רשומות מתוך %s');
define ('LBL_ADMIN_PRUNING','מחיקת רשומות ישנות');
define ('LBL_ADMIN_DOMAIN_FOLDER_LBL','מחיצות');
define ('LBL_ADMIN_DOMAIN_CHANNEL_LBL','ערוצים');
define ('LBL_ADMIN_DOMAIN_ITEM_LBL','רשומות');
define ('LBL_ADMIN_DOMAIN_CONFIG_LBL','הגדרות');
define ('LBL_ADMIN_DOMAIN_LBL_OPML_LBL','opml');
define ('LBL_ADMIN_BOOKMARKET_LABEL','סימניית הרשמה (Bookmarklet) [<a href="http://www.squarefree.com/bookmarklets/">?</a>]:');
define ('LBL_ADMIN_BOOKMARKLET_TITLE','הוסף לגרגריוס');

define ('LBL_ADMIN_ERROR_NOT_AUTHORIZED',"אתה לא מורשה לגשת למסך הניהול, אנא <a href=\"%s\">חזור</a>  למסך הראשי");
				
define ('LBL_ADMIN_ERROR_PRUNING_PERIOD','תקופת מחיקה לא חוקית');
define ('LBL_ADMIN_ERROR_NO_PERIOD','אופס, לא צויינה תקופת מחיקה');
define ('LBL_ADMIN_BAD_RSS_URL',"לצערי אני לא יכול להתמודד עם כתובת האינטרנט %s");
define ('LBL_ADMIN_ERROR_CANT_DELETE_HOME_FOLDER',"אתה לא יכול למחוק את המחיצה ".LBL_HOME_FOLDER);
define ('LBL_ADMIN_CANT_RENAME',"מחיצה בשם זה כבר קיימת");
define('LBL_ADMIN_ERROR_CANT_CREATE',"מחיצה בשם זה כבר קיימת");

define ('LBL_TAG_TAGS','תויות');
define ('LBL_TAG_EDIT','ערוך');
define ('LBL_TAG_SUBMIT','החל');
define ('LBL_TAG_CANCEL','בטל');
define ('LBL_TAG_SUBMITTING','...');
define ('LBL_TAG_ERROR_NO_TAG',"אופס, לא נמצאו רשומת שמתוייגות ב&laquo;%s&raquo");
define ('LBL_TAG_ALL_TAGS','כל התוויות');
define ('LBL_TAG_TAGGED','מתוייג');
define ('LBL_TAG_TAGGEDP','מתוייג');
define ('LBL_TAG_SUGGESTIONS','הצעות');
define ('LBL_TAG_SUGGESTIONS_NONE','אין הצעות');
define ('LBL_TAG_RELATED','תגיות קשורות: ');

define ('LBL_SHOW_UNREAD_ALL_SHOW','הראה רשומות: ');
define ('LBL_SHOW_UNREAD_ALL_UNREAD_ONLY','שלא נקראו');
define ('LBL_SHOW_UNREAD_ALL_READ_AND_UNREAD','הכל');

define ('LBL_STATE_UNREAD','לא נקרא (סמן כנקרא/לא נקרא)');
define ('LBL_STATE_STICKY','דביק (לא ימחק כאשר אתה מנקה רשומות ישנות)');
define ('LBL_STATE_PRIVATE','פרטי (רק מנהלים יכולים לראות רשומות פרטיות)');
define ('LBL_STICKY','דביק');
define ('LBL_DEPRECATED','מבוטל');
define ('LBL_PRIVATE','פרטי');
define ('LBL_ADMIN_STATE','מצב:');
define ('LBL_ADMIN_STATE_SET','החל');
define ('LBL_ADMIN_IM_SURE','אני בטוח!');


// new in 0.5.1:
define ('LBL_LOGGED_IN_AS','מחובר בשם <strong>%s</strong>');
define ('LBL_NOT_LOGGED_IN','לא מחובר');
define ('LBL_LOG_OUT','יציאה');
define ('LBL_LOG_IN','כניסה');

define ('LBL_ADMIN_OPML_IMPORT_AND','יבא ערוצים חדשים ו: ');
define ('LBL_ADMIN_OPML_IMPORT_WIPE','... החלף את כל הערוצים והרשומות הקיימים');
define ('LBL_ADMIN_OPML_IMPORT_FOLDER','... והוסף אותם למחיצה: ');
define ('LBL_ADMIN_OPML_IMPORT_MERGE','... ושלב אותם עם הערוצים הקיימים');

define ('LBL_ADMIN_OPML_IMPORT_FEED_INFO','מוסיף את %s אל %s ');

define ('LBL_TAG_FOLDERS','קטגוריות');
define ('LBL_SIDE_ITEMS','(%d רשומות)');
define ('LBL_SIDE_UNREAD_FEEDS','(%d שלא נקראו ב%d ערוצים)');
define ('LBL_CATCNT_PF', '<strong>%d</strong> ערוצים ב<strong>%d</strong> קטגוריות');

define ('LBL_RATING','ניקוד:');


// New in 0.5.3:
define ('LBL_ENCLOSURE', 'מעטפה');
define ('LBL_DOWNLOAD', 'הורד');
define ('LBL_PLAY', 'נגן');

define ('LBL_MARK_READ', "סמן את הרשומות כקרואות");
define ('LBL_MARK_CHANNEL_READ', "סמן את הרשומות כקרואות");
define ('LBL_MARK_FOLDER_READ', "סמן את הרשומות כקרואות");

define ('LBL_MARK_CHANNEL_READ_ALL', "סמן את הערוץ כקרוא");
define ('LBL_MARK_FOLDER_READ_ALL',"סמן את המחיצה כקרואה");
define ('LBL_MARK_CATEGORY_READ_ALL',"סמן את הקטגוריה כקרואה");

// New in 0.5.x:
define ('LBL_FOOTER_LAST_MODIF_NEVER', 'אף פעם');
define ('LBL_ADMIN_DASHBOARD','לוח מחוונים'); 

define ('LBL_ADMIN_MUST_SET_PASS','<p>עדיין לא נקבע מנהל!</p>'
		.'<p>נא לקבוע שם וסיסמא למנהל עכשיו!</p>');
define ('LBL_USERNAME','שם משתמש');		
define ('LBL_PASSWORD','סיסמא');
define ('LBL_PASSWORD2','סיסמא (שוב)');
define ('LBL_ADMIN_LOGIN','הכנס בבקשה');
define ('LBL_ADMIN_PASS_NO_MATCH','הסיסמאות לא תואמות!');

define ('LBL_ADMIN_PLUGINS','תוספים');
define ('LBL_ADMIN_DOMAIN_PLUGINS_LBL','תוספים');
define ('LBL_ADMIN_PLUGINS_HEADING_UPDATES','עדכון זמין');
define ('LBL_ADMIN_CHECK_FOR_UPDATES','בדוק אם יש עדכונים');
define ('LBL_ADMIN_LOGIN_BAD_LOGIN','סיסמא או שם משתמש שגויים!');
define ('LBL_ADMIN_LOGIN_NO_ADMIN','אופס, נכנסת בהצלחה בשם %s אבל אין לך הרשאות ניהול, הכנס שוב בבקשה עם הרשאות ניהול, או חזור למסך הראשי');

define('LBL_ADMIN_ACTIVE_THEME','ערכת נושא פעילה');
define('LBL_ADMIN_USE_THIS_THEME','השתמש בערכת הנושא הזו');

define ('LBL_ADMIN_PLUGINS_GET_MORE', '<p style="font-size:small">'
.'תוספים הם קוד של צד שלישי שמרחיבים את היכולות של גרגריוס. '
.'אפשר להוריד תוספים נוספים ב<a style="text-decoration:underline" '
.' href="http://plugins.gregarius.net/">מאגר התוספים</a>.</p>');

define ('LBL_LAST_UPDATE','עדכון אחרון');
define ('LBL_ADMIN_DOMAIN_THEMES_LBL','ערכות נושא');
define ('LBL_ADMIN_THEMES','ערכות נושא');

define ('LBL_ADMIN_THEMES_GET_MORE', '<p style="font-size:small">'
.'ערכות נושא הם חבילות קבצים שמגדירות איך גרגריוס נראה.<br />'
.'אפשר להוריד ערכות נושא נוספות ב<a style="text-decoration:underline" '
.' href="http://themes.gregarius.net/">מאגר ערכות הנושא</a>.</p>');

define ('LBL_STATE_FLAG','אחר כך (סמן רשומה לקריאה אחר כך)');
define ('LBL_FLAG','אחר כך');

?>
