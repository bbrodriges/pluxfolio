<?php

/* THIS IS ONLY A MAJOR OVERVIEW. TOTALLY UNTESTED */

class Templates extends Mustashe {
	
	private $dictionary; //Contains array of words from lang files
	private $renderArray; //Contains all data to be passed to Mustache
	private $siteDB; //Contains whole DB
	private $pagetype; //Contains page type from REQUEST_URI

	/* Function to be called on templates render */
	public function init(){
		
		/* GATHERING NECESSARY SITE DATA */
		$siteDB = Database::readDB( true ); //Reads whole DB one time
		$themeFolder = ROOT.'themes/'.$siteDB['site']['theme'].'/'; //Contains current theme folder path
		$renderArray = Array();
		$dictionary = json_decode( file_get_contents( ROOT.'core/lang/'.$siteDB['site']['language'].'.json' ) , TRUE ); //opens dictionary
		$pagetype = $_GET['pagetype'];
		
		/* GATHERING GENERAL SITE DATA WITH CAN BE EMBEDED TO ANY PAGE */
		/* Data from DB */
		$renderArray['title'] = $siteDB['site']['title']; //Site title
		$renderArray['subtitle'] = $siteDB['site']['subtitle']; //Site subtitle
		$renderArray['artworkscounter'] = $siteDB['site']['totalartworks']; //Total artworks count
		
		/* Data from language files */
		$renderArray['totalartworks'] = self::getTranslation( 'totalartworks' ); //Artworks counter translation
		
		switch ( $pagetype ) {
			case 'article': //Article page. Get article by given pageid
				if( !self::complileArticle( $_GET['pageid'] ) ) { //Compilation error
					header('Location: ./error/404');
				}
				die;
				break;
			case 'gallery': //Gallery page. Get gallery by given pageid
				die;
				break;
			case 'static': //Static page. Get static by given pageid
				if( !self::complileStatic( $_GET['pageid'] ) ) { //Compilation error
					header('Location: ./error/404');
				}
				die;
				break;
			case 'tag': //Tag filtered articles. Get all articles by given tag
				die;
				break;
			case 'error': //Error page. Generate error page by given pageid, e.g. 403, 404 etc.
				die;
				break;
			case 'default': //Index page. Generate articles list
				$pagetype = 'index';
				die;
				break;
		}
		
		
		/* Renders header, body and footer of page */
		echo Mustache::render( file_get_contents( $themeFolder.'header.tpl' ) , $renderArray );
		echo Mustache::render( file_get_contents( $themeFolder.$pagetype.'.tpl' ) , $renderArray );
		echo Mustache::render( file_get_contents( $themeFolder.'footer.tpl' ) , $renderArray );
			
	}
	
	/* Returns translate from dictionary */
	private function getTranslation( $id ) {
		return $dictionary[$id];
	}
	
	/* Converts http and www into clickable links */
	private function makeLinks($text) {
		$text = preg_replace('%(((f|ht){1}tp://)[-a-zA-^Z0-9@:\%_\+.~#?&//=]+)%i',
		'<a href="\\1">\\1</a>', $text);
		$text = preg_replace('%([[:space:]()[{}])(www.[-a-zA-Z0-9@:\%_\+.~#?&//=]+)%i',
		'\\1<a href="http://\\2">\\2</a>', $text);
		return $text;
	}
	
/* ------------- COMPILATION PART ------------- */
	
	/* Compiles article */
	private function complileArticle( $articleid ) {
		if( isset( $siteDB['articles'][$articleid] ) && $siteDB['articles'][$articleid]['visible'] == 'true' ) { //Article exists and available for reading. Proceed compiling
		
			$renderArray['article_title'] = $siteDB['articles'][$articleid]['title'];
			$renderArray['article_pretext'] = self::makeLinks( $siteDB['articles'][$articleid]['pretext'] );
			$renderArray['article_text'] = self::makeLinks( $siteDB['articles'][$articleid]['text'] );
			$renderArray['article_tags'] = self::makeTags( $siteDB['articles'][$articleid]['tags'] );
			$renderArray['article_date'] = date( 'Y.m.d G:i' , $siteDB['articles'][$articleid]['date'] );
			$renderArray['article_author'] = $siteDB['articles'][$articleid]['author'];
			
			/* Translations */
			$renderArray['tags'] = getTranslation( 'tags' );
			
			return true;
		} else { //Article does not exists. Return false to redirect to error page
			return false;
		}
	}
	
		/* Compiles article tags */
		private function makeTags( $tags ) {
			$tagString = '';
			$tags = explode( ',' , $tags );
			foreach( $tags as $tag ){
				$tag = trim( $tag );
				$tagString .= ' <a href="./tag/'.$tag.'">'.$tag.'</a>,';
			}
			return substr( $tagString , 0 , -1 );
		}
	
	/* Compiles static */	
	private function complileStatic( $staticid ) {
		if( isset( $siteDB['statics'][$staticid] ) && $siteDB['statics'][$staticid]['visible'] == 'true' ) { //Static exists and available for reading. Proceed compiling
			$renderArray['static_title'] = $siteDB['statics'][$staticid]['title'];
			$renderArray['static_text'] = self::makeLinks( $siteDB['statics'][$staticid]['text'] );
			return true;
		} else { //Static does not exists. Return false to redirect to error page
			return false;
		}
	}
	
}

?>