<br /><br /><h4><a href="<?php
  $url = rss_item_url();
  if (substr($url,0,4) == 'http') {
    echo $url;
  } else {
    echo rss_item_pl_url();
  }
?>"><?php echo rss_item_title(); 
?></a></h4>
<h5><?php echo rss_item_date(); ?><?php echo rss_item_author(); ?></h5>
<?php if (rss_item_has_enclosure()) { ?>
<h5><?php echo LBL_ENCLOSURE; ?>&nbsp;[<a href="<?php echo rss_item_enclosure(); ?>"><?php echo LBL_DOWNLOAD; ?></a>]</h5>
<?php } ?>
<?php	if (rss_item_display_tags() && count($GLOBALS['rss']->currentItem->tags)) { ?>
<h5><a href="<?php echo rss_item_tagslink(); ?>"><?php echo LBL_TAG_TAGS; ?></a>:&nbsp;<span><?php echo rss_item_tags(); ?></h5>
<?php } ?>
<br />
<div class="content">
<?php echo rss_item_content(); ?>

<?php if(!hidePrivate()) { ?>
	<br />
	<div class="mobileform">
	<input type="radio" value="mobile_read" name="<?= rss_item_id(); ?>" <?php
	  if( !$GLOBALS['rss'] -> currentItem -> isUnread && !$GLOBALS['rss'] -> currentItem -> isSticky ) {
		echo "checked";
	  }
	?> >read
	<input type="radio" name="<?= rss_item_id(); ?>" <?php
	  if( $GLOBALS['rss'] -> currentItem -> isUnread ) {
		echo "checked"; 
	  }
	?> >unread
	<input type="radio" value="mobile_sticky" name="<?= rss_item_id(); ?>" <?php
	  if( $GLOBALS['rss'] -> currentItem -> isSticky ) {
		echo "checked";
	  }
	?> >sticky
	</div>
<?php } ?>

</div>
