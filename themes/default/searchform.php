<h2><?php echo  rss_search_title() ?></h2>

<form action="<?php echo  getPath() ?>search.php" method="post" id="srchfrm">
	<fieldset style="display:block;text-align:left">
	<legend><?php echo  LBL_TITLE_SEARCH ?></legend>
	<p>
		<label for="<?php echo  QUERY_PRM ?>"><?php echo  LBL_SEARCH_SEARCH_QUERY ?></label>
	   	<input type="text" name="<?php echo  QUERY_PRM ?>"  id="<?php echo  QUERY_PRM ?>" value="<?php echo  rss_search_query() ?>"/>
	</p>
	
	<p>
		<input type="radio" id="qry_match_or" name="<?php echo  QUERY_MATCH_MODE ?>" value="<?php echo  QUERY_MATCH_OR ?>" <?php echo  rss_search_or_checked() ?> />
		<label for="qry_match_or"><?php echo  LBL_SEARCH_MATCH_OR ?></label>
		<input type="radio" id="qry_match_and" name="<?php echo  QUERY_MATCH_MODE ?>" value="<?php echo  QUERY_MATCH_AND ?>" <?php echo  rss_search_and_checked() ?>/>
		<label for="qry_match_and"><?php echo  LBL_SEARCH_MATCH_AND ?></label>
		<input type="radio" id="qry_match_exact" name="<?php echo  QUERY_MATCH_MODE ?>" value="<?php echo  QUERY_MATCH_EXACT ?>" <?php echo  rss_search_exact_checked() ?>/>
		<label for="qry_match_exact"><?php echo  LBL_SEARCH_MATCH_EXACT ?></label>
	</p>

	<p>
		<label for="<?php echo  QUERY_CHANNEL ?>"><?php echo  LBL_SEARCH_CHANNELS ?></label>
		<?php echo  rss_search_channels_combo(QUERY_CHANNEL); ?>
	</p>

	<p>
		<input type="radio" id="qry_order_date" name="<?php echo  QUERY_ORDER_BY ?>" value="<?php echo  QUERY_ORDER_BY_DATE ?>" <?php echo  rss_search_order_date_checked() ?> />
		<label for="qry_order_date"><?php echo  LBL_SEARCH_ORDER_DATE_CHANNEL ?></label>
		<input type="radio" id="qry_order_channel" name="<?php echo  QUERY_ORDER_BY ?>" value="<?php echo  QUERY_ORDER_BY_CHANNEL ?>" <?php echo  rss_search_order_channel_checked() ?> />
		<label for="qry_order_channel"><?php echo  LBL_SEARCH_ORDER_CHANNEL_DATE ?></label>
	</p>

	<p>
		<label for="query_res_per_page"><?php echo  LBL_SEARCH_RESULTS_PER_PAGE ?></label>
		<?php echo  rss_search_results_per_page_combo(QUERY_RESULTS) ?>
	</p>

	<p>
		<input type="hidden" name="<?php echo  QUERY_CURRENT_PAGE ?>" value="0" />
		<input id="search_go" type="submit" value="<?php echo  LBL_SEARCH_GO ?>"/>
	</p>
	</fieldset>
</form>
