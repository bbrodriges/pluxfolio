<?php

class CStatic extends Database {

	/* Renders static */
	public function init() {
		$renderer = new Mustache;
		$staticData = Utilities::getById( 'statics' , $_GET['pageid'] );
		if( $staticData && $staticData['visible'] == 'true' ) { //Static exists and available for reading. Proceed compiling
			$renderArray['static_title'] = $staticData['title'];
			$renderArray['static_text'] = $staticData['text'];
			$renderer->renderPage( 'static' , $renderArray );
		} else { //Static does not exists. Return redirect to error page
			$renderer->complileError();
		}
	}
	
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
			echo '1';
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