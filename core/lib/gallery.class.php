<?php

/* Galleries control */
class CGallery extends Database {

	public function init() {
		$renderer = new Mustache;
		$galleryData = Utilities::getById( 'galleries' , $_GET['pageid'] );
		if( $galleryData && $galleryData['visible'] == 'true' ) {
			$renderArray['gallery_title'] = $galleryData['name'];
			$renderArray['gallery_text'] = $galleryData['text'];
			$galleryImages = $galleryData['images'];
			if( !empty( $galleryImages ) ) {
				foreach( $galleryImages as $imageFile => $imageData ) {
					$imageFilePath = Utilities::readSiteData( 'address' ).'/galleries/'.$galleryData['folder'].'/'.$imageFile;
					$thumbsList[] = array( 'image_thumb' => '<a href="'.$imageFilePath.'" rel="shadowbox" title="'.$imageData['description'].'"><img src="'.$imageFilePath.'.tb" alt="'.$imageData['name'].'"></a>', 'thumb_name' => $imageData['name'], 'thumb_description' => $imageData['description'] );
				}
			} else { //if no images - return empty images array
				$thumbsList[] = array();
			}
			$renderArray['thumbs_list'] = new ArrayIterator( $thumbsList );
			$renderer->renderPage( 'gallery' , $renderArray );
		} else { //no such gallery, or it is empty, or invisible
			$renderer->complileError();
		}
	}
	
	/* Creates and append new gallery to DB */
	/* $data is Array("name" => string, "folder" => string, "text" => string, "description" => string, "visible" => 'true'/'false'); */
	/* If $id passed - edits existing gallery. */
	public function Modify( $data, $id = false ){
		$database = Database::readDB( 'galleries' , true );
		$newId = Utilities::Translit( $data['name'] ); //transliterating title to use as static key
		if( $id ) {
			$oldname = $database[(string)$id]['folder']; //old folder name
			if( $oldname != $data['folder'] ) { //if folder name changed
				if( !file_exists( ROOT.'galleries/'.$data['folder'] ) ) { //if no existing folder has given name already
					rename( ROOT.'galleries/'.$oldname , ROOT.'galleries/'.$data['folder'] );
				} else {
					return 6;
				}
			}
			$images = $database[(string)$id]['images']; //saving images
			if( $id != $newId ) {
				unset( $database[(string)$id] ); //Deleting old gallery
				$database[(string)$newId] = $data; //Writing under new id
				$database[(string)$newId]['images'] = $images; //writing images back
			} else {
				$database[(string)$id] = $data;
				$database[(string)$id]['images'] = $images; //writing images back
			}
		} else {
			if( !file_exists( ROOT.'galleries/'.$data['folder'] ) ) {
				$database[(string)$newId] = $data;
				$database[(string)$newId]['images'] = Array();
				mkdir( ROOT.'galleries/'.$data['folder'] );
			} else {
				return 6;	
			}
		}
		return Database::writeDB( 'galleries' , $database );
	}
		
	/* Delete gallery */
	public function Delete( $galleryid ){ //id of static to be deleted
		if( Utilities::getById( 'galleries' , $galleryid ) ) { //if gallery exists
			$database = Database::readDB( 'galleries' , true );
			$folder = ROOT.'galleries/'.$database[$galleryid]['folder'];
			$mask = $folder.'/*.*'; //all files except '.' and '..'
			if( ( $files = scandir( $folder ) ) && ( count( $files ) > 2 ) ) { //if any files exists delete them
				array_map( "unlink", glob( $mask ) );
			}
			if( rmdir( $folder ) ){ //if folder deleted
				unset( $database[$galleryid] );
				if( Database::writeDB( 'galleries' , $database ) ){
					Utilities::renewArtworksCount(); //recalculating artworks count
					return true;
				} else {
					return 3;
				}
			} else {
				return 7;
			}
		} else {
			return 8;
		}
	}

}

class CCategory extends Database {

