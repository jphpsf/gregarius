<?php

class DB {
	function DB() {
		$this -> DBConnect(DBSERVER,DBUNAME,DBPASS);
		$this -> DBSelectDB(DBNAME);
	}
	
	function getTable($tableName) {
		if (defined('DB_TABLE_PREFIX') && "" != DB_TABLE_PREFIX) {
			  return (" " . DB_TABLE_PREFIX . "_" . $tableName . " ");
		} else {
			  return (" $tableName ");
		}
	}
}

?>
