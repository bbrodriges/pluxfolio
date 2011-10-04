<?php

/**
 * Classe plxUtils rassemblant les fonctions utiles à Pluxml
 *
 * @package PLX
 * @author	Florent MONTHEL et Stephane F
 **/
class plxUtils {

	function getCalendar($key, $value) {
	
		include('core/lang/'.$this->plxMotor->aConf['site_lang'].'.php');

		$aMonth = array(
			'01' => $SITE_month_january,
			'02' => $SITE_month_february,
			'03' => $SITE_month_march,
			'04' => $SITE_month_april,
			'05' => $SITE_month_may,
			'06' => $SITE_month_june,
			'07' => $SITE_month_july,
			'08' => $SITE_month_august,
			'09' => $SITE_month_september,
			'10' => $SITE_month_october,
			'11' => $SITE_month_november,
			'12' => $SITE_month_december);	
		$aDay = array(
			'1' => $SITE_day_monday,
			'2' => $SITE_day_tuesday,
			'3' => $SITE_day_wednesday,
			'4' => $SITE_day_thursday,
			'5' => $SITE_day_friday,
			'6' => $SITE_day_saturday,
			'0' => $SITE_day_sunday);
	
		switch ($key) {
			case 'day':
				return "<b>" .$aDay[ $value ] . "</b>"; break;
			case 'month':
				return $aMonth[ $value ]; break;
		}
	}

	function getGets() {

		if(!empty($_GET)) {
			$a = array_keys($_GET);
			return $a[0];
		}
		return false;
	}

	function unSlash($content) {

		if(get_magic_quotes_gpc() == 1) {
			if(is_array($content)) { # On traite un tableau
				while(list($k,$v) = each($content)) # On parcourt le tableau
					$new_content[ $k ] = stripslashes($v);
			} else { # On traite une chaine
				$new_content = stripslashes($content);
			}
			# On retourne le tableau modifie
			return $new_content;
		} else {
			return $content;
		}
	}

	function microtime() {

		$t = explode(' ',microtime());
		return $t[0]+$t[1];
	}

	function dateToIso($date,$delta) {

		return substr($date,0,4).'-'.
		substr($date,4,2).'-'.
		substr($date,6,2).'T'.
		substr($date,8,2).':'.
		substr($date,10,2).':00'.$delta;
	}

	function timestampToIso($timestamp,$delta) {

		return date('Y-m-d\TH:i:s',$timestamp).$delta;
	}

	function dateIsoToHum($date) {

		# On decoupe notre date
		$year = substr($date, 0, 4);
		$month = substr($date, 5, 2);
		$day = substr($date, 8, 2);
		$day_num = date('w',mktime(0,0,0,$month,$day,$year));
		# On genere nos tableaux de traduction

		# On retourne notre date au format humain
		return plxUtils::getCalendar('',$day_num).' '.$day.' '.plxUtils::getCalendar('month', $month).' '.$year;
	}

	function heureIsoToHum($date) {

		# On retourne l'heure au format 12:55
		return substr($date,11,2).':'.substr($date,14,2);
	}

