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

class RSSl10n {
	
	var $l10n;
	var $cache;
	
	function RSSl10n($locale) {
		$path = GREGARIUS_HOME .'/intl/' . $locale . '/LC_MESSAGES/messages.mo';
		$streamer = new FileReader($path);
		$this -> l10n = new gettext_reader($streamer);
		$this -> cache = array();
	}
	
	function translate($msg) {
		if (isset($this -> cache[$msg])) {
			return $this -> cache[$msg];
		} 
		$ret = $this -> l10n -> translate($msg);
		$this -> cache[$msg] = $ret;
		return $ret;
		
	}
}

function __($msg) {
	return $GLOBALS['rssl10n'] -> translate($msg);
}
?>