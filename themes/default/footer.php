	<span><a href="#top">TOP</a></span>
	<?php echo rss_plugin_hook("rss.plugins.footer.span",null); ?>
	<span>
		<a href="http://gregarius.net/">Gregarius</a> <?php echo _VERSION_; ?><?php echo rss_svn_rev(); ?>
		<?php echo LBL_FTR_POWERED_BY ?> <a href="http://php.net">PHP</a>, 
		<a href="http://magpierss.sourceforge.net/">MagpieRSS</a>, 
		<a href="http://sourceforge.net/projects/kses">kses</a>,
		<a href="http://www.modernmethod.com/sajax/">SAJAX</a></span>
	<span>
		Tentatively valid <a title="Tentatively valid XHTML: the layout validates, but the actual content coming from the feeds I can't do very much."  href="http://validator.w3.org/check/referer">XHTML1.0</a>, 
		<a href="http://jigsaw.w3.org/css-validator/check/referer">CSS2.0</a>
	</span>
	<span>
		Last update: <?php echo rss_footer_last_modif(); ?>
	</span>
