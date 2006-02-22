<?php if(rss_feed_do_title()) { ?>
<h3><a href="<?php echo rss_feed_url(); ?>"><?php echo rss_feed_title(); ?></a></h3>
<?php } 
rss_feed_items();
?>
