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
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at gmail dot com
# Web page:	   http://gregarius.net/
#
###############################################################################
rss_require('cls/wrappers/user.php');
class RSSUser {
	
	var $_uid;
	var $_level;
	var $_uname;
	var $_hash;
	var $_validIPs;
	
	function RSSUser() {
		$this -> _uid = 0;
		$this -> _validIPs = array();
		$this -> _level = RSS_USER_LEVEL_NOLEVEL;
		$this -> _uname = '';
		$this -> _realName = '';		
		$this -> _hash = null;
				
		if (array_key_exists('logout',$_GET)) {
			$this -> logout();
			rss_redirect('');
		}
		
		$cuname = $chash = null;
    if (isset($_COOKIE[RSS_USER_COOKIE])) {
    	list($cuname,$chash) = explode('|',$_COOKIE[RSS_USER_COOKIE]);
    }  elseif(isset($_SESSION['mobile'])) {
    	list($cuname,$chash) = explode('|',$_SESSION['mobile']);
    } elseif (isset($_POST['username']) && isset($_POST['password'])) {
    	$_cuname = trim($_POST['username']);
    	$_chash = md5($_POST['password']);
    	if ($this -> login($_cuname,$_chash)) {
    		$cuname = $_cuname;
    		$chash = $_chash;
    	}
    }
		if ($cuname && $chash) {
				$sql = "select * from " . getTable('users') . " where uname='"
							 .rss_real_escape_string($cuname) ."' and password='"
							 .preg_replace('#[^a-zA-Z0-9]#','',md5($chash)) ."'";
				$rs = rss_query($sql);
				if (rss_num_rows($rs) == 1) {
						list($uid, $uname, $dummy, $level, $realName, $dummy2, $dummy3, $tmpUserIps) = rss_fetch_row($rs);
						$userIPs = explode(' ',$tmpUserIps);
						$subnet = preg_replace('#^([0-9]+\.[0-9]+\.[0-9]+)\.[0-9]+$#','\1',$_SERVER['REMOTE_ADDR']);
						if (array_search($subnet, $userIPs) !== FALSE) {
							$this -> _uid = $uid;
							$this -> _uname = $uname;
							$this -> _validIPs = $userIPs;
							$this -> _level = $level;
							$this -> _realName = $realName;
							$this -> _hash = $chash;
						}
				}
			}
	}
	
	
	function login($uname,$pass) {
    $sql ="select uname,ulevel,userips from " .getTable('users') . "where uname='"
          .rss_real_escape_string($uname)."' and password='".md5($pass)."'";
    list($uname,$ulevel,$userips) = rss_fetch_row(rss_query($sql));
    if ($ulevel == '') {
        $ulevel = RSS_USER_LEVEL_NOLEVEL;
        return false;
    } else {
        // "push" the user IP into the list of logged-in IP subnets
        $subnet = preg_replace('#^([0-9]+\.[0-9]+\.[0-9]+)\.[0-9]+$#','\1',$_SERVER['REMOTE_ADDR']);
        $this -> _validIPs = explode(' ',$userips);
        $this -> _validIPs[] = $subnet;
        $sql = "update " .getTable('users')
               . " set userips = '" . implode(' ', $this -> _validIPs ) ."'"
               ." where uname = '$uname' ";
        rss_query($sql);
        $this -> setUserCookie($uname,$pass);
        rss_invalidate_cache();
        return true;
    }
    return false;
	}

	function setUserCookie($user,$hash) {
   // if (getConfig('rss.config.autologout')) {
   //     $t = 0;
   // } else {
        $t =time()+COOKIE_LIFESPAN;
   // }
    setcookie(RSS_USER_COOKIE, $user .'|' . $hash , $t, getPath());
	}


	function logout() {
    if (array_key_exists(RSS_USER_COOKIE, $_COOKIE)) {
        $subnet = preg_replace('#^([0-9]+\.[0-9]+\.[0-9]+)\.[0-9]+$#','\1',$_SERVER['REMOTE_ADDR']);

        if (($idx = array_search($subnet, $this -> _validIPs)) !== FALSE) {
            $cnt = count($this -> _validIPs);
            unset($this -> _validIPs[$idx]);
            $uname = trim($this -> _uname);
            if ($uname && ($cnt > count($this -> _validIPs))) {
                $sql = "update " .getTable('users')
                       . " set userips = '" . implode(' ',$this -> _validIPs) ."'"
                       ." where uname = '$uname' ";
                rss_query($sql);
            }
        }

        // get rid of the cookie
        unset($_COOKIE[RSS_USER_COOKIE]);
        setcookie(RSS_USER_COOKIE, "", -1, getPath());
        rss_invalidate_cache();
    }
	}

	///// Getters //////
	function getUserName() {
		return $this -> _uname;
	}
	
	function getUserLevel() {
		return $this -> _level;
	}
}

$GLOBALS['rssuser'] = new RSSUser();
?>