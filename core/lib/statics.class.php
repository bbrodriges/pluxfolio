<?php

class Statics extends Database {
	
	/* Append new static to DB */
	/* $data is Array("title" => string, "text" => string, "visible" => 'true'/'false'); */
	/* If $id passed - edits existing static */
	function Modify( $data, $id = false ){
		$database = Database::readDB( 'statics' , true );
		$newId = Utilities::Translit( $data['title'] ); //transliterating title to use as static key
		if( $id ) {
			if( $id != $newId ) { //if title changed...
				unset( $database[$id] ); //delete old static
				if( !Utilities::getById( 'statics' , $newId ) ) { //... and it is unique changing id
					$database[$newId] = $data;
				} else {
					return 5; //key is not unique
				}
			} else {
				$database[$id] = $data;
			}
		} else {
			if( !Utilities::getById( 'statics' , $newId ) ) { //if key is unique write data
				$database[$newId] = $data;
			} else { //key is not unique
				return 5;
			}
		}
		return Database::writeDB( 'statics' , $database );
	}

}

?>