<?php

class Utilities extends Database {
	
	/* Returns all user credentials */
	public function returnUser() {
		$database = Database::readDB( 'site' , true );
		return array( 'login' => $database['login'] , 'password' => $database['password'] );
	}
	
	/* Writes new user credentials */
	 public function updateUser( $login , $password ) { //user credentials as Array or Object
		$database = Database::readDB( 'site' , true );
		$database['login'] = Database::clearQuery( $login );
		$database['password'] = md5( $password );
		return Database::writeDB( 'site' , $database );
	}
	
	/* Writes site info */
	public function writeSiteData( $type, $data ) { //(1) type: 'title', 'subtitle' etc... ; (2) Data to write
		$database = Database::readDB( 'site' , true );
		$database[$type] = Database::sanitiseQuery( $data );
		return Database::writeDB( 'site' , $database );
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
				foreach( $gallery['images'] ) {
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
			return self::getTranslation( 'errorcodetitle' ).' '.$error;
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
	function getById( $database , $id ){
		$database = Database::readDB( $database , true );
		return $database[(string)$id];
	}
	
	/* Delete element from $database by $id */
	function Delete( $database , $id ){ //id of article to be deleted
		$data = Database::readDB( $database , true );
		unset($data[(string)$id]);
		return Database::writeDB( $database , $data );
	}
	
	/* Set visibility on and off */
	function toggleVisiblity( $database , $id, $state ){ //id as int, state as string 'true' or 'false'
		$data = Database::readDB( $database , true );
		$data[(string)$id]['visible'] = $state;
		return Database::writeDB( $database , $data );
	}
	
	/* Returns only visible article */
	/* For more simple main menu generation */
	function returnVisible( $database ){
		$data = Database::readDB( $database , true );
		$result = Array();
		foreach( $data as $elementid => $element ) {
			if( $element['visible'] == 'true' ){
				$result[$elementid] = $element;
			}
		}
		return $result;
	}
  
}

?>