	function dateIso2Admin($date) {

		preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9:]{8})((\+|-)[0-9:]{5})/',$date,$capture);
		return array ('year' => $capture[1],'month' => $capture[2],'day' => $capture[3],'time' => substr($capture[4],0,5),'delta' => $capture[5]);
	}

	function printSelect($name, $array, $selected='', $readonly=false, $class='') {

		if($readonly)
			echo '<select id="id_'.$name.'" name="'.$name.'" disabled="disabled" class="readonly">'."\n";
		else
			echo '<select id="id_'.$name.'" name="'.$name.'" class="'.$class.'">'."\n";			
		foreach($array as $a => $b) {
			if(is_array($b)) {
				echo '<optgroup label="'.$a.'">'."\n";
				foreach($b as $c=>$d) {
					if($c == $selected)
						echo "\t".'<option value="'.$c.'" selected="selected">'.$d.'</option>'."\n";
					else
						echo "\t".'<option value="'.$c.'">'.$d.'</option>'."\n";
				}
				echo '</optgroup>'."\n";
			} else {
				if($a == $selected)
					echo "\t".'<option value="'.$a.'" selected="selected">'.$b.'</option>'."\n";
				else
					echo "\t".'<option value="'.$a.'">'.$b.'</option>'."\n";
			}
		}
		echo '</select>'."\n";
	}

	function printInput($name, $value='', $type='text', $size='50-255', $readonly=false, $class='') {

		$size = explode('-',$size);
		if($readonly)
			echo '<input id="id_'.$name.'" name="'.$name.'" type="'.$type.'" class="'.$class.'" value="'.$value.'" size="'.$size[0].'" maxlength="'.$size[1].'" class="readonly" readonly="readonly" />'."\n";
		else
			echo '<input id="id_'.$name.'" name="'.$name.'" type="'.$type.'" class="'.$class.'" value="'.$value.'" size="'.$size[0].'" maxlength="'.$size[1].'" />'."\n";	
	}

	function printArea($name, $value='', $cols='', $rows='', $readonly=false, $class='') {

		if($readonly)
			echo '<textarea id="id_'.$name.'" name="'.$name.'" class="readonly" cols="'.$cols.'" rows="'.$rows.'" readonly="readonly">'.$value.'</textarea>'."\n";
		else
			echo '<textarea id="id_'.$name.'" name="'.$name.'" class="'.$class.'" cols="'.$cols.'" rows="'.$rows.'">'.$value.'</textarea>'."\n";
	}

	function testWrite($file) {

		if(is_writable($file))
			echo $file.' <font color="green">OK</font>';
		else
			echo '<font color="red">'.$file.'</font>';
	}

	function removeAccents($str,$charset='utf-8') {

		$str = plxUtils::imTranslite($str);
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml|uro)\;#', '\1', $str);
	    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); # pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#\&[^;]+\;#', '', $str); # supprime les autres caractères    
	    return $str;
	}

	function imTranslite($str)
	{
		static $tbl= array(
			'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'g', 'з'=>'z',
			'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p',
			'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e', 'А'=>'A',
			'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'G', 'З'=>'Z', 'И'=>'I',
			'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R',
			'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'I', 'Э'=>'E', 'ё'=>"yo", 'х'=>"h",
			'ц'=>"ts", 'ч'=>"ch", 'ш'=>"sh", 'щ'=>"shch", 'ъ'=>"", 'ь'=>"", 'ю'=>"yu", 'я'=>"ya",
			'Ё'=>"YO", 'Х'=>"H", 'Ц'=>"TS", 'Ч'=>"CH", 'Ш'=>"SH", 'Щ'=>"SHCH", 'Ъ'=>"", 'Ь'=>"",
			'Ю'=>"YU", 'Я'=>"YA"
		);

	return strtr($str, $tbl);
	}
	
	function title2url($str) {

		$str = strtolower(plxUtils::removeAccents($str,PLX_CHARSET));
		$str = preg_replace('/[^[:alnum:]]+/',' ',$str);
		return strtr(trim($str), ' ', '-');
	}

	function title2filename($str) {

		$str = strtolower(plxUtils::removeAccents($str,PLX_CHARSET));
		$str = preg_replace('/[^[:alnum:]|.|_]+/',' ',$str);
		return strtr(trim($str), ' ', '-');
	}

	function formatRelatif($num, $lenght) {

		$fnum = str_pad(abs($num), $lenght, '0', STR_PAD_LEFT);
		if($num > -1)
			return '+'.$fnum;
		else
			return '-'.$fnum;
	}

	function write($xml, $filename) {

		if(file_exists($filename)) {
			$f = fopen($filename.'.tmp', 'w'); # On ouvre le fichier temporaire
			fwrite($f, trim($xml)); # On écrit
			fclose($f); # On ferme
			unlink($filename);
			rename($filename.'.tmp', $filename); # On renomme le fichier temporaire avec le nom de l'ancien
		} else {
			$f = fopen($filename, 'w'); # On ouvre le fichier
			fwrite($f, trim($xml)); # On écrit
			fclose($f); # On ferme
		}
		# On place les bons droits
		@chmod($filename,0644);
		# On vérifie le résultat
		if(file_exists($filename) AND !file_exists($filename.'.tmp'))
			return true;
		else
			return false;
	}
	
	//A bit tricky, but all in all it works, returns the filename without the extension!

	function file_name($key) {
		$key=strrev(substr(strstr(strrev($key), "."), 1));
		return($key);
	}
	//Lets get the file extension.

	function file_ext($key) { 
		$key=strtolower(substr(strrchr($key, "."), 1));
		$key=str_replace("jpeg", "jpg", $key);
		return($key); 
	}

	function makeThumb($filename, $filename_out, $width, $height, $quality, $thumbtype) {

		# Informations sur l'image
		list($width_orig,$height_orig,$type) = getimagesize($filename);
		
		# Création de l'image
		$image_p = imagecreatetruecolor($width,$height);
		if($type == 2)
			$image = imagecreatefromjpeg($filename);
		elseif($type == 3)
			$image = imagecreatefrompng($filename);
		elseif($type == 1)
			$image = imagecreatefromgif($filename);	
			
		if($thumbtype == 1) imagecopy($image_p, $image, 0, 0, 0, 0, $width, $height); // this do magic
		else imagecopyresized($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig );
		
		if($type == 2)
			imagejpeg($image_p, $filename_out, $quality);
		elseif($type == 3)
			imagepng($image_p, $filename_out);
		elseif ($type==1) imagegif($image_p, $filename_out);
		
	}
	
	function appendWatermark($file,$text) {
		list($width_orig,$height_orig,$type) = getimagesize($file);
		if($type == 2)
			$im = imagecreatefromjpeg($file);
		elseif($type == 3)
			$im = imagecreatefrompng($file);
		elseif($type == 1)
			$im = imagecreatefromgif($file);
		//figure out where to put the text
		$x_offset = 20;
		$y_offset = $height_orig-30;
		//allocate text color
		$white = imagecolorallocate($im, 255, 255, 255);
		$black = imagecolorallocate($im, 0, 0, 0);
		//write out the watermark
		//Подавляем ошибки т.к. с некоторыми версиями GD imagettftext не поддерживает русский.
		@imagettftext($im, 10, 0, $x_offset, $y_offset, $white, './js/font.ttf', $text);
		@imagettftext($im, 10, 0, $x_offset-1, $y_offset-1, $black, './js/font.ttf', $text);
								
		if($type == 2) imagejpeg($im, $file, '80');
		elseif($type == 3) imagepng($im, $file);
		elseif ($type==1) imagegif($im, $file);
	}

	function showMsg($msg) {

		echo '<p class="msg"><strong>'.$msg.'</strong></p>';
	}

	function getRacine() {

		$doc = str_replace('install.php', '', $_SERVER['SCRIPT_NAME']);
		return trim('http://'.$_SERVER['HTTP_HOST'].$doc);
	}

	function charAleatoire($taille='10') {

		$string = '';	 
		$chaine = 'abcdefghijklmnpqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';	 
		mt_srand((float)microtime()*1000000);	 
		for($i=0; $i<$taille; $i++)
			$string .= $chaine[ mt_rand()%strlen($chaine) ];	 
		return $string;	 
	}

	function strCut($str='', $len=25) {

		return strlen($str) > $len ? substr($str, 0, $len-3).'...' : $str;
	}

	function getSousNav() {

		$file = preg_split('[/]',$_SERVER['SCRIPT_NAME']);
		$script = array_pop($file);
		$template = preg_split('[_.]',$script);
		if(file_exists('sous_navigation/'.$template[0].'.php'))
			return 'sous_navigation/'.$template[0].'.php';
		if(file_exists('sous_navigation/'.$template[0].'s.php'))
			return 'sous_navigation/'.$template[0].'s.php';
	}

	function strToHex($string){
		$hex='';
		for ($i=0; $i < strlen($string); $i++)
		{
			$hex .= dechex(ord($string[$i]));
		}
		return $hex;
	}
	
	function hexToStr($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2)
		{
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
	
	function parseYoutube($jsondata, $url, $folder) {
		$youtube = json_decode($jsondata, TRUE);
			//Getting title
			$title = $youtube['feed']['entry'];
			$title = $title[0]['title']['$t'];
			//Getting url
			$yurl = 'http://youtube.com/v/'.$url;
			//Setting dimensions
			$height = '360';
			$width = '480';
			//Getting thumb
			$thumb = 'http://i.ytimg.com/vi/'.$url.'/hqdefault.jpg';
		//Formatting data
		$formatted = '{"title":"'.htmlspecialchars($title).'", "url":"'.$yurl.'", "width":"'.$width.'", "height":"'.$height.'", "thumb":"'.$thumb.'"}';
		//Creating file
		$fp = fopen($folder.$url.'.youtube', 'w');
		fwrite($fp, $formatted);
		fclose($fp);
	}

}
?>
