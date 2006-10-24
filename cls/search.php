<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2006 Marco Bonetti
#
###############################################################################
# This program is free software and open source software; you can redistribute
# it and/or modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of the License,
# or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
# http://www.gnu.org/licenses/gpl.html
#
###############################################################################
# E-mail:      mbonetti at gmail dot com
# Web page:    http://gregarius.net/
#
###############################################################################

define ('INFINE_RESULTS',-1);

define ('QUERY_PRM','rss_query');
define ('QUERY_MATCH_MODE', 'rss_query_match');
define ('QUERY_CHANNEL', 'rss_query_channel');
define ('QUERY_RESULTS','rss_query_res_per_page');
define ('QUERY_CURRENT_PAGE','rss_query_current_page');
define ('HIT_BEFORE',"<span class=\"searchhit\">");
define ('HIT_AFTER',"</span>");

define ('QUERY_ORDER_BY','rss_order');
define ('QUERY_ORDER_BY_DATE','date');
define ('QUERY_ORDER_BY_CHANNEL','channel');
define ('QUERY_MATCH_OR','or');
define ('QUERY_MATCH_AND','and');
define ('QUERY_MATCH_EXACT','exact');

// This is needed for some constants
rss_require('cls/wrappers/toolkit.php');

class SearchItemList extends ItemList {

    var $searchTerms = array();
    var $matchMode;
    var $regMatch = "";

    var $currentPage;
    var $resultsPerPage = 0;
    var $startItem;
    var $endItem;
    var $orderBy;
    var $query = "";
    var $logicSep;

    function SearchItemList($query=null,$results=0) {
        parent::ItemList();
		if ($query) {
			$this -> query=$query;
		} elseif(isset($_REQUEST[QUERY_PRM])) {
			$this->query = $_REQUEST[QUERY_PRM];
		}else{
			$this -> query = null;
		}
		
		
		// Sanitize the query parameters:
		// fixme: this probably breaks on queries with weird characters, depending
		// on the locale. 
		// see: http://php.benscom.com/manual/en/reference.pcre.pattern.syntax.php
		if ($this -> query) {
      		$this -> query = trim(preg_replace('#[^\w\s\x80-\xff]#','',$this -> query));
		}
		
		$this->resultsPerPage = (int) $results;
		
        $this -> populate();

        $this->humanReadableQuery = implode(" ".strtoupper($this->logicSep)." ", $this->searchTerms);
    }

    function filterItems() {
        $cntr = 0;
        foreach($this->feeds as $fkey => $feed) {
            foreach ($this -> feeds[$fkey]->items as $ikey => $item) {
                $descr_noTags = strip_tags($item -> description);
                $title_noTags = strip_tags($item -> title);
                $match = false;
                reset($this->searchTerms);
                $match = ($this->matchMode == QUERY_MATCH_AND || $this->matchMode == QUERY_MATCH_EXACT);
                foreach ($this->searchTerms as $term) {
                    if ($this->matchMode == QUERY_MATCH_AND || $this->matchMode == QUERY_MATCH_EXACT) {
                        $match = ((stristr($descr_noTags, $term) || stristr($title_noTags, $term)) && $match);
                    } else {
                        $match = ($match || (stristr($descr_noTags, $term) || stristr($title_noTags, $term)));
                    }
                }

                if (!$match) {
                    $this->removeItem($fkey,$ikey,true);
                } else {

                    if ($cntr >= $this->startItem && $cntr <= $this->endItem) {
                        $this->feeds[$fkey] -> items[$ikey] -> description =
                            preg_replace("'(?!<.*?)(".$this->regMatch.")(?![^<>]*?>)'si",
                                         HIT_BEFORE."\\1".HIT_AFTER, $item -> description);
                    } else {
                        $this->removeItem($fkey,$ikey,false);
                    }

                    $cntr++;
                }
            }
        }
    }

