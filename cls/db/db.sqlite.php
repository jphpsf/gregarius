<?php
###############################################################################
# Gregarius - A PHP based RSS aggregator.
# Copyright (C) 2003 - 2005 Marco Bonetti
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
# E-mail:      mbonetti at users dot sourceforge dot net
# Web page:    http://sourceforge.net/projects/gregarius
#
###############################################################################
# SQLITE wrapper developped by Olivier Ruffin
# E-mail: blog at ruffin dot info
# Web page: http://www.ruffin.info
# 
# to use this wrapper, you must edit dbinit.php and set the constant:
# - DBSERVER with the full path to your sqlite db file
# - DBTYPE to 'sqlite'
# all others DB const are ignored
# if the DB file does not exists, it will be created and default schema will 
# be created
# BE SURE that the path to your file is writable by the web server
# Exemple: 
# define ('DBTYPE','sqlite');
# define (DBSERVER, "/path/to/my/gregarius.db")
#
# history
# v0.1: release
# v0.2: fixed a parser bug that wrongly replaced some text values
###############################################################################


rss_require('cls/db/db.php');

class SqliteDB extends DB {

  var $db=false;
	var $useParsingClass=false;
	
	function  SQLite() {
		parent::DB();
	}

	
	//// The following are just wrappers of their mysql counterpart for now
	//// maybe one day I shall support other rdbms and add some differentiation
	//// in here.
	
	function DBConnect($dbpath, $dbuname, $dbpass) {
		$this->db=@sqlite_open($dbpath,0666,$msg_err);
		if (!$this->db) {
			  die( "<h1>Error connecting to the database!</h1>\n"
					 ."<p>Have you edited dbinit.php and correctly defined "
					 ."the database path?</p>\n" );		  
		}
	}
	
	function DBSelectDB($dbname) {
		return true; //sqlite only contain one DB so no need of this
	}
	
		
	function mysql2sqlite($query) {
	//converts mysql specific syntax to sqlite syntax
	//order of replace is important to optimize stuff
	
	
	$doReturn=false;
	$query=preg_replace("/(\s+)/is"," ",$query);
	//table struct query
	if (preg_match("/show\s+tables/is",$query)) {
		$query="SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
		$doReturn=true;
	}
	else if (preg_match("/drop\s+table\s+(if\s+exists)/is",$query,$matches)) {
		$query=str_replace($matches[1],"",$query);
		$doReturn=true;
	}
	else if ($this->useParsingClass || preg_match("/create\s+table/is",$query)) {
		/* We are using a class found into SQLLiteManager project!
		We need to check the licence of SQLLiteManager and (include it?)
		Otherwise, we must rebuild this function by ourself
		*/
		$this->useParsingClass=true; //to be sure that the rest of the schema will go through parsing
		include_once("ParsingQuery.class.php");
		$parse=new ParsingQuery($query,2); //2=type mysql
		$query=$parse->convertQuery();
		$doReturn=true;
	}

	if ($doReturn) return $query;
	
	//echo "<b>BEFORE</b>: $query<br><br>";
	
	/*
	we must be carefull to not change text content but only SQL part
	We do a really dirty hack here:
	we replace all texts by an REFERENCES that will not interfere into the parsing
	/REM: the best solution would be to be sure that SQL Request do not use syntax
	specific to MySQL...
	*/
	$i=0;
	$tabStrings=array();
	if (preg_match_all("/('([^']|'')*')/is",$query,$matches,PREG_SET_ORDER)) {
		foreach($matches as $onematch) {
			$search=$onematch[1];
			$replace="##GREGA#$i##";
			$tabStrings["$replace"]=$search;
			$query=str_replace($search,$replace,$query);
			$i++;
		}
	}

	$query=str_replace("!(","not(",$query);

	//date_sub(now ... it seems to be the only kind of date_sub used so let's do it like this
	if (preg_match("/(date_sub\s*\(\s*now\s*\(\s*\)\s*,\s*interval\s+([0-9+-]+)\s+([^\)]*)\))/is",$query,$matches)) {
		$nb=-(int)$matches[2];
		$interval=trim($matches[3]);
		if ($interval=="day") $interval="days";
		else if ($interval=="month") $interval="months";
		else if ($interval=="year") $interval="years";
		$query=str_replace($matches[0],"date('now','$nb $interval')",$query);
	}

	//if ( field is null, true, false)
	$query=preg_replace("/if\s*\(\s*[^\s]+\s+is\s+null\s*,([^,]+),([^\)]+)\)/is","ifnull(\$1,\$2)",$query);
	//unix_timestamp
	$query=preg_replace("/unix_timestamp\s*\(([^\)]+)\)/is","strftime('%s',\$1)",$query);
	//FROM_UNIXTIME
	$query=preg_replace("/from_unixtime\s*\(([^\)]+)\)/is","datetime(\$1,'unixepoch')",$query);
	//dayofmonth
	$query=preg_replace("/dayofmonth\s*\(/is","strftime('%d',",$query);
	//year
	$query=preg_replace("/year\s*\(/is","strftime('%Y',",$query);
	//month
	$query=preg_replace("/month\s*\(/is","strftime('%m',",$query);
	//year
	$query=preg_replace("/day\s*\(/is","\$1strftime('%d',",$query);
	//now()
	$query=preg_replace("/(now\s*\(\s*\))/is","datetime('now')",$query);
	//count(distinct)
	if (preg_match("/(count\s*\(distinct\s*\(([^\)]+)\)\s*\))/is",$query,$matches)) {
		$field=$matches[2];
		$query=str_replace($matches[0],"count(*)",$query);
		$query=preg_replace("/from\s/is","from (select distinct($field) from ",$query);
		$query.=")";
	}

	//we restore all the strings
	if (is_array($tabStrings) && count($tabStrings)>0) {
		foreach ($tabStrings as $search=>$replace) $query=str_replace($search,$replace,$query);
	}
	//echo "AFTER: $query<br>";
	return $query;
	}
	
