<?php

include('database.class.php');

class Utilities extends Database {
	
/* ----------- USER SECTION ----------- */
	
	/* Returns all user credentials */
	function returnUser() {
		$database = Database::readDB( true );
		return $database['user'];
	}
	
	/* Writes new user credentials */
	function updateUser( $login , $password ) { //user credentials as Array or Object
		$database = Database::readDB( true );
		$database['user']['login'] = Database::clearQuery( $login );
		$database['user']['password'] = md5( $login );
		return Database::writeDB( $database );
	}
	
/* ----------- END USER SECTION ----------- */

/* ----------- SITE SECTION ----------- */
	
	/* Writes site info */
	function writeSiteData( $type, $data ) { //(1) type: 'title', 'subtitle' etc... ; (2) Data to write
		$database = Database::readDB( true );
		$database['site'][$type] == Database::sanitiseQuery( $data );
		return Database::writeDB( $database );
	}
	
	/* Writes site info */
	function readSiteData( $type ) { //type: 'title', 'subtitle' etc...
		$database = Database::readDB( true );
		return $database['site'][$type];
	}
	
	/* Changes count of site's artworks  */
	function modifyArtworksCount( $mode ) { //mode: 'increase', 'decrease'
		$count = (int) readSiteData( 'totalartworks' );
		if( $mode == 'increase' ) {
			$count++;
		} else {
			$count--;
		}
		return writeSiteData( 'totalartworks', (string) $count );
	}
	
	/* Compleatly recalculates count of site's artworks  */
	function renewArtworksCount() { //mode: 'increase', 'decrease'
		$newcount = 0;
		$database = Database::readDB( true );
		foreach( $database['galleries'] as $gallery ){
			foreach( $gallery['images'] as $image ) {
				$newcount++;
			}
		}
		return writeSiteData( 'totalartworks', (string) $count );
	}
	
/* ----------- END SITE SECTION ----------- */

}

?>