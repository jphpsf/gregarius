<?php
	include_once('intl/en.php');

	function Convert($dir)
	{
		if(!is_dir($dir) || "intl" == $dir) {
			return;
		}

		$baseDir = opendir($dir);
		while($file = readdir($baseDir)) {
			if("." == $file ||
				 ".." == $file) {
				continue;
			}

			if(is_dir($file)) {
				Convert($file);
			} else {
				$fp = fopen($file, 'w+');

				while(($line = fread($fp) != feof()) {


				}
				flush($fp);
				fclose($fp);

#				define('A', "Hello, World");
#				$con = "A";
#				$val = eval('echo ' . $con . ';');
#				echo $val;
			}
		}
	}

	Convert(".");
?>
