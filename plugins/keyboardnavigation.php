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


/// Name: Keyboard naviation
/// Author: Marco Bonetti &amp; Chris J. Friesen
/// Description: Navigate between items without using your mouse.
/// Version: 0.5
/// Configuration: __kbnav_config

$__kbnav_version = 'kbnav_0.5';

/**
* Changelog:
* 0.1 - basic navigation: j/k: down/up, m: mark as read, s: toggle sticky
* 0.2 - f: toggle flag, c: lilina theme un/collapse
* 0.3 - o: open URL for item, shift-o: open URL in new window, fix: item-collapse/expand for non-logged in users.
* 0.4 - configuration
*/

/**
* Wishlist:
*  Mark all as read
*  Next Feed / Previous Feed
*/

define ('KBNAVIGATIONPLUGIN_KMAP_CONFIG_OPTIONS', 'kbnav.options.keymapping');
/**
 * Labels
 */
function __kbnav_config_actions() {
	return array(
		'__kbnav_Next' => 'Navigate to next item',
        '__kbnav_Prev' => 'Navigate to previous item',
        '__kbnav_ToggleSticky' => 'Toggle Sticky state of the current item',
        '__kbnav_ToggleFlag' => 'Toggle Flagged state of the current item',
        '__kbnav_NextMarkRead' => 'Mark current item as read, move to the next one',
        '__kbnav_MarkAllRead' => 'Mark all shown items as read',
        '__kbnav_ScrollTop' => 'Scroll to the top of the window',
        '__kbnav_ToggleCollapse' => 'In the Lilina theme, toggle the collapsed state of the current item',
		'__kbnav_OpenUrl' => 'Navigate to the URL of the current item',
		'__kbnav_OpenUrlNW' => 'Navigate to the URL of the current item in a new window',        
		'__kbnav_EditTags' => 'Edit tags of the current item',        
	);
}
/**
 * Fetch the config from the config, add default values for missing keys
 */
function __kbnav_config_action_keys() {
	$kmap = rss_plugins_get_option(KBNAVIGATIONPLUGIN_KMAP_CONFIG_OPTIONS);
	if (!isset($kmap['__kbnav_Next']['key'])) {$kmap['__kbnav_Next']['key'] = 'j';}
	if (!isset($kmap['__kbnav_Prev']['key'])) {$kmap['__kbnav_Prev']['key'] = 'k';}
	if (!isset($kmap['__kbnav_ToggleSticky']['key'])) {$kmap['__kbnav_ToggleSticky']['key'] = 's';}
	if (!isset($kmap['__kbnav_ToggleFlag']['key'])) {$kmap['__kbnav_ToggleFlag']['key'] = 'f';}
	if (!isset($kmap['__kbnav_NextMarkRead']['key'])) {$kmap['__kbnav_NextMarkRead']['key'] = 'm';}
	if (!isset($kmap['__kbnav_MarkAllRead']['key'])) {
		$kmap['__kbnav_MarkAllRead']['key'] = 'm';
		$kmap['__kbnav_MarkAllRead']['modifier'] = 'shift';
	}	
	if (!isset($kmap['__kbnav_ScrollTop']['key'])) {$kmap['__kbnav_ScrollTop']['key'] = 'h';}
	if (!isset($kmap['__kbnav_ToggleCollapse']['key'])) {$kmap['__kbnav_ToggleCollapse']['key'] = 'c';}
	if (!isset($kmap['__kbnav_OpenUrl']['key'])) {$kmap['__kbnav_OpenUrl']['key'] = 'o';}
	if (!isset($kmap['__kbnav_OpenUrlNW']['key'])) {
		$kmap['__kbnav_OpenUrlNW']['key'] = 'o';
		$kmap['__kbnav_OpenUrlNW']['modifier'] = 'shift';
	}
	if (!isset($kmap['__kbnav_EditTags']['key'])) {$kmap['__kbnav_EditTags']['key'] = 't';}
	return $kmap;
}

/**
 * Configuration: display a table row for each possible action
 */
