<?php
	include_once('intl/en.php');

	$EXCLUDE = array('intl', 'extlib');

	function Convert($dir)
	{
		global $EXCLUDE;

		if(!is_dir($dir) || in_array($dir, $EXCLUDE)) {
			return;
		}

		$baseDir = opendir($dir);
		while($file = readdir($baseDir)) {
			if('.' == $file ||
				 '..' == $file) {
				continue;
			}

			if(is_dir($file)) {
				Convert($file);
			} else {
				$old = fopen($file, 'r');
				$new = fopen($file . '.new', 'w');

				while(($line = fread($old)) != feof($old)) {
					preg_match('/\.*.LBL_*.\.', $line, $matches);

					if(is_array($matches)) {
						foreach($matches as $match) {
							preg_replace($match, '__("' . eval('echo ' . $match . ';') . '")', $line);
						}
					}

					fwrite($new, $line);
				}

				flush($new);
				fclose($old);
				fclose($new);

				rename($old, $old . '.old');
				rename($new, $old);

				echo "Completed .. " . $old . "\n";
			}
		}
	}

	Convert('.');
?>
