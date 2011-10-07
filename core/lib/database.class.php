<?php

define('DATABASE', '../db/database.json'); //defining DB file

class Database {
	
	/* Opens DB file and returns all data */
	function readDB( $type = false ) { //if TRUE - Array, if FALSE - Object.
		return json_decode( file_get_contents( DATABASE ) , $type );
	}
	
	function writeDB( $data ) { //whole DB with changes. Array or Object.
		if(	$fp = fopen( DATABASE , 'w' ) ) {
			$data = json_encode( (object) $data );
			fwrite( $fp , $data );
			fclose( $fp );
			return true;
		} else {
			return false;
		}
	}
	
	function sanitiseQuery( $data ) { //replace unexepatable chars
		return str_replace( '"' , '&quot;' , $data );
	}
	
	function clearQuery( $data ) { //delete unexepatable chars
		return str_replace( '"' , '' , $data );
	}
	
}

?>