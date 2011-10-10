<?php

/* Galleries control */
class Galleries extends Database {
	
	/* Returns all galleries as Array */
	function getAll(){
		$database = Database::readDB( true );
		return $database['galleries'];
	}
	
	/* Returns gallery as Array by id */
	function getById( $id ){ //id as int
		$galleries = self::getAll();
		return $galleries[(string)$id];
	}
	
	/* Creates and append new gallery to DB */
	/* $data is Array("name" => string, "folder" => string, "visible" => 'true'/'false'); */
	/* If $id passed - edits existing gallery */
	function Modify( $data, $id = false ){
		$database = Database::readDB( true );
		if( $id ) {
			$oldname = $database['galleries'][(string)$id]['folder']; //old folder name
			if( $oldname != $data['folder'] ) { //if folder name changed
				if( !file_exists( ROOT.'galleries/'.$data['folder'] ) ) { //if no existing folder has given name already
					rename( ROOT.'galleries/'.$oldname , ROOT.'galleries/'.$data['folder'] );
				} else {
					return false;
				}
			}
			$images = $database['galleries'][(string)$id]['images']; //saving images
			$database['galleries'][(string)$id] = $data;
			$database['galleries'][(string)$id]['images'] = $images; //writing images back
		} else {
			if( !file_exists( ROOT.'galleries/'.$data['folder'] ) ) {
				krsort( $database['galleries'] );
				$keys = array_keys($database['galleries']);
				$newId = (int)$keys[0] + 1;
				$database['galleries'][(string)$newId] = $data;
				$database['galleries'][(string)$newId]['images'] = Array();
				mkdir( ROOT.'galleries/'.$data['folder'] );
			} else {
				return false;	
			}
		}
		return Database::writeDB( $database );
	}
		
	/* Delete gallery */
	function Delete( $galleryid ){ //id of static to be deleted
		if( self::getById( $galleryid ) ) { //if gallery exists
			$database = Database::readDB( true );
			$folder = ROOT.'galleries/'.$database['galleries'][$galleryid]['folder'];
			$mask = $folder.'/*.*'; //all files except '.' and '..'
			if( ( $files = scandir( $folder ) ) && ( count( $files ) > 2 ) ) { //if any files exists delete them
				array_map( "unlink", glob( $mask ) );
			}
			if( rmdir( $folder ) ){ //if folder deleted
				unset($database['galleries'][$galleryid]);
				Utilities::renewArtworksCount(); //recalculating artworks count
				return Database::writeDB( $database );
			} else {
				return false;
			}
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
		foreach( $galleries as $id => $gallery ) {
			if( $gallery['visible'] == 'true' ){
				$result[$id] = $gallery;
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
	function modifyArtwork( $galleryid , $data ){
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

	/* Perform artwork vote */
	function Vote( $galleryid , $filename , $mode ){ //$mode: 'good', 'bad'
		$database = Database::readDB( true );
		$votes = (int)$database['galleries'][$galleryid]['images'][$filename][$mode];
		$database['galleries'][$galleryid]['images'][$filename][$mode] = $votes + 1;
		return Database::writeDB( $database );
	}

}

?>