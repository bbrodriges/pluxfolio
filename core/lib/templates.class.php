<?php

/* THIS IS ONLY A MAJOR OVERVIEW. TOTALLY UNTESTED */

class Templates extends Mustache {
	
	public $dictionary; //Contains array of words from lang files
	public $renderArray; //Contains all data to be passed to Mustache
	public $siteDB; //Contains whole DB

	/* Function to be called on templates render */
	public function render(){
		
		/* GATHERING NECESSARY SITE DATA */
		$this->siteDB = Database::readDB( true ); //Reads whole DB one time
		$this->renderArray = Array(); //Empty render array. Will contain all necessary mustache tags
		$this->dictionary = json_decode( file_get_contents( ROOT.'core/lang/'.$this->siteDB['site']['language'].'.json' ) , TRUE ); //opens dictionary
		$themeFolder = ROOT.'themes/'.$this->siteDB['site']['theme'].'/'; //Contains current theme folder path
		
		if( !empty( $_GET ) ) {
			$pagetype = $_GET['pagetype'];
		} else {
			$pagetype = 'index'; //if no page type given - we are on index page
		}
		
		/* GATHERING GENERAL SITE DATA WITH CAN BE EMBEDED TO ANY PAGE */
		/* Data from DB */
		$this->renderArray['title'] = $this->siteDB['site']['title']; //Site title
		$this->renderArray['subtitle'] = $this->siteDB['site']['subtitle']; //Site subtitle
		$this->renderArray['artworkscounter'] = $this->siteDB['site']['totalartworks']; //Total artworks count
		$this->renderArray['mainmenu'] = $this->compileMainMenu(); //Site main menu 
		$this->renderArray['language'] = $this->siteDB['site']['language']; //Site language
		$this->renderArray['version'] = $this->siteDB['site']['version']; //Site language
		
		/* Data from language files */
		$this->renderArray['totalartworks'] = $this->getTranslation( 'totalartworks' ); //Artworks counter translation
		$this->renderArray['footer'] = $this->getTranslation( 'footer' ); //Site footer translation
		
		switch ( $pagetype ) {
			case 'article': //Article page. Get article by given pageid
				if( !$this->complileArticle( $_GET['pageid'] ) ) { //Compilation error
					header('Location: '.$this->siteDB['site']['address'].'/error/404');
				}
				break;
			case 'gallery': //Gallery page. Get gallery by given pageid
				die;
				break;
			case 'static': //Static page. Get static by given pageid
				if( !$this->complileStatic( $_GET['pageid'] ) ) { //Compilation error
					header('Location: '.$this->siteDB['site']['address'].'/error/404');
				}
				break;
			case 'tag': //Tag filtered articles. Get all articles by given tag
				break;
			case 'error': //Error page. Generate error page by given pageid, e.g. 403, 404 etc.
				break;
		}
		
		/* Renders header, body and footer of page */
		$renderer = new Mustache;
		echo $renderer->render( file_get_contents( $themeFolder.'header.tpl' ) , $this->renderArray )."\n";
		echo $renderer->render( file_get_contents( $themeFolder.$pagetype.'.tpl' ) , $this->renderArray )."\n";
		echo $renderer->render( file_get_contents( $themeFolder.'footer.tpl' ) , $this->renderArray );
			
	}
	
	/* Returns translate from dictionary */
	public function getTranslation( $id ) {
		if( isset( $this->dictionary[$id] ) && !empty( $this->dictionary[$id] ) ) {
			return $this->dictionary[$id];
		} else {
			return '%'.$id.'%';
		}
	}
	
	/* Converts http and www into clickable links */
	public function makeLinks($text) {
		$text = preg_replace('%(((f|ht){1}tp://)[-a-zA-^Z0-9@:\%_\+.~#?&//=]+)%i',
		'<a href="\\1">\\1</a>', $text);
		$text = preg_replace('%([[:space:]()[{}])(www.[-a-zA-Z0-9@:\%_\+.~#?&//=]+)%i',
		'\\1<a href="http://\\2">\\2</a>', $text);
		return $text;
	}
	
/* ------------- COMPILATION PART ------------- */

	/* Compiles main menu */
	public function compileMainMenu() {
		$mainMenu = '<li><a href="'.$this->siteDB['site']['address'].'">'.$this->getTranslation( 'blog' ).'</a></li>';
		$galleries = Galleries::returnVisible();
		foreach( $galleries as $galleryid => $gallery ) {
			$mainMenu .= '<li><a href="'.$this->siteDB['site']['address'].'/gallery/'.$galleryid.'">'.$gallery['name'].'</a></li>';
		}
		$statics = Statics::returnVisible();
		foreach( $statics as $staticid => $static ) {
			$mainMenu .= '<li><a href="'.$this->siteDB['site']['address'].'/static/'.$staticid.'">'.$static['title'].'</a></li>';
		}
		return $mainMenu;
	}
	
	/* Compiles article */
	public function complileArticle( $articleid ) {
		if( isset( $this->siteDB['articles'][$articleid] ) && $this->siteDB['articles'][$articleid]['visible'] == 'true' ) { //Article exists and available for reading. Proceed compiling
			$this->renderArray['article_title'] = $this->siteDB['articles'][$articleid]['title'];
			$this->renderArray['article_pretext'] = $this->makeLinks( $this->siteDB['articles'][$articleid]['pretext'] );
			$this->renderArray['article_text'] = $this->makeLinks( $this->siteDB['articles'][$articleid]['text'] );
			$this->renderArray['article_tags'] = $this->makeTags( $this->siteDB['articles'][$articleid]['tags'] );
			$this->renderArray['article_date'] = date( 'Y.m.d G:i' , $this->siteDB['articles'][$articleid]['date'] );
			$this->renderArray['article_author'] = $this->siteDB['articles'][$articleid]['author'];
			
			/* Translations */
			$this->renderArray['tags'] = $this->getTranslation( 'tags' );
			$this->renderArray['publishedby'] = $this->getTranslation( 'publishedby' );
			$this->renderArray['publishedat'] = $this->getTranslation( 'publishedat' );
			
			return true;
		} else { //Article does not exists. Return false to redirect to error page
			return false;
		}
	}
	
		/* Compiles article tags */
		public function makeTags( $tags ) {
			$tagString = '';
			$tags = explode( ',' , $tags );
			foreach( $tags as $tag ){
				$tag = trim( $tag );
				$tagString .= '<a href="'.$this->siteDB['site']['address'].'/tag/'.$tag.'">'.$tag.'</a>, ';
			}
			return substr( $tagString , 0 , -2 );
		}
	
	/* Compiles static */	
	public function complileStatic( $staticid ) {
		if( isset( $this->siteDB['statics'][$staticid] ) && $this->siteDB['statics'][$staticid]['visible'] == 'true' ) { //Static exists and available for reading. Proceed compiling
			$this->renderArray['static_title'] = $this->siteDB['statics'][$staticid]['title'];
			$this->renderArray['static_text'] = $this->makeLinks( $this->siteDB['statics'][$staticid]['text'] );
			return true;
		} else { //Static does not exists. Return false to redirect to error page
			return false;
		}
	}
	
}

?>