function __kbnav_config() {
	$kmap = __kbnav_config_action_keys();
	if (rss_plugins_is_submit()) {
		foreach($_POST as $k => $v) {
			if (preg_match('#key_([a-zA-Z_]+)#',$k,$matches)) {
				$action = $matches[1];
				if(isset($kmap[$action])) {
					$kmap[$action]['key'] = $v;
				}
			} elseif(preg_match('#mod_([a-zA-Z_]+)#',$k,$matches)) {
				$action = $matches[1];
				if(isset($kmap[$action])) {
					$kmap[$action]['modifier'] = $v;
				}
			} 
		}
		rss_plugins_add_option(KBNAVIGATIONPLUGIN_KMAP_CONFIG_OPTIONS, $kmap, 'array'); 
		return;
	} 
	$actions = __kbnav_config_actions();
?>
	<table class="frame">
		<tr>
			<th>Action</th>
			<th>Modifier</th>
			<th>Key</th>
		</tr>
<?php foreach($kmap as $action => $data) { ?>
	<tr>
		<td><?php echo $actions[$action] ?></td>
		<td><?php  __kbnav_config_modifier_combo($action,@$kmap[$action]['modifier']); ?></td>
		<td><?php  __kbnav_config_key_combo($action,@$kmap[$action]['key']); ?></td>
	</tr>
<?php } ?>
	</table>
<?php	
}

/**
 * Helper: displays a modifier combo
 */
function __kbnav_config_modifier_combo($name,$modifier = null) {
?>
	<select name="mod_<?php echo $name; ?>">
	<option value="">None</option>
	<option <?php echo $modifier == 'shift' ? 'selected="selected" ':''; ?> value="shift">Shift</option>
	</select>
<?php
}

/**
 * Helper: displays a list of keys, from a to z.
 * Fixme: up/down arrows?
 */
function __kbnav_config_key_combo($name,$key = null) {
?>
	<select name="key_<?php echo $name; ?>">
<?php for($c=ord('a');$c<=ord('z');$c++) { ?>
	<option <?php echo $key == chr($c) ? 'selected="selected" ':''; ?> value="<?php echo chr($c); ?>"><?php echo strtoupper(chr($c)); ?></option>
<?php } ?>
	</select>
<?php 
}

function __kbnav_js_register($js) {
    $js[] = getPath(). RSS_PLUGINS_DIR . "/keyboardnavigation.php?kbnjs";
    return $js;
}

function __kbnav_init_js($dummy) {
?>
    <script type="text/javascript">
    <!--
    document.onkeypress = function(event) {
		event = event || window.event;
		var code = event.which || event.keyCode;
    	var target = event.target || event.srcElement;
    	if (target.nodeName.toUpperCase() == 'INPUT') {
        	return true;
    	}

    	switch(String.fromCharCode(code)) {
			<?php
				foreach(__kbnav_config_action_keys() as $action => $data) {
					printf( "\tcase '%s': return %s();break;\n",  (@$data['modifier'] == 'shift' ? strtoupper($data['key']):strtolower($data['key'])), $action);
				}
			?>
        	default : return true;
    	}
    };
	__kbnav_init();
    // -->
    </script>
<?php
    return $dummy;
}

