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
/// Author: Marco Bonetti
/// Description: Navigate between items without using your mouse.
/// Version: 0.2

$__kbnav_version = 'kbnav_0.2';

/**
* Changelog:
* 0.1 - basic navigation: j/k: down/up, m: mark as read, s: toggle sticky
* 0.2 - f: toggle flag, c: lilina theme un/collapse
*/

/**
* Wishlist:
*  Mark all as read
*  Un/Collapse when not logged in
*/

function __kbnav_js_register($js) {
    $js[] = getPath(). RSS_PLUGINS_DIR . "/keyboardnavigation.php?kbnjs";
    return $js;
}

function __kbnav_init_js($dummy) {
?>
    <script type="text/javascript">
    <!--
    document.onkeypress = function(event) {return __kbnav_getKey(event)};
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
    
    
    function __kbnav_getKey(event) {
        if (!event) {
            event = window.event;
            var code = event.keyCode;
        } else {
            var code = event.which;
        }
        var target = event.target || event.srcElement;
        
        if (target.nodeName.toUpperCase() == 'INPUT') {
            return true;
        }
        
        switch(String.fromCharCode(code).toLowerCase()) {
            case 'j': return __kbnav_Next(); break;
            case 'k': return __kbnav_Prev(); break;
            case 's': return __kbnav_ToggleSticky(); break;
            case 'f': return __kbnav_ToggleFlag(); break;
            case 'm': return __kbnav_NextMarkRead(); break;
            case 'h': return __kbnav_ScrollTop(); break;
            case 'c': return __kbnav_ToggleCollapse(); break;
			case 'o': return (event.shiftKey ? __kbnav_OpenUrlNW() : __kbnav_OpenUrl()); break;
            default : return true;
        }
    }
    
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
			}
            if (href = item.getElementsByTagName('h4').item(0).getElementsByTagName('a').item(0).href) {
				ret.url = href;
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
            //kbNavItems[kbNavCurrent+i].style. // do something
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
	
<?php
    flush();
    exit();
}

rss_set_hook('rss.plugins.javascript','__kbnav_js_register');
rss_set_hook('rss.plugins.bodyend','__kbnav_init_js');

?>