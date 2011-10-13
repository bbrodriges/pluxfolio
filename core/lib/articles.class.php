<?php

class Articles extends Database {
	
	/* Returns all articles as Array */
	function getAll(){
		$database = Database::readDB( true );
		return $database['articles'];
	}
	
	/* Returns article as Array by id */
	function getById( $id ){ //id as int
		$articles = self::getAll();
		return $articles[(string)$id];
	}
	
	/* Append new article to DB */
	/* $data is Array("title" => string, "pretext" => string, "text" => string, "tags" => string, "date" => timestamp, "author" => string, "visible" => 'true'/'false'); */
	/* If $id passed - edits existing article */
	function Modify( $data, $id = false ){
		$database = Database::readDB( true );
		$newId = Utilities::Translit( $data['title'] ); //transliterating title to use as static key
		if( $id ) {
			if( $id != $newId ) { //if title changed...
				unset( $database['articles'][$id] ); //delete old static
				if( !self::getById( $newId ) ) { //... and it is unique changing id
					$database['articles'][$newId] = $data;
				} else {
					$newId = $newId.'-'.time(); //creating truly unique id
					$database['articles'][$newId] = $data;
				}
			} else {
				$database['articles'][$id] = $data;
			}
		} else {
			if( !self::getById( $newId ) ) //if key is unique write data
				$database['articles'][$newId] = $data;
			} else { //key is not unique
				return false;
			}
		}
		return Database::writeDB( $database );
	}
	
	/* Delete article from DB */
	function Delete( $id ){ //id of article to be deleted
		$database = Database::readDB( true );
		unset($database['articles'][(string)$id]);
		return Database::writeDB( $database );
	}
	
	/* Set visibility of article on and off */
	function toggleVisiblity( $id, $state ){ //id as int, state as string 'true' or 'false'
		$database = Database::readDB( true );
		$database['articles'][(string)$id]['visible'] = $state;
		return Database::writeDB( $database );
	}
	
	/* Returns only visible article */
	/* For more simple main menu generation */
	function returnVisible(){
		$articles = self::getAll();
		$result = Array();
		foreach( $articles as $id => $article ) {
			if( $article['visible'] == 'true' ){
				$result[$id] = $article;
			}
		}
		return $result;
	}
	
	/* Returns only visible article with given tag */
	function returnWithTag( $tag ){ //$tag as string
		$articles = self::returnVisible();
		$result = Array();
		foreach( $articles as $id => $article ) {
			/* Adding space to the begining of tags string to insure that our position will never be 0 */
			if( mb_strpos( ' '.$article['tags'] , $tag ) ){
				$result[$id] = $article;
			}
		}
		return $result;
	}

}

?>