    function populate() {
        
        if (!$this->query) {
            return;
        }

        $this->matchMode = sanitize(
        		(!array_key_exists(QUERY_MATCH_MODE, $_REQUEST) ? QUERY_MATCH_AND : $_REQUEST[QUERY_MATCH_MODE]), 
        	RSS_SANITIZER_CHARACTERS_EXT);
        	
        $this->channelId = sanitize(
        	((array_key_exists(QUERY_CHANNEL, $_REQUEST)) ? $_REQUEST[QUERY_CHANNEL] : ALL_CHANNELS_ID),
        	RSS_SANITIZER_NUMERIC);

		if (!$this->resultsPerPage) {
        	$this->resultsPerPage = sanitize(
        		((array_key_exists(QUERY_RESULTS, $_REQUEST)) ? $_REQUEST[QUERY_RESULTS] : INFINE_RESULTS),
        		RSS_SANITIZER_NUMERIC);
        }

        $this->currentPage = sanitize(
        	(array_key_exists(QUERY_CURRENT_PAGE, $_REQUEST) ? $_REQUEST[QUERY_CURRENT_PAGE] : 0),
        	RSS_SANITIZER_NUMERIC);

        $this->startItem = $this->resultsPerPage * $this->currentPage;

        $this->endItem = $this->startItem + $this->resultsPerPage -1;

        if ($this->resultsPerPage == INFINE_RESULTS) {
            $this->endItem = 99999999;
        }

        $this->orderBy = sanitize(
        	(array_key_exists(QUERY_ORDER_BY, $_REQUEST) ? $_REQUEST[QUERY_ORDER_BY] : QUERY_ORDER_BY_DATE),
        	RSS_SANITIZER_CHARACTERS_EXT);
        	
        $qWhere = "";
        $this->regMatch = "";
        $term = "";

        if ($this->matchMode == QUERY_MATCH_OR || $this->matchMode == QUERY_MATCH_AND) {

            $this->logicSep = ($this->matchMode == QUERY_MATCH_OR ? "or" : "and");
            $this->searchTerms = explode(" ", $this->query);
            foreach ($this->searchTerms as $term) {
                $term = trim($term);
                if ($term != "") {
                    $qWhere .= "(i.description like '%$term%' or "." i.title like '%$term%') ".$this->logicSep;
                }
                // this will be used later for the highliting regexp
                if ($this->regMatch != "") {
                    $this->regMatch .= "|";
                }
                $this->regMatch .= $term;
            }

            $qWhere .= ($this->matchMode == QUERY_MATCH_OR ? " 1=0 " : " 1=1 ");
        } else {
            $this->logicSep = "";
            $this->searchTerms[0] = $this->query;
            $term = $this->query;
            $qWhere .= "(i.description like '%$term%' or "." i.title like '%$term%') ";
            $this->regMatch = $this->query;
        }

        $qWhere= "(" . $qWhere. ") ";



        if ($this->channelId != ALL_CHANNELS_ID) {
            $qWhere .= " and c.id = " . $this->channelId  . " ";
        }

        if (hidePrivate()) {
            $qWhere .= " and not(i.unread & ".RSS_MODE_PRIVATE_STATE.") ";
        }
        $qWhere .= " and not(i.unread & ".RSS_MODE_DELETED_STATE.") ";

        if ($this->orderBy == QUERY_ORDER_BY_DATE) {
            $qOrder = "  ts desc";
        } else {
            if (getConfig('rss.config.absoluteordering')) {
                $qOrder = "  f.position asc, c.position asc";
            } else {
                $qOrder = "  f.name asc, c.title asc";
            }
        }



        $qOrder .= ", i.added desc";



        parent::populate($qWhere,$qOrder,0,getConfig("rss.search.maxitems"),ITEM_SORT_HINT_MIXED,true);

        $this -> filterItems();
        $this -> nav();
    }

