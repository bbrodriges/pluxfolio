<?php

class Utilities extends Database {
	
	/* Returns all user credentials */
	public function returnUser() {
		$database = Database::readDB( 'site' , true );
		return array( 'login' => $database['login'] , 'password' => $database['password'] );
	}
	
	/* Writes site info */
	public function writeSiteData( $type, $data ) { //(1) type: 'title', 'subtitle' etc... ; (2) Data to write
		$database = Database::readDB( 'site' , true );
		if( isset( $database[$type] ) ) {
			$database[$type] = $data;
			return Database::writeDB( 'site' , $database );
		} else {
			return 5;
		}
	}
	
	/* Reads site info */
	public function readSiteData( $type ) { //type: 'title', 'subtitle' etc...
		$database = Database::readDB( 'site' , true );
		return $database[$type];
	}
	
	/* Changes count of site's artworks  */
	public function modifyArtworksCount( $mode ) { //mode: 'increase', 'decrease'
		$count = (int) self::readSiteData( 'totalartworks' );
		if( $mode == 'increase' ) {
			$count++;
		} elseif ( $mode == 'decrease' && $count > 0 ) {
			$count--;
		} else {
			return false;	
		}
		return self::writeSiteData( 'totalartworks', (string) $count );
	}
	
	/* Compleatly recalculates count of site's artworks  */
	public function renewArtworksCount() { //mode: 'increase', 'decrease'
		$newcount = 0;
		$database = Database::readDB( 'galleries' , true );
		if( count($database) > 0 ){ //if any gallery exists
			foreach( $database as $gallery ){
				foreach( $gallery['images'] as $image ) {
					$newcount++;
				}
			}
		}
		return self::writeSiteData( 'totalartworks', (string) $newcount );
	}

	/* Convert russian to translit */
	public function Translit( $string ) {
		$string = str_replace(' - ','-',$string);
		$string = str_replace(' ','-',$string);
		if( preg_match('/[А-Яа-яЁё]/u', $string) ) { //if any russian chars exists
			$table=array(
				"а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo",
				"ж" => "zh", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m",
				"н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u",
				"ф" => "f", "х" => "h", "ц" => "c", "ч" => "ch", "ш" => "sch", "щ" => "sh", "ъ" => "",
				"ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
				"А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E", "Ё" => "YO",
				"Ж" => "ZH", "З" => "Z", "И" => "I", "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M",
				"Н" => "N", "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T", "У" => "U",
				"Ф" => "F", "Х" => "H", "Ц" => "C", "Ч" => "CH", "Ш" => "SCH", "Щ" => "SH", "Ъ" => "",
				"Ы" => "Y", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA"
			);
			$string = strtr( $string, $table );
		}
		$string = preg_replace('#[^0-9a-zA-Z-]#','',$string);
		return ucfirst( strtolower( $string ) );
	}
  
	/* Displays human readable error */
	public function parseError( $error ){
		if( (int)$error > 1 ) {
			return self::getTranslation( 'errorcodetitle' ).' <strong>'.$error.'</strong>';
		} else {
            return 1;
        }
	}
  
