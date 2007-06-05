<?php header('Content-Type: text/xml; charset='.strtoupper( rss_header_charset())); ?>
<?php echo "<?xml version=\"1.0\" encoding=\"".strtolower(rss_header_charset())."\"?>\n"; ?>
<feed xmlns="http://www.w3.org/2005/Atom">
        <id>urn:<?php echo str_replace('.', '-', $_SERVER['HTTP_HOST']).":feeds:atom"; ?></id>
	<title><?php echo rss_header_title() ?></title>
	<subtitle><?php echo rss_header_title() ?></subtitle>      
        <link rel="alternate" type="text/html" href="<?php echo guessTransportProto() . $_SERVER['HTTP_HOST'] . getPath() ?>" />
        <link rel="self" type="text/xml" href="<?php echo guessTransportProto() . $_SERVER['HTTP_HOST'] . getPath() ?>?media=atom"/>
        <updated><?php echo rss_date('c',getLastModif()); ?></updated>
	<?php rss_main_object(); ?>
</feed>
