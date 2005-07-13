	<meta http-equiv="Content-Type" content="text/html; charset=<?= rss_header_charset() ?>" />
	<title><?= rss_header_title() ?></title>
	<meta name="robots" content="<?= rss_header_robotmeta(); ?>" />
	<link rel="stylesheet" type="text/css" href="<?= rss_theme_path() ?>/css/layout.css" />
	<link rel="stylesheet" type="text/css" href="<?= rss_theme_path() ?>/css/look.css" />
	<link rel="stylesheet" type="text/css" href="<?= getPath() ?>css/print.css" media="print" />
<?= rss_plugin_hook('rss.plugins.stylesheets', null); ?>
<?php if(rss_header_autorefreshtime() > 0) { ?>
	<meta http-equiv="refresh"  content="<?= rss_header_autorefreshtime() ?>;<?= rss_header_autorefreshurl() ?>" />
<?php } ?>
<?php 
	foreach(rss_header_links() as $link) { 
		list($rel,$title,$href) = $link; ?>
	<link rel="<?= $rel ?>" title="<?= $title ?>" href="<?= $href ?>" />
<?php } ?>
<?php foreach(rss_header_javascripts() as $script) { ?>
	<script type="text/javascript" src="<?= $script ?>"></script>	
<?php } ?> 

<?php rss_plugin_hook('rss.plugins.javascript', null); ?>

