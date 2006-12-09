<?php
/*****************************************************************
 * File name: browser.php
 * Author: Gary White & Damien Raude-Morvan
 * Last modified: December 10, 2006
 *
 **************************************************************
 * Copyright (C) 2003  Gary White
 * Copyright (C) 2006  Damien Raude-Morvan <drazzib@drazzib.com>
 *
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.
 *
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details at:
 http://www.gnu.org/copyleft/gpl.html
 */

/**
 **************************************************************
 *
 Browser class
 *
 Identifies the user's Operating system, browser and version
 by parsing the HTTP_USER_AGENT string sent to the server
 *
 Typical Usage:
 *
 require_once($_SERVER['DOCUMENT_ROOT'].'/include/browser.php');
 $br = new Browser;
 echo "$br->Platform, $br->Name version $br->Version";
 *
 For operating systems, it will correctly identify:
 Microsoft Windows
 MacIntosh
 Linux
 *
 Anything not determined to be one of the above is considered to by Unix
 because most Unix based browsers seem to not report the operating system.
 The only known problem here is that, if a HTTP_USER_AGENT string does not
 contain the operating system, it will be identified as Unix. For unknown
 browsers, this may not be correct.
 *
 For browsers, it should correctly identify all versions of:
 Amaya
 Galeon
 iCab
 Internet Explorer
 For AOL versions it will identify as Internet Explorer (AOL) and the version
 will be the AOL version instead of the IE version.
 Konqueror
 Lynx
 Mozilla
 Netscape Navigator/Communicator
 OmniWeb
 Opera
 Pocket Internet Explorer for handhelds
 Safari
 WebTV
 *****************************************************************/
class browser {

    var $Name = "Unknown";
    var $Version = "Unknown";
    var $Platform = "Unknown";
    var $UserAgent = "Not reported";
    var $AOL = false;
    var $isMoz = false;
    var $isOpera = false;
    var $isSafari = false;
    var $isKonqueror = false;
    var $isIE = false;

    function browser() {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $agent = null;
        }
        // initialize properties
        $bd['platform'] = "Unknown";
        $bd['browser'] = "Unknown";
        $bd['version'] = "Unknown";
        $this->UserAgent = $agent;

        // find operating system
        if (eregi("win", $agent))
            $bd['platform'] = "Windows";
        elseif (eregi("mac", $agent)) $bd['platform'] = "MacIntosh";
        elseif (eregi("linux", $agent)) $bd['platform'] = "Linux";
        elseif (eregi("OS/2", $agent)) $bd['platform'] = "OS/2";
        elseif (eregi("BeOS", $agent)) $bd['platform'] = "BeOS";

