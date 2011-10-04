<?php

/**
 * Classe plxAdmin responsable des modifications dans l'administration
 *
 * @package PLX
 * @author	Anthony GUÉRIN, Florent MONTHEL et Stephane F
 **/
class plxAdmin extends plxMotor {

	function plxAdmin($filename) {

		parent::plxMotor($filename);
	}

	function editConfiguration($global,$content) {

		# Tableau des clés à mettre sous chaîne cdata
		$aCdata = array('title','description', 'intro', 'racine');
		# Début du fichier XML
		$xml = "<?xml version='1.0' encoding='".PLX_CHARSET."'?>\n";
		$xml .= "<document>\n";
		foreach($content as $k=>$v) {
			$global[ $k ] = $v;
		}
		foreach($global as $k=>$v) {
			if(in_array($k,$aCdata))
				$xml .= "\t<parametre name=\"$k\"><![CDATA[".$v."]]></parametre>\n";
			else
				$xml .= "\t<parametre name=\"$k\">".$v."</parametre>\n";
		}
		$xml .= "</document>";
		# On écrit le fichier
		if(plxUtils::write($xml,PLX_CONF))
			return 'Конфигурация изменена '.date('H:i:s');
		else
			return 'Ошибка при изменении конфигурации '.PLX_CONF;
	}

	function getPasswd($filename) {

		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		$array = array();
		# On verifie qu'il existe des tags "user"
		if(isset($iTags['user'])) {
			# On compte le nombre de tags "user"
			$nb = count($iTags['user']);
			# On boucle sur $nb
			for($i = 0; $i < $nb; $i++) {
				$array[ $values[ $iTags['user'][$i] ]['attributes']['login'] ] = $values[ $iTags['user'][$i] ]['value'];
			}
		}
		# On retourne le tableau
		return $array;
	}

	function editRedacteur($content) {

		# Contrôle des données
		if(trim($content['login']) == '' OR trim($content['pwd']) == '')
			return 'Merci de remplir tous les champs';
		if(trim($content['pwd']) != trim($content['pwd2']))
			return 'Le mot de passe et sa confirmation ne sont pas identiques';
		
		# Génération du fichier XML
		$xml = "<?xml version='1.0' encoding='".PLX_CHARSET."'?>\n";
		$xml .= "<document>\n";
		$xml .= "\t".'<user login="'.trim($content['login']).'">'.md5(trim($content['pwd'])).'</user>'."\n";
		$xml .= "</document>";
		
		# On écrit le fichier
		if(plxUtils::write($xml,PLX_ROOT.$this->aConf['passwords'])) {
			# On modifie les variables de sessions
			$_SESSION['author'] = trim($content['login']);
			$_SESSION['pass'] = md5(trim($content['pwd']));
			# On sort
			return 'Настройки пользователя изменены '.date('H:i:s');
		} else {
			return 'Ошибка при изменении настроек пользователя '.PLX_ROOT.$this->aConf['passwords'];
		}
	}

	function editCategories($content) {

		# Début du fichier XML
		$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
		$xml .= "<document>\n";
		# On va trier les clés selon l'ordre choisi
		foreach($content as $k=>$v) {
			if(is_numeric($k))
				$array[$k] = $content[$k.'_ordre'];
		}
		asort($array);
		# On va générer l'entrée XML pour chaque catégorie
		foreach($array as $k=>$v) {
			$cat_num = $k;
			$cat_name = trim($content[$k]);
			if($cat_name != '') {
				$cat_url = (isset($content[$k.'_url']))?trim($content[$k.'_url']):'';
				$cat_tri = $content[$k.'_tri'];
				$cat_bypage = intval($content[$k.'_bypage']);
				if($cat_url != '')
					$cat_url = plxUtils::title2url($cat_url);
				else
					$cat_url = plxUtils::title2url($cat_name);
				# URL vide après le passage de la fonction ;)
				if($cat_url == '') $cat_url = 'nouvelle-categorie';
				# On génère notre ligne
				$xml .= "\t<categorie number=\"".$cat_num."\" tri=\"".$cat_tri."\" bypage=\"".$cat_bypage."\" url=\"".$cat_url."\"><![CDATA[".$cat_name."]]></categorie>\n";
			}
		}
		$xml .= "</document>";
		# On écrit le fichier
		if(plxUtils::write($xml,PLX_ROOT.$this->aConf['categories']))
			return 'Категория изменена '.date('H:i:s');
		else
			return 'Ошибка при изменении категории '.PLX_ROOT.$this->aConf['categories'];
	}


