<h2><?= rss_search_title() ?></h2>

<form action="<?= getPath() ?>search.php" method="post" id="srchfrm">
	<fieldset style="display:block;text-align:left">
	<legend><?= LBL_TITLE_SEARCH ?></legend>
	<p>
		<label for="query"><?= LBL_SEARCH_SEARCH_QUERY ?></label>
	   	<input type="text" name="query"  id="query" value="<?= rss_search_query() ?>"/>
	</p>
	
	<p>
		<input type="radio" id="qry_match_or" name="query_match" value="or" <?= rss_search_or_checked() ?> />
		<label for="qry_match_or"><?= LBL_SEARCH_MATCH_OR ?></label>
		<input type="radio" id="qry_match_and" name="query_match" value="and" <?= rss_search_and_checked() ?>/>
		<label for="qry_match_and"><?= LBL_SEARCH_MATCH_AND ?></label>
		<input type="radio" id="qry_match_exact" name="query_match" value="exact" <?= rss_search_exact_checked() ?>/>
		<label for="qry_match_exact"><?= LBL_SEARCH_MATCH_EXACT ?></label>
	</p>

	<p>
		<label for="query_channel"><?= LBL_SEARCH_CHANNELS ?></label>
		<?= rss_search_channels_combo('query_channel'); ?>
	</p>

	<p>
		<input type="radio" id="qry_order_date" name="order" value="date" <?= rss_search_order_date_checked() ?> />
		<label for="qry_order_date"><?= LBL_SEARCH_ORDER_DATE_CHANNEL ?></label>
		<input type="radio" id="qry_order_channel" name="order" value="channel" <?= rss_search_order_channel_checked() ?> />
		<label for="qry_order_channel"><?= LBL_SEARCH_ORDER_CHANNEL_DATE ?></label>
	</p>

	<p>
		<label for="query_res_per_page"><?= LBL_SEARCH_RESULTS_PER_PAGE ?></label>
		<?= rss_search_results_per_page_combo('query_res_per_page') ?>
	</p>

	<p>
		<input type="hidden" name="query_current_page" value="0" />
		<input id="search_go" type="submit" value="<?= LBL_SEARCH_GO ?>"/>
	</p>
	</fieldset>
</form>
