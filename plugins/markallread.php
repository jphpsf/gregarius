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

/// Name: Mark All Read
/// Author: Keith D. Zimmerman
/// Description: Display "Mark All Read" buttons and links in many places
/// Version: 0.5
/// Configuration: __markallread_config

/**
 * Changelog:
 *
 * 0.1	getting started
 * 0.2	initial public release
 * 0.3	support for i18n
 * 0.4  Fixed a couple validation issues
 * 0.5  Adapted for new l10n method
 */

define ('MARKALLREAD_CONFIG_OPTIONS', 'markallread.options');

define ('MARKALLREAD_OPTION_BUTTON', 0x1);
define ('MARKALLREAD_OPTION_LINK_FEED', 0x2);
define ('MARKALLREAD_OPTION_LINK_FOLDER', 0x4);
define ('MARKALLREAD_OPTION_LINK_CATEGORY', 0x8);
define ('MARKALLREAD_OPTION_CONFIIRM', 0x10);
define ('MARKALLREAD_OPTION_BOTTOM_BUTTON', 0x20);

function __markallread_js($js) {
    $js[] = getPath(). RSS_PLUGINS_DIR . "/markallread.php?myjs";
    return $js;
}

function __markallread_sidemenu_categoryunreadlabel( $existingText ) {
	if( hidePrivate() )
		return $existingText;

	$options = rss_plugins_get_option( MARKALLREAD_CONFIG_OPTIONS );
	if( $options & MARKALLREAD_OPTION_LINK_CATEGORY )
		return "<a title='".LBL_MARK_CATEGORY_READ_ALL."' href='". getPath() ."feed.php?metaaction=ACT_MARK_VFOLDER_READ&vfolder=" . rss_feeds_folder_id() . "' onclick='javascript: return _markallread(this,\"category\",\"" . rss_feeds_folder_name() . "\");'>" . $existingText . '</a>';
	else
		return $existingText;
}

function __markallread_sidemenu_folderunreadlabel( $existingText ) {
	if( hidePrivate() )
		return $existingText;

	$options = rss_plugins_get_option( MARKALLREAD_CONFIG_OPTIONS );
	if( $options & MARKALLREAD_OPTION_LINK_FOLDER )
		return "<a title='".LBL_MARK_FOLDER_READ_ALL."' href='". getPath() ."feed.php?metaaction=ACT_MARK_FOLDER_READ&amp;folder=" . rss_feeds_folder_id() . "' onclick='javascript: return _markallread(this,\"folder\",\"" . rss_feeds_folder_name() . "\");'>" . $existingText . '</a>';
	else
		return $existingText;
}

function __markallread_sidemenu_feedunreadlabel( $existingText ) {
	if( hidePrivate() )
		return $existingText;

	$options = rss_plugins_get_option( MARKALLREAD_CONFIG_OPTIONS );
	if( $options & MARKALLREAD_OPTION_LINK_FEED )
		return "<a title='".LBL_MARK_CHANNEL_READ_ALL."' href='". getPath() ."feed.php?metaaction=ACT_MARK_CHANNEL_READ&amp;channel=" . $GLOBALS['rss']->currentFeedsFeed-> id . "' onclick='javascript: return _markallread(this,\"feed\",\"" . rss_feeds_feed_title() . "\");'>" . $existingText . '</a>';
	else
		return $existingText;
}

function __markallread_buttondisplay() {
	$options = rss_plugins_get_option( MARKALLREAD_CONFIG_OPTIONS );
	$safety = '';

	if(defined('MARK_READ_FEED_FORM')) {
			$metaaction = "ACT_MARK_CHANNEL_READ";
			$id = 'name="channel" value="' . MARK_READ_FEED_FORM . '"';
			$text = LBL_MARK_CHANNEL_READ_ALL;
			if( $options & MARKALLREAD_OPTION_CONFIIRM )
				$safety = ' onclick=\'javascript: return _confirmmarkallread("feed", "");\'';
	} elseif(defined('MARK_READ_FOLDER_FORM')) {
			$metaaction = "ACT_MARK_FOLDER_READ";
			$id = 'name="folder" value="' . MARK_READ_FOLDER_FORM . '"';
			$text = LBL_MARK_FOLDER_READ_ALL;
			if( $options & MARKALLREAD_OPTION_CONFIIRM )
				$safety = ' onclick=\'javascript: return _confirmmarkallread("folder", "");\'';
	} elseif(defined('MARK_READ_VFOLDER_FORM')){
			$metaaction = "ACT_MARK_VFOLDER_READ";
			$id = 'name="vfolder" value="' . MARK_READ_VFOLDER_FORM . '"';
			$text = LBL_MARK_CATEGORY_READ_ALL;
			if( $options & MARKALLREAD_OPTION_CONFIIRM )
				$safety = ' onclick=\'javascript: return _confirmmarkallread("category", "");\'';
	}
	
	if( isset( $id ) && isset( $metaaction ) && isset( $text ) )
	{
?>
<form action="<?php echo getPath(); ?>feed.php" method="post">
	<p><input id="_markReadButton" type="submit" name="action" accesskey="m" value="<?php echo $text;?>" <?php echo $safety ?>/>
	<input type="hidden" name="metaaction" value="<?php echo $metaaction; ?>"/>
	<input type="hidden" <?php echo $id; ?>/>
</p>
</form>
<?php
	}
}

function __markallread_beforeitemsimmediate( $in ) {
	$options = rss_plugins_get_option( MARKALLREAD_CONFIG_OPTIONS );
	if( hidePrivate() || !( $options & MARKALLREAD_OPTION_BUTTON ) )
		return $in;
	__markallread_buttondisplay();
	return $in;
}

