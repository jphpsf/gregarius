<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<?php rss_main_header(); ?>
</head>

<body<?php echo rss_header_onLoadAction() ?>>

<script type="text/javascript" src="<?php echo getThemePath();?>/js/lilina_header.js"></script>

<div id="nav" class="frame">
    <?php echo rss_header_logininfo() ?>
    <a class="hidden" href="#feedcontent">skip to content</a>
    <h1 id="top"><?php echo rss_main_title() ?></h1>
    <?php echo rss_nav() ?>
    <?php echo rss_nav_afternav() ?>
</div>

<div id="ctnr">

<ul id="sidemenu">
	<?php rss_main_sidemenu("li") ?>
</ul>

<div id="channels" class="frame">
	<?php rss_main_feeds(); ?>
</div>

<?php rss_errors_render() ?>
<?php rss_plugin_hook('rss.plugins.before.maindiv', rss_main_div_id()); ?>
<div <?php echo rss_main_div_id(); ?> class="frame">
	<span id="collapser">
		<a style="font-weight:bold; font-size:150%; border: 1px solid #ddd; background-color: #f5f5f5;" 
			href="#" onclick="_lilina_channels_collapse(this)" accesskey="f">&nbsp;&laquo;&nbsp;</a>
	</span>
	<a href="#" onclick="_lilina_toggleAlldivs();return false;" accesskey="c"><?php echo LILINA_EXPAND_COLLAPSE; ?></a>
 	<?php rss_main_object(); ?>
</div>

<div id="footer" class="frame">
	<?php rss_main_footer(); ?>
</div>

</div>
<script type="text/javascript">
<!--
<?php
echo "var _lilinaCookiePath = '" . getPath() . "';\n";
?>
-->
</script>
<script type="text/javascript" src="<?php echo getThemePath();?>/js/lilina_footer.js"></script>	

<?php rss_plugin_hook('rss.plugins.bodyend', null); ?>
</body>
</html>