	public function init() {
		$renderer = new Mustache;
		$categoryData = Utilities::getById( 'categories' , $_GET['pageid'] );
		if( $categoryData && $categoryData['visible'] == 'true' ) {
			$renderArray['category_title'] = $categoryData['name'];
			$renderArray['category_description'] = $categoryData['description'];
			$visibleGalleries = Utilities::returnVisible( 'galleries' );
			if( !empty( $categoryData['galleries'] ) ) {
				foreach( $categoryData['galleries'] as $gallery ) {
					if( in_array( $gallery , array_keys( $visibleGalleries ) ) ) {
						$categoryGalleries[$gallery] = $visibleGalleries[$gallery];
					}
				}
			}
			if( !empty( $categoryGalleries ) ) {
				if( count( $categoryGalleries ) > 1 ) { //if more than one gallery - show list
					foreach( $categoryGalleries as $galleryid => $gallery ) {
						$galleryTags = array( 'gallery_name' => $gallery['name'] , 'gallery_description' => $gallery['description'], 'gallery_link' => Utilities::readSiteData( 'address' ).'/gallery/'.$galleryid );
						foreach( $gallery['images'] as $filename => $image ) {
							$firstImage['gallery_thumb'] = '<img src="'.Utilities::readSiteData( 'address' ).'/galleries/'.$gallery['folder'].'/'.$filename.'.tb">';
							$galleryTags = array_merge( $galleryTags , $firstImage );
							break;
						}
						$galleriesList[] = $galleryTags;
					}
				} else { //if one gallery - show it immediately
					foreach( $categoryGalleries as $galleryid => $gallery ) {
						header('Location: '.Utilities::readSiteData( 'address' ).'/gallery/'.$galleryid );
					}
				}
			} else { //if no galleries - return empty array
				$galleriesList[] = array();
			}
				$renderArray['galleries_list'] = new ArrayIterator( $galleriesList );
				$renderer->renderPage( 'category' , $renderArray );
			
		} else { //no such gallery, or it is empty, or invisible
			$renderer->complileError();
		}
	}
	
	/* Creates category or creates new one */
	/* $data = Array( "name" => string, "description" => string, "visible" => 'true'/'false' ); */
	public function Create( $data ){
		$database = Database::readDB( 'categories' , true );
		if( !isset( $database[(string)$newId] ) ) {
			$database[(string)$newId] = $data;
			$database[(string)$newId]['galleries'] = Array();
		} else {
			return 6;	
		}
		return Database::writeDB( 'categories' , $database );
	}
	
	/* Adds or removes */
	/* $categoryid - id of category, $action - 'add'/'remove', $galleryid - id of gallery */
	public function modifyGallery( $categoryid, $action, $galleryid ){
		$database = Database::readDB( 'categories' , true );
		switch ($action) {
			case 'add':
				if( !isset( $database[$categoryid]['galleries'][$galleryid] ) ) { //if gallery is not presented
					array_push( $database[$categoryid]['galleries'] , $galleryid );
				} else {
					return 1;
				}
				break;
			case 'remove':
				unset( $database[$categoryid]['galleries'][$galleryid] );
				break;
		}
		return Database::writeDB( 'categories' , $database );
	}
}

/* Artworks in galleries control */
class Artwork extends CGallery {

	/* Append or modify artwork in gallery */
	/* $galleryid is id of artwork's parent gallery */
	/* $data is Array("filename" => string, "name" => string, "description" => string, "added" => timestamp); */
	public function modifyArtwork( $galleryid , $data ){
		$database = Database::readDB( 'galleries' , true );
		$filename = $data['filename'];
		$increase = false;
		unset($data['filename']); // removing unnecessary data
		if( !isset( $database[$galleryid]['images'][$filename] ) ){
			 $increase = true;//increases artworks counter if appending new artwork
			 self::makeThumb( ROOT.'galleries/'.$database[$galleryid]['folder'].'/'.$filename ); //Create thumbnail
			 self::rotateLatestArtworks( array( 'filename' => $filename , 'folder' => $database[$galleryid]['folder'] , 'gallery' => $database[$galleryid]['name'] , 'name' => $data['filename'] ) );
		}
		$database[$galleryid]['images'][$filename] = $data;
		if( Database::writeDB( 'galleries' , $database ) ) {
			if( $increase ) {
				Utilities::modifyArtworksCount( 'increase' );
			}
			return true;
		} else {
			return 3;
		}
	}
	