    function nav() {
        $nav = "";

        if ($this->resultsPerPage != INFINE_RESULTS && $this->itemCount >  $this->resultsPerPage) {
            $nav .= "<div class=\"readmore\">";
            $nav .= __('Results: ');

            // first page
            $fp = 0;
            //last page
            $lp = floor(($this->itemCount -1) / $this -> resultsPerPage);
            // current page
            $cp = $this -> currentPage;
            //shown pages
            $pages = array ();

            for ($i = 0; $i < 4; $i ++) {
                if ($cp - $i >= 0) {
                    $pages[$cp - $i] = true;
                } else {
                    if ($cp + $i < $lp) {
                        $pages[$cp + $i] = true;
                    }
                }
                if ($cp + $i < $lp) {
                    $pages[$cp + $i] = true;
                } else {
                    if ($cp - $i >= 0) {
                        $pages[$cp - $i] = true;
                    }
                }
            }

            $pages[0] = true;
            $pages[$lp] = true;

            for ($p = $fp; $p < $lp; $p ++) {
                if (!array_key_exists($p, $pages)) {
                    if (array_key_exists($p -1, $pages)) {
                        $nav .= " ... ";
                    }
                    continue;
                }

                $cpp = ($p * $this -> resultsPerPage == $this->startItem);
                if (!$cpp) {
                    $nav .= " <a href=\"".$_SERVER['PHP_SELF']."?".QUERY_PRM."=".$this->query."&amp;"
                            .QUERY_MATCH_MODE."=".$this->matchMode."&amp;"
                            .QUERY_CHANNEL."=".$this->channelId ."&amp;"
                            .QUERY_RESULTS."=".$this->resultsPerPage."&amp;"
                            .QUERY_ORDER_BY."=".$this->orderBy."&amp;"
                            .QUERY_CURRENT_PAGE."=$p"."\">";
                } else {
                    $nav .= HIT_BEFORE;
                }

                $nav .= "". (1 + $p * $this -> resultsPerPage)."-". ((1 + $p) * $this -> resultsPerPage)."";

                if (!$cpp) {
                    $nav .= "</a>";
                } else {
                    $nav .= HIT_AFTER;
                }
                if ((1 + $p) * $this -> resultsPerPage < $this->itemCount) {
                    $nav .= ", \n";
                }
            }

            if ($p * $this -> resultsPerPage >= $this->endItem) {
                $nav .= " <a href=\"".$_SERVER['PHP_SELF']."?".QUERY_PRM."=".$this->query."&amp;"
                        .QUERY_MATCH_MODE."=".$this->matchMode."&amp;"
                        .QUERY_CHANNEL."=".$this->channelId ."&amp;"
                        .QUERY_RESULTS."=".$this->resultsPerPage."&amp;"
                        .QUERY_ORDER_BY."=".$this->orderBy."&amp;"
                        .QUERY_CURRENT_PAGE."=$p"."\">";
                $nav .= (1 + $p * $this -> resultsPerPage)."-" . $this->itemCount;
                $nav .= "</a> \n";
            } else {
                $nav .= HIT_BEFORE. (1 + $p * $this -> resultsPerPage)."-" . $this->itemCount.HIT_AFTER;
            }
            $nav .= "<hr class=\"clearer hidden\"/>\n</div>\n";
        }

        if ($nav) {
            $this -> beforeList .= $nav;
            $this -> afterList .= $nav ;
        }
    }

    function render() {
        $GLOBALS['rss'] -> searchFormTitle = $this->title;
        $this->title="";
        require($GLOBALS['rss'] ->getTemplateFile("searchform.php"));

        $GLOBALS['rss'] -> currentItemList = $this;
        rss_plugin_hook('rss.plugins.items.beforeitems', null);
        require($GLOBALS['rss'] ->getTemplateFile("itemlist.php"));
        rss_plugin_hook('rss.plugins.items.afteritems', null);
    }

}
?>
