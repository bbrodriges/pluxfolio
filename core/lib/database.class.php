<?php

class Database {
	
	/* Opens DB file and returns all data */
	public function readDB( $database, $type = false ) { //if TRUE - Array, if FALSE - Object.
		return json_decode( file_get_contents( ROOT.'core/db/'.$database.'.json' ) , $type );
	}
	
	/* Opens DB an wrtie changes */
	public function writeDB( $database, $data ) { //input is a whole DB with changes. Array or Object.
		$data = json_encode( (object) $data );
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
	
	public function sanitiseQuery( $data ) { //replace unexepatable chars
		return str_replace( '"' , '&quot;' , $data );
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
	
}

?>