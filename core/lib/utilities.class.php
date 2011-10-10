<?php

class Utilities extends Database {
	
/* ----------- USER SECTION ----------- */
	
	/* Returns all user credentials */
	public function returnUser() {
		$database = Database::readDB( true );
		return $database['user'];
	}
	
	/* Writes new user credentials */
	 public function updateUser( $login , $password ) { //user credentials as Array or Object
		$database = Database::readDB( true );
		$database['user']['login'] = Database::clearQuery( $login );
		$database['user']['password'] = md5( $password );
		return Database::writeDB( $database );
	}
	
/* ----------- END USER SECTION ----------- */

/* ----------- SITE SECTION ----------- */
	
	/* Writes site info */
	public function writeSiteData( $type, $data ) { //(1) type: 'title', 'subtitle' etc... ; (2) Data to write
		$database = Database::readDB( true );
		$database['site'][$type] = Database::sanitiseQuery( $data );
		return Database::writeDB( $database );
	}
	
	/* Reads site info */
	public function readSiteData( $type ) { //type: 'title', 'subtitle' etc...
		$database = Database::readDB( true );
		return $database['site'][$type];
	}
	
	/* Changes count of site's artworks  */
	public function modifyArtworksCount( $mode ) { //mode: 'increase', 'decrease'
		$count = (int) self::readSiteData( 'totalartworks' );
		if( $mode == 'increase' ) {
			$count++;
		} elseif ( $mode == 'decrease' && $count > 0 ) {
			$count--;
		} else {
			return false;	
		}
		return self::writeSiteData( 'totalartworks', (string) $count );
	}
	
	/* Compleatly recalculates count of site's artworks  */
	public function renewArtworksCount() { //mode: 'increase', 'decrease'
		$newcount = 0;
		$database = Database::readDB( true );
		if( count($database['galleries']) > 0 ){ //if any gallery exists
			foreach( $database['galleries'] as $gallery ){
				foreach( $gallery['images'] as $image ) {
					$newcount++;
				}
			}
		}
		return self::writeSiteData( 'totalartworks', (string) $newcount );
	}
	
/* ----------- END SITE SECTION ----------- */

}

?>