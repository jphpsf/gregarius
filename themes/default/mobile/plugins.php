<?php

// stores constants for overrides' defaults
require_once('mobileconstants.php');

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
	$ml = rss_theme_config_override_option('rss.output.maxlength',DEFAULT_MOBILE_OUTPUT_MAXLENGTH);
	$i -> description =  html_substr($i -> description, $ml) ;
	return $i;
}

if (rss_theme_config_override_option('rss.content.strip.images',DEFAULT_MOBILE_CONTENT_STRIP_IMAGES)) {
	rss_set_hook('rss.plugins.items.beforerender','__mobile_strip_images');
} 

if (rss_theme_config_override_option('rss.output.maxlength',DEFAULT_MOBILE_OUTPUT_MAXLENGTH) > 0) {
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
<label for="username">Username:</label>
<input type="text" id="username" name="username" value="" /></p>
<p><label for="password">Password:</label>
<input type="password" name="password" id="password"  value="" /></p>
<p><input type="submit" name="login" value="Go" /></p>
</form>
</body>
</html>
<?php
flush();
exit();
}

if (isset($_REQUEST['mobilelogin'])) {
	mobileLoginForm();
}

?>
