<?php
if (!isset($GLOBALS['__item__idx__'])) {
	$GLOBALS['__item__idx__']=0;
}
$cls = $GLOBALS['__item__idx__']++ % 2 ? 'e':'o';
?>
<div class="item <?php echo $cls; ?>">
<h4><a href="<?php
  $url = rss_item_url();
  if (substr($url,0,4) == 'http') {
    echo $url;
  } else {
    echo rss_item_pl_url();
  }
?>"><?php echo rss_item_title(); 
?></a></h4>
<h5><?php echo rss_item_date(); ?> <?php echo rss_item_author(); ?></h5>
<?php if (rss_item_has_enclosure()) { ?>
<h5><?php echo __('Enclosure:'); ?>&nbsp;[<a href="<?php echo rss_item_enclosure(); ?>"><?php echo __('download'); ?></a>]</h5>
<?php } ?>
<?php	if (rss_item_display_tags() && count($GLOBALS['rss']->currentItem->tags)) { ?>
<h5><a href="<?php echo rss_item_tagslink(); ?>"><?php echo __('Tags'); ?></a>:&nbsp;<span><?php echo rss_item_tags(); ?></h5>
<?php } ?>
<br />
<div class="content">
<?php echo rss_item_content(); ?>

<?php if(!hidePrivate()) { ?>
<div class="mobileform">
<label for="">State:</label>
<select name="<?= rss_item_id(); ?>">
	<option value="mobile_read" <?php 
		if( !$GLOBALS['rss'] -> currentItem -> isUnread && !$GLOBALS['rss'] -> currentItem -> isSticky ) { 
			echo "selected=\"selected\""; 
		} ?>>Read</option>
	<option value="mobile_unread" 	<?php
		  if( $GLOBALS['rss'] -> currentItem -> isUnread ) {
			echo "selected=\"selected\""; 
		  }
		?>>Unread</option>
	<option value="mobile_sticky" 	<?php
		  if( $GLOBALS['rss'] -> currentItem -> isSticky ) {
			echo "selected=\"selected\""; 
		  }
		?>>Sticky</option>
</select>
</div>
<?php } ?>
</div>
</div>