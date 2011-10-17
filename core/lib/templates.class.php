<?php

/* THIS IS ONLY A MAJOR OVERVIEW. TOTALLY UNTESTED */

class Templates extends Mustache {
	
	public $renderArray; //Contains all data to be passed to Mustache
	public $siteDB; //Contains whole DB

	/* Function to be called on templates render */
	public function render(){
		
		/* GATHERING NECESSARY SITE DATA */
		$this->siteDB = Database::readDB( true ); //Reads whole DB one time
		$this->renderArray = Array(); //Empty render array. Will contain all necessary mustache tags
		$themeFolder = ROOT.'themes/'.$this->siteDB['site']['theme'].'/'; //Contains current theme folder path
		
		if( !empty( $_GET ) ) {
			$pagetype = $_GET['pagetype'];
		} else {
			$pagetype = 'index'; //if no page type given - we are on index page
		}
		
		/* GATHERING GENERAL SITE DATA WITH CAN BE EMBEDED TO ANY PAGE */
		/* Data from DB */
		$this->renderArray['homepage'] = $this->siteDB['site']['address']; //Site index page
		$this->renderArray['title'] = $this->siteDB['site']['title']; //Site title
		$this->renderArray['subtitle'] = $this->siteDB['site']['subtitle']; //Site subtitle
		$this->renderArray['artworkscounter'] = $this->siteDB['site']['totalartworks']; //Total artworks count
		$this->renderArray['mainmenu'] = $this->compileMainMenu(); //Site main menu 
		$this->renderArray['language'] = $this->siteDB['site']['language']; //Site language
		$this->renderArray['version'] = $this->siteDB['site']['version']; //Site language
		
		/* Data from language files */
		$this->renderArray['totalartworks'] = Utilities::getTranslation( 'totalartworks' ); //Artworks counter translation
		$this->renderArray['footer'] = Utilities::getTranslation( 'footer' ); //Site footer translation
		
		switch ( $pagetype ) {
			case 'article': //Article page. Get article by given pageid
				if( !$this->complileArticle( $_GET['pageid'] ) ) { //Compilation error
					$this->complileError();
					$pagetype = 'error';
				}
				break;
			case 'gallery': //Gallery page. Get gallery by given pageid
				if( !$this->complileGallery( $_GET['pageid'] ) ) { //Compilation error
					$this->complileError();
					$pagetype = 'error';
				}
				break;
			case 'static': //Static page. Get static by given pageid
				if( !$this->complileStatic( $_GET['pageid'] ) ) { //Compilation error
					$this->complileError();
					$pagetype = 'error';
				}
				break;
			case 'tag': //Tag filtered articles. Get all articles by given tag
				$pagetype = 'index'; //use index page template
				$this->renderArray['articles_list'] = $this->itterateArticlesByTag( $_GET['pageid'] );
				break;
			case 'index': //Index page. Ganerate articles list
				$this->renderArray['articles_list'] = $this->itterateArticles( '1' );
				$totalPages = ceil( count( Articles::returnVisible() ) / $this->siteDB['site']['articlesperpage'] );
				$this->compilePagination( '1' , $totalPages );
				break;
			case 'page': //if user opens next page of blog
				if( (int)$_GET['pageid'] < 2 ) {
					header('Location: '.$this->siteDB['site']['address'].'/');
				} else {
					$totalPages = ceil( count( Articles::returnVisible() ) / $this->siteDB['site']['articlesperpage'] );
					if( $_GET['pageid'] <= $totalPages ) { //if pageid integer and it is less or equal numbers of total pages
						$pagetype = 'index'; //use index page template
						$this->compilePagination( $_GET['pageid'] , $totalPages );
						$this->renderArray['articles_list'] = $this->itterateArticles( $_GET['pageid'] );
					} else { //return error page
						$this->complileError();
						$pagetype = 'error';
					}
				}
				break;
			default: //If none of this matched - show error page
				$this->complileError();
				$pagetype = 'error';
				break;
		}
		
		/* Renders header, body and footer of page */
		$renderer = new Mustache;
		echo $renderer->render( file_get_contents( $themeFolder.'header.tpl' ) , $this->renderArray )."\n";
		echo $renderer->render( file_get_contents( $themeFolder.$pagetype.'.tpl' ) , $this->renderArray )."\n";
		echo $renderer->render( file_get_contents( $themeFolder.'footer.tpl' ) , $this->renderArray );
			
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
		$mainMenu = '<li><a href="'.$this->siteDB['site']['address'].'">'.Utilities::getTranslation( 'blog' ).'</a></li>';
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
			$this->renderArray['article_link'] = $this->siteDB['site']['address'].'/article/'.$articleid;
			
			/* Translations */
			$this->renderArray['tags'] = Utilities::getTranslation( 'tags' );
			$this->renderArray['publishedby'] = Utilities::getTranslation( 'publishedby' );
			$this->renderArray['publishedat'] = Utilities::getTranslation( 'publishedat' );
			
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
	
	/* Compiles error page */
	public function complileError() {
		$this->renderArray['error_code'] = '404';
		$this->renderArray['error_title'] = Utilities::getTranslation( 'errortitle' );
		$this->renderArray['error_text'] = Utilities::getTranslation( 'errortext' );
	}
	
	/* Compiles pagination */
	function compilePagination( $page, $totalpages ) {
		if( $totalpages > 1 ) {
			switch ( $totalpages ) {
				case 2:
					if( $page == 1 ) {
						$pagination = '<li class="current">'.$page.'</li><li><a href="'.$this->siteDB['site']['address'].'/page/'.$totalpages.'">'.$totalpages.'</a></li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page+1).'">'.Utilities::getTranslation( 'nextpage' ).'</a> >></li>';
					} else {
						$pagination = '<li><< <a href="'.$this->siteDB['site']['address'].'/page/'.($page-1).'">'.Utilities::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$totalpages.'</li>';
					}
					break;
				default:
					if( $page > 1 && $page < $totalpages ) {
						$pagination = '<li><< <a href="'.$this->siteDB['site']['address'].'/page/'.($page-1).'">'.Utilities::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$page.'</li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page+1).'">'. ( $page+1 ) .'</a></li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page+1).'">'.Utilities::getTranslation( 'nextpage' ).'</a> >></li>';
					} elseif( $page == 1 ) {
						$pagination = '<li class="current">'.$page.'</li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page+1).'">'.( $page+1 ).'</a></li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page+2).'">'. ( $page+2 ) .'</a></li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page+1).'">'.Utilities::getTranslation( 'nextpage' ).'</a> >></li>';
					} elseif( $page == $totalpages )  {
						$pagination = '<li><< <a href="'.$this->siteDB['site']['address'].'/page/'.($page-1).'">'.Utilities::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page-2).'">'.( $page-2 ).'</a></li><li><a href="'.$this->siteDB['site']['address'].'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$page.'</li>';
					}
					break;
			}
		} else {
			$pagination = '';
		}
		$this->renderArray['pagination'] = $pagination;
	}
	
	/* Itterates through all visible articles and return result to mustache tags */
	public function itterateArticles( $pageNumber ) {
		$articlesArray[] = array('article_title' => Utilities::getTranslation( 'noarticles' ) );
		if( !empty( $this->siteDB['articles'] ) ) { //At least one article exists
			$articleKeys = array_keys( Articles::returnVisible() ); //get all keys of visible articles
			$articleKeys = array_slice( $articleKeys, ($pageNumber-1)*$this->siteDB['site']['articlesperpage'], $this->siteDB['site']['articlesperpage'] ); //slice articles based on page number
			if( !empty( $articleKeys ) ) {
				$articlesArray = Array();
			}
			foreach( $articleKeys as $articleKey ) {
				$articlesArray[] = array(
						'article_title' => $this->siteDB['articles'][$articleKey]['title'],
						'article_pretext' => $this->siteDB['articles'][$articleKey]['pretext'],
						'article_text' => $this->siteDB['articles'][$articleKey]['text'],
						'article_tags' => $this->makeTags( $this->siteDB['articles'][$articleKey]['tags'] ),
						'article_date' => date( 'Y.m.d G:i' , $this->siteDB['articles'][$articleKey]['date'] ),
						'article_author' => $this->siteDB['articles'][$articleKey]['author'],
						'tags' => Utilities::getTranslation( 'tags' ),
						'publishedby' => Utilities::getTranslation( 'publishedby' ),
						'publishedat' => Utilities::getTranslation( 'publishedat' ),
						'article_link' => $this->siteDB['site']['address'].'/article/'.$articleKey,
						'more' => Utilities::getTranslation( 'more' )
					);
			}
		}
		return new ArrayIterator( $articlesArray );
	}
	
	/* Itterates through all visible articles and return result to mustache tags */
	public function itterateArticlesByTag( $tag ) {
		$articlesArray[] = array('article_title' => Utilities::getTranslation( 'noarticleswithtag' ) );
		if( !empty( $this->siteDB['articles'] ) ) { //At least one article exists
			$articleKeys = array_keys( Articles::returnWithTag( $tag ) ); //get all keys of visible articles
			if( !empty( $articleKeys ) ) {
				$articlesArray = Array();
			}
			foreach( $articleKeys as $articleKey ) {
				$articlesArray[] = array(
						'article_title' => $this->siteDB['articles'][$articleKey]['title'],
						'article_pretext' => $this->siteDB['articles'][$articleKey]['pretext'],
						'article_text' => $this->siteDB['articles'][$articleKey]['text'],
						'article_tags' => $this->makeTags( $this->siteDB['articles'][$articleKey]['tags'] ),
						'article_date' => date( 'Y.m.d G:i' , $this->siteDB['articles'][$articleKey]['date'] ),
						'article_author' => $this->siteDB['articles'][$articleKey]['author'],
						'tags' => Utilities::getTranslation( 'tags' ),
						'publishedby' => Utilities::getTranslation( 'publishedby' ),
						'publishedat' => Utilities::getTranslation( 'publishedat' ),
						'article_link' => $this->siteDB['site']['address'].'/article/'.$articleKey,
						'more' => Utilities::getTranslation( 'more' )
					);
			}
		}
		return new ArrayIterator( $articlesArray );
	}
	
	public function complileGallery( $galleryid ) {
		if( isset( $this->siteDB['galleries'][$galleryid] ) && !empty( $this->siteDB['galleries'][$galleryid] ) && $this->siteDB['galleries'][$galleryid]['visible'] == 'true' ) {
			$this->renderArray['gallery_title'] = $this->siteDB['galleries'][$galleryid]['name'];
			$this->renderArray['gallery_text'] = $this->siteDB['galleries'][$galleryid]['text'];
			$galleryImages = $this->siteDB['galleries'][$galleryid]['images'];
			$thumbsList = '';
			$firstImage = '';
			foreach( $galleryImages as $imageFile => $imageData ) {
				if( empty( $firstImage ) ) {
					$firstImage = $this->siteDB['site']['address'].'/galleries/'.$galleryid.'/'.$imageFile;
				}
				$imageFilePath = $this->siteDB['site']['address'].'/galleries/'.$galleryid.'/'.$imageFile;
				$thumbsList .= '<li><a href="#"><img src="'.$imageFilePath.'.tb" data-large="'.$imageFilePath.'" alt="'.$imageData['name'].'" data-description="'.$imageData['description'].'"></a></li>';
			}
			$this->renderArray['gallery_thumbs'] = '<div class="rg-thumbs"><div class="es-carousel-wrapper"><div class="es-nav"><span class="es-nav-prev">'.Utilities::getTranslation( 'prev' ).'</span><span class="es-nav-next">'.Utilities::getTranslation( 'next' ).'</span></div><div class="es-carousel"><ul>'.$thumbsList.'</ul></div></div></div>';
			$this->renderArray['gallery_imageview'] = '<div class="rg-image-wrapper"><div class="rg-image-nav"><a href="#" class="rg-image-nav-prev">'.Utilities::getTranslation( 'previmage' ).'</a><a href="#" class="rg-image-nav-next">'.Utilities::getTranslation( 'nextimage' ).'</a></div><div class="rg-image"><img src="'.$firstImage.'"></div><div class="rg-loading"></div><div class="rg-caption-wrapper"><div class="rg-caption"><p></p></div></div></div>';
			return true;
		} else { //no such gallery, or it is empty, or invisible
			return false;
		}
	}
	
}

?>