if (isset($_GET['kbnjs'])) {
    $etag = $__kbnav_version;
    if (array_key_exists('HTTP_IF_NONE_MATCH',$_SERVER)  && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
        header("HTTP/1.1 304 Not Modified");
        header("ETag: $etag");
        flush();
        exit();
    } else {
        header("ETag: $etag");
    }
    
?>
    var kbNavCurrent = -1;
    var kbNavItems = new Array();
    
    function __kbnav_init() {
        kbNavItems = new Array();
        var list = document.getElementsByTagName('li');
        for(var i=0;i<list.length;i++) {
            if (list[i].className && list[i].className.indexOf('item') == 0) {
                kbNavItems.push(list[i]);
            }
        }
        var span = document.createElement('span');
        span.style.position='absolute';
        span.style.color = 'black';
        span.style.display = 'none';
        span.style.fontWeight = 'bold';
        span.id = 'kbnavptr';
        span.innerHTML = '&gt;';
        document.body.appendChild(span);
    }

    function __kbnav_CurrentItemData() {
        if (kbNavCurrent == -1)kbNavCurrent=0;
        var item = kbNavItems[kbNavCurrent];
        if (item) {
			var ret = {};
			if (rx = new RegExp(".*es.([0-9]+),([0-9]+).*,([0-9]+).*","gm").exec(item.innerHTML)) {
				ret.id = rx[1];
				ret.state = rx[2];
				ret.cid = rx[3];
			} else {
				ret.id = item.id.replace(/[^0-9]/g,'');
			}
			var links = item.getElementsByTagName('a');
			for(var i=0;i<links.length;i++){
				if(links[i].className.indexOf('item_url') > -1) {
					ret.url = links[i].href;
					break;
				}
			}
			return ret;
        }

        return null;
    }

    function __kbnav_ToggleCollapse() {
        if ('function' == typeof(toggleItemByID)) {
            var r=__kbnav_CurrentItemData();
            if (null != r && r.id) {
                toggleItemByID(r.id);
            }
        }
		return false;
    }
    
    function __kbnav_ToggleSticky() {
        if ('function' == typeof(_stickyflag_sticky)) {
            var r=__kbnav_CurrentItemData();
            if (null != r && r.id && r.state) {
                _stickyflag_sticky(r.id, r.state);
            }
        }
        return false;
    }

    function __kbnav_ToggleFlag() {
        if ('function' == typeof(_stickyflag_flag)) {
            var r=__kbnav_CurrentItemData();
            if (null != r && r.id && r.state) {
                _stickyflag_flag(r.id, r.state);
            }
        }
        return false;
    }
    
    function __kbnav_scrollTo(i) {
        if (kbNavItems[kbNavCurrent+i]) {
            var y = kbNavItems[kbNavCurrent+i].offsetTop - 10;
            var span = document.getElementById('kbnavptr');
            
            if (y > 0) {
                window.scrollTo(0,y -5);
                span.style.display = 'inline';
                span.style.top = (10 + kbNavItems[kbNavCurrent+i].offsetTop) +'px';
                span.style.left = (-12 + kbNavItems[kbNavCurrent+i].offsetLeft) +'px';
                kbNavCurrent += i;
                if (kbNavCurrent < 0) {
                    kbNavCurrent = 0;
                } else if (kbNavCurrent > kbNavItems.length -1) {
                    kbNavCurrent = kbNavItems.length -1;
                }
            }
        }
        return false;
    }
    
    function __kbnav_Next() {
        return __kbnav_scrollTo(1);
    }
    
    function __kbnav_ScrollTop() {
        window.scrollTo(0,0);
        document.getElementById('kbnavptr').style.display = 'none';
        kbNavCurrent = -1;
        return false;
    }
    
    function __kbnav_NextMarkRead() {
        var r=__kbnav_CurrentItemData();

        if (null != r && (r.state & 1)) {
            if (! document.all) {
                c = unreadCnt(-1,r.cid);
         	} else {
            	c = 1;
         	}
            setItemHide(r.id, (c == 0));
            setState(r.id,r.state & 30);
            kbNavItems.splice(kbNavCurrent,1);
            __kbnav_scrollTo(0);
        } else if(null != r) {
            // non logged in users can't mark as read, so let this behave as a scroll.
            __kbnav_scrollTo(1);
        }
        return false;
    }
    function __kbnav_Prev() {
        return __kbnav_scrollTo(-1);
    }
    
	function __kbnav_OpenUrl() {
		var r = __kbnav_CurrentItemData();
		if (null != r && r.url) {
			document.location=r.url;
		}
		return false;
	}
	function __kbnav_OpenUrlNW() {
		var r = __kbnav_CurrentItemData();
		if (null != r && r.url) {
			window.open(r.url,'_blank');
		}
		return false;
	}
	function __kbnav_MarkAllRead() {
		var forms = document.getElementsByTagName('form');
		for(var i=0;i<forms.length;i++) {
			if (forms[i].className == 'markReadForm') {
				forms[i].submit();
				break;
			}
		}
		return false;
	}
	function __kbnav_EditTags() {
		var r = __kbnav_CurrentItemData();
		if (null != r && r.id && 'function' == typeof(_et)) {
			_et(r.id);
		}
	}
<?php
    flush();
    exit();
}

rss_set_hook('rss.plugins.javascript','__kbnav_js_register');
rss_set_hook('rss.plugins.bodyend','__kbnav_init_js');

?>