	function rss_query ($query, $dieOnError=true, $preventRecursion=false) {
		//we use a wrapper to convert MySQL specific instruction to sqlite
		$query=$this->mysql2sqlite($query);
		
		if (is_array($query)) {
			//means that it's a SCHEMA creation so we process each query
			foreach ($query as $sql_query) {
				$result =  @sqlite_query($this->db,$sql_query);
			 	if ($error = $this -> rss_sql_error()) break;
			}
		}
		else $result =  @sqlite_query($this->db,$query);
		
		 if ($error = $this -> rss_sql_error()) {
			  $errorString = $this -> rss_sql_error_message();
		 }
		 if ($error==1 && preg_match("/DROP TABLE/is",$query)) $error=0; //means that table does not exists so it's OK;)
	
		 // if we got a missing table error, look for missing tables in the schema
		 // and try to create them
		 if ($error == 1 && !$preventRecursion && $dieOnError) {
			  rss_require('schema.php');
			  checkSchema();
			  return $this -> rss_query ($query, $dieOnError, true);
		 } elseif ($error == 1054 && !$preventRecursion && $dieOnError) {
			  if (preg_match("/^[^']+'([^']+)'.*$/",$errorString,$matches)) {
					rss_require('schema.php');
					checkSchemaColumns($matches[1]);
					return $this -> rss_query ($query, $dieOnError, true);
			  }
		 }
	
		if ($error && $dieOnError) {
			  die ("<p>Failed to execute the SQL query <pre>$query</pre> </p>"
					 ."<p>Error $error: $errorString</p>");
		 }
		 return $result;
	}
	
	function rss_fetch_row(&$rs) {
		return sqlite_fetch_array($rs,SQLITE_NUM);
	}
	
	function rss_fetch_assoc(&$rs) {
		return sqlite_fetch_array($rs,SQLITE_ASSOC);
	}

	function rss_num_rows(&$rs) {
		return sqlite_num_rows($rs);
	}
	
	function rss_sql_error() {
		$err_code=sqlite_last_error($this->db);
		return $err_code;
	}
	
	function rss_sql_error_message () {
		 return sqlite_error_string(sqlite_last_error($this->db));
	}
	
	function rss_insert_id() {
		return sqlite_last_insert_rowid($this->db);
	}
	
	function rss_real_escape_string($string) {
		return sqlite_escape_string ($string);
	}
	
	function rss_is_sql_error($kind) {
		switch ($kind) {
			case RSS_SQL_ERROR_NO_ERROR:
				return (sqlite_last_error($this->db) == 0);
				break;
			case RSS_SQL_ERROR_DUPLICATE_ROW:
				return (sqlite_last_error($this->db) == 19);
				break;
			default:
				return false;
		}
	}
	
}
?>