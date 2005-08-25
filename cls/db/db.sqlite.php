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
# v0.3: fixed a parser trim bug which lead to items behing marked unread after
#       each refresh of a feed
# v0.4: added alter table and fixed processing of columns update schema
###############################################################################

rss_require('cls/db/db.php');

class SqliteDB extends DB {

	var $db=false;
	var $dbpath="";
	var $debug=false;

	function  SQLite() {
		parent::DB();
	}
    
	//// The following are just wrappers of their mysql counterpart for now
	//// maybe one day I shall support other rdbms and add some differentiation
	//// in here.

	function DBConnect($dbpath, $dbuname, $dbpass) {
		if (defined("DBDEBUG")) $this->debug=constant("DBDEBUG");
		$this->dbpath=$dbpath;
		$this->db=@sqlite_open($dbpath,0666,$msg_err);
		if (!$this->db) {
			die( "<h1>Error connecting to the database!</h1>\n"
			."<p>Have you edited dbinit.php and correctly defined "
			."the database path?</p>\n" );
		}
		sqlite_query($this->db,"PRAGMA synchronous = FULL");

		//we emulate via callback some missing MySQL function
		sqlite_create_function($this->db,"year","__cb_sqlite_year",1);
		sqlite_create_function($this->db,"month","__cb_sqlite_month",1);
		sqlite_create_function($this->db,"day","__cb_sqlite_dayofmonth",1);
		sqlite_create_function($this->db,"dayofmonth","__cb_sqlite_dayofmonth",1);
		sqlite_create_function($this->db,"from_unixtime","__cb_sqlite_from_unixtime");
		sqlite_create_function($this->db,"unix_timestamp","__cb_sqlite_unix_timestamp");
		sqlite_create_function($this->db,"now","__cb_sqlite_now");
		sqlite_create_function($this->db,"md5","__cb_sqlite_md5");
	}

	function DBSelectDB($dbname) {
		return true; //sqlite only contain one DB so no need of this
	}

