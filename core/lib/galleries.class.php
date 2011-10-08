<?php

include('database.class.php');
include('utilities.class.php');

/* Galleries control */
class Galleries extends Database {
	
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
		
	/* Delete gallery */
	function Delete( $galleryid ){ //id of static to be deleted
		$database = Database::readDB( true );
		$folder = '../../galleries/'.$databese['galleries'][$galleryid]['folder'];
		$mask = $folder.'/*.*';
		if( array_map( "unlink", glob( $mask ) ) && rmdir( $folder ){ //if all files and folder deleted
			unset($databese['galleries'][$galleryid]);
			Utilities::renewArtworksCount(); //recalculating artworks count
			return Database::writeDB( $database );
		} else {
			return false;
		}
	}
	
	/* Set visibility of gallery on and off */
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

}

/* Artworks in galleries control */
class Artworks extends Galleries {

	/* Append or modify artwork in gallery */
	/* $galleryid is id of artwork's parent gallery */
	/* $data is Array("filename" => string, "name" => string, "description" => string, "added" => timestamp); */
	function modifyArtworks( $galleryid , $data ){
		$database = Database::readDB( true );
		$filename = $data['filename'];
		unset($data['filename']); // removing unnecessary data
		if( !isset( $database['galleries'][$galleryid][$filename] ) ){
			Utilities::modifyArtworksCount( 'increase' ); //increases artworks counter if appending new artwork	
		}
		$database['galleries'][$galleryid][$filename] = $data;
		return Database::writeDB( $database );
	}
	
	/* Delete specific artwork */
	function removeArtwork( $galleryid , $filename ){
		$database = Database::readDB( true );
		$gallery = Galleries::getById( $galleryid );
		if( unlink( '../../galleries/'.$gallery['folder'].'/'.$filename ) ){
			unset( $database['galleries'][$galleryid]['images'][$filename] );
			Utilities::modifyArtworksCount( 'decrease' ); //decreases artworks counter
			return Database::writeDB( $database );
		} else {
			return false;	
		}
	}
	
	/* Returns all artworks in gallery */
	function allArtworks( $galleryid ){
		$database = Database::readDB( true );
		return $database['galleries'][$galleryid]['images'];
	}
	
	/* Returns specific artwork from gallery */
	function returnArtwork( $galleryid , $filename ){
		$database = Database::readDB( true );
		return $database['galleries'][$galleryid]['images'][$filename];
	}
	
	/* Returns artwork age in days */
	function returnArtworkAge( $timestamp ){
		$unixages = time() - $timestamp; // calculating differense between now and upload time
		return floor( $unixages * 86400 ); //round to lesser number of days
	}	

}

?>