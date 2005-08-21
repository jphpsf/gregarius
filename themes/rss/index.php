<?php header('Content-Type: text/xml; charset='.strtoupper( rss_header_charset())); ?>
<?php echo "<?xml version=\"1.0\" encoding=\"".strtolower(rss_header_charset())."\"?>\n"; ?>
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	>

<channel>
	<title><?= rss_header_title() ?></title>
	<link>http://<?= $_SERVER['HTTP_HOST'] . getPath() ?></link>
	<description><?= rss_header_title() ?></description>
	<generator>Gregarius <?= _VERSION_ ?></generator>
	<language>en</language>
	<?php rss_main_object(); ?>
</channel>
</rss>
