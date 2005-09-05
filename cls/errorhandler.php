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

class ErrorHandler {

	var $errors = null;
	
	function ErrorHandler() {
		$this -> errors = array(
			RSS_ERROR_ERROR => array(),
			RSS_ERROR_WARNING => array(),
			RSS_ERROR_NOTICE => array()
		);
	}
	
	function getErrorCount() {
		return 
			(count($this -> errors[RSS_ERROR_NOTICE]) +
			count($this -> errors[RSS_ERROR_WARNING]) + 
			count($this -> errors[RSS_ERROR_ERROR]));
	}
	
	function appendError($error, $severity = RSS_ERROR_ERROR) {
		$this -> errors[$severity][] = $error;
	}
		
	function render() {
		rss_require(RSS::getTemplateFile("error.php"));
  }
	

}

?>