	function alter_table($table,$alterdefs){
		/* function written by jon jenseng and realeased as Opensource
		on his web site: http://code.jenseng.com/db/
		SQlite does not fully support ALTER TABLE, and it support it only
		in SQLite 3.2 +
		So we use this function to execute alter table query
		*/
		if($alterdefs != ''){
			$result = @sqlite_query($this->db,"SELECT sql,name,type FROM sqlite_master WHERE tbl_name = '".$table."' ORDER BY type DESC");
			if(@sqlite_num_rows($result)>0){
				$row = @sqlite_fetch_array($result); //table sql
				$tmpname = 't'.time();
				$origsql = trim(preg_replace("/[\s]+/"," ",str_replace(",",", ",preg_replace("/[\(]/","( ",$row['sql'],1))));
				$createtemptableSQL = 'CREATE TEMPORARY '.substr(trim(preg_replace("'".$table."'",$tmpname,$origsql,1)),6);
				$createindexsql = array();
				$i = 0;
				$defs = preg_split("/[,]+/",$alterdefs,-1,PREG_SPLIT_NO_EMPTY);
				$prevword = $table;
				$oldcols = preg_split("/[,]+/",substr(trim($createtemptableSQL),strpos(trim($createtemptableSQL),'(')+1),-1,PREG_SPLIT_NO_EMPTY);
				$newcols = array();
				for($i=0;$i<sizeof($oldcols);$i++){
					$colparts = preg_split("/[\s]+/",$oldcols[$i],-1,PREG_SPLIT_NO_EMPTY);
					$oldcols[$i] = $colparts[0];
					$newcols[$colparts[0]] = $colparts[0];
				}
				$newcolumns = '';
				$oldcolumns = '';
				reset($newcols);
				while(list($key,$val) = each($newcols)){
					$newcolumns .= ($newcolumns?', ':'').$val;
					$oldcolumns .= ($oldcolumns?', ':'').$key;
				}
				$copytotempsql = 'INSERT INTO '.$tmpname.'('.$newcolumns.') SELECT '.$oldcolumns.' FROM '.$table;
				$dropoldsql = 'DROP TABLE '.$table;
				$createtesttableSQL = $createtemptableSQL;
				foreach($defs as $def){
					$defparts = preg_split("/[\s]+/",$def,-1,PREG_SPLIT_NO_EMPTY);
					$action = strtolower($defparts[0]);
					switch($action){
						case 'add':
							if(sizeof($defparts) <= 2){
								trigger_error('near "'.$defparts[0].($defparts[1]?' '.$defparts[1]:'').'": syntax error',E_USER_WARNING);
								return false;
							}
							$createtesttableSQL = substr($createtesttableSQL,0,strlen($createtesttableSQL)-1).',';
							for($i=1;$i<sizeof($defparts);$i++) $createtesttableSQL.=' '.$defparts[$i];
							$createtesttableSQL.=')';
							break;
		 				case 'change':
							if(sizeof($defparts) <= 3){
								trigger_error('near "'.$defparts[0].($defparts[1]?' '.$defparts[1]:'').($defparts[2]?' '.$defparts[2]:'').'": syntax error',E_USER_WARNING);
								return false;
							}
							if($severpos = strpos($createtesttableSQL,' '.$defparts[1].' ')){
								if($newcols[$defparts[1]] != $defparts[1]){
									trigger_error('unknown column "'.$defparts[1].'" in "'.$table.'"',E_USER_WARNING);
									return false;
								}
								$newcols[$defparts[1]] = $defparts[2];
								$nextcommapos = strpos($createtesttableSQL,',',$severpos);
								$insertval = '';
								for($i=2;$i<sizeof($defparts);$i++) $insertval.=' '.$defparts[$i];
								if($nextcommapos) $createtesttableSQL = substr($createtesttableSQL,0,$severpos).$insertval.substr($createtesttableSQL,$nextcommapos);
								else $createtesttableSQL = substr($createtesttableSQL,0,$severpos-(strpos($createtesttableSQL,',')?0:1)).$insertval.')';
							}
							else{
								trigger_error('unknown column "'.$defparts[1].'" in "'.$table.'"',E_USER_WARNING);
								return false;
							}
							break;
		 				case 'drop':
							if(sizeof($defparts) < 2){
								trigger_error('near "'.$defparts[0].($defparts[1]?' '.$defparts[1]:'').'": syntax error',E_USER_WARNING);
								return false;
							}
							if($severpos = strpos($createtesttableSQL,' '.$defparts[1].' ')){
								$nextcommapos = strpos($createtesttableSQL,',',$severpos);
								if($nextcommapos)$createtesttableSQL = substr($createtesttableSQL,0,$severpos).substr($createtesttableSQL,$nextcommapos + 1);
								else $createtesttableSQL = substr($createtesttableSQL,0,$severpos-(strpos($createtesttableSQL,',')?0:1) - 1).')';
								unset($newcols[$defparts[1]]);
							}
							else{
								trigger_error('unknown column "'.$defparts[1].'" in "'.$table.'"',E_USER_WARNING);
								return false;
							}
							break;
		 				default:
							trigger_error('near "'.$prevword.'": syntax error',E_USER_WARNING);
							return false;
					}
					$prevword = $defparts[sizeof($defparts)-1];
				}

				//this block of code generates a test table simply to verify that the columns specifed are valid in an sql statement
				//this ensures that no reserved words are used as columns, for example
				@sqlite_query($this->db,$createtesttableSQL);
				if ($this->rss_sql_error()) return false;
  
				$droptempsql = 'DROP TABLE '.$tmpname;
				@sqlite_query($this->db,$droptempsql);
				//end block
  
				$createnewtableSQL = 'CREATE '.substr(trim(preg_replace("'".$tmpname."'",$table,$createtesttableSQL,1)),17);
				$newcolumns = '';
				$oldcolumns = '';
				reset($newcols);
				while(list($key,$val) = each($newcols)){
					$newcolumns .= ($newcolumns?', ':'').$val;
					$oldcolumns .= ($oldcolumns?', ':'').$key;
				}
				$copytonewsql = 'INSERT INTO '.$table.'('.$newcolumns.') SELECT '.$oldcolumns.' FROM '.$tmpname;
  
				@sqlite_query($this->db,$createtemptableSQL); //create temp table
				@sqlite_query($this->db,$copytotempsql); //copy to table
				@sqlite_query($this->db,$dropoldsql); //drop old table
  
				@sqlite_query($this->db,$createnewtableSQL); //recreate original table
				@sqlite_query($this->db,$copytonewsql); //copy back to original table
				@sqlite_query($this->db,$droptempsql); //drop temp table
			}
			else{
				trigger_error('no such table: '.$table,E_USER_WARNING);
				return false;
			}
			return true;
  		}
	}

