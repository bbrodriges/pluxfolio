<?php

class Utilities extends Database {
	
/* ----------- USER SECTION ----------- */
	
	/* Returns all user credentials */
	public function returnUser() {
		$database = Database::readDB( true );
		return $database['user'];
	}
	
	/* Writes new user credentials */
	 public function updateUser( $login , $password ) { //user credentials as Array or Object
		$database = Database::readDB( true );
		$database['user']['login'] = Database::clearQuery( $login );
		$database['user']['password'] = md5( $password );
		return Database::writeDB( $database );
	}
	
/* ----------- END USER SECTION ----------- */

/* ----------- SITE SECTION ----------- */
	
	/* Writes site info */
	public function writeSiteData( $type, $data ) { //(1) type: 'title', 'subtitle' etc... ; (2) Data to write
		$database = Database::readDB( true );
		$database['site'][$type] = Database::sanitiseQuery( $data );
		return Database::writeDB( $database );
	}
	
	/* Reads site info */
	public function readSiteData( $type ) { //type: 'title', 'subtitle' etc...
		$database = Database::readDB( true );
		return $database['site'][$type];
	}
	
	/* Changes count of site's artworks  */
	public function modifyArtworksCount( $mode ) { //mode: 'increase', 'decrease'
		$count = (int) $this->readSiteData( 'totalartworks' );
		if( $mode == 'increase' ) {
			$count++;
		} elseif ( $mode == 'decrease' && $count > 0 ) {
			$count--;
		} else {
			return false;	
		}
		return $this->writeSiteData( 'totalartworks', (string) $count );
	}
	
	/* Compleatly recalculates count of site's artworks  */
	public function renewArtworksCount() { //mode: 'increase', 'decrease'
		$newcount = 0;
		$database = Database::readDB( true );
		if( count($database['galleries']) > 0 ){ //if any gallery exists
			foreach( $database['galleries'] as $gallery ){
				foreach( $gallery['images'] as $image ) {
					$newcount++;
				}
			}
		}
		return $this->writeSiteData( 'totalartworks', (string) $newcount );
	}
	
/* ----------- END SITE SECTION ----------- */

/* ----------- ADDITIONAL ----------------- */

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
		return $this->getTranslation( 'errorcodetitle' ).' '.$error;
	}
  }
  
  /* Returns translation from dictionary */
	public function getTranslation( $id ) {
		$dictionary = json_decode( file_get_contents( ROOT.'core/lang/'.readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
		if( isset( $dictionary[$id] ) && !empty( $dictionary[$id] ) ) {
			return $this->dictionary[$id];
		} else {
			return '%'.$id.'%';
		}
	}

/* ----------- END ADDITIONAL ----------------- */
  
}

?>