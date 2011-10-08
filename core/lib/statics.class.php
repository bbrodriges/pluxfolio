<?php

include('database.class.php');

class Statics extends Database {
	
	/* Returns all statics as Array */
	function getAll(){
		$database = Database::readDB( true );
		return $databese['statics'];
	}
	
	/* Returns static as Array by id */
	function getById( $id ){ //id as int
		$statics = self::getAll();
		return $statics[$id];
	}
	
	/* Append new static to DB */
	/* $data is Array("title" => string, "text": string, "visible": bool); */
	/* If $id passed - edits existing static */
	function Modify( $data, $id = false ){
		$database = Database::readDB( true );
		if( $id ) {
			$database['statics'][$id] = $data;
		} else {
			$newId = key( end( $database['statics'] ) ) + 1; //last static id + 1. Might need to apply reset() function to return pointer.
			$database['statics'][$newId] = $data;
		}
		return Database::writeDB( $database );
	}
	
	/* Delete static from DB */
	function Delete( $id ){ //id of static to be deleted
		$database = Database::readDB( true );
		unset($databese['statics'][$id]);
		return Database::writeDB( $database );
	}
	
	/* Set visibility of static on and off */
	function toggleVisiblity( $id, $state ){ //id as int, state as string 'true' or 'false'
		$database = Database::readDB( true );
		$database['statics'][$id]['visible'] = $state;
		return Database::writeDB( $database );
	}
	
	/* Returns only visible statics */
	/* For more simple main menu generation */
	function returnVisible(){
		$statics = self::getAll();
		$result = Array();
		foreach( $statics as $static ) {
			if( $static['visible'] == 'true' ){
				$result[] = $static;
			}
		}
		return $result;
	}

}

?>