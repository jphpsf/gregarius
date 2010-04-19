<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo rss_header_doclang(); ?>">
<head>
<?php rss_main_header(); ?>
</head>
<?php flush(); // flush early ?>

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
			<?php rss_main_object(); ?>
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
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<?php foreach(rss_header_javascripts() as $script) { ?>
	<script type="text/javascript" src="<?php echo $script ?>"></script>
<?php } ?>
	<script type="text/javascript">
// <![CDATA[
(function(){
	$(document).ready(function(){
		var $spinner=$('<span class="spinner" style="float:right"><img src="<?php echo getExternalThemeFile('media/busy.gif'); ?>" /></span>');
		var loading=[];
		var $folder=$('#channels a.folderhide').parent('h3').next('ul');

		$('body').click(function(e){
			var $target=$(e.target);

			// external click
			if ($target.is('a') && $target.attr('target')==='_blank') {
				$target=null;
				return;
			}

			// folder expand
			if ($target.is('a.foldershow')) {
				e.preventDefault();
				if ($folder!==false) {
					$folder.hide();
					$folder.prev('h3').children('a.folderhide').removeClass('folderhide').addClass('foldershow');
				}
				$folder=$target.parent('h3').next('ul');
				$folder.show();
				$target.removeClass('foldershow').addClass('folderhide');
				return false;
			} else if  ($target.is('a.folderhide')) {
				e.preventDefault();
				if ($folder!==false) {
					$folder.hide();
				}
				$target.removeClass('folderhide').addClass('foldershow');
				return false;
			}

			// ajax item
			if ($target.not('div.item') && $target.not('a') && $target.parents('div.item').size()==1) {
				$target=$target.parents('div.item');
			}

			if ($target.is('div.item')) {
				$title=$target.children('div.item-title');
				$body=$target.children('div.item-body');

				if ($body.children().size()===0) {
					var item=$target.children('div.item-title').attr('id');
					if ($.inArray(item,loading)<0) {
						loading.push(item);
						$spinner.clone().prependTo($title);
					}
					$body.load("<?php echo getPath() ?>ajaxitem.php?item="+item,false,function(){
						if ($title.children('span.spinner').size()===1) {
							$title.children('span.spinner').remove();
						}
						$body.show();
						$body=null;
					});
				} else {
					$body.toggle();
					$body=null;
				}
			}
			$target=null;
		})
	});
})();
// ]]>
	</script>
<?php rss_plugin_hook('rss.plugins.bodyend', null); ?>
</body>
</html>