	function editStatiques($content) {

		# Début du fichier XML
		$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
		$xml .= "<document>\n";
		# On va trier les clés selon l'ordre choisi
		foreach($content as $k=>$v) {
			if(is_numeric($k))
				$array[$k] = $content[$k.'_ordre'];
		}
		asort($array);
		# On va générer l'entrée XML pour chaque page statique
		foreach($array as $k=>$v) {
			$stat_num = $k;
			$stat_name = trim($content[$k]);
			if($stat_name != '') {
				$stat_url = (isset($content[$k.'_url']))?trim($content[$k.'_url']):'';
				$stat_active = intval($content[$k.'_active']);
				if($stat_url != '')
					$stat_url = plxUtils::title2url($stat_url);
				else
					$stat_url = plxUtils::title2url($stat_name);
				# URL vide après le passage de la fonction ;)
				if($stat_url == '') $stat_url = 'nouvelle-page-statique';
				# On va vérifier si on a besoin de renommer la page statique
				if(isset($this->aStats[ $stat_num ]) AND $this->aStats[ $stat_num ]['url'] != $stat_url) {
					if(file_exists(PLX_ROOT.$this->aConf['racine_statiques'].$stat_num.'.'.$this->aStats[ $stat_num ]['url'].'.php'))
						@rename(PLX_ROOT.$this->aConf['racine_statiques'].$stat_num.'.'.$this->aStats[ $stat_num ]['url'].'.php',PLX_ROOT.$this->aConf['racine_statiques'].$stat_num.'.'.$stat_url.'.php');
				}
				# On génère notre ligne
				$xml .= "\t<statique number=\"".$stat_num."\" active=\"".$stat_active."\" url=\"".$stat_url."\"><![CDATA[".$stat_name."]]></statique>\n";
			} else { # On supprime la ligne donc le fichier de la page statique
				if(isset($this->aStats[ $stat_num ]) AND file_exists(PLX_ROOT.$this->aConf['racine_statiques'].$stat_num.'.'.$this->aStats[ $stat_num ]['url'].'.php'))
					@unlink(PLX_ROOT.$this->aConf['racine_statiques'].$stat_num.'.'.$this->aStats[ $stat_num ]['url'].'.php');
			}
		}
		$xml .= "</document>";
		# On écrit le fichier
		if(plxUtils::write($xml,PLX_ROOT.$this->aConf['statiques']))
			return 'Статичная страница изменена '.date('H:i:s');
		else
			return 'Ошибка при изменении статичной страницы '.PLX_ROOT.$this->aConf['statiques'];
	}

	function getFileStatique($num) {

		# Emplacement de la page
		$filename = PLX_ROOT.$this->aConf['racine_statiques'].$num.'.'.$this->aStats[ $num ]['url'].'.php';
		if(file_exists($filename) AND filesize($filename) > 0) {
			if($f = fopen($filename, 'r')) {
				$content = fread($f, filesize($filename));
				fclose($f);
				# On retourne le contenu
				return $content;
			}
		}
		return null;
	}

	function editFileStatique($content) {

		# Génération du nom du fichier
		$filename = PLX_ROOT.$this->aConf['racine_statiques'].$content['id'].'.'.$this->aStats[ $content['id'] ]['url'].'.php';
		# On écrit le fichier
		if(plxUtils::write($content['content'],$filename))
			return 'Исходный код статичной страницы изменен '.date('H:i:s');
		else
			return 'Ошибка при изменении исходного кода '.$filename;
	}




