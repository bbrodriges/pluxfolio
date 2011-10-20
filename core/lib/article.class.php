<?php

class CArticle extends Database {

	public function init() {
		$renderer = new Mustache;
		$articleData = Utilities::getById( 'articles' , $_GET['pageid'] );
		if( $articleData && $articleData['visible'] == 'true' ) { //Article exists and available for reading. Proceed compiling
			$renderArray['article_title'] = $articleData['title'];
			$renderArray['article_pretext'] = $articleData['pretext'];
			$renderArray['article_text'] = $articleData['text'];
			$renderArray['article_tags'] = Utilities::makeTags( $articleData['tags'] );
			$renderArray['article_date'] = date( 'Y.m.d G:i' , $articleData['date'] );
			$renderArray['article_author'] = $articleData['author'];
			$renderArray['article_link'] = Utilities::readSiteData( 'address' ).'/article/'.$articleid;
			
			/* Translations */
			$renderArray['tags'] = Utilities::getTranslation( 'tags' );
			$renderArray['publishedby'] = Utilities::getTranslation( 'publishedby' );
			$renderArray['publishedat'] = Utilities::getTranslation( 'publishedat' );
			
			$renderer->renderPage( 'article' , $renderArray );
		} else {
			$renderer->complileError();
		}
	}
	
	/* Append new article to DB */
	/* $data is Array("title" => string, "pretext" => string, "text" => string, "tags" => string, "date" => (string)timestamp, "author" => string, "visible" => 'true'/'false'); */
	/* If $id passed - edits existing article */
	function Modify( $data, $id = false ){
		$database = Database::readDB( 'articles' , true );
		$newId = Utilities::Translit( $data['title'] ); //transliterating title to use as static key
		if( $id ) {
			if( $id != $newId ) { //if title changed...
				if( !Utilities::getById( 'articles' , $newId ) ) { //... and it is unique pasting new article on old article place
					$oldDate = $database[$id]['date'];
					$keys = array_keys( $database ); 
					$values = array_values( $database ); 
					foreach ($keys as $key => $value) {
						if ( $value == $id ){ //searching for old id in db
							$keys[$key] = $newId; //replace it with new
						} 
					} 
					$database = array_combine($keys, $values); //combining keys with values back into database
					$database[$newId] = $data;
					$database[$newId]['date'] = $oldDate;
				} else {
					return 5; //key is not unique
				}
			} else {
				$database[$id] = $data;
			}
		} else {
			if( !Utilities::getById( 'articles' , $newId ) ) { //if key is unique write data
				$newArticle[$newId] = $data;
				$database = array_merge( $newArticle , $database );
			} else { //key is not unique
				return 5;
			}
		}
		return Database::writeDB( 'articles' , $database );
	}

}

class CIndex {

	/* Renders index page */
	public function init() {
		$renderArray['articles_list'] = Utilities::itterateArticles( 1 );
		$renderArray['pagination'] = Utilities::Pagination( 1 );
		$renderer = new Mustache;
		$renderer->renderPage( 'index' , $renderArray );
	}
	
}

class CTag {

	public function init() {
		$articlesArray[] = array('article_title' => Utilities::getTranslation( 'noarticleswithtag' ) );
		$articlesData = self::returnWithTag( $_GET['pageid'] );
		if( !empty( $articlesData ) ) { //At least one article exists
			$articleKeys = array_keys( $articlesData ); //get all keys of visible articles
			if( !empty( $articleKeys ) ) {
				$articlesArray = Array();
				foreach( $articleKeys as $articleKey ) {
					$articlesArray[] = array(
							'article_title' => $articlesData[$articleKey]['title'],
							'article_pretext' => $articlesData[$articleKey]['pretext'],
							'article_text' => $articlesData[$articleKey]['text'],
							'article_tags' => Utilities::makeTags( $articlesData[$articleKey]['tags'] ),
							'article_date' => date( 'Y.m.d G:i' , $articlesData[$articleKey]['date'] ),
							'article_author' => $articlesData[$articleKey]['author'],
							'tags' => Utilities::getTranslation( 'tags' ),
							'publishedby' => Utilities::getTranslation( 'publishedby' ),
							'publishedat' => Utilities::getTranslation( 'publishedat' ),
							'article_link' => Utilities::readSiteData( 'address' ).'/article/'.$articleKey,
							'more' => Utilities::getTranslation( 'more' )
						);
				}
			}
		}
		$renderArray['articles_list'] = new ArrayIterator( $articlesArray );
		$renderer = new Mustache;
		$renderer->renderPage( 'index' , $renderArray );
	}

	/* Returns only visible article with given tag */
	function returnWithTag( $tag ){ //$tag as string
		$articles = Utilities::returnVisible( 'articles' );
		$result = Array();
		foreach( $articles as $articleid => $article ) {
			if( mb_strpos( ' '.$article['tags'] , $tag ) ){ // Adding space to string to insure that position will never be zero
				$result[$articleid] = $article;
			}
		}
		return $result;
	}
	
}

class CPage {

	public function init() {
		$renderer = new Mustache;
		if( $_GET['pageid'] == 1 ) { //if first page - redirect to index
			header( 'Location: '.Utilities::readSiteData( 'address' ) );
		} elseif( $_GET['pageid'] > 1 && $_GET['pageid'] <= Utilities::paginationPages() ) {
			$pageNumber = ( ( $_GET['pageid'] ) ? $_GET['pageid'] : 1 ); //if no page given - it is first page
			$renderArray['articles_list'] = Utilities::itterateArticles( $pageNumber );
			$renderArray['pagination'] = Utilities::Pagination( $_GET['pageid'] );
			$renderer->renderPage( 'index' , $renderArray );
		} else {
			$renderer->complileError();
		}
	}

}

?>