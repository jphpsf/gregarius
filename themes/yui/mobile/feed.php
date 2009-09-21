<?php if(rss_feed_do_title()) { ?>
<h3>
<?php if (rss_feed_favicon_url()) { ?>
<img src="<?php echo rss_feed_favicon_url(); ?>" class="favicon" width="16" height="16" alt="" />
<?php } ?>
<a href="<?php echo rss_feed_url(); ?>"><?php echo rss_feed_title(); ?></a></h3>
<?php } 
rss_feed_items();
?>
