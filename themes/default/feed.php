<?php if(rss_feed_do_title()) { ?>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  -->
<h3<?= rss_feed_class(); ?>>
<?php if(rss_feed_allow_collapsing()) { ?>
<a class="<?= (rss_feed_collapsed()?"expand":"collapse") ?>" href="<?= rss_feed_expand_collapse_link() ?>">
	<img src="<?= rss_theme_path() ?>/media/<?= (rss_feed_collapsed()?"plus":"minus") ?>.gif" alt="" />
</a>

<?php } elseif (rss_feed_do_favicon()) { ?>
	<img src="<?= rss_feed_favicon_url() ?>" class="favicon" alt="" />
<?php } ?>
	<a href="<?= rss_feed_url() ?>" name="<?= rss_feed_escaped_title() ?>"><?= rss_feed_title() ?></a>
</h3>
<?php } ?>

<?php if (!rss_feed_collapsed()) { ?>
<ul<?= rss_feed_id() ?>>
<?php rss_feed_items() ?>
</ul>
<?php } ?>
