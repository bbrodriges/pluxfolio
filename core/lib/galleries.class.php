<?php

include('database.class.php');

class Galleries extends Database {
	
/* ----------- GALLERY SECTION ----------- */
	
	/* Returns all galleries as Array */
	function getAll(){
		$database = Database::readDB( true );
		return $databese['galleries'];
	}
	
	/* Returns gallery as Array by id */
	function getById( $id ){ //id as int
		$galleries = self::getAll();
		return $galleries[$id];
	}
		
	/* Delete gallery from DB */
	function Delete( $id ){ //id of static to be deleted
		$database = Database::readDB( true );
		unset($databese['galleries'][$id]);
		return Database::writeDB( $database );
	}
	
	/* Set visibility of static on and off */
	function toggleVisiblity( $id, $state ){ //id as int, state as string 'true' or 'false'
		$database = Database::readDB( true );
		$database['galleries'][$id]['visible'] = $state;
		return Database::writeDB( $database );
	}
	
	/* Returns only visible statics */
	/* For more simple main menu generation */
	function returnVisible(){
		$galleries = self::getAll();
		$result = Array();
		foreach( $galleries as $gallery ) {
			if( $gallery['visible'] == 'true' ){
				$result[] = $gallery;
			}
		}
		return $result;
	}
	
/* ----------- END GALLERY SECTION ----------- */

/* ----------- ARTWORKS SECTION ----------- */

	/* Append new static to DB */
	/* $galleryid is id of artwork's parent gallery */
	/* $data is Array("filename" => string, "name" => string, "description" => string, "added" => timestamp); */
	function modifyArtwork( $galleryid , $data ){
		$database = Database::readDB( true );
		$filename = $data['filename'];
		unset($data['filename']); // removing unnecessary data
		$database['galleries'][$galleryid][$filename] = $data;
		return Database::writeDB( $database );
	}

/* ----------- END ARTWORKS SECTION ----------- */

}

?>