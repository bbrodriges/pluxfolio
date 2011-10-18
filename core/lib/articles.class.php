<?php

class Articles extends Database {
	
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

?>