	function editGalerie($content) {
		function removedir ($directory) {
			$dir = opendir($directory);
			while(($file = readdir($dir))) {
				if ( is_file ($directory."/".$file)) unlink ($directory."/".$file);
				else if ( is_dir ($directory."/".$file) && ($file != ".") && ($file != "..")) removedir ($directory."/".$file);
			}
			closedir ($dir);
			rmdir ($directory);
			return TRUE;  
		}

		# Début du fichier XML
		$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
		$xml .= "<document>\n";
		# On va trier les clés selon l'ordre choisi
		foreach($content as $k=>$v) {
			if(is_numeric($k))
				$array[$k] = $content[$k.'_ordre'];
		}
		asort($array);
		# On va générer l'entrée XML pour chaque page galeria
		foreach($array as $k=>$v) {
			$gal_num = $k;
			$gal_name = trim($content[$k]);
			if($gal_name != '') {
				$gal_url = (isset($content[$k.'_url']))?trim($content[$k.'_url']):'';
				$gal_active = intval($content[$k.'_active']);
				if($gal_url != '')
					$gal_url = plxUtils::title2url($gal_url);
				else
					$gal_url = plxUtils::title2url($gal_name);
				# URL vide après le passage de la fonction ;)
				if($gal_url == '') $gal_url = 'nouvelle-page-galeria';
				
				$url = '../../album/'.$gal_url; 
				
				# On va vérifier si on a besoin de renommer la page galeria
				if(isset($this->aGals[ $gal_num ]) AND $this->aGals[ $gal_num ]['url'] != $gal_url) {
					rename("../../album/".$this->aGals[ $gal_num ]['url'], "../../album/".$gal_url);
					if(file_exists(PLX_ROOT.$this->aConf['gallery'].$gal_num.'.'.$this->aGals[ $gal_num ]['url'].'.php'))
						{
						@rename(PLX_ROOT.$this->aConf['gallery'].$gal_num.'.'.$this->aGals[ $gal_num ]['url'].'.php',PLX_ROOT.$this->aConf['gallery'].$gal_num.'.'.$gal_url.'.php');
						}
				} 
				
				 if (!isset($this->aGals[ $gal_num ]) && !is_dir($url)) mkdir($url); 
				
				
				# On génère notre ligne
				$xml .= "\t<galerie number=\"".$gal_num."\" active=\"".$gal_active."\" url=\"".$gal_url."\"><![CDATA[".$gal_name."]]></galerie>\n";
			} else { # On supprime la ligne donc le fichier de la page galerie			
				$url = '../../album/'.$this->aGals[ $gal_num ]['url'];
				if($this->aGals[ $gal_num ]['url'] != '') removedir($url);
				
				if(isset($this->aGals[ $gal_num ]) AND file_exists(PLX_ROOT.$this->aConf['gallery'].$gal_num.'.'.$this->aGals[ $gal_num ]['url'].'.php'))
					{
					@unlink(PLX_ROOT.$this->aConf['gallery'].$gal_num.'.'.$this->aGals[ $gal_num ]['url'].'.php');
					}
			}
		}
		$xml .= "</document>";
		# On écrit le fichier
		if(plxUtils::write($xml,PLX_ROOT.$this->aConf['galerie']))
			return 'Галерея изменена '.date('H:i:s');
		else
			return 'Ошибка при изменении галереи '.PLX_ROOT.$this->aConf['galerie'];
			
			
	}

	function getFileGaleria($num) {

		# Emplacement de la page
		$filename = PLX_ROOT.$this->aConf['gallery'].$num.'.'.$this->aGals[ $num ]['url'].'.php';
		if(file_exists($filename) AND filesize($filename) > 0) {
			if($f = fopen($filename, 'r')) {
				$content = fread($f, filesize($filename));
				fclose($f);
				# On retourne le contenu
				return $content;
			}
		}
		return null;
	}

	function editFileGaleria($content) {

		# Génération du nom du fichier
		$filename = PLX_ROOT.$this->aConf['gallery'].$content['id'].'.'.$this->aGals[ $content['id'] ]['url'].'.php';
		# On écrit le fichier
		if(plxUtils::write($content['content'],$filename))
			return 'Исходный код галереи изменен '.date('H:i:s');
		else
			return 'Ошибка при изменении исходного кода '.$filename;
	}
		
	function nextIdArticle() {

		if(!$aFiles = $this->plxGlob_arts->query('/^[0-9{4}].(.*).xml$/', '', 'rsort', 0, 1))
			return '0001';
		return str_pad($this->artInfoFromFilename($aFiles['0'],'artId')+1,4, '0', STR_PAD_LEFT);
	}
		
