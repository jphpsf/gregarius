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
# FITNESS FOR A PARTICULAR PURPOSE.	 See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:	   mbonetti at users dot sourceforge dot net
# Web page:	   http://sourceforge.net/projects/gregarius
#
###############################################################################


class Profiler {

    var $__init_timer = 0;
    var $__prev_timer = 0;
    var $__data = array();

    function Profiler() {
        $this -> __init_timer = $this->getmicrotime();
        $this -> __prev_timer = $this -> __init_timer;
        $this-> _pf('start');
    }

    function getmicrotime() {
        list ($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    function _pf($comment) {
        $current_timer = $this->getmicrotime();
        $t = (1000 * ($current_timer - $this->__init_timer));
        $d = (1000 * ($current_timer - $this->__prev_timer));
        $this -> __prev_timer = $current_timer;
        
        $this->__data[] = array($t,$comment,$d);
    }
    
    function render() {
        $this-> _pf('end');
        echo "\n\n<!--\n";
        foreach ($this->__data as $e) {
            list($t,$c,$d) = $e;
            printf ("%03.2fms (+%03.2fms)\t%s\n",$t,$d,$c);
        }
        echo "\n-->\n\n";
    }
}
?>