	/* Delete specific artwork */
	public function removeArtwork( $galleryid , $filename ){
		$database = Database::readDB( 'galleries' , true );
		$gallery = $database[$galleryid];
		$file = ROOT.'galleries/'.$gallery['folder'].'/'.$filename;
		if( file_exists( $file ) ) {
			unlink( $file );
		}
		if( file_exists( $file.'.tb' ) ) {
			unlink( $file.'.tb' );
		}
		unset( $database[$galleryid]['images'][$filename] );
		if( Database::writeDB( 'galleries' , $database ) ) {
			Utilities::modifyArtworksCount( 'decrease' ); //decreases artworks counter
			return true;
		} else {
			return 3;
		}
	}
	
	/* Returns all artworks in gallery */
	public function allArtworks( $galleryid ){
		$database = Database::readDB( 'galleries' , true );
		return $database[$galleryid]['images'];
	}
	
	/* Returns specific artwork from gallery */
	public function returnArtwork( $galleryid , $filename ){
		$database = Database::readDB( 'galleries' , true );
		return $database[$galleryid]['images'][$filename];
	}
	
	/* Returns artwork age in days */
	public function returnArtworkAge( $timestamp ){
		$unixages = time() - $timestamp; // calculating differense between now and upload time
		return floor( $unixages * 86400 ); //round to lesser number of days
	}

	/* Perform artwork vote */
	public function Vote( $galleryid , $filename , $mode ){ //$mode: 'good', 'bad'
		$database = Database::readDB( 'galleries' , true );
		$votes = (int)$database[$galleryid]['images'][$filename][$mode];
		$database[$galleryid]['images'][$filename][$mode] = (string)(++$votes);
		return Database::writeDB( 'galleries' , $database );
	}
	
	/* Uploads artwork to directory */
	/* $galleryid - id of parent gallery, $files - not needed */
	public function Upload( $galleryid ) {
		$uploaded = 0;
		$totalartworks = count($_FILES['img']['name']);
		$gallery = Utilities::getById( 'galleries' , $galleryid );
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
			return 9;
		}
	}
	
	/* Rescans all galleries directories for artworks uploaded/deleted via FTP or other non-predictable way */
	public function Rescan() {
		$galleries = Database::readDB( 'galleries' , true );
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
		switch( $type ) {
			case 1: //if gif
				$image = imagecreatefromgif( $filename );
				break;
			case 2: //if jpg
				$image = imagecreatefromjpeg( $filename );
				break;
			case 3: //if png
				$image = imagecreatefrompng( $filename );
				break;
		}

		if( $x !== false && $y !== false && $resampledWidth !== false && $resampledHeight !== false ) { //if axis given use imagecopy to get part of image
			if( !imagecopyresampled( $imageTemplate, $image, 0, 0, $x, $y, $width, $height, $resampledWidth, $resampledHeight ) ) {
				return 10;
			}
		} else { //if no axis given just resize whole image
			if( !imagecopyresized( $imageTemplate, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig ) ) {
				return 10;
			}
		}
		
		switch( $type ) {
			case 1: //if gif
				imagegif( $imageTemplate , $filename.'.tb' );
				break;
			case 2: //if jpg
				imagejpeg( $imageTemplate , $filename.'.tb' , 75 ); //third parameter is quality of jpg
				break;
			case 3: //if png
				imagepng( $imageTemplate , $filename.'.tb' );
				break;
		}
		chmod( $filename.'.tb', 0644 );
        return true;
	}
	
	/* Rotates 3 latest artworks */
	/* $artworkinfo = array( 'filename' => string , 'folder' => string , 'gallery' => string , 'name' => string ) */
	public function rotateLatestArtworks( $artworkinfo ) {
		$data = Database::readDB( 'site' , true );
		$data['latestartworks'] = array_slice( array_merge( $artworkinfo , $data['latestartworks'] ) , 0 , 3 );
		Database::writeDB( 'site' , $data );
	}

}

?>