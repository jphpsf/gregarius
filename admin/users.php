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


function set_admin_pass($uname=null,$pass=null) {
	if ($uname && $pass) {
		rss_query( "update " . getTable('users') . " set uname='$uname', "
		 ."password='" . md5($pass) . "' where ulevel=99" );
		rss_redirect('admin/');
		exit();
	}
	
	admin_header();
		?>
	<script type="text/javascript">
	<!--
		function on_submit_password_match() {
			pass=document.getElementById('admin_pass').value;
			pass2=document.getElementById('admin_pass2').value;
			if(pass !== pass2){
				msg = '<?php echo LBL_ADMIN_PASS_NO_MATCH ?>';
				document.getElementById('admin_match_result').innerHTML = msg;
				document.getElementById('admin_pass').value = '';
				document.getElementById('admin_pass2').value = '';
				return false;
			}else{
				return true;
			}
		}	
	-->
	</script>
	
	<?php
  echo "\n<div id=\"channel_admin\" class=\"frame\">";
	echo "<h2></h2>\n"
		. LBL_ADMIN_MUST_SET_PASS;
	
	echo "<form action=\"".getPath()."admin/\" onsubmit=\"return on_submit_password_match();\" method=\"post\">\n"
	."<fieldset style=\"width:400px;\">"
	."<p><label style=\"display:block\" for=\"admin_uname\">".LBL_USERNAME.":</label>\n"
	."<input type=\"text\" id=\"admin_uname\" name=\"admin_uname\"></p>\n"
	."<p><label style=\"display:block\" for=\"admin_pass\">".LBL_PASSWORD.":</label>\n"
	."<input type=\"password\" id=\"admin_pass\" name=\"admin_pass\"></p>\n"
	."<p><label style=\"display:block\" for=\"admin_pass2\">".LBL_PASSWORD2.":</label>\n"
	."<input type=\"password\" id=\"admin_pass2\" name=\"admin_pass2\"></p>\n"
	."<p><input type=\"submit\" value=\"".LBL_ADMIN_OK."\"></p>\n"
	."<div style=\"display:inline;\" id=\"admin_match_result\"></div>\n"
	."</fieldset>\n"
	."</form>\n";
	
	echo "</div>\n";
	admin_footer();	
	exit();
}

function rss_login_form($uname=null,$pass=null) {
	
	admin_header();
	?>
	<script type="text/javascript">
	<!--
		
		function on_submit_login_form() {
			uname=document.getElementById('login_uname').value;
			pass=hex_md5(document.getElementById('login_pass').value);
			ajax_login(uname,pass,admin_login_hdlr);
			return false;
		}
		
		
		function admin_login_hdlr(data) {
			tokens=data.split('|');
			ulevel=tokens[0];
			uname=tokens[1];
			pass=tokens[2];
			if (ulevel > 0) {
				setCookie('<?php echo RSS_USER_COOKIE; ?>',uname+'|'+pass,'<?php echo getPath(); ?>');
				
			}
			msg = '';
			if (ulevel == <?php echo RSS_USER_LEVEL_NOLEVEL ?>) {
				msg = '<?php echo LBL_ADMIN_LOGIN_BAD_LOGIN ?>';
			} else if (ulevel > <?php echo RSS_USER_LEVEL_NOLEVEL ?> && ulevel < <?php echo RSS_USER_LEVEL_ADMIN ?>) {
				msg = '<?php echo LBL_ADMIN_LOGIN_NO_ADMIN ?>'.replace('%s',uname);
			} else if (ulevel >= <?php echo RSS_USER_LEVEL_ADMIN ?>) {
				document.location=document.location;
				return 0;
			}
			
			if (msg != '') {
				document.getElementById('admin_login_result').innerHTML = '<br>' + msg;
			}
		}
	-->
	</script>
	
	<?php
  	echo "\n<div id=\"channel_admin\" class=\"frame\">";
	echo "<h2></h2>\n";
	
	echo "<form id=\"admin_login\" style=\"text-align:center\" action=\"".getPath()."admin/\" onsubmit=\"return on_submit_login_form();\" method=\"post\">\n"
//	."<p>".		 LBL_ADMIN_LOGIN ."</p>\n"
	."<fieldset>"
	."<p><label style=\"display:block\" for=\"login_uname\">".LBL_USERNAME.":</label>\n"
	."<input type=\"text\" id=\"login_uname\" name=\"login_uname\" /></p>\n"
	."<p><label style=\"display:block\" for=\"login_pass\">".LBL_PASSWORD.":</label>\n"
	."<input type=\"password\" id=\"login_pass\" name=\"login_pass\" /></p>\n"
	."<p id=\"admin_login_submit\"><input type=\"submit\" value=\"".LBL_LOG_IN."\" /></p>\n"
	."<span style=\"display:inline;\" id=\"admin_login_result\"></span>\n"
	."</fieldset>\n"
	."</form>\n";
	
	echo "</div>\n";
	admin_footer();	
	exit();
}

?>
