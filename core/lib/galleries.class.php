<?php

/* Galleries control */
class Galleries extends Database {
	
	/* Returns all galleries as Array */
	public function getAll(){
		$database = Database::readDB( true );
		return $database['galleries'];
	}
	
	/* Returns gallery as Array by id */
	public function getById( $id ){ //id as int
		$galleries = self::getAll();
		return $galleries[(string)$id];
	}
	
	/* Creates and append new gallery to DB */
	/* $data is Array("name" => string, "folder" => string, "visible" => 'true'/'false'); */
	/* If $id passed - edits existing gallery */
	public function Modify( $data, $id = false ){
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
	public function Delete( $galleryid ){ //id of static to be deleted
		if( self::getById( $galleryid ) ) { //if gallery exists
			$database = Database::readDB( true );
			$folder = ROOT.'galleries/'.$database['galleries'][$galleryid]['folder'];
			$mask = $folder.'/*.*'; //all files except '.' and '..'
			if( ( $files = scandir( $folder ) ) && ( count( $files ) > 2 ) ) { //if any files exists delete them
				array_map( "unlink", glob( $mask ) );
			}
			if( rmdir( $folder ) ){ //if folder deleted
				unset($database['galleries'][$galleryid]);
				if( Database::writeDB( $database ) ){
					Utilities::renewArtworksCount(); //recalculating artworks count
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/* Set visibility of gallery on and off */
	public function toggleVisiblity( $id, $state ){ //id as int, state as string 'true' or 'false'
		$database = Database::readDB( true );
		$database['galleries'][$id]['visible'] = $state;
		return Database::writeDB( $database );
	}
	
	/* Returns only visible statics */
	/* For more simple main menu generation */
	public function returnVisible(){
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
	public function modifyArtwork( $galleryid , $data ){
		$database = Database::readDB( true );
		$filename = $data['filename'];
		$increase = false;
		unset($data['filename']); // removing unnecessary data
		if( !isset( $database['galleries'][$galleryid]['images'][$filename] ) ){
			 $increase = true;//increases artworks counter if appending new artwork
			 self::makeThumb( ROOT.'galleries/'.$database['galleries'][$galleryid]['folder'].'/'.$filename ); //Create thumbnail
		}
		$database['galleries'][$galleryid]['images'][$filename] = $data;
		if( Database::writeDB( $database ) ) {
			if( $increase ) {
				Utilities::modifyArtworksCount( 'increase' );
			}
			return true;
		} else {
			return false;
		}
	}
	
	/* Delete specific artwork */
	public function removeArtwork( $galleryid , $filename ){
		$database = Database::readDB( true );
		$gallery = Galleries::getById( $galleryid );
		$file = ROOT.'galleries/'.$gallery['folder'].'/'.$filename;
		if( file_exists( $file ) ) {
			unlink( $file );
			@unlink( $file.'.tb' );
		}
		unset( $database['galleries'][$galleryid]['images'][$filename] );
		if( Database::writeDB( $database ) ) {
			Utilities::modifyArtworksCount( 'decrease' ); //decreases artworks counter
			return true;
		} else {
			return false;
		}
	}
	
	/* Returns all artworks in gallery */
	public function allArtworks( $galleryid ){
		$database = Database::readDB( true );
		return $database['galleries'][$galleryid]['images'];
	}
	
	/* Returns specific artwork from gallery */
	public function returnArtwork( $galleryid , $filename ){
		$database = Database::readDB( true );
		return $database['galleries'][$galleryid]['images'][$filename];
	}
	
	/* Returns artwork age in days */
	public function returnArtworkAge( $timestamp ){
		$unixages = time() - $timestamp; // calculating differense between now and upload time
		return floor( $unixages * 86400 ); //round to lesser number of days
	}

	/* Perform artwork vote */
	public function Vote( $galleryid , $filename , $mode ){ //$mode: 'good', 'bad'
		$database = Database::readDB( true );
		$votes = (int)$database['galleries'][$galleryid]['images'][$filename][$mode];
		$database['galleries'][$galleryid]['images'][$filename][$mode] = $votes + 1;
		return Database::writeDB( $database );
	}
	
	/* Uploads artwork to directory */
	/* $galleryid - id of parent gallery, $files - not needed */
	public function Upload( $galleryid ) {
		$uploaded = 0;
		$totalartworks = count($_FILES['img']['name']);
		$gallery = self::getById( $galleryid );
		
		foreach( $_FILES['img']['name'] as $id => $file) {
			$savepath = ROOT.'galleries/'.$gallery['folder'].'/';
			$name = basename( $_FILES['img']['name'][$id] );
			if( @move_uploaded_file( $_FILES['img']['tmp_name'][$id] , $savepath.$name ) ) {
				@chmod( $savepath.$name,0644 );
				$data = Array( "filename" => $name, "name" => '', "description" => '', "added" => time() );
				if( self::modifyArtwork( $galleryid , $data ) ){
					$uploaded++;
				}
			}
		}
		
		if( $uploaded == $totalartworks ) {
			return true;
		} else {
			return false;
		}
	}
	
	/* Rescans all galleries directories for artworks uploaded via FTP or other non-predictable way */
	public function Rescan() {
		$galleries = Galleries::getAll();
		$galeriesFolder = ROOT.'galleries/';
		$acceptedFiles = Array( 'jpg' , 'gif' , 'png' , 'jpeg' ); //Acceptable file types
		foreach( $galleries as $galleryid => $gallery ) {
			$galleryFiles = glob( $galeriesFolder.$gallery['folder'].'/*.*' ); //all files except '..' and '.'
			if( count( $galleryFiles ) > count( $gallery['images'] ) ) { //if files in folder more than files in gallery DB
				foreach( $galleryFiles as $file ) {
					$fileInfo = pathinfo( $galeriesFolder.$gallery['folder'].$file );
					if( in_array( $fileInfo['extension'] , $acceptedFiles ) && !in_array( $file , $gallery['images'] ) ) { //if acceptable file type and not in DB
						$data = Array( "filename" => $fileInfo['basename'], "name" => '', "description" => '', "added" => (string)filemtime( $file ) );
						self::modifyArtwork( $galleryid , $data );
					}
				}
			} elseif( count( $galleryFiles ) < count( $gallery['images'] ) ) { //if files in folder less than files in gallery DB
				foreach( $gallery['images'] as $filename => $image ) {
					if( !in_array( $galeriesFolder.$gallery['folder'].'/'.$filename , $galleryFiles ) ) { //if DB record has no existing image
						self::removeArtwork( $galleryid , $filename );
					}
				}
			}
		}
	}
	
	/* Makes thumb from given image */
	/* $filename - full path to image */
	/* Optional integers: $x - px from left , $y - px from top , $resampledWidth - new width , $resampledHeight - new height. If optional given crop part of image */
	public function makeThumb( $filename, $x = false, $y = false, $resampledWidth = false , $resampledHeight = false ) {
		list( $width_orig , $height_orig , $type ) = getimagesize( $filename ); //Gathering source image info
		list( $width , $height ) = explode( 'x' , Utilities::readSiteData( 'thumbsize' ) ); //Gathering thumb info
		
		$imageTemplate = imagecreatetruecolor( $width , $height ); //Creating image template with width and height
		if($type == 2) //if jpg
			$image = imagecreatefromjpeg( $filename );
		elseif($type == 3) //if png
			$image = imagecreatefrompng( $filename );
		elseif($type == 1) //if gif
			$image = imagecreatefromgif( $filename );	

		if( $x !== false && $y !== false && $resampledWidth !== false && $resampledHeight !== false ) { //if axis given use imagecopy to get part of image
			imagecopyresampled( $imageTemplate, $image, 0, 0, $x, $y, $width, $height, $resampledWidth, $resampledHeight );
		} else { //if no axis given just resize whole image
			imagecopyresized( $imageTemplate, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig );
		}
		
		if($type == 2)
			imagejpeg( $imageTemplate , $filename.'.tb' , 75 ); //third parameter is quality of jpg
		elseif($type == 3)
			imagepng( $imageTemplate , $filename.'.tb' );
		elseif ($type==1) 
			imagegif( $imageTemplate , $filename.'.tb' );
		@chmod( $filename.'.tb', 0644 );
	}

}

?>