<?php

define( 'DATABASE' , ROOT.'core/db/database.json' );

class Database {
	
	/* Opens DB file and returns all data */
	public function readDB( $type = false ) { //if TRUE - Array, if FALSE - Object.
		return json_decode( file_get_contents( DATABASE ) , $type );
	}
	
	/* Opens DB an wrtie changes */
	public function writeDB( $data ) { //input is a whole DB with changes. Array or Object.
		$data = json_encode( (object) $data );
		if( self::checkDatabase( $data ) ) {
			if(	$fp = fopen( DATABASE , 'w' ) ) {
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
	public function checkDatabase( $database ) {
		json_decode($database);
		switch ( json_last_error() ) {
			case JSON_ERROR_NONE:
				self::backupDatabase();
				return true;
				die;
				break;
			default:
				return false;
				die;
				break;
		}
	}
	
	/* Makes backup of database */
	public function backupDatabase() {
		$backupFiles = glob( ROOT.'core/db/backups/database-*.json' );
		if( count( $backupFiles ) > 4 ) {
			sort( $backupFiles );
			unlink( $backupFiles[0] );
		}
		if( copy( ROOT.'core/db/database.json' , ROOT.'core/db/backups/database-'.time().'.json' ) ) {
			return true;
		} else {
			return 4; //error creating database backup
		}
	}
	
}

?>