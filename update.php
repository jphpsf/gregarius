<?
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003, 2004 Marco Bonetti
#
###############################################################################
# File: $Id$ $Name$
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
# E-mail:      mbonetti at users dot sourceforge dot net
# Web page:    http://sourceforge.net/projects/gregarius
#
###############################################################################


require_once('init.php');

if (! array_key_exists('visual',$_GET)) {
    update("");    

    header("Location: http://"
	   . $_SERVER['HTTP_HOST']
	   . dirname($_SERVER['PHP_SELF'])

	   );  
    
    exit();
} else {    
    
    $red = 'http://' .$_SERVER['HTTP_HOST']
      . dirname($_SERVER['PHP_SELF'])
	. "/index.php";
    
      
    rss_header("update",LOCATION_UPDATE,"location.replace('$red');");
    /*
    echo "<script type=\"text/javascript\">\n"
      ."<!-- \n"
      ."function delay(gap){\n"
      ."\tvar then,now; then=new Date().getTime();\n"
      ."\tnow=then;\n"
      ."\twhile((now-then)<gap)\n"
      ."\t{now=new Date().getTime();}\n"
      ."}\n"
      ."-->\n"
      ."</script>\n\n";
    */
    $res = rss_query( "select "
		      ." id, title "
		      ." from channels "
		      ." order by parent, title asc");
    
    echo "<div id=\"update\" class=\"frame\">\n";
    echo "<ul>\n";
    while(list($id,$name) = mysql_fetch_row($res)) {
	echo "\t<li>$name";
	$ret = update($id);
	
	if ($ret == "") {
	    // no error
	    echo "<span class=\"updateres ok\">ok</span>";
	} else {
	    echo "<span class=\"updateres ok\">ko ($ret)</span>";
	}
	echo "</li>\n";
    }
    echo "</ul>\n";
    echo "</div>\n\n";
    rss_footer();
}
?>
