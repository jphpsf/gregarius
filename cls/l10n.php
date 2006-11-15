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

rss_require('extlib/l10n/streams.php');
rss_require('extlib/l10n/gettext.php');
define('RSS_LOCALE_COOKIE','rss_preferred_locale');
class RSSl10n {
	
	var $l10n;
	var $cache;
	var $locale;
	var $isolang;
	
	function RSSl10n() {
		$this -> locale = preg_replace('#[^a-zA-Z_]#','',$this -> __detectUserLang());
		$this -> isolang=str_replace('_','-',$this -> locale);
		if (function_exists('version_compare') && version_compare("4.3.0",PHP_VERSION, "<=") && preg_match('#([a-z]{2})_?([A-Z]{2})?#',$this -> locale,$m)) {
			$locales=array(
				$m[0].'.UTF-8',
				$m[0].'.utf-8',
				$m[0],
				$m[1],				
				$m[1].'_'.strtoupper($m[1])
				//$m[2]
			);
			setlocale(LC_ALL, $locales);	
		} else {
			setlocale(LC_ALL, $this -> locale);
		}
	
		$path = GREGARIUS_HOME .'/intl/' . $this -> locale . '/LC_MESSAGES/messages.mo';
		$streamer = new FileReader($path);
		$this -> l10n = new gettext_reader($streamer);
		$this -> cache = array();
	}
	
	function translate($msg, $cnt = null) {
		if (isset($this -> cache[$msg . $cnt])) {
			return $this -> cache[$msg . $cnt];
		} 
		$ret = $this -> l10n -> translate($msg, $cnt);
		$this -> cache[$msg . $cnt] = $ret;
		return $ret;
	}
	
	function getLocale() {
		return $this -> locale;
	}
	function getISOLang() {
		return $this ->isolang;
	}
    /**
     * Detect users preferred language. Losely based on http://grep.be/data/accept-to-gettext.inc
     */
	function __detectUserLang() {
		if (defined('RSS_LANGUAGE_OVERRIDE')) {
			return constant('RSS_LANGUAGE_OVERRIDE');
		} elseif (isset($_REQUEST['lang']) && preg_match('#^[a-z]{2}_?([A-Z]{2})?$#',$_REQUEST['lang']) && ($_REQUEST['lang'] == 'en' || file_exists(GREGARIUS_HOME .'intl/'.$_REQUEST['lang']))) {
			$this -> __setLocaleCookie($_REQUEST['lang']);
			rss_invalidate_cache();
            return  $_REQUEST['lang'];
        } elseif (isset($_COOKIE[RSS_LOCALE_COOKIE])) {
            return trim($_COOKIE[RSS_LOCALE_COOKIE]);
       } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $alparts=@preg_split("/,/",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach($alparts as $part) {
                $part=trim($part);
                if(preg_match("/;/", $part)) {
                    $lang=@preg_split("/;/",$part);
                    $ll = $lang[0];
                } else {
                    $ll = $part;
                }

                if (preg_match('#^([a-z]{2})[\-_]?([a-z]{2})?$#i',$ll,$pm)) {
                	$ret =null;
                	if (isset($pm[2]) && file_exists(GREGARIUS_HOME .'intl/'.$pm[1] ."_".strtoupper($pm[2]))) {
                		// xx-yy -> xx_YY
               			$ret= $pm[1] ."_".strtoupper($pm[2]);
                	} elseif(file_exists(GREGARIUS_HOME .'intl/'.$pm[1] )) {
                		// xx  -> xx
                		$ret= $pm[1];
                	} elseif($pm[1] == 'en') {
                		// ugly: a better way would be to look up all the available locales
                		// and match against that list
                		$ret='en_US';
                	}
                	if ($ret) {
                		// remember the detected locale for a couple hours
						$this -> __setLocaleCookie($ret);
                		return $ret;
                	}
                }
                
            }
        }
        // If everything fails, return the user selected language
        return getConfig('rss.output.lang');
    }

	function __setLocaleCookie($value) {
		setcookie(RSS_LOCALE_COOKIE,$value,time()+3600*6,getPath());
	}
}
	


function __($msg, $cnt = null) {
	return $GLOBALS['rssl10n'] -> translate($msg, $cnt);
}
?>