function __markallread_afteritems( $in ) {
	$options = rss_plugins_get_option( MARKALLREAD_CONFIG_OPTIONS );
	if( hidePrivate() || !( $options & MARKALLREAD_OPTION_BOTTOM_BUTTON ) )
		return $in;
	
	if (defined ('MARK_READ_FEED_FORM') 			|| 
		 defined ('MARK_READ_FOLDER_FORM') 		|| 
		 defined ('MARK_READ_VFOLDER_FORM'))	{
        
        echo "<div style=\"text-align:right\">\n";
        __markallread_buttondisplay();
        echo "</div>\n";
    }
	
	return $in;
}

function __markallread_config() {
    $options = rss_plugins_get_option( MARKALLREAD_CONFIG_OPTIONS );
    if(null == $options) {
        $options = MARKALLREAD_OPTION_CONFIIRM;
    }

	if( rss_plugins_is_submit() )
	{
		$options = 0;
		if(!empty($_REQUEST['chkMarkReadButton'])) {
			$options |= MARKALLREAD_OPTION_BUTTON;
		}
		if(!empty($_REQUEST['chkMarkReadButtonBottom'])) {
			$options |= MARKALLREAD_OPTION_BOTTOM_BUTTON;
		}
		if(!empty($_REQUEST['chkFeedLink'])) {
			$options |= MARKALLREAD_OPTION_LINK_FEED;
		}
		if(!empty($_REQUEST['chkFolderLink'])) {
			$options |= MARKALLREAD_OPTION_LINK_FOLDER;
		}
		if(!empty($_REQUEST['chkChannelLink'])) {
			$options |= MARKALLREAD_OPTION_LINK_CATEGORY;
		}
		if(!empty($_REQUEST['chkConfirm'])) {
			$options |= MARKALLREAD_OPTION_CONFIIRM;
		}

		rss_plugins_add_option(MARKALLREAD_CONFIG_OPTIONS, $options, 'num');
	}
	else
	{
?>
<p>
  <input type='checkbox' value='1' name='chkMarkReadButton' id='chkMarkReadButton'<?php echo( $options & MARKALLREAD_OPTION_BUTTON ? " checked='1'" : "" );?> />
  <label for='chkMarkReadButton'>Display a button to mark all items read in the current feed, folder, or channel</label>
</p>
<p>
  <input type='checkbox' value='1' name='chkMarkReadButtonBottom' id='chkMarkReadButtonBottom'<?php echo( $options & MARKALLREAD_OPTION_BOTTOM_BUTTON ? " checked='1'" : "" );?> />
  <label for='chkMarkReadButtonBottom'>Display an identical button at the bottom of the screen</label>
</p>
<fieldset>
  <legend>Sidebar</legend>
  <p>
    <input type='checkbox' value='1' name='chkFeedLink' id='chkFeedLink'<?php echo( $options & MARKALLREAD_OPTION_LINK_FEED ? " checked='1'" : "" );?> />
    <label for='chkFeedLink'>Display a mark read link for feeds</label>
  </p>
  <p>
    <input type='checkbox' value ='1' name='chkFolderLink' id='chkFolderLink'<?php echo( $options & MARKALLREAD_OPTION_LINK_FOLDER ? " checked='1'" : "" );?> />
	<label for='chkFolderLink'>Display a mark read link for folders</label>
  </p>
  <p>
    <input type='checkbox' value='1' name='chkChannelLink' id='chkChannelLink'<?php echo( $options & MARKALLREAD_OPTION_LINK_CATEGORY ? " checked='1'" : "" );?> />
	<label for='chkChannelLink'>Display a mark read link for channels</label>
  </p>
</fieldset>
<p>
  <input type='checkbox' value='1' name='chkConfirm' id='chkConfirm'<?php echo( $options & MARKALLREAD_OPTION_CONFIIRM ? " checked='1'" : "" );?> />
  <label for='chkConfirm'>Ask for confirmation before marking items read</label>
</p>
<?php
	}
}

if (isset($_REQUEST['myjs'])) {
	 require_once('../core.php');
	 rss_bootstrap(false,'$Revision: 1181 $',0);
    require_once('../init.php');

    if (hidePrivate()) {
		return "";
    }
    
    ?>
function _confirmmarkallread( type, name )
{
	if( name != "" )
		name = " " + name;
	return window.confirm( "Are you sure that you want to mark all items in the " + type + name + " as read?" );
}

function _markallread(o,type,name)
{
	//window.alert( o.href + "&redirectto=" + escape(window.location) );
	<?php
	    $options = rss_plugins_get_option( MARKALLREAD_CONFIG_OPTIONS );
		if(null == $options) {
			$options = MARKALLREAD_OPTION_CONFIIRM;
		}
		if( $options & MARKALLREAD_OPTION_CONFIIRM ) {
	?>
	if( _confirmmarkallread( type, name ) )
	<?php }	?>
		window.location = o.href + "&redirectto=" + escape(window.location);
	return false;
}

	<?php
    flush();
    exit();
}
else
{
	rss_set_hook('rss.plugins.javascript','__markallread_js');
	rss_set_hook('rss.plugins.sidemenu.categoryunreadlabel', '__markallread_sidemenu_categoryunreadlabel');
	rss_set_hook('rss.plugins.sidemenu.folderunreadlabel', '__markallread_sidemenu_folderunreadlabel');
	rss_set_hook('rss.plugins.sidemenu.feedunreadlabel', '__markallread_sidemenu_feedunreadlabel');
	rss_set_hook('rss.plugins.items.beforeitemsimmediate', '__markallread_beforeitemsimmediate');
	rss_set_hook('rss.plugins.items.afteritems','__markallread_afteritems');
}
?>