        // test for Opera
        if (eregi("opera", $agent)) {
            $val = stristr($agent, "opera");
            if (eregi("/", $val)) {
                $val = explode("/", $val);
                $bd['browser'] = $val[0];
                $val = explode(" ", $val[1]);
                $bd['version'] = $val[0];
            } else {
                $val = explode(" ", stristr($val, "opera"));
                $bd['browser'] = $val[0];
                $bd['version'] = $val[1];
            }

            $this->isOpera = true;

            // test for WebTV
        }
        elseif (eregi("webtv", $agent)) {
            $val = explode("/", stristr($agent, "webtv"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            // test for MS Internet Explorer version 1
        }
        elseif (eregi("microsoft internet explorer", $agent)) {
            $bd['browser'] = "MSIE";
            $bd['version'] = "1.0";
            $var = stristr($agent, "/");
            if (ereg("308|425|426|474|0b1", $var)) {
                $bd['version'] = "1.5";
            }
            $this -> isIE = true;

            // test for NetPositive
        }
        elseif (eregi("NetPositive", $agent)) {
            $val = explode("/", stristr($agent, "NetPositive"));
            $bd['platform'] = "BeOS";
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            // test for MS Internet Explorer
        }
        elseif (eregi("msie", $agent) && !eregi("opera", $agent)) {
            $val = explode(" ", stristr($agent, "msie"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            $this -> isIE = true;

            // test for MS Pocket Internet Explorer
        }
        elseif (eregi("mspie", $agent) || eregi('pocket', $agent)) {
            $val = explode(" ", stristr($agent, "mspie"));
            $bd['browser'] = "MSPIE";
            $bd['platform'] = "WindowsCE";
            if (eregi("mspie", $agent))
                $bd['version'] = $val[1];
            else {
                $val = explode("/", $agent);
                $bd['version'] = $val[1];
            }

            // test for Galeon
        }
        elseif (eregi("galeon", $agent)) {
            $val = explode(" ", stristr($agent, "galeon"));
            $val = explode("/", $val[0]);
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            $this->isMoz = true;

            // test for Konqueror
        }
        elseif (eregi("Konqueror", $agent)) {
            $val = explode(" ", stristr($agent, "Konqueror"));
            $val = explode("/", $val[0]);
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            $this->isKonqueror = true;

            // test for iCab
        }
        elseif (eregi("icab", $agent)) {
            $val = explode(" ", stristr($agent, "icab"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            // test for OmniWeb
        }
        elseif (eregi("omniweb", $agent)) {
            $val = explode("/", stristr($agent, "omniweb"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            // test for Phoenix
        }
        elseif (eregi("Phoenix", $agent)) {
            $bd['browser'] = "Phoenix";
            $val = explode("/", stristr($agent, "Phoenix/"));
            $bd['version'] = $val[1];

            $this->isMoz = true;

            // test for Firebird
        }
        elseif (eregi("firebird", $agent)) {
            $bd['browser'] = "Firebird";
            $val = stristr($agent, "Firebird");
            $val = explode("/", $val);
            $bd['version'] = $val[1];
            $this->isMoz = true;

            // test for Firefox
        }
        elseif (eregi("Firefox", $agent)) {
            $bd['browser'] = "Firefox";
            $val = stristr($agent, "Firefox");
            $val = explode("/", $val);
            $bd['version'] = $val[1];
            $this->isMoz = true;

            // test for Mozilla Alpha/Beta Versions
        }
        elseif (eregi("mozilla", $agent) && eregi("rv:[0-9].[0-9][a-b]", $agent) && !eregi("netscape", $agent)) {
            $bd['browser'] = "Mozilla";
            $val = explode(" ", stristr($agent, "rv:"));
            eregi("rv:[0-9].[0-9][a-b]", $agent, $val);
            $bd['version'] = str_replace("rv:", "", $val[0]);
            $this->isMoz = true;

            // test for Mozilla Stable Versions
        }
        elseif (eregi("mozilla", $agent) && eregi("rv:[0-9]\.[0-9]", $agent) && !eregi("netscape", $agent)) {
            $bd['browser'] = "Mozilla";
            $val = explode(" ", stristr($agent, "rv:"));
            eregi("rv:[0-9]\.[0-9]\.[0-9]", $agent, $val);
            $bd['version'] = str_replace("rv:", "", $val[0]);
            $this->isMoz = true;

            // test for Lynx & Amaya
        }
        elseif (eregi("libwww", $agent)) {
            if (eregi("amaya", $agent)) {
                $val = explode("/", stristr($agent, "amaya"));
                $bd['browser'] = "Amaya";
                $val = explode(" ", $val[1]);
                $bd['version'] = $val[0];
            } else {
                $val = explode("/", $agent);
                $bd['browser'] = "Lynx";
                $bd['version'] = $val[1];
            }

            // test for Safari
        }
        elseif (eregi("AppleWebKit", $agent)) {
            $bd['browser'] = "Safari";
            $val = explode("/", $agent);
            $bd['version'] = $val[3];
            $this -> isSafari = true;

            // remaining two tests are for Netscape
        }
        elseif (eregi("netscape", $agent)) {
            $val = explode(" ", stristr($agent, "netscape"));
            $val = explode("/", $val[0]);
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            if ($bd['version'] > 6) {
                $this->isMoz = true;
            }
        }
        elseif (eregi("mozilla", $agent) && !eregi("rv:[0-9]\.[0-9]\.[0-9]", $agent)) {
            $val = explode(" ", stristr($agent, "mozilla"));
            $val = explode("/", $val[0]);
            $bd['browser'] = "Netscape";
            $bd['version'] = $val[1];

            if ($bd['version'] > 6) {
                $this->isMoz = true;
            }
        }

        // clean up extraneous garbage that may be in the name
        $bd['browser'] = ereg_replace("[^a-z,A-Z]", "", $bd['browser']);
        // clean up extraneous garbage that may be in the version
        $bd['version'] = ereg_replace("[^0-9,.,a-z,A-Z]", "", $bd['version']);

        // check for AOL
        if (eregi("AOL", $agent)) {
            $var = stristr($agent, "AOL");
            $var = explode(" ", $var);
            $bd['aol'] = ereg_replace("[^0-9,.,a-z,A-Z]", "", $var[1]);
        } else {
            $bd['aol'] = false;
        }

        // finally assign our properties
        $this->Name = $bd['browser'];
        $this->Version = $bd['version'];
        $this->Platform = $bd['platform'];
        $this->AOL = $bd['aol'];
    }

    function isGecko() {
        return $this->isMoz;
    }

    function isOpera() {
        return $this->isOpera;
    }

    /** Support of multipart/x-mixed-replace complete and stable :
      * o in Safari 2.0.2 (http://webkit.org/blog/?p=32)
      *     (http://developer.apple.com/internet/safari/uamatrix.html)
      * o in Konqueror at least 3.4.3
      *     (http://websvn.kde.org/trunk/KDE/kdelibs/khtml/kmultipart/kmultipart.cpp/)
      */
    function supportsServerPush() {
        return  ($this->isMoz
             ||  $this->isOpera
             || ($this->isSafari && $this->compareBrowserVersion("416", $this->Version))
             || ($this->isKonqueror && $this->compareBrowserVersion("3.4.3", $this->Version))
                );
    }

    /** Support of XMLHTTPRequest complete and stable :
      * o in Konqueror at least 3.4 (since 2004 -
      *     http://websvn.kde.org/trunk/KDE/kdelibs/khtml/ecma/xmlhttprequest.cpp/)
      */
    function supportsAJAX() {
        return  ($this->isMoz
             ||  $this->isOpera
             ||  $this->isSafari
             || ($this->isIE && $this->compareBrowserVersion("5", $this->Version))
             || ($this->isKonqueror && $this->compareBrowserVersion("3.4", $this->Version))
                );
    }

    /**
     * Compare two version strings of browser.
     * @param $requiredVersion minimal version number required for feature
     * @param $browserVersion current detected browser version
     * @return true if $browserVersion is greater than $requiredVersion
     */
    function compareBrowserVersion($requiredVersion, $browserVersion) {
        // Standardise versions
        $requiredVersion = preg_replace('/([^0-9\.]+)/', '.$1.', $requiredVersion);
        $requiredVersion = trim($requiredVersion);
        $v1 = explode('.', $requiredVersion);

        $browserVersion = preg_replace('/([^0-9\.]+)/', '.$1.', $browserVersion);
        $browserVersion = trim($browserVersion);
        $v2 = explode('.', $browserVersion);

        $compare = 0;
        for ($i = 0, $x = min(count($v1), count($v2)); $i < $x; $i++) {
            if ($v1[$i] == $v2[$i]) {
                continue;
            }

            $i1 = $v1[$i];
            $i2 = $v2[$i];

            if (is_numeric($i1) && is_numeric($i2)) {
                $compare = ($i1 < $i2) ? -1 : 1;
            }
        }

        return (bool) ($compare <= 0);
    }
}
?>