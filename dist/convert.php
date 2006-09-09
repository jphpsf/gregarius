<?php
	include_once('intl/en.php');

	$EXCLUDE = '#(intl|extlib|dist)#';

	//Convert('.');
	// buildPOs();
	
	function __callback__($matches) {
		if (defined($matches[1])) {
			return '__(\''.constant($matches[1]) .'\')';
		} else {
			echo "??? " . print_r($matches,true) . "\n";
		}
	}
	
	function buildPOs() {
		$files=array_keys(preg_find('/\.php$/','intl',PREG_FIND_RECURSIVE | PREG_FIND_FULLPATH|PREG_FIND_DIRONLY));
		if (($i=array_search('intl/en.php',$files)) !== FALSE) {
			unset($files[$i]);
		}
		foreach($files as $file) {
			echo "Now handling $file ...";
			$translations = array();
			$content = file_get_contents($file);
			if(preg_match_all('#define\s*\([\'"](LOCALE_LINUX)[\'"][^,]*,\s*[\'"](.+)[\'"]\s*\);#',$content,$matches,PREG_SET_ORDER)) {
				$locale = $matches[0][2];
				if (!file_exists("intl/$locale/LC_MESSAGES")) {
					mkdir("intl/$locale",0755);
					mkdir("intl/$locale/LC_MESSAGES",0755);
				}
				$fp = fopen("intl/$locale/LC_MESSAGES/messages.po",'w');
				if(!$fp) {
					echo "\tERROR, Couldn't create output file!\n";
					continue;
				}
				$poheader=''
.'#
#
msgid ""
msgstr ""
"Project-Id-Version: Gregarius 0.5.5\n"
"Report-Msgid-Bugs-To: \n"
"POT-Creation-Date: \n"
"PO-Revision-Date: \n"
"Last-Translator: \n"
"Language-Team: \n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=utf-8\n"
"Content-Transfer-Encoding: 8bit\n"
';
				fwrite($fp,$poheader."\n");
			} else {
				echo "\tERROR, Unknown locale!\n";
				continue;
			}
			
			if (preg_match_all('#define\s*\([\'"](LBL_[^\'"]+)[\'"][^,]*,\s*[\'"](.+)[\'"]\s*\);#',$content,$matches,PREG_SET_ORDER)) {
				foreach($matches as $match) {
					if (defined($match[1])) {
						
						fwrite($fp,"#: $match[1]\n");
						$en=str_replace('"','\"',stripslashes(stripslashes(constant($match[1]))));
						$intl=str_replace('"','\"',stripslashes(stripslashes($match[2])));
						fwrite($fp,"msgid \"$en\"\n");
						fwrite($fp,"msgstr \"$intl\"\n\n");
					}
				}
				fwrite($fp,"#### END Automatic translation - ". date('r')." #\n");
				echo "\tdone!\n";
			} else {
				echo "\tERROR, couldn't extract labels\n";
			}
			if ($fp) {
				@fclose($fp);
			}
		}
	}
	
	function Convert($dir)
	{
		global $EXCLUDE;

		$files=array_keys(preg_find('/\.php$/',$dir,PREG_FIND_RECURSIVE | PREG_FIND_FULLPATH));
		foreach($files as  $file) {
			if (preg_match($EXCLUDE,$file)) {
				echo "Skiping $file\n";
				continue;
			}
			echo "Now handling $file ... ";
	
			$oldfile=$file;
			$newfile=$file.'.new';
			$old = fopen($oldfile, 'r');
			$new = fopen($newfile, 'w');
			if (! ($old && $new)){
				die("Failed opening $oldfile and $newfile\n");
			}
			while (!feof($old)) {
				fwrite($new, preg_replace_callback('#(LBL_[A-Z0-9_]+)#','__callback__', fgets($old)));
			}

			flush($new);
			fclose($old);
			fclose($new);

			rename($oldfile, $oldfile . '.old');
			rename($newfile, $oldfile);

			echo "\tdone!\n";
		}
	}

	
	/*
	 * Find files in a directory matching a pattern
	 *
	 *
	 * Paul Gregg <pgregg@pgregg.com>
	 * 20 March 2004,  Updated 20 April 2004
	 *
	 * Open Source Code:   If you use this code on your site for public
	 * access (i.e. on the Internet) then you must attribute the author and
	 * source web site: http://www.pgregg.com/projects/php/code/preg_find.phps
	 * Working example: http://www.pgregg.com/projects/php/code/preg_find_ex.phps
	 *
	 */
	
	define('PREG_FIND_RECURSIVE', 1);
	define('PREG_FIND_DIRMATCH', 2);
	define('PREG_FIND_FULLPATH', 4);
	define('PREG_FIND_NEGATE', 8);
	define('PREG_FIND_DIRONLY', 16);
	define('PREG_FIND_RETURNASSOC', 32);
	
	// PREG_FIND_RECURSIVE   - go into subdirectorys looking for more files
	// PREG_FIND_DIRMATCH    - return directorys that match the pattern also
	// PREG_FIND_DIRONLY     - return only directorys that match the pattern (no files)
	// PREG_FIND_FULLPATH    - search for the pattern in the full path (dir+file)
	// PREG_FIND_NEGATE      - return files that don't match the pattern
	// PREG_FIND_RETURNASSOC - Instead of just returning a plain array of matches,
	//                         return an associative array with file stats
	// to use more than one simply seperate them with a | character
	
	
	
	// Search for files matching $pattern in $start_dir.
	// if args contains PREG_FIND_RECURSIVE then do a recursive search
	// return value is an associative array, the key of which is the path/file
	// and the value is the stat of the file.
	Function preg_find($pattern, $start_dir='.', $args=NULL) {
	
	
	  $files_matched = array();
	
	  $fh = opendir($start_dir);
	
	  while (($file = readdir($fh)) !== false) {
		if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
		$filepath = $start_dir . '/' . $file;
		if (preg_match($pattern,
					   ($args & PREG_FIND_FULLPATH) ? $filepath : $file)) {
		  $doadd =    is_file($filepath)
				   || (is_dir($filepath) && ($args & PREG_FIND_DIRMATCH))
				   || (is_dir($filepath) && ($args & PREG_FIND_DIRONLY));
		  if ($args & PREG_FIND_DIRONLY && $doadd && !is_dir($filepath)) $doadd = false;
		  if ($args & PREG_FIND_NEGATE) $doadd = !$doadd;
		  if ($doadd) {
			if ($args & PREG_FIND_RETURNASSOC) { // return more than just the filenames
			  $fileres = array();
			  if (function_exists('stat')) {
				$fileres['stat'] = stat($filepath);
				$fileres['du'] = $fileres['stat']['blocks'] * 512;
			  }
			  if (function_exists('fileowner')) $fileres['uid'] = fileowner($filepath);
			  if (function_exists('filegroup')) $fileres['gid'] = filegroup($filepath);
			  if (function_exists('filetype')) $fileres['filetype'] = filetype($filepath);
			  if (function_exists('mime_content_type')) $fileres['mimetype'] = mime_content_type($filepath);
			  if (function_exists('dirname')) $fileres['dirname'] = dirname($filepath);
			  if (function_exists('basename')) $fileres['basename'] = basename($filepath);
			  if (isset($fileres['uid']) && function_exists('posix_getpwuid ')) $fileres['owner'] = posix_getpwuid ($fileres['uid']);
			  $files_matched[$filepath] = $fileres;
			} else
			  array_push($files_matched, $filepath);
		  }
		}
		if ( is_dir($filepath) && ($args & PREG_FIND_RECURSIVE) ) {
		  $files_matched = array_merge($files_matched,
									   preg_find($pattern, $filepath, $args));
		}
	  }
	
	  closedir($fh); 
	  return $files_matched;
	
	}
?>