	function is_beetween_quote($search,$query) {
		//check that $search is not inside quote into query
		return preg_match("/('([^']|'')*".preg_quote($search)."([^']|'')*')/is",$query);
	}
	
	function mysql2sqlite($query) {
  		//converts mysql specific syntax to sqlite syntax
  		//order of replace is important to optimize stuff
  		$doReturn=false;
  
		//table struct query
		if (preg_match("/^\s*show\s+tables/is",$query)) {
			$query="SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
			$doReturn=true;
  		}
  		else if (preg_match("/^\s*drop\s+table\s+(if\s+exists)/is",$query,$matches)) {
  			$query=str_replace($matches[1],"",$query);
			$query=preg_replace("/(\s+)/is"," ",$query); // no risk to trim data here
			$doReturn=true;
  		}
  		else if (preg_match("/^\s*alter\s+table/is",$query,$matches)) {
			$query=preg_replace("/(COLUMN)/is","",$query);
			$query=preg_replace("/(\s+)/is"," ",$query); // no risk to trim data here
			$doReturn=true;
		}
		else if (preg_match("/^\s*create\s+table/is",$query)) {
			/* We are using a class found into SQLLiteManager project!
			 We need to check the licence of SQLLiteManager and (include it?)
			 Otherwise, we must rebuild this function by ourself
			 */
			include_once("ParsingQuery.class.php");
			$query=preg_replace("/(\s+)/is"," ",$query); // no risk to trim data here
			$parse=new ParsingQuery($query,2); //2=type mysql
			$query=$parse->convertQuery();
			$doReturn=true;
		}
  
		if ($doReturn || !$query) return $query;

		//count(distinct(x)) is not supported
		if (preg_match("/select\s+(count\s*\(distinct\s*\(([^\)]+)\)\s*\))/is",$query,$matches)) {
			$field=$matches[2];
			$query=str_replace($matches[0],"count(*)",$query);
			$query=preg_replace("/from\s/is","from (select distinct($field) from ",$query);
			$query.=")";
		}
  
		/*
		we must be carefull to not change text content but only SQL part
		We do a really dirty hack here:
		we replace all texts by an REFERENCES that will not interfere into the parsing
		$i=0;
		$tabStrings=array();
		
		//when note description are too big, doing basic str_replace/preg_replace
		//sometime crash... so we try to be less "aggressive"
		$len=strlen($query);
		$quoteOpen=false;
		$j=0;
		$new_query=$buffer=$field="";
		$tabReplace=array();
		for ($i=0;$i<$len;$i++) {
			$current=substr($query,$i,1);
			if ($i<$len-1) $next=substr($query,$i+1,1);
			else $next="";
			if ("$current"=="'") {
				if (!$quoteOpen) {
					if ($next=="'") {
						if ($i<$len-2 && substr($query,$i+2,1)!="'") {
							$new_query.="''";
							$i++;
							continue;
						}
					}
					$buffer=$current;
					$field="__GREGA_${j}__";
					$new_query.=$field;
					$quoteOpen=true;
					$j++;
				}
				else if ("$next"=="'") {
					$buffer.="''";
					$i++;
				}
				else {
					//closing quote
					$buffer.=$current;
					$tabReplace["$field"]=$buffer;
					$buffer="";
					$quoteOpen=false;
				}
			}
			else if ($quoteOpen) $buffer.=$current;
			else $new_query.=$current;
		}
		unset($query);

		$query=preg_replace("/(\s+)/is"," ",$new_query); // we can now trim data, but not before to prevent a change in values
		
		//we restore all the strings values
		if (is_array($tabReplace) && count($tabReplace)>0) {
			foreach ($tabReplace as $search=>$replace) {
				$query=str_replace($search,$replace,$query);
				unset($tabReplace["$search"]);
			}
			unset($tabReplace);
		}
		*/

		return $query;
	}
	
		
	function rss_query ($query, $dieOnError=true, $preventRecursion=false) {
		//we use a wrapper to convert MySQL specific instruction to sqlite
		$result=false;
		$GLOBALS["sqlite_extented_error"]="";
		$errorString = " (none) ";

		$this->debugLog("SQL BEFORE: $query");
		$query=$this->mysql2sqlite($query);
		$this->debugLog("SQL AFTER: $query");
  
		if (is_string($query) && preg_match("/^alter table/is",$query)) {
			$queryparts = preg_split("/[\s]+/",$query,4,PREG_SPLIT_NO_EMPTY);
			$tablename = $queryparts[2];
			$alterdefs = $queryparts[3];
			if (strtolower($queryparts[1]) != 'table' || $queryparts[2] == '') {
				$error=1;
				$errorString = 'near "'.$queryparts[0] . '": syntax error';
			}
			else{
				set_error_handler(array(&$this, 'rss_catch_error_handler'));
				$result = $this->alter_table($tablename,$alterdefs);
				restore_error_handler();
			}
		}
		else {
			if (is_array($query)) {
				//means that it's a SCHEMA creation so we process each query
				foreach ($query as $sql_query) {
					set_error_handler(array(&$this, 'rss_catch_error_handler'));
					$result =  sqlite_query($this->db,$sql_query);
					restore_error_handler();
					if ($error = $this -> rss_sql_error()) {
						if ($error==1 && preg_match("/DROP TABLE/is",$sql_query)) {
							//not a real error to drop a table which do not exists
							$error=0;
							continue;
						}
						break;
					}
				}
			}
			else {
				set_error_handler(array(&$this, 'rss_catch_error_handler'));
				$result =  sqlite_query($this->db,$query);
				restore_error_handler();
			}
			if ($error = $this -> rss_sql_error()) {
				if ($error==1 && is_string($query) && preg_match("/^\s*DROP TABLE/is",$query)) {
					//not a real error to drop a table which do not exists
					$error=0;
				}
				else $errorString = $this -> rss_sql_error_message();
			}
		}
		
		if ($error) $this->debugLog("SQL EXEC ERR: $error - $errorString");
		else $this->debugLog("SQL EXEC OK");
		
		if ($error == 1 && $dieOnError && !$preventRecursion) {
			if (preg_match("/no\s+such\s+table/is",$errorString)) {
				ob_start();
				rss_require('schema.php');
				checkSchema();
				ob_clean();
				return $this -> rss_query ($query, $dieOnError, true);
			}
			else if (preg_match("/(no\s+such\s+column\s*:\s*|no\s+column\s+named\s+)(.+)/is",$errorString,$matches)) {
				ob_start();
				rss_require('schema.php');
				checkSchemaColumns(trim($matches[2]));
				ob_clean();
				return $this -> rss_query ($query, $dieOnError, true);
			}
		}
	
		if ($error && $dieOnError) {
			die("<p>Failed to execute the SQL query <pre>$query</pre> </p>"
			."<p>Error $error: $errorString</p>");
		}
		return $result;
	}

