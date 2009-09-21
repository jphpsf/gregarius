	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo rss_header_charset() ?>" />
	<title><?php echo rss_header_title() ?></title>
	<meta name="robots" content="<?php echo rss_header_robotmeta(); ?>" />
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.0/build/reset-fonts-grids/reset-fonts-grids.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo getExternalThemeFile('css/style.css'); ?>" />
<?php
	foreach(rss_header_links() as $link) {
		list($rel,$title,$href) = $link; ?>
	<link rel="<?php echo $rel ?>" title="<?php echo $title ?>" href="<?php echo $href ?>" />
<?php } ?>


