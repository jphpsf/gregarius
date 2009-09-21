<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo rss_header_doclang(); ?>">
<head>
<?php rss_main_header(); ?>
</head>

<body id="doc3" class="yui-t3 rounded">

<div id="hd" class="frame">
    <?php echo rss_header_logininfo() ?>
    <h1 id="top"><?php echo rss_main_title() ?></h1>
    <?php echo rss_nav() ?>
    <?php echo rss_nav_afternav() ?>
</div>

<div id="bd">

	<div id="yui-main">
		<div class="yui-b">
			<?php rss_errors_render() ?>
			<?php rss_plugin_hook('rss.plugins.before.maindiv', rss_main_div_id()); ?>
			<div <?php echo rss_main_div_id(); ?> class="frame">
				<?php rss_main_object(); ?>
			</div>
		</div>
	</div>

	<div class="yui-b">
		<div id="channels" class="frame">
			<?php rss_main_feeds(); ?>
		</div>
	</div>

</div>

<div id="ft" class="frame">
	<?php rss_main_footer(); ?>
</div>

<?php foreach(rss_header_javascripts() as $script) { ?>
	<script type="text/javascript" src="<?php echo $script ?>"></script>
<?php } ?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript">
// <![CDATA[
$(document).ready(function(){
	$('body').click(function(e){
		var $target=$(e.target);
		if ($target.is('div.item-title')) {
			$target.next('div.item-body').toggle();
		} else if ($target.is('div.item-body')) {
			$target.hide();
		};
		$target=null;
	})
});
// ]]>
	</script>
<?php rss_plugin_hook('rss.plugins.bodyend', null); ?>
</body>
</html>
