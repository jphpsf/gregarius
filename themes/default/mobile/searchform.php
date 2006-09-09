<h2><?php echo rss_search_title() ?></h2>

<form action="<?php echo getPath() ?>search.php" method="post" id="srchfrm">
	<fieldset style="display:block;text-align:left">
	<legend><?php echo __('Search') ?></legend>
	<p>
		<label for="<?php echo QUERY_PRM ?>"><?php echo __('Search terms:') ?></label>
	   	<input type="text" name="<?php echo QUERY_PRM ?>"  id="<?php echo QUERY_PRM ?>" value="<?php echo rss_search_query() ?>"/>
	</p>
	
	<p>
		<input type="radio" id="qry_match_or" name="<?php echo QUERY_MATCH_MODE ?>" value="<?php echo QUERY_MATCH_OR ?>" <?php echo rss_search_or_checked() ?> />
		<label for="qry_match_or"><?php echo __('Some terms (OR)') ?></label>
		<input type="radio" id="qry_match_and" name="<?php echo QUERY_MATCH_MODE ?>" value="<?php echo QUERY_MATCH_AND ?>" <?php echo rss_search_and_checked() ?>/>
		<label for="qry_match_and"><?php echo __('All terms (AND)') ?></label>
		<input type="radio" id="qry_match_exact" name="<?php echo QUERY_MATCH_MODE ?>" value="<?php echo QUERY_MATCH_EXACT ?>" <?php echo rss_search_exact_checked() ?>/>
		<label for="qry_match_exact"><?php echo __('Exact match') ?></label>
	</p>

	<p>
		<label for="<?php echo QUERY_CHANNEL ?>"><?php echo __('Feed:') ?></label>
		<?php echo rss_search_channels_combo(QUERY_CHANNEL); ?>
	</p>

	<p>
		<input type="radio" id="qry_order_date" name="<?php echo QUERY_ORDER_BY ?>" value="<?php echo QUERY_ORDER_BY_DATE ?>" <?php echo rss_search_order_date_checked() ?> />
		<label for="qry_order_date"><?php echo __('Order by date, feed') ?></label>
		<input type="radio" id="qry_order_channel" name="<?php echo QUERY_ORDER_BY ?>" value="<?php echo QUERY_ORDER_BY_CHANNEL ?>" <?php echo rss_search_order_channel_checked() ?> />
		<label for="qry_order_channel"><?php echo __('Order by feed, date') ?></label>
	</p>

	<p>
		<label for="query_res_per_page"><?php echo __('Results per page:') ?></label>
		<?php echo rss_search_results_per_page_combo(QUERY_RESULTS) ?>
	</p>

	<p>
		<input type="hidden" name="<?php echo QUERY_CURRENT_PAGE ?>" value="0" />
		<input id="search_go" type="submit" value="<?php echo __('Search') ?>"/>
	</p>
	</fieldset>
</form>