	function editArticle($content, &$id) {

		# Détermine le numero de fichier si besoin est
		if($id == '0000' OR $id == '')
			$id = $this->nextIdArticle();
		# Vérification de l'intégrité de l'identifiant
		if(!preg_match('/^[0-9]{4}$/',$id))
			return 'Identifiant d\'article invalide !';
		# Génération de notre url d'article
		if(trim($content['url']) == '')
			$content['url'] = plxUtils::title2url($content['title']);
		else
			$content['url'] = plxUtils::title2url($content['url']);
		# URL vide après le passage de la fonction ;)
		if($content['url'] == '') $content['url'] = 'nouvel-article';
		# Génération du fichier XML
		$xml = "<?xml version='1.0' encoding='".PLX_CHARSET."'?>\n";
		$xml .= "<document>\n";
		$xml .= "\t".'<infopost>'."\n";
		$xml .= "\t\t".'<title><![CDATA['.trim($content['title']).']]></title>'."\n";
		$xml .= "\t\t".'<author><![CDATA['.$_SESSION['author'].']]></author>'."\n";
		$xml .= "\t\t".'<allow_com>'.$content['allow_com'].'</allow_com>'."\n";
		$xml .= "\t".'</infopost>'."\n";
		$xml .= "\t".'<chapo><![CDATA['.trim($content['chapo']).']]></chapo>'."\n";
		$xml .= "\t".'<content><![CDATA['.trim($content['content']).']]></content>'."\n";
		$xml .= "\t".'<adres><![CDATA['.trim($content['adres']).']]></adres>'."\n";
		$xml .= "</document>\n";
		
		# A t'on besoin de supprimer un fichier ?
		if($globArt = $this->plxGlob_arts->query('/^'.$id.'.(.*).xml$/','','sort',0,1,'all')) {
			if(file_exists(PLX_ROOT.$this->aConf['racine_articles'].$globArt['0'])) # Un fichier existe, on le supprime
				@unlink(PLX_ROOT.$this->aConf['racine_articles'].$globArt['0']);
		}
		# On genère le nom de notre fichier
		$time = $content['year'].$content['month'].$content['day'].substr(str_replace(':','',$content['time']),0,4);
		if(!preg_match('/^[0-9]{12}$/',$time)) $time = date('YmdHi'); # Check de la date au cas ou...
		$filename = PLX_ROOT.$this->aConf['racine_articles'].$id.'.'.$content['catId'].'.'.$time.'.'.$content['url'].'.xml';
		# On va mettre à jour notre fichier
		if(plxUtils::write($xml,$filename)) {
			if($content['artId'] == '0000' OR $content['artId'] == '')
				return 'Новость создана '.date('H:i:s');
			else
				return 'Новость отредактирована '.date('H:i:s');
		} else {
			return 'Ошибка при изменении новости';
		}
	}

	function delArticle($id) {

		# Vérification de l'intégrité de l'identifiant
		if(!preg_match('/^[0-9]{4}$/',$id))
			return 'Identifiant d\'article invalide !';
		# Variable d'état
		$resDelArt = $resDelCom = true;
		# Suppression de l'article
		if($globArt = $this->plxGlob_arts->query('/^'.$id.'.(.*).xml$/')) {
			@unlink(PLX_ROOT.$this->aConf['racine_articles'].$globArt['0']);
			$resDelArt = !file_exists(PLX_ROOT.$this->aConf['racine_articles'].$globArt['0']);
		}
		# Suppression des commentaires
		if($globComs = $this->plxGlob_coms->query('/^_?'.$id.'.(.*).xml$/')) {
			for($i=0; $i<$this->plxGlob_coms->count; $i++) {
				@unlink(PLX_ROOT.$this->aConf['racine_commentaires'].$globComs[$i]);
				$resDelCom = (!file_exists(PLX_ROOT.$this->aConf['racine_commentaires'].$globComs[$i]) AND $resDelCom);
			}
		}
		# On renvoi le résultat
		if($resDelArt AND $resDelCom)
			return 'Новость удалена '.date('H:i:s');
		else
			return 'Ошибка при удалении новости';
	}

}
?>