	/* Returns translation from dictionary */
	public function getTranslation( $id ) {
		$dictionary = json_decode( file_get_contents( ROOT.'core/lang/'.self::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
		if( isset( $dictionary[$id] ) && !empty( $dictionary[$id] ) ) {
			return $dictionary[$id];
		} else {
			return '%'.$id.'%';
		}
	}
	
	/* Returns element from $database as Array by $id */
	public function getById( $database , $id ){
		$database = Database::readDB( $database , true );
		return $database[(string)$id];
	}
	
	/* Delete element from $database by $id */
	public function Delete( $database , $id ){ //id of article to be deleted
		$data = Database::readDB( $database , true );
		unset($data[(string)$id]);
		if( Database::writeDB( $database , $data ) ) {
			if( $database == 'articles' ) {
				self::collectTags();
			}
			return 1;
		}
	}
	
	/* Set visibility on and off */
	public function toggleVisiblity( $database , $id, $state ){ //id as int, state as string 'true' or 'false'
		$data = Database::readDB( $database , true );
		$data[(string)$id]['visible'] = $state;
		return Database::writeDB( $database , $data );
	}
	
	/* Returns only visible article */
	/* For more simple main menu generation */
	public function returnVisible( $database ){
		$data = Database::readDB( $database , true );
		$result = Array();
		foreach( $data as $elementid => $element ) {
			if( $element['visible'] == 'true' ){
				$result[$elementid] = $element;
			}
		}
		return $result;
	}
	
	/* Returns page type */
	public function getPageType() {
		$acceptable = array( 'index' , 'page' , 'tag' , 'article' , 'gallery' , 'static' , 'category' ); //acceptable page types
		$page = ( ( $_GET['pagetype'] ) ? $_GET['pagetype'] : 'index' );
		if( !in_array( $page , $acceptable ) ) {
			return 'error';
		} else {
			return $page;
		}
	}
	
	/* Itterates through all visible articles and return result to mustache tags */
	public function itterateArticles( $pageNumber ) {
		$siteSettings = Database::readDB( 'site' , true );
		$articlesArray[] = array('article_title' => self::getTranslation( 'noarticles' ) );
		$articlesData = self::returnVisible( 'articles' );
		if( !empty( $articlesData ) ) { //At least one visible article exists
			$articleKeys = array_keys( $articlesData ); //get all keys of visible articles
			$articleKeys = array_slice( $articleKeys, ($pageNumber-1)*$siteSettings['articlesperpage'], $siteSettings['articlesperpage'] ); //slice articles based on page number
			if( !empty( $articleKeys ) ) {
				$articlesArray = Array();
				foreach( $articleKeys as $articleKey ) {
					$articlesArray[] = array(
							'article_title' => $articlesData[$articleKey]['title'],
							'article_pretext' => $articlesData[$articleKey]['pretext'],
							'article_text' => $articlesData[$articleKey]['text'],
							'article_tags' => self::makeTags( $articlesData[$articleKey]['tags'] ),
							'article_date' => date( 'Y.m.d G:i' , $articlesData[$articleKey]['date'] ),
							'article_author' => $articlesData[$articleKey]['author'],
							'tags' => self::getTranslation( 'tags' ),
							'publishedby' => self::getTranslation( 'publishedby' ),
							'publishedat' => self::getTranslation( 'publishedat' ),
							'article_link' => $siteSettings['address'].'/article/'.$articleKey,
							'more' => self::getTranslation( 'more' )
						);
				}
			}
		}
		return new ArrayIterator( $articlesArray );
	}
	
	/* Compiles article tags */
	/* If $toptags = true removes commas between tags */
	public function makeTags( $tags , $toptags = false ) {
		if( !empty( $tags ) ) {
			$tagString = '';
			$tags = explode( ',' , $tags );
			foreach( $tags as $tag ){
				$tag = trim( $tag );
				$tagString .= '<a href="'.self::readSiteData( 'address' ).'/tag/'.$tag.'">'.$tag.'</a>';
				if( !$toptags ) {
					$tagString .= ', ';
				}
			}
			return substr( $tagString , 0 , -2 );
		} else {
			return self::getTranslation( 'notags' );
		}
	}
	
	/* Compiles pagination */
	function Pagination( $page ) {
		$page = ( $page ? $page : 1 ); //if $page is empty - this is first page
		$totalpages = self::paginationPages();
		if( $totalpages > 1 ) {
			$address = self::readSiteData( 'address' );
			switch ( $totalpages ) {
				case 2:
					if( $page == 1 ) {
						$pagination = '<li class="current">'.$page.'</li><li><a href="'.$address.'/page/'.$totalpages.'">'.$totalpages.'</a></li><li><a href="'.$address.'/page/'.($page+1).'">'.self::getTranslation( 'nextpage' ).'</a> &raquo;</li>';
					} else {
						$pagination = '<li>&laquo; <a href="'.$address.'/page/'.($page-1).'">'.self::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$address.'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$totalpages.'</li>';
					}
					break;
				default:
					if( $page > 1 && $page < $totalpages ) {
						$pagination = '<li>&laquo; <a href="'.$address.'/page/'.($page-1).'">'.self::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$address.'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$page.'</li><li><a href="'.$address.'/page/'.($page+1).'">'. ( $page+1 ) .'</a></li><li><a href="'.$address.'/page/'.($page+1).'">'.self::getTranslation( 'nextpage' ).'</a> &raquo;</li>';
					} elseif( $page == 1 ) {
						$pagination = '<li class="current">'.$page.'</li><li><a href="'.$address.'/page/'.($page+1).'">'.( $page+1 ).'</a></li><li><a href="'.$address.'/page/'.($page+2).'">'. ( $page+2 ) .'</a></li><li><a href="'.$address.'/page/'.($page+1).'">'.self::getTranslation( 'nextpage' ).'</a> &raquo;</li>';
					} elseif( $page == $totalpages )  {
						$pagination = '<li>&laquo; <a href="'.$address.'/page/'.($page-1).'">'.self::getTranslation( 'prevpage' ).'</a></li><li><a href="'.$address.'/page/'.($page-2).'">'.( $page-2 ).'</a></li><li><a href="'.$address.'/page/'.($page-1).'">'.( $page-1 ).'</a></li><li class="current">'.$page.'</li>';
					}
					break;
			}
		} else {
			$pagination = '';
		}
		return $pagination;
	}
	
	/* Counts number of total pages in pagination */
	public function paginationPages() {
		return ceil( count( self::returnVisible( 'articles' ) ) / self::readSiteData( 'articlesperpage' ) );
	}
	
	/* Collects most popular tags */ 
	/* Writes top 5 tags into 'site' table and all of unique tags into 'tags' table*/
	public function collectTags() {
		$articles = self::returnVisible( 'articles' );
		$allTags = array();
		foreach( $articles as $article ) {
			$tags = explode( ',' , $article['tags'] );
			foreach( $tags as $tag ) {
				$allTags[] = trim( $tag );
			}
		}
		$database = Database::readDB( 'site' , true );
		$database['toptags'] = array_slice( array_keys( array_count_values( $allTags ) ) , 0 , 5 );
		if( Database::writeDB( 'site' , $database ) && Database::writeDB( 'tags' , array_unique( $allTags ) ) ) {
			return true;
		} else {
			return false;
		}
	}
  
}

?>