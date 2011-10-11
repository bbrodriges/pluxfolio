<?php

/* THIS IS ONLY A MAJOR OVERVIEW. TOTALLY UNTESTED */

class Templates extends Mustashe {
	
	private $dictionary; //Contains array of words from lang files

	/* Function to be called on templates render */
	public function init(){
		
		$siteDB = Database::readDB( true );
		
		$pagetype = $_GET['pagetype']; //Contains page type from REQUEST_URI
		
		$themeFolder = ROOT.'themes/'.Utilities::readSiteData( 'theme' ).'/';
		
		$renderArray = Array(); //Contains all data to be passed to Mustache
		
		$languageFile = ROOT.'core/lang/'.Utilities::readSiteData( 'language' ).'.json';
		$dictionary = json_decode( file_get_contents( $languageFile ) , TRUE ); //opens dictionary
		
		/* Public site parts */
		$renderArray['title'] = $siteDB['site']['title'];
		$renderArray['subtitle'] = $siteDB['site']['subtitle'];
		$renderArray['artworkscounter'] = $siteDB['site']['totalartworks'];
		
		/* Admin site parts */
		
		/* Language part*/
		$renderArray['totalartworks'] = self::getTranslation( 'totalartworks' );
		
		/* Renders header, body and footer of page */
		echo Mustache::render( file_get_contents( $themeFolder.'header.tpl' ) , $renderArray );
		echo Mustache::render( file_get_contents( $themeFolder.$pagetype.'.tpl' ) , $renderArray );
		echo Mustache::render( file_get_contents( $themeFolder.'footer.tpl' ) , $renderArray );
			
	}
	
	/* Returns translate from dictionary */
	public function getTranslation( $id ) {
		return $dictionary[$id];
	}
	
}

?>