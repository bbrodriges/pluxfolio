<?php
// This file contains all functions linked to engine language packs
	$files = scandir('../lang');
	 
	// Loop through each filename of scandir
	foreach ($files as $filename) {
	     if (strrpos($filename, ".php")) {
	       $langpack_list[substr($filename, 0, 5)] .= substr($filename, 0, 5);
	     }
        }
?>
