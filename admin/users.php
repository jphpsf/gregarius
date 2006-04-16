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


function set_admin_pass($uname=null,$pass=null) {
	$sql = "select count(*) from " . getTable('users')
		." where password != '' and ulevel >=99";
	list($adminexists) = rss_fetch_row(rss_query($sql));
	if ($adminexists) {
		die('Oops. Admin already exists!');
	}
	
	if ($uname && $pass) {
		rss_query( "update " . getTable('users') . " set uname='$uname', "
		 ."password='" . md5(md5($pass)) . "' where ulevel=99" );
		rss_invalidate_cache(); 
		rss_redirect('admin/');
		exit();
	}
	
	admin_header();
		?>
	<script type="text/javascript">
	<!--
		function on_submit_password_match() {
			pass=document.getElementById('password').value;
			pass2=document.getElementById('password2').value;
			if(pass !== pass2){
				msg = '<?php echo LBL_ADMIN_PASS_NO_MATCH ?>';
				document.getElementById('admin_match_result').innerHTML = msg;
				document.getElementById('password').value = '';
				document.getElementById('password2').value = '';
				return false;
			}else{
				document.getElementById('password2').value = '';
				return loginHandler();
			}
		}	
	-->
	</script>
	
	<?php
  echo "\n<div id=\"channel_admin\" class=\"frame\">";
	echo "<h2></h2>\n"
		. LBL_ADMIN_MUST_SET_PASS;
	
	echo "<form action=\"".$_SERVER['PHP_SELF'] . "\" onsubmit=\"return on_submit_password_match();\" method=\"post\">\n"
	."<fieldset style=\"width:400px;\">"
	."<p><label style=\"display:block\" for=\"username\">".LBL_USERNAME.":</label>\n"
	."<input type=\"text\" id=\"username\" name=\"username\" /></p>\n"
	."<p><label style=\"display:block\" for=\"password\">".LBL_PASSWORD.":</label>\n"
	."<input type=\"password\" id=\"password\" name=\"password\" /></p>\n"
	."<p><label style=\"display:block\" for=\"password2\">".LBL_PASSWORD2.":</label>\n"
	."<input type=\"password\" id=\"password2\" name=\"password2\" /></p>\n"
	."<p><input type=\"submit\" value=\"".LBL_ADMIN_OK."\" /></p>\n"
	."<div style=\"display:inline;\" id=\"admin_match_result\"></div>\n"
	."</fieldset>\n"
	."</form>\n";
	
	echo "</div>\n";
	admin_footer();	
	exit();
}

function rss_login_form($uname=null,$pass=null) {
	
	admin_header();
  echo "\n<div id=\"channel_admin\" class=\"frame\">";
		
	echo "<form id=\"admin_login\" onsubmit=\"return loginHandler();\" 
		style=\"text-align:center\" action=\"".$_SERVER['PHP_SELF'] ."\" method=\"post\">\n"
	."<fieldset>"
	."<legend>" . LBL_ADMIN_LOGIN . "</legend>\n"
	."<p><label style=\"display:block\" for=\"username\">".LBL_USERNAME.":</label>\n"
	."<input type=\"text\" id=\"username\" name=\"username\" /></p>\n"
	."<p><label style=\"display:block\" for=\"password\">".LBL_PASSWORD.":</label>\n"
	."<input type=\"password\" id=\"password\" name=\"password\" /></p>\n"
	."<p id=\"admin_login_submit\"><input type=\"submit\" value=\"".LBL_LOG_IN."\" /></p>\n"
	."<span style=\"display:inline;\" id=\"admin_login_result\"></span>\n"
	."</fieldset>\n"
	."</form>\n";
	
	echo "</div>\n";
	admin_footer();	
	exit();
}

?>
