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
		$this->dbpath=$dbpath;
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
              for($i=2;$i<sizeof($defparts);$i++)
                $insertval.=' '.$defparts[$i];
              if($nextcommapos)
                $createtesttableSQL = substr($createtesttableSQL,0,$severpos).$insertval.substr($createtesttableSQL,$nextcommapos);
              else
                $createtesttableSQL = substr($createtesttableSQL,0,$severpos-(strpos($createtesttableSQL,',')?0:1)).$insertval.')';
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
              if($nextcommapos)
                $createtesttableSQL = substr($createtesttableSQL,0,$severpos).substr($createtesttableSQL,$nextcommapos + 1);
              else
                $createtesttableSQL = substr($createtesttableSQL,0,$severpos-(strpos($createtesttableSQL,',')?0:1) - 1).')';
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

	function mysql2sqlite($query) {
	//converts mysql specific syntax to sqlite syntax
	//order of replace is important to optimize stuff
	$doReturn=false;

	//table struct query
	if (preg_match("/show\s+tables/is",$query)) {
		$query="SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
		$doReturn=true;
	}
	else if (preg_match("/drop\s+table\s+(if\s+exists)/is",$query,$matches)) {
		$query=str_replace($matches[1],"",$query);
		$query=preg_replace("/(\s+)/is"," ",$query); // no risk to trim data here
		$doReturn=true;
	}
	else if (preg_match("/alter\s+table/is",$query,$matches)) {
		$query=preg_replace("/(COLUMN)/is","",$query);
		$query=preg_replace("/(\s+)/is"," ",$query); // no risk to trim data here
		$doReturn=true;
	}
	else if (preg_match("/create\s+table/is",$query)) {
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

	if ($doReturn) return $query;
	
	/*
	we must be carefull to not change text content but only SQL part
	We do a really dirty hack here:
	we replace all texts by an REFERENCES that will not interfere into the parsing
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
	$query=preg_replace("/(\s+)/is"," ",$query); // we can now trim data, but not before to prevent a change in values

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

	//we restore all the strings values
	if (is_array($tabStrings) && count($tabStrings)>0) {
		foreach ($tabStrings as $search=>$replace) $query=str_replace($search,$replace,$query);
	}
	return $query;
	}
	
	function rss_query ($query, $dieOnError=true, $preventRecursion=false) {
		//we use a wrapper to convert MySQL specific instruction to sqlite
		$GLOBALS["sqlite_extented_error"]="";
		
		$this->debugLog("SQL BEFORE: $query");
		$query=$this->mysql2sqlite($query);
		$this->debugLog("SQL AFTER: $query");

		if (is_string($query) && preg_match("/alter table/is",$query)) {
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
  		set_error_handler(array(&$this, 'rss_catch_error_handler'));
  		if (is_array($query)) {
  			//means that it's a SCHEMA creation so we process each query
  			foreach ($query as $sql_query) {
  				$result =  @sqlite_query($this->db,$sql_query);
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
  		else $result =  @sqlite_query($this->db,$query);
  		restore_error_handler();
			
  		if ($error = $this -> rss_sql_error()) {
  			if ($error==1 && is_string($query) && preg_match("/DROP TABLE/is",$query)) {
  				//not a real error to drop a table which do not exists
  				$error=0;
  			}
  			else $errorString = $this -> rss_sql_error_message();
  		}
		}
				
		$this->debugLog("SQL ERR: $error - $errorString");
		
		if ($error == 1 && $dieOnError && !$preventRecursion) {
			if (preg_match("/no\s+such\s+table/is",$errorString)) {
				ob_start();
				rss_require('schema.php');
		  	checkSchema();
				ob_clean();
		  	return $this -> rss_query ($query, $dieOnError, true);
			}
			else if (preg_match("/no\s+column\s+named\s+(.+)/is",$errorString,$matches)) {
				ob_start();
				rss_require('schema.php');
				checkSchemaColumns(trim($matches[1]));
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
	
	function rss_fetch_row(&$rs) {
		return @sqlite_fetch_array($rs,SQLITE_NUM);
	}
	
	function rss_fetch_assoc(&$rs) {
		return @sqlite_fetch_array($rs,SQLITE_ASSOC);
	}

	function rss_num_rows(&$rs) {
		return @sqlite_num_rows($rs);
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
		return @sqlite_last_insert_rowid($this->db);
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
			@fputs($fp,trim($str)."\n");
			@fclose($fp);
		}
	}
	}//end function

	
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

	//error handler taken from sqlitemanager project
	function rss_catch_error_handler($errno, $errstr, $errfile, $errline) {
		if (preg_match("/:(.*)/", $errstr, $errorResult)) $GLOBALS["sqlite_extented_error"] = $errorResult[1];
	}
	
}

?>