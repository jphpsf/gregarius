<?php if(rss_feed_do_title()) { ?>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->
<h3<?php echo rss_feed_class(); ?>>
<?php if(rss_feed_allow_collapsing()) { ?>
<a class="<?php echo (rss_feed_collapsed()?"expand":"collapse"); ?>" href="#" onclick="<?php echo rss_feed_expand_collapse_js(); ?>">
	<img src="<?php echo getExternalThemeFile('media/'. (rss_feed_collapsed()?"plus":"minus") . '.gif'); ?>" alt="" <?php echo rss_feed_id("cli"); ?>/>
</a>

<?php } elseif (rss_feed_do_favicon()) { ?>
	<img src="<?php echo rss_feed_favicon_url(); ?>" class="favicon" alt="" />
<?php } ?>
	<a href="<?php echo rss_feed_url(); ?>" <?php echo rss_feed_anchor_name(); ?>><?php echo rss_feed_title(); ?></a>
</h3>
<?php } ?>

<ul<?php echo rss_feed_id(); ?>>
<?php if (!rss_feed_collapsed()) {
	rss_feed_items();
} ?></ul>
