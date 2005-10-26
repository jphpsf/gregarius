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
# E-mail:	   mbonetti at gmail dot com
# Web page:	   http://gregarius.net/
#
###############################################################################


class RDFItemList  {
	
	var $baselink;
	var $resource;
	var $items;
	
	function RDFItemList($items) {
		$this -> items = $items;
	}
	
	function render($title) {
		// trash the output, just in case
		@ ob_end_clean();
		ob_start();
		header('Content-Type: text/xml');
		
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n"
		."<rdf:RDF\n"."\txmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n"
		."\txmlns=\"http://purl.org/rss/1.0/\"\n"
		."\txmlns:taxo=\"http://purl.org/rss/1.0/modules/taxonomy/\"\n"
		."\txmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n"
		."\txmlns:syn=\"http://purl.org/rss/1.0/modules/syndication/\"\n"
		."\txmlns:admin=\"http://webns.net/mvcb/\"\n"
		.">\n\n";

		echo "<channel rdf:about=\"".$this->baselink.$this->resource."\">\n"
		."\t<title>".htmlentities($title, ENT_QUOTES, 'UTF-8')
		."</title>\n"."\t<link>".$this->baselink.$this->resource."</link>\n"
		."\t<description></description>\n"
		."</channel>\n\n";
		
		if ($this -> items) {
			foreach ($this -> items -> feeds as $feed) {
					foreach($feed->items as $item) {
						$xmlTitle = htmlentities($item->title, ENT_QUOTES, 'UTF-8');
						echo "<item rdf:about=\"".$item->url."\">\n"."\t<title>$xmlTitle</title>\n"."\t<link>".$item->url."</link>\n"
						// http://www.jschreiber.com/archives/2004/03/php_and_timesta_1.html
						."\t<dc:date>".rss_date('Y-m-d\TH:i:sO', $item->date)."</dc:date>\n"
						."\t<dc:subject>$xmlTitle</dc:subject>\n";
				
						if (count($item -> tags)) {
							echo "\t<taxo:topics>\n"."\t\t<rdf:Bag>\n";
							foreach ($item -> tags as $tag) {
								echo "\t\t\t<rdf:li rdf:resource=\"".$this->baselink.$tag."\" />\n";
							}
							echo "\t\t</rdf:Bag>\n".
							"\t</taxo:topics>\n";
				
						}
						echo "</item>\n\n";
					}
			}
		}
	echo "</rdf:RDF>\n";
	}
}
?>