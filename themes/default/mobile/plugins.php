<?php

// Mobile theme relys on PHP sessions
ini_set('session.use_trans_sid',true);
session_start();

//var_dump($_SESSION['mobile']);

function __mobile_strip_images($i) {
    static $allowed;
    if (!$allowed) {
			$allowed = getConfig('rss.input.allowed');
			if(isset($allowed['img'])) {
					unset($allowed['img']);
			}
    }
    $i -> description = kses($i -> description ,$allowed);
    return $i;
}

function __mobile_truncate_content($i) {
	$ml = getProperty('rss.prop.theme.default.mobile','rss.output.maxlength');
	$i -> description =  html_substr($i -> description, $ml) ;
	return $i;
}

if (getProperty('rss.prop.theme.default.mobile','rss.content.strip.images')) {
	rss_set_hook('rss.plugins.items.beforerender','__mobile_strip_images');
} 

if (getProperty('rss.prop.theme.default.mobile','rss.output.maxlength')) {
		rss_set_hook('rss.plugins.items.beforerender','__mobile_truncate_content');
}


/*else {
setProperty('rss.prop.theme.default.mobile','rss.content.strip.images','theme',true) ;
}*/

function mobileLoginForm() {
?>
<html><head><title>Login</title></head>
<body>
<form method="post" action="<?php echo getPath(); ?>">
<p><input type="hidden" name="media" value="mobile" />
<label for="mobile_username">Username:</label>
<input type="text" id="mobile_username" name="mobile_username" value="" /></p>
<p><label for="mobile_password">Password:</label>
<input type="password" name="mobile_password" id="mobile_password"  value="" /></p>
<p><input type="submit" name="mobilelogin" value="Go" /></p>
</form>
</body>
</html>
<?php
flush();
exit();
}

if (isset($_REQUEST['mobilelogin'])) {
	if (isset($_POST['mobile_username']) && isset($_POST['mobile_password'])) {
		$un = rss_real_escape_string($_POST['mobile_username']);
		$pw = md5($_POST['mobile_password']);
		$dt = explode('|',__exp_login($un,$pw,null));
		if ($dt[0] == RSS_USER_LEVEL_NOLEVEL) {
			mobileLoginForm();
		} else {
			setUserCookie($dt[1], $dt[2]);
			$_SESSION['mobile']=($dt[1] ."|" .$dt[2]);
			echo "Hello, " . $dt[1] . ", <a href=\"".getPath()."?media=mobile\">continue...</a>";
			flush();
			exit();
		}
	} else {
		mobileLoginForm();
	}
}
?>
