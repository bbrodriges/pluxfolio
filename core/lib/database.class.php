<?php

define( 'DATABASE' , 'core/db/database.json' );

class Database {
	
	/* Opens DB file and returns all data */
	public function readDB( $type = false ) { //if TRUE - Array, if FALSE - Object.
		return json_decode( file_get_contents( DATABASE ) , $type );
	}
	
	/* Opens DB an wrtie changes */
	public function writeDB( $data ) { //input is a whole DB with changes. Array or Object.
		if(	$fp = fopen( DATABASE , 'w' ) ) {
			$data = json_encode( (object) $data );
			fwrite( $fp , $data );
			fclose( $fp );
			return true;
		} else {
			return false;
		}
	}
	
	public function sanitiseQuery( $data ) { //replace unexepatable chars
		return str_replace( '"' , '&quot;' , $data );
	}
	
	public function clearQuery( $data ) { //delete unexepatable chars
		return str_replace( '"' , '' , $data );
	}
	
}

?>