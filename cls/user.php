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

/**
 * The RSSUser class holds all the business logic to handle Gregarius users 
 */
class RSSUser {
    /** Userid */
    var $_uid;
    /** Userlevel */
    var $_level;
    /** Username */
    var $_uname;
    /** md5 hash of the user password */
    var $_hash;
    /** List of valid IP subnets this user is allowed to log in via a cookie */
    var $_validIPs;
    /** Mobile session */
    var $_mobileSession;

    /**
     * RSSUser constructor:
     * Handles: 
     * -logout
     * -cookie login (with validation)
     * -login
     */
    function RSSUser() {
    
        $this -> _uid = 0;
        $this -> _validIPs = array();
        $this -> _level = RSS_USER_LEVEL_NOLEVEL;
        $this -> _uname = '';
        $this -> _realName = '';
        $this -> _hash = null;
				$this -> _mobileSession = 
					isset($_POST['media']) && 'mobile' == $_POST['media'];
				
				if ('mobile' ==  getThemeMedia()) {
					ini_set('session.use_trans_sid',true);
    			session_start();
				}
				
        if (array_key_exists('logout',$_GET)) {
            $this -> logout();
            rss_redirect('');
        }
				
        $cuname = $chash = null;
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $_cuname = trim($_POST['username']);
            if ($this -> _mobileSession) {
            	$_chash = md5(md5($_POST['password'] . $_POST['username']));
            } else {
            	$_chash = md5($_POST['password']);
            }
            if ($this -> login($_cuname,$_chash)) {
                $cuname = $_cuname;
                $chash = $_chash;
            }
        }
        elseif (isset($_COOKIE[RSS_USER_COOKIE])) {
            list($cuname,$chash) = explode('|',$_COOKIE[RSS_USER_COOKIE]);
        }
        elseif(isset($_SESSION['mobile'])) {
            list($cuname,$chash) = explode('|',$_SESSION['mobile']);
            $this -> _mobileSession = true;
        }
        if ($cuname && $chash) {
            $sql = "select uid, uname, ulevel, realname, userips from " . getTable('users') . " where uname='"
                   .rss_real_escape_string($cuname) ."' and password='"
                   .preg_replace('#[^a-zA-Z0-9]#','',md5($chash)) ."'";
            $rs = rss_query($sql);
            if (rss_num_rows($rs) == 1) {
                list($uid, $uname, $level, $realName, $tmpUserIps) = rss_fetch_row($rs);
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

    /**
     * Logs in a user given the username and password.
     * If the user provided valid username and password,
     * he is given a cookie and his IP address subnet is added 
     * to the list of valid IPs this user is allowed to log in
     * via a cookie
     *
     * Returns true on a successful login, false otherwise.
     */
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
            if ($this -> _mobileSession) {
            	$this -> setUserSession($uname,$pass);
           	} else {
            	$this -> setUserCookie($uname,$pass);
            }
            rss_invalidate_cache();
            return true;
        }
        return false;
    }

    /**
     * Hands the user a yummy cookie.
     * The cookie holds the md5 hash of the user password
     */
    function setUserCookie($user,$hash) {
    		$rs = rss_query(
    			'select value_ from ' .getTable('config') . "where key_ = 'rss.config.autologout'", false,true);
				if (rss_is_sql_error(RSS_SQL_ERROR_NO_ERROR) && rss_num_rows($rs) > 0) {
					list($als) = rss_fetch_row($rs);
					$al = ($als == 'true');
				} else {
					$al = false;
				}
        $t = $al ? 0: time()+COOKIE_LIFESPAN;
        setcookie(RSS_USER_COOKIE, $user .'|' . $hash , $t, getPath());
    }

		function setUserSession($user,$hash) {
			$_SESSION['mobile'] = $user . "|" . $hash;
		}
		
    /**
     * Logs the user out.
     * - deletes the cookie
     * - removes the user's IP subnet from the list of valid subnets this
     *   user is allowed to log in with a cookie.
     */
    function logout() {
        if (array_key_exists(RSS_USER_COOKIE, $_COOKIE) || isset($_SESSION['mobile'])) {
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
            if (isset($_SESSION['mobile'])) {
            	unset($_SESSION['mobile']);
            }

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

// Create the unique instance. Todo: make the RSSUser a singleton.
$GLOBALS['rssuser'] = new RSSUser();
?>
