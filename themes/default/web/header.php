	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo rss_header_charset() ?>" />
	<title><?php echo rss_header_title() ?></title>
	<meta name="robots" content="<?php echo rss_header_robotmeta(); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo getExternalThemeFile('css/layout.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo getExternalThemeFile('css/look.css'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo getPath() ?>css/print.css" media="print" />
	<?php echo rss_scheme_stylesheets(); ?>
<?php echo rss_plugin_hook('rss.plugins.stylesheets', null); ?>
<?php if(rss_header_autorefreshtime() > 0 && !getConfig('rss.config.restrictrefresh')) { ?>
	<meta http-equiv="refresh"  content="<?php echo rss_header_autorefreshtime(); ?>;<?php echo rss_header_autorefreshurl() ?>" />
<?php } ?>
<?php 
	foreach(rss_header_links() as $link) { 
		list($rel,$title,$href) = $link; ?>
	<link rel="<?php echo $rel ?>" title="<?php echo $title ?>" href="<?php echo $href ?>" />
<?php } ?>
<?php foreach(rss_header_javascripts() as $script) { ?>
	<script type="text/javascript" src="<?php echo $script ?>"></script>	
<?php } ?> 


