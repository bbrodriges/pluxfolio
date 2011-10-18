<?php

/* THIS IS ONLY A MAJOR OVERVIEW. TOTALLY UNTESTED */

class Templates extends Mustache {
	
	public $renderArray; //Contains all data to be passed to Mustache
	public $pagetype;

	/* Function to be called on templates render */
	public function render( $page ){
		
		$this->renderArray = Array(); //Empty render array. Will contain all necessary mustache tags
		$this->pagetype = $page;
		
		$siteSettings = Database::readDB( 'site' , true );
		$this->renderArray['homepage'] = $siteSettings['address']; //Site index page
		$this->renderArray['theme'] = $siteSettings['theme']; //Site theme
		$this->renderArray['title'] = $siteSettings['title']; //Site title
		$this->renderArray['subtitle'] = $siteSettings['subtitle']; //Site subtitle
		$this->renderArray['artworkscounter'] = $siteSettings['totalartworks']; //Total artworks count
		$this->renderArray['language'] = $siteSettings['language']; //Site language
		$this->renderArray['version'] = $siteSettings['version']; //Site language
		
		/* Data from language files */
		$this->renderArray['totalartworks'] = Utilities::getTranslation( 'totalartworks' ); //Artworks counter translation
		$this->renderArray['footer'] = Utilities::getTranslation( 'footer' ); //Site footer translation
		
		switch ( $this->pagetype ) {
			case 'article': //Article page. Get article by given pageid
				if( !$this->complileArticle( $_GET['pageid'] ) ) { //Compilation error
					$this->complileError();
					$this->pagetype = 'error';
				}
				break;
			case 'gallery': //Gallery page. Get gallery by given pageid
				if( !$this->complileGallery( $_GET['pageid'] ) ) { //Compilation error
					$this->complileError();
					$this->pagetype = 'error';
				}
				break;
			case 'static': //Static page. Get static by given pageid
				if( !$this->complileStatic( $_GET['pageid'] ) ) { //Compilation error
					$this->complileError();
					$this->pagetype = 'error';
				}
				break;
			case 'tag': //Tag filtered articles. Get all articles by given tag
				$this->pagetype = 'index'; //use index page template
				$this->renderArray['articles_list'] = $this->itterateArticlesByTag( $_GET['pageid'] );
				$this->renderArray['filtered_tag'] = '"'.$_GET['pageid'].'"';
				$this->renderArray['filteredtagtitle'] = Utilities::getTranslation( 'filteredtag' );
				break;
			case 'index': //Index page. Ganerate articles list
				$this->renderArray['articles_list'] = $this->itterateArticles( '1' );
				$totalPages = ceil( count( Utilities::returnVisible( 'articles' ) ) / $siteSettings['articlesperpage'] );
				$this->compilePagination( '1' , $totalPages );
				$this->renderArray['site_description'] = $siteSettings['sitedescription']; //Site description
				break;
			case '':
				header('Location: '.$siteSettings['address'].'/');
			case 'page': //if user opens next page of blog
				if( (int)$_GET['pageid'] < 2 ) {
					header('Location: '.$siteSettings['address'].'/');
				} else {
					$totalPages = ceil( count( Utilities::returnVisible( 'articles' ) ) / $siteSettings['articlesperpage'] );
					if( $_GET['pageid'] <= $totalPages ) { //if pageid integer and it is less or equal numbers of total pages
						$this->pagetype = 'index'; //use index page template
						$this->compilePagination( $_GET['pageid'] , $totalPages );
						$this->renderArray['articles_list'] = $this->itterateArticles( $_GET['pageid'] );
						$this->renderArray['site_description'] = $siteSettings['sitedescription']; //Site description
					} else { //return error page
						$this->complileError();
						$this->pagetype = 'error';
					}
				}
				break;
			default: //If none of this matched - show error page
				$this->complileError();
				$this->pagetype = 'error';
				break;
		}
		
		$this->renderArray['mainmenu'] = $this->compileMainMenu(); //Site main menu 
		
		/* Renders header, body and footer of page */
		$themeFolder = ; //Contains current theme folder path
		$renderer = new Mustache;
		echo $renderer->render( file_get_contents( ROOT.'themes/'.$siteSettings['theme'].'/header.tpl' ) , $this->renderArray )."\n";
		echo $renderer->render( file_get_contents( ROOT.'themes/'.$siteSettings['theme'].'/'.$this->pagetype.'.tpl' ) , $this->renderArray )."\n";
		echo $renderer->render( file_get_contents( ROOT.'themes/'.$siteSettings['theme'].'/footer.tpl' ) , $this->renderArray );
			
	}
	
/* ------------- COMPILATION PART ------------- */

