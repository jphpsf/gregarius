<li class="<?= rss_item_css_class(); ?>">
	<?php if(rss_item_permalink()) { ?>
	<a class="plink" title="<?= rss_item_pl_title(); ?>" href="<?= rss_item_pl_url(); ?>">
		<img src="<?= rss_theme_path() ?>/media/pl.gif" alt="<?= rss_item_pl_title(); ?>" />
	</a>
	<?php } ?>
	<?php if(!hidePrivate()) { ?>
	<a id="sa<?= rss_item_id(); ?>" href="#" onclick="_es(<?= rss_item_id(); ?>,<?= rss_item_flags() ?>); return false;">
		<img src="<?= rss_theme_path() ?>/media/edit.gif" alt="edit" />
	</a>
	<?php } ?>
	
	<h4><a href="<?= rss_item_url(); ?>"><?= rss_item_title(); ?></a></h4>
	<div id="sad<?= rss_item_id(); ?>" style="display:none"></div>
	<h5><?= rss_item_date(); ?></h5>
	<h5>
		<a href="<?= rss_item_tagslink(); ?>"><?= LBL_TAG_TAGS ?></a>:&nbsp;
		<span id="t<?= rss_item_id(); ?>"><?= rss_item_tags(); ?></span>&nbsp;
		[<span id="ta<?= rss_item_id(); ?>"><a href="#" onclick="_et(<?= rss_item_id(); ?>); return false;"><?= LBL_TAG_EDIT ?></a></span>]
	</h5>

	<div class="content">
		<?= rss_item_content(); ?>
	</div>
</li>
