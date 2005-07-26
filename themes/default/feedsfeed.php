
<li<?= rss_feeds_feed_li_class()?>>
<?php if($icon = rss_feeds_feed_icon()) { ?>
	<img src="<?= $icon ?>" class="favicon" alt="" />
<? } ?>
	<a class="<?= rss_feeds_feed_class() ?>" title="<?= rss_feeds_feed_title_entities() ?>" href="<?= rss_feeds_feed_link() ?>"><?= rss_feeds_feed_title() ?></a>
<?php if (($rdLbl = rss_feeds_feed_read_label()) != "") { ?>
	<?= $rdLbl ?>
<?php } ?>
<?= rss_feeds_feed_meta() ?>
</li>
