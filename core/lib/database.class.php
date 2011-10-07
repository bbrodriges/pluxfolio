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
	
	/* Returns all user credentials */
	function returnUser() {
		$database = readDB();
		return $database->user;
	}
	
	/* Writes new user credentials */
	/* Input format: ("login" => "admin", "password" => "c4ca4238a0b923820dcc509a6f75849b") */
	function updateUser( $data ) { //user credentials as Array or Object
		$database = readDB(true);
		$database['user'] = (array) $data;
		return writeDB( $database );
	}
	
}

?>