	function rss_fetch_row($rs) {
		if ($rs) return @sqlite_fetch_array($rs,SQLITE_NUM);
		else return false;
	}

	function rss_fetch_assoc($rs) {
		if ($rs) return @sqlite_fetch_array($rs,SQLITE_ASSOC);
		else return false;
	}

	function rss_num_rows($rs) {
		if ($rs) return @sqlite_num_rows($rs);
		else return false;
	}

	function rss_sql_error() {
		$err_code=@sqlite_last_error($this->db);
		return $err_code;
	}

	function rss_sql_error_message () {
		$errorString="";
		$error=@sqlite_last_error($this->db);
		if ($error) $errorString=@sqlite_error_string($error)." : ".$GLOBALS["sqlite_extented_error"];
		return $errorString;
	}

	function rss_insert_id() {
		if ($this->db) return @sqlite_last_insert_rowid($this->db);
		else return false;
	}

	function rss_real_escape_string($string) {
		return @sqlite_escape_string ($string);
	}

	function debugLog($str) {
		if ($this->debug===true) {
			$file=@dirname($this->dbpath);
			if ($file) {
			$file.="/debug.log";;
			$fp=@fopen($file,"a");
			@fputs($fp,"-== " . date('r') . " =======================================-\n");
			@fputs($fp,trim($str)."\n\n");
			@fclose($fp);
			}
		}
	}//end function

