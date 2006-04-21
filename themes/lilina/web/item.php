<?php  
	global $lastDate;  
	$thisDate = rss_locale_date("%B %d, %Y", $GLOBALS['rss']->currentItem->date);  
	if (!$lastDate || $lastDate != $thisDate) {   
		$thisYear = rss_locale_date("%Y", $GLOBALS['rss']->currentItem->date);  
		$thisMon = rss_locale_date("%m", $GLOBALS['rss']->currentItem->date); 
		$thisDay = rss_locale_date("%d", $GLOBALS['rss']->currentItem->date); 
		if(getConfig('rss.output.usemodrewrite')) {
			$dateURL =  getPath() . "$thisYear/$thisMon/$thisDay/";
		}else{
			$dateURL = getPath() . "feed.php?y=$thisYear&m=$thisMon&d=$thisDay";
		}
		$lastDate=$thisDate; 
?>
<li>
	<div class="dateheader"><a href="<?php echo $dateURL; ?>"><?php echo $thisDate; ?></a></div>
</li>
<?php
	}
?>
<?php
if ( (isset($_REQUEST["iid"]) && $_REQUEST["iid"]) ) {
?>
<li class="<?php echo rss_item_css_class(); ?>">
	<?php if(rss_item_permalink()) { ?>
	<a class="plink" title="<?php echo rss_item_pl_title(); ?>" href="<?php echo rss_item_pl_url(); ?>">
		<img src="<?php echo rss_theme_path(); ?>/media/mark_on.gif" alt="<?php echo rss_item_pl_title(); ?>" />
	</a>
	<?php } ?>
	<?php if(!hidePrivate()) { ?>
	<a id="sa<?php echo rss_item_id(); ?>" href="#" onclick="_es(<?php echo rss_item_id(); ?>,<?php echo rss_item_flags(); ?>); return false;">
		<img src="<?php echo rss_theme_path(); ?>/media/edit.gif" alt="edit" />
	</a>
	<?php } ?>
	<?php rss_plugin_hook("rss.plugins.items.beforetitle", rss_item_id()); ?>
	<h4><a href="<?php echo rss_item_url(); ?>"><?php echo rss_item_title(); ?></a></h4>
	<div id="sad<?php echo rss_item_id(); ?>" style="display:none"></div>
	<h5><?php echo rss_item_date(); ?><?php echo rss_item_author(); ?></h5>
	<?php if (rss_item_do_rating()) { ?>
	<div class="rating">
		<h5><?php echo LBL_RATING; ?></h5>
		<?php echo rss_item_rating(); ?>
		<?php rss_item_rating(); ?>
	</div>
	<?php } ?>
	<?php if (_VERSION_ > "0.5.2" && rss_item_has_enclosure()) { ?>
      <h5><?php echo LBL_ENCLOSURE; ?>&nbsp;[<a href="<?php echo rss_item_enclosure(); ?>"><?php echo LBL_DOWNLOAD; ?></a><?php rss_plugin_hook("rss.plugins.items.enclosure", null); ?>]</h5>
	<?php } ?>
	<?php	if (rss_item_display_tags()) { ?>
	<h5>
		<a href="<?php echo rss_item_tagslink(); ?>"><?php echo LBL_TAG_TAGS; ?></a>:&nbsp;
		<span id="t<?php echo rss_item_id(); ?>"><?php echo rss_item_tags(); ?></span>&nbsp;
		<?php if (rss_item_can_edit_tags()) { ?>
		[<span id="ta<?php echo rss_item_id(); ?>"><a href="#" onclick="_et(<?php echo rss_item_id(); ?>); return false;"><?php echo LBL_TAG_EDIT; ?></a></span>]
		<?php } ?>
	</h5>
	<?php } ?>
	<div class="content" id="c<?php echo rss_item_id(); ?>">
		<?php echo rss_item_content(); ?>
	</div>
</li>
<?
}
else {
	//list mode : we only show titles
?>
<li class="<?php echo rss_item_css_class(); ?>">
	<?php if(rss_item_permalink()) { ?>
	<a class="plink" title="<?php echo rss_item_pl_title(); ?>" href="<?php echo rss_item_pl_url(); ?>">
		<img src="<?php echo rss_theme_path(); ?>/media/mark_off.gif" alt="<?php echo rss_item_pl_title(); ?>" />
	</a>
	<?php } ?>
	<?php if(!hidePrivate()) { ?>
	<a id="sa<?php echo rss_item_id(); ?>" href="#" onclick="_es(<?php echo rss_item_id(); ?>,<?php echo rss_item_flags(); ?>); return false;">
		<img src="<?php echo rss_theme_path(); ?>/media/edit.gif" alt="edit" />
	</a>
	<?php } ?>
	<?php rss_plugin_hook("rss.plugins.items.beforetitle", rss_item_id()); ?>
	<?php if (getConfig('rss.output.showfavicons')) { 
		$tmpIcon = rss_feed_favicon_url();	
		if(!$tmpIcon) {
			$tmpIcon = rss_theme_path() ."/media/noicon.png";
		}
	?>
        <img src="<?php echo $tmpIcon; ?>" class="favicon" alt="" />
	<?php } ?>
	<?php echo rss_item_date_with_format("G:i"); ?>
	<h4><a class="tlink" href="#" onclick="toggleItemByID(<?php echo rss_item_id();?>);return false;">
	<?php echo rss_item_title(); ?></a></h4> 
	<a href="<?php echo rss_item_url(); ?>">&nbsp;&raquo;&nbsp;&lrm;
	<?php echo rss_feed_title();?></a>
	<div id="sad<?php echo rss_item_id(); ?>" style="display:none"></div>
	<div class="content" id="c<?php echo rss_item_id(); ?>" style="display:none">
		<?php echo rss_item_content(); ?>
	</div>
</li>
<?
}
?>