	/* Compiles main menu */
	public function compileMainMenu() {
		$mainMenu = '';
		$statics = Utilities::returnVisible( 'statics' );
		foreach( $statics as $staticid => $static ) {
			if( $this->pagetype == 'static' && $staticid == $_GET['pageid'] ) {
				$mainMenu .= '<li class="current"><a href="'.$siteSettings['address'].'/static/'.$staticid.'">'.$static['title'].'</a></li>';
			} else {
				$mainMenu .= '<li><a href="'.$siteSettings['address'].'/static/'.$staticid.'">'.$static['title'].'</a></li>';
			}
		}
		$galleries = Utilities::returnVisible( 'galleries' );
		foreach( $galleries as $galleryid => $gallery ) {
			if( $this->pagetype == 'gallery' && $galleryid == $_GET['pageid'] ) {
				$mainMenu .= '<li class="current"><a href="'.$siteSettings['address'].'/gallery/'.$galleryid.'">'.$gallery['name'].'</a></li>';
			} else {
				$mainMenu .= '<li><a href="'.$siteSettings['address'].'/gallery/'.$galleryid.'">'.$gallery['name'].'</a></li>';
			}
		}
		if( $this->pagetype == 'index' || $this->pagetype == 'article' ) {
			$mainMenu .= '<li class="current"><a href="'.$siteSettings['address'].'">'.Utilities::getTranslation( 'blog' ).'</a></li>';
		} else {
			$mainMenu .= '<li><a href="'.$siteSettings['address'].'">'.Utilities::getTranslation( 'blog' ).'</a></li>';
		}
		return $mainMenu;
	}
	
	/* Compiles article */
	public function complileArticle( $articleid ) {
		$articleData = Utilities::getById( 'articles' , $articleid );
		if( $articleData && $articleData['visible'] == 'true' ) { //Article exists and available for reading. Proceed compiling
			$this->renderArray['article_title'] = $articleData['title'];
			$this->renderArray['article_pretext'] = $this->makeLinks( $articleData['pretext'] );
			$this->renderArray['article_text'] = $this->makeLinks( $articleData['text'] );
			$this->renderArray['article_tags'] = $this->makeTags( $articleData['tags'] );
			$this->renderArray['article_date'] = date( 'Y.m.d G:i' , $articleData['date'] );
			$this->renderArray['article_author'] = $articleData['author'];
			$this->renderArray['article_link'] = $siteSettings['address'].'/article/'.$articleid;
			
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
				$tagString .= '<a href="'.$siteSettings['address'].'/tag/'.$tag.'">'.$tag.'</a>, ';
			}
			return substr( $tagString , 0 , -2 );
		}
	