	function rss_is_sql_error($kind) {
		switch ($kind) {
		 case RSS_SQL_ERROR_NO_ERROR:
			return (@sqlite_last_error($this->db) == 0);
			break;
		 case RSS_SQL_ERROR_DUPLICATE_ROW:
			return (@sqlite_last_error($this->db) == 19);
			break;
		 default:
			return false;
		}
	}

	//error handler taken from sqlitemanager project
	function rss_catch_error_handler($errno, $errstr, $errfile, $errline) {
		if (preg_match("/:(.*)/", $errstr, $errorResult)) $GLOBALS["sqlite_extented_error"] = $errorResult[1];
	}

}

//sqlite callbacks to emulate some MySQL func
if (!defined("SQLITE_CALLBACK")) {
	define("SQLITE_CALLBACK",true);
	function __cb_sqlite_month($timestamp) {
		$timestamp=trim($timestamp);
		if (!preg_match("/^[0-9]+$/is",$timestamp)) $ret=strtotime($timestamp);
		else $ret=$timestamp;
		$ret=date("m",$ret);
		__cb_sqlite_debug("MONTH ($timestamp) = $ret");
		return $ret; 
	}
	
	function __cb_sqlite_year($timestamp) {
		$timestamp=trim($timestamp);
		if (!preg_match("/^[0-9]+$/is",$timestamp)) $ret=strtotime($timestamp);
		else $ret=$timestamp;
		$ret=date("Y",$ret);
		__cb_sqlite_debug("YEAR ($timestamp) =  $ret");
		return $ret; 
	}

	function __cb_sqlite_dayofmonth($timestamp) {
		$timestamp=trim($timestamp);
		if (!preg_match("/^[0-9]+$/is",$timestamp)) $ret=strtotime($timestamp);
		else $ret=$timestamp;
		$ret=date("d",$ret);
		__cb_sqlite_debug("DAYOFMONTH ($timestamp) = $ret");
		return $ret;
	}

	function __cb_sqlite_from_unixtime($timestamp) {
		$timestamp=trim($timestamp);
		if (!preg_match("/^[0-9]+$/is",$timestamp)) $ret=strtotime($timestamp);
		else $ret=$timestamp;
		$ret=date("Y-m-d H:i:s",$ret);
		__cb_sqlite_debug("FROM_UNIXTIME ($timestamp) = $ret");
		return $ret;
	}

	function __cb_sqlite_unix_timestamp($timestamp="") {
		$timestamp=trim($timestamp);
		if (!$timestamp) $ret=time();
		else if (!preg_match("/^[0-9]+$/is",$timestamp)) $ret=strtotime($timestamp);
		else $ret=$timestamp;
		__cb_sqlite_debug("UNIX_TIMESTAMP ($timestamp) = $ret");
		return $ret;
	}

	function __cb_sqlite_now() {
		return date("Y-m-d H:i:s");
	}
	
	function __cb_sqlite_md5($str) {
		return md5($str);
	}

	function __cb_sqlite_debug($str) {
		if (defined("DBDEBUG") && constant("DBDEBUG")==true) {
			$file="/tmp/debug_cb.log";
			$fp=@fopen($file,"a");
			@fputs($fp,"-== " . date('r') . " =======================================-\n");
			@fputs($fp,trim($str)."\n\n");
			@fclose($fp);
		}
	}	

}
?>
