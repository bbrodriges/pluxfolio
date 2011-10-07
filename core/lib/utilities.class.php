<?php

include('database.class.php');

class Utilities extends Database {
	
/* ----------- USER SECTION ----------- */
	
	/* Returns all user credentials */
	function returnUser() {
		$database = readDB();
		return $database->user;
	}
	
	/* Writes new user credentials */
	function updateUser( $login , $password ) { //user credentials as Array or Object
		$database = readDB(true);
		$database['user']['login'] = clearQuery( $login );
		$database['user']['password'] = md5( $login );
		return writeDB( $database );
	}
	
/* ----------- END USER SECTION ----------- */

/* ----------- SITE SECTION ----------- */
	
	/* Writes site info */
	function writeSiteData( $type, $data ) { //(1) type: 'title', 'subtitle' etc... ; (2) Data to write
		$database = readDB();
		$database->site->address == sanitiseQuery( $newaddress );
		return writeDB( $database );
	}
	
	/* Writes site info */
	function readSiteData( $type ) { //type: 'title', 'subtitle' etc...
		$database = readDB(true);
		return $database['site'][$type];
	}
	
/* ----------- END SITE SECTION ----------- */

}

?>