	/* Compiles static */	
	public function complileStatic( $staticid ) {
		$staticData = Utilities::getById( 'statics' , $staticid );
		if( $staticData && $staticData['visible'] == 'true' ) { //Static exists and available for reading. Proceed compiling
			$this->renderArray['static_title'] = $staticData['title'];
			$this->renderArray['static_text'] = $this->makeLinks( $staticData['text'] );
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
						$pagination = '<li class="current">'.$page.'</li><li><a href="'.$siteSettings['address'].'/page/'.$totalpages.'">'.$totalpages.'</a></li><li><a href="'.$siteSettings['address'].'/page/'.($page+1).'">'.Utilities::getTranslation( 'nextpage' ).'</a> &raquo;</li>';
					} else {
						$pagination = '<li>&laquo; <a href="'.$siteSettings['address'].'/page/'.($page-1).'">'.Utilities::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$siteSettings['address'].'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$totalpages.'</li>';
					}
					break;
				default:
					if( $page > 1 && $page < $totalpages ) {
						$pagination = '<li>&laquo; <a href="'.$siteSettings['address'].'/page/'.($page-1).'">'.Utilities::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$siteSettings['address'].'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$page.'</li><li><a href="'.$siteSettings['address'].'/page/'.($page+1).'">'. ( $page+1 ) .'</a></li><li><a href="'.$siteSettings['address'].'/page/'.($page+1).'">'.Utilities::getTranslation( 'nextpage' ).'</a> &raquo;</li>';
					} elseif( $page == 1 ) {
						$pagination = '<li class="current">'.$page.'</li><li><a href="'.$siteSettings['address'].'/page/'.($page+1).'">'.( $page+1 ).'</a></li><li><a href="'.$siteSettings['address'].'/page/'.($page+2).'">'. ( $page+2 ) .'</a></li><li><a href="'.$siteSettings['address'].'/page/'.($page+1).'">'.Utilities::getTranslation( 'nextpage' ).'</a> &raquo;</li>';
					} elseif( $page == $totalpages )  {
						$pagination = '<li>&laquo; <a href="'.$siteSettings['address'].'/page/'.($page-1).'">'.Utilities::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$siteSettings['address'].'/page/'.($page-2).'">'.( $page-2 ).'</a></li><li><a href="'.$siteSettings['address'].'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$page.'</li>';
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
		$articlesData = Utilities::returnVisible( 'articles' );
		if( !empty( $articlesData ) ) { //At least one visible article exists
			$articleKeys = array_keys( $articlesData ); //get all keys of visible articles
			$articleKeys = array_slice( $articleKeys, ($pageNumber-1)*$siteSettings['articlesperpage'], $siteSettings['articlesperpage'] ); //slice articles based on page number
			if( !empty( $articleKeys ) ) {
				$articlesArray = Array();
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
							'article_link' => $siteSettings['address'].'/article/'.$articleKey,
							'more' => Utilities::getTranslation( 'more' )
						);
				}
			}
		}
		return new ArrayIterator( $articlesArray );
	}
	
	/* Itterates through all visible articles and return result to mustache tags */
	public function itterateArticlesByTag( $tag ) {
		$articlesArray[] = array('article_title' => Utilities::getTranslation( 'noarticleswithtag' ) );
		$articlesData = Articles::returnWithTag( $tag );
		if( !empty( $articlesData ) ) { //At least one article exists
			$articleKeys = array_keys( $articlesData ); //get all keys of visible articles
			if( !empty( $articleKeys ) ) {
				$articlesArray = Array();
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
							'article_link' => $siteSettings['address'].'/article/'.$articleKey,
							'more' => Utilities::getTranslation( 'more' )
						);
				}
			}
		}
		return new ArrayIterator( $articlesArray );
	}
	
	/* Compiles gallery */
	/* REWRITE TO ITERATOR! */
	public function complileGallery( $galleryid ) {
		if( isset( $this->siteDB['galleries'][$galleryid] ) && !empty( $this->siteDB['galleries'][$galleryid] ) && $this->siteDB['galleries'][$galleryid]['visible'] == 'true' ) {
			$this->renderArray['gallery_title'] = $this->siteDB['galleries'][$galleryid]['name'];
			$this->renderArray['gallery_text'] = $this->siteDB['galleries'][$galleryid]['text'];
			$galleryImages = $this->siteDB['galleries'][$galleryid]['images'];
			$thumbsList = '';
			foreach( $galleryImages as $imageFile => $imageData ) {
				$imageFilePath = $siteSettings['address'].'/galleries/'.$this->siteDB['galleries'][$galleryid]['folder'].'/'.$imageFile;
				$thumbsList .= '<li><a href="'.$imageFilePath.'" rel="shadowbox" title="'.$imageData['description'].'"><img src="'.$imageFilePath.'.tb" alt="'.$imageData['name'].'"></a></li>';
			}
			return true;
		} else { //no such gallery, or it is empty, or invisible
			return false;
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
	
}

?>