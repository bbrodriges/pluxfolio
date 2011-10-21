<?php

class Database {
	
	/* Opens DB file and returns all data */
	public function readDB( $database, $type = false ) { //if TRUE - Array, if FALSE - Object.
		return json_decode( file_get_contents( ROOT.'core/db/'.$database.'.json' ) , $type );
	}
	
	/* Opens DB an wrtie changes */
	/* $data must be array! */
	public function writeDB( $database, $data ) { //input is a whole DB with changes. Array or Object.
		$data = self::jsonEncode( $data );
		if( self::checkDatabase( $data ) ) {
			if(	$fp = fopen( ROOT.'core/db/'.$database.'.json' , 'w' ) ) {
				fwrite( $fp , $data );
				fclose( $fp );
				return true;
			} else {
				return 2; //error opening database file
			}
		} else {
			return 3; //failed to write database. bad structure
		}
	}
	
	public function clearQuery( $data ) { //delete unexepatable chars
		return str_replace( '"' , '' , $data );
	}

	/* Checks for database errors */
	public function checkDatabase( $data ) {
		json_decode( $data );
		switch ( json_last_error() ) {
			case JSON_ERROR_NONE:
				return true;
			default:
				return false;
		}
	}
	
	/* JSON encode with JSON_UNESCAPED_UNICODE */
	function jsonEncode($arr) {
        array_walk_recursive($arr, function (&$item, $key) { 
			if (is_string($item)) 
				$item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); 
			}
		);
        return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
	}
	
}

?>