<?php

/**
 * Classe plxMotor responsable du traitement global du script
 *
 * @package PLX
 * @author	Anthony GUÉRIN et Florent MONTHEL
 **/
class plxMotor {

	var $version = false; # Version de Pluxml
	var $start = false; # Microtime du debut de l'execution de Pluxml
	var $get = false; # Donnees variable GET
	var $racine = false; # Url de Pluxml
	var $style = false; # Dossier contenant le thème
	var $index_get = '';
	var $twidth = '200';
	var $theight = '150';
	var $toffset = '0';
	var $intro = false; # Dossier introductionen
	var $tri; # Tri d'affichage
	var $bypage = false; # Pagination
	var $page = 1; # Numéro de la page
	var $motif = false; # Motif de recherche
	var $mode = false; # Mode de traitement
	var $cible = false; # Article, categorie ou page statique cible
	var $message_com = false; # Message à la création d'un commentaire

	var $aConf = array(); # Tableau de configuration
	var $aCats = array(); # Tableau de toutes les catégories
	var $aStats = array(); # Tableau de toutes les pages statiques
	var $aGals = array(); # Tableau de toutes les pages galeries
	var $aFiles = array(); # Tableau de fichiers à traiter

	var $plxGlob_arts = null; # Objet plxGlob des articles
	var $plxGlob_coms = null; # Objet plxGlob des commentaires
	var $plxRecord_arts = null; # Objet plxRecord des articles
	var $plxRecord_coms = null; # Objet plxRecord des commentaires
	var $plxCapcha = null; # Objet plxCapcha
	var $plxErreur = null; # Objet plxErreur

	function plxMotor($filename) {

		# Version de Pluxml
		if(!is_readable(PLX_ROOT.'version')) {
			header('Content-Type: text/plain charset=UTF-8');
			echo 'Le fichier "'.PLX_ROOT.'version" est nécessaire au fonctionnement de Pluxml';
			exit;
		}
		$f = file(PLX_ROOT.'version');
		$this->version = $f['0'];
		
		# Traitement initial
		$this->start = plxUtils::microtime();
		$this->get = plxUtils::getGets();
		
		# On parse le fichier de configuration
		$this->getConfiguration($filename);
		$this->racine = $this->aConf['racine'];
		$this->bypage = $this->aConf['bypage'];
		$this->style = $this->aConf['style'];
		$this->index_get = $this->aConf['index_get'];
		if ($this->aConf['twidth']>0) $this->twidth = $this->aConf['twidth'];
		if ($this->aConf['theight']>0) $this->theight = $this->aConf['theight'];
		$this->intro = $this->aConf['intro'];
		$this->tri = $this->aConf['tri'];
		$this->categ_get = $this->aConf['categ_get'];
		$this->wysiwyg = $this->aConf['wysiwyg'];
		$this->admin_conf = $this->aConf['admin_conf'];
		$this->counter_enabled = $this->aConf['counter_enabled'];
		$this->site_lang = $this->aConf['site_lang'];
		$this->postas = $this->aConf['postas'];
		$this->images_sets = $this->aConf['images_sets'];
		$this->freshness = $this->aConf['freshness'];
		$this->freshnesstime = $this->aConf['freshnesstime'];
		$this->twitter = $this->aConf['twitter'];
		$this->maintence = $this->aConf['maintence'];
		$this->templatecheck = $this->aConf['templatecheck'];
		$this->image_caption = $this->aConf['image_caption'];
		$this->imgorderby = $this->aConf['imgorderby'];
		$this->keywordstag = $this->aConf['keywordstag'];
		$this->descriptiontag = $this->aConf['descriptiontag'];
		$this->thumbtype = $this->aConf['thumbtype'];
		$this->watermark = $this->aConf['watermark'];
		$this->watermark_text = $this->aConf['watermark_text'];
		$this->googleanalytics = $this->aConf['googleanalytics'];
		$this->yandexmetrika = $this->aConf['yandexmetrika'];
        $this->nonews = $this->aConf['nonews'];
		
		# Traitement sur les répertoires des articles et des commentaires
		$this->plxGlob_arts = new plxGlob(PLX_ROOT.$this->aConf['racine_articles']);
		$this->plxGlob_coms = new plxGlob(PLX_ROOT.$this->aConf['racine_commentaires']);
		
		# On récupère les catégories et les pages statiques
		$this->getCategories(PLX_ROOT.$this->aConf['categories']);
		$this->getStatiques(PLX_ROOT.$this->aConf['statiques']);
		$this->getGalerie(PLX_ROOT.$this->aConf['galerie']);
	}

	function getConfiguration($filename) {

		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		# On verifie qu'il existe des tags "parametre"
		if(isset($iTags['parametre'])) {
			# On compte le nombre de tags "parametre"
			$nb = count($iTags['parametre']);
			# On boucle sur $nb
			for($i = 0; $i < $nb; $i++) {
				if(isset($values[ $iTags['parametre'][$i] ]['value'])) # On a une valeur pour ce parametre
					$this->aConf[ $values[ $iTags['parametre'][$i] ]['attributes']['name'] ] = $values[ $iTags['parametre'][$i] ]['value'];
				else # On n'a pas de valeur
					$this->aConf[ $values[ $iTags['parametre'][$i] ]['attributes']['name'] ] = '';
			}
		}
	}

	function getCategories($filename) {

		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		# On verifie qu'il existe des tags "categorie"
		if(isset($iTags['categorie'])) {
			# On compte le nombre de tags "categorie"
			$nb = count($iTags['categorie']);
			# On boucle sur $nb
			for($i = 0; $i < $nb; $i++) {
				# Recuperation du nom de la categorie
				$this->aCats[ $values[ $iTags['categorie'][$i] ]['attributes']['number'] ]['name']
				= $values[ $iTags['categorie'][$i] ]['value'];
				# Recuperation de l'url de la categorie
				$this->aCats[ $values[ $iTags['categorie'][$i] ]['attributes']['number'] ]['url']
				= strtolower($values[ $iTags['categorie'][$i] ]['attributes']['url']);
				# Recuperation du tri de la categorie si besoin est
				if(isset($values[ $iTags['categorie'][$i] ]['attributes']['tri']))
					$this->aCats[ $values[ $iTags['categorie'][$i] ]['attributes']['number'] ]['tri']
					= $values[ $iTags['categorie'][$i] ]['attributes']['tri'];
				else # Tri par defaut
					$this->aCats[ $values[ $iTags['categorie'][$i] ]['attributes']['number'] ]['tri']
					= $this->aConf['tri'];
				# Recuperation du nb d'articles par page de la categorie si besoin est
				if(isset($values[ $iTags['categorie'][$i] ]['attributes']['bypage']))
					$this->aCats[ $values[ $iTags['categorie'][$i] ]['attributes']['number'] ]['bypage']
					= $values[ $iTags['categorie'][$i] ]['attributes']['bypage'];
				else # Nb d'articles par page par defaut
					$array[ $values[ $iTags['categorie'][$i] ]['attributes']['number'] ]['bypage']
					= $this->bypage;
				# Recuperer du nombre d'article de la categorie
				$motif = '/^[0-9]{4}.'.$values[ $iTags['categorie'][$i] ]['attributes']['number'].'.[0-9]{12}.[A-Za-z0-9-]+.xml$/';
				$this->aCats[ $values[ $iTags['categorie'][$i] ]['attributes']['number'] ]['articles']
				= ($this->plxGlob_arts->query($motif))?$this->plxGlob_arts->count:0;
			}
		}
	}

	function getStatiques($filename) {

		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		# On verifie qu'il existe des tags "statique"
		if(isset($iTags['statique'])) {
			# On compte le nombre de tags "statique"
			$nb = count($iTags['statique']);
			# On boucle sur $nb
			for($i = 0; $i < $nb; $i++) {
				# Recuperation du nom de la page statique
				$this->aStats[ $values[ $iTags['statique'][$i] ]['attributes']['number'] ]['name']
				= $values[ $iTags['statique'][$i] ]['value'];
				# Recuperation de l'url de la page statique
				$this->aStats[ $values[ $iTags['statique'][$i] ]['attributes']['number'] ]['url']
				= strtolower($values[ $iTags['statique'][$i] ]['attributes']['url']);
				# Recuperation de l'etat de la page
				$this->aStats[ $values[ $iTags['statique'][$i] ]['attributes']['number'] ]['active']
				= intval($values[ $iTags['statique'][$i] ]['attributes']['active']);
				# On verifie que la page statique existe bien
				$file = PLX_ROOT.$this->aConf['racine_statiques'].$values[ $iTags['statique'][$i] ]['attributes']['number'];
				$file .= '.'.$values[ $iTags['statique'][$i] ]['attributes']['url'].'.php';
				
				if(is_readable($file)) # Le fichier existe
					$this->aStats[ $values[ $iTags['statique'][$i] ]['attributes']['number'] ]['readable'] = 1;
				else # Le fichier est illisible
					$this->aStats[ $values[ $iTags['statique'][$i] ]['attributes']['number'] ]['readable'] = 0;
			}
		}
	}

	function getGalerie($filename) {

		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		# On verifie qu'il existe des tags "galerie"
		if(isset($iTags['galerie'])) {
			# On compte le nombre de tags "galerie"
			$nb = count($iTags['galerie']);
			# On boucle sur $nb
			for($i = 0; $i < $nb; $i++) {
				# Recuperation du nom de la page galerie
				$this->aGals[ $values[ $iTags['galerie'][$i] ]['attributes']['number'] ]['name']
				= $values[ $iTags['galerie'][$i] ]['value'];
				# Recuperation de l'url de la page galerie
				$this->aGals[ $values[ $iTags['galerie'][$i] ]['attributes']['number'] ]['url']
				= strtolower($values[ $iTags['galerie'][$i] ]['attributes']['url']);
				# Recuperation de l'etat de la page
				$this->aGals[ $values[ $iTags['galerie'][$i] ]['attributes']['number'] ]['active']
				= intval($values[ $iTags['galerie'][$i] ]['attributes']['active']);
				# On verifie que la page galerie existe bien
				$file = PLX_ROOT.$this->aConf['gallery'].$values[ $iTags['galerie'][$i] ]['attributes']['number'];
				$file .= '.'.$values[ $iTags['galerie'][$i] ]['attributes']['url'].'.php';
				
				if(is_readable($file)) # Le fichier existe
					$this->aGals[ $values[ $iTags['galerie'][$i] ]['attributes']['number'] ]['readable'] = 1;
				else # Le fichier est illisible
					$this->aGals[ $values[ $iTags['galerie'][$i] ]['attributes']['number'] ]['readable'] = 0;
			}
		}
	}

	function prechauffage($mode = '',$motif = '',$bypage = '') {

		if($mode != '' AND $motif != '') {
			$this->mode = $mode; # Mode
			$this->motif = $motif; # Motif de recherche
			$this->bypage = $bypage; # Nombre d'article par page
		}
		elseif($this->get AND preg_match('/^article([0-9]+)\//',$this->get,$capture)) {
			$this->mode = 'article'; # Mode article
			$this->cible = str_pad($capture[1],4,'0',STR_PAD_LEFT); # On complete sur 4 caracteres
			$this->motif = '/^'.$this->cible.'.([0-9]{3}|home).[0-9]{12}.[a-z0-9-]+.xml$/'; # Motif de recherche
			$this->bypage = NULL; # Pas de pagination pour ce mode bien sur
		}
		elseif($this->get AND preg_match('/^categorie([0-9]+)\//',$this->get,$capture)) {
			$this->mode = 'categorie'; # Mode categorie
			$this->cible = str_pad($capture[1],3,'0',STR_PAD_LEFT); # On complete sur 3 caracteres
			$this->motif = '/^[0-9]{4}.'.$this->cible.'.[0-9]{12}.[a-z0-9-]+.xml$/'; # Motif de recherche
			$this->tri = $this->aCats[ $this->cible ]['tri']; # Recuperation du tri des articles
			# On a une pagination particuliere pour la categorie (bypage != 0)
			if($this->aCats[ $this->cible ]['bypage'] > 0)
				$this->bypage = ceil($this->aCats[ $this->cible ]['bypage']);
		}			
		elseif($this->get AND preg_match('/^static([0-9]+)\//',$this->get,$capture)) {
			$this->mode = 'static'; # Mode static
			$this->cible = str_pad($capture[1],3,'0',STR_PAD_LEFT); # On complete sur 3 caracteres			
			$this->bypage = NULL; # Pas de pagination pour ce mode bien sur ;)
		}
		elseif($this->get AND preg_match('/^galeria([0-9]+)\//',$this->get,$capture)) {
			$this->mode = 'galeria'; # Mode galeria
			$this->cible = str_pad($capture[1],3,'0',STR_PAD_LEFT); # On complete sur 3 caracteres			
			$this->bypage = NULL; # Pas de pagination pour ce mode bien sur ;)
		}
		elseif($this->get AND preg_match('/^telechargement\/(.+)$/',$this->get,$capture)) {
			$this->mode = 'telechargement'; # Mode telechargement
			$this->cible = $capture[1];	
			$this->bypage = NULL; # Pas de pagination pour ce mode bien sur ;)
		}
		else {
			$this->mode = 'home';
			# On regarde si on a des articles en mode "home"
			if($this->plxGlob_arts->query('/^[0-9]{4}.home.[0-9]{12}.[a-z0-9-]+.xml$/')) {
				$this->motif = '/^[0-9]{4}.home.[0-9]{12}.[a-z0-9-]+.xml$/';
				$this->bypage = NULL; # Tous les articles sur une page
			} else { # Sinon on recupere tous les articles
				$this->motif = '/^[0-9]{4}.[0-9]{3}.[0-9]{12}.[a-z0-9-]+.xml$/';
			}
		}
	}

	function demarrage() {

		if($this->mode == 'home' OR $this->mode == 'categorie') { 
			$this->getPage(); # Recuperation de la page
			$this->getFiles('before'); # Recuperation des fichiers
			$this->getArticles(); # Recuperation des articles
			if(!$this->plxGlob_arts->count OR !$this->plxRecord_arts->size) { # Aucun article
			    header("HTTP/1.1 404 Not Found");
				$this->plxErreur = new plxErreur('Error 404');
				$this->mode = 'erreur';
			}
		}
		elseif($this->mode == 'article') {
			$this->getFiles('before'); # Recuperation des fichiers
			$this->getArticles(); # Recuperation des articles
			if(!$this->plxGlob_arts->count OR !$this->plxRecord_arts->size) { # Aucun article
			    header("HTTP/1.1 404 Not Found");
				$this->plxErreur = new plxErreur('Error 404');
				$this->mode = 'erreur';
				return;
			}
		
			# Récupération des commentaires
			
		}
		elseif($this->mode == 'static') {
			# On va verifier que la page existe vraiment
			if(!isset($this->aStats[ $this->cible ]) /*OR intval($this->aStats[ $this->cible ]['active']) != 1*/) {
				header("HTTP/1.1 404 Not Found");
				$this->plxErreur = new plxErreur('Error 404');
				$this->mode = 'erreur';
			}
			# On va verifier que la page a inclure est lisible
			elseif($this->aStats[ $this->cible ]['readable'] != 1) {
				header("HTTP/1.1 404 Not Found");
				$this->plxErreur = new plxErreur('Error 404');
				$this->mode = 'erreur';
			}
		}
		elseif($this->mode == 'galeria') {
			# On va verifier que la page existe vraiment
			if(!isset($this->aGals[ $this->cible ]) /*OR intval($this->aGals[ $this->cible ]['active']) != 1*/) {
				header("HTTP/1.1 404 Not Found");
				$this->plxErreur = new plxErreur('Error 404');
				$this->mode = 'erreur';
			}
			# On va verifier que la page a inclure est lisible
			elseif($this->aGals[ $this->cible ]['readable'] != 1) {
				header("HTTP/1.1 404 Not Found");
				$this->plxErreur = new plxErreur('Error 404');
				$this->mode = 'erreur';
			}
		}
elseif($this->mode == 'fonts') {
			# On va verifier que la page existe vraiment
			if(!isset($this->aFonts[ $this->cible ]) OR intval($this->aFonts[ $this->cible ]['active']) != 1) {
				$this->plxErreur = new plxErreur('Cette page n\'existe pas ou n\'existe plus !');
				$this->mode = 'erreur';
			}
			# On va verifier que la page a inclure est lisible
			elseif($this->aFonts[ $this->cible ]['readable'] != 1) {
				$this->plxErreur = new plxErreur('Cette page est actuellement en cours de r&eacute;daction');
				$this->mode = 'erreur';
			}
		}



elseif($this->mode == 'telechargement') {
			# On va verifier que la page existe vraiment
			if(!$this->sendTelechargement($this->cible)) {
				$this->plxErreur = new plxErreur('Le document sp&eacute;cifi&eacute; est introuvable');
				$this->mode = 'erreur';
			}
		}
	}

	function getPage() {
		# On check pour avoir le numero de page
		if(preg_match('/page([0-9]*)/',$this->get,$capture))
			$this->page = $capture[1];
	}

	function getFiles($publi='before') {

		# On fait notre traitement sur notre tri
		if($this->tri == 'asc')
			$ordre = 'sort';
		else
			$ordre = 'rsort';
		# On recupere nos fichiers (tries) selon le motif, la pagination, la date de publication
		$this->aFiles = $this->plxGlob_arts->query($this->motif,'art',$ordre,$this->bypage*($this->page-1),$this->bypage,$publi);
	}

	function getArticles() {

		if(is_array($this->aFiles)) { # On a des fichiers
			while(list($k,$v) = each($this->aFiles)) # On parcourt tous les fichiers
				$array[ $k ] = $this->parseArticle(PLX_ROOT.$this->aConf['racine_articles'].$v);
			# On stocke les enregistrements dans un objet plxRecord
			$this->plxRecord_arts = new plxRecord($array);
		}
	}
	
	function artInfoFromFilename($filename,$output) {

		# On effectue notre capture d'informations
		if(preg_match('/([0-9]{4}).([0-9]{3}|home|draft).([0-9]{12}).([a-z0-9-]+).xml$/',$filename,$capture)) {
			if($output == 'all') # On recupere toutes les informations
				return array('artId'=>$capture[1],'catId'=>$capture[2],'artDate'=>$capture[3],'artUrl'=>$capture[4]);
			if($output == 'artId') # Id de l'article
				return $capture[1];
			if($output == 'catId') # Id de la categorie
				return $capture[2];
			if($output == 'artDate') # Date
				return $capture[3];
			if($output == 'artUrl') # Url de l'article
				return $capture[4];
		}
	}

	function parseArticle($filename) {

		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		
		# Recuperation des valeurs de nos champs XML
		$art['title'] = trim($values[ $iTags['title'][0] ]['value']);
		$art['author'] = trim($values[ $iTags['author'][0] ]['value']);
		$art['allow_com'] = trim($values[ $iTags['allow_com'][0] ]['value']);
		$art['chapo'] = (isset($values[ $iTags['chapo'][0] ]['value']))?trim($values[ $iTags['chapo'][0] ]['value']):'';
		$art['content'] = (isset($values[ $iTags['content'][0] ]['value']))?trim($values[ $iTags['content'][0] ]['value']):'';
		$art['adres'] = (isset($values[ $iTags['adres'][0] ]['value']))?trim($values[ $iTags['adres'][0] ]['value']):'';
				
		# Informations obtenues en analysant le nom du fichier
		$art['filename'] = $filename;
		$art['size'] = filesize($filename);
		$tmp = $this->artInfoFromFilename($filename, 'all');
		$art['numero'] = $tmp['artId'];
		$art['categorie'] = $tmp['catId'];
		$art['url'] = $tmp['artUrl'];
		$art['date'] = plxUtils::dateToIso($tmp['artDate'],$this->aConf['delta']);
		
		
		# On retourne le tableau
		return $art;
	}

	function sendTelechargement($cible) {

		# On décrypte le nom du fichier
		//$file = PLX_ROOT.$this->aConf['documents'].plxEncrypt::decryptId($cible);
		if(@file_exists($file)) { # On lance le téléchargement
			header('Content-Description: File Transfer');
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: no-cache');
			header('Content-Length: '.filesize($file));
			readfile($file);
			return true;
		} else { # On retourne false
			return false;
		}
	}
	
	function siteMap($host) {
		$rootdate = 0;
		$units = glob(PLX_ROOT.'data/articles/*.xml');
		foreach($units as $unit) if ($rootdate < filemtime($unit)) $rootdate = date ("Y-m-d", filemtime($unit));
		$units = glob(PLX_ROOT.'data/galerie/*.php');
		foreach($units as $unit) if ($rootdate < filemtime($unit)) $rootdate = date ("Y-m-d", filemtime($unit));
		$units = glob(PLX_ROOT.'data/statiques/*.php');
		foreach($units as $unit) if ($rootdate < filemtime($unit)) $rootdate = date ("Y-m-d", filemtime($unit));
		
		$fp = fopen(PLX_ROOT.'sitemap.xml', 'w');
		fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>');
		fwrite($fp, "\n");
		fwrite($fp, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
		fwrite($fp, "\n");
			fwrite($fp, "\t<url>\n");
				fwrite($fp, "\t\t<loc>");
				fwrite($fp, $host);
				fwrite($fp, "</loc>\n");
				fwrite($fp, "\t\t<lastmod>".$rootdate."</lastmod>\n");
				fwrite($fp, "\t\t<priority>0.8</priority>\n");
				fwrite($fp, "\t\t<image:image>\n");
					$units = glob(PLX_ROOT.'album/*');
					foreach($units as $unit) {
						if($unit!='.' && $unit!='..'){
							$files = glob("$unit/*.*");
							foreach($files as $file) 
								if(!strpos($file,'.tb')) fwrite($fp, "\t\t\t<image:loc>".$host.substr($file, 6)."</image:loc>\n");
						}		
					}
				fwrite($fp, "\t\t</image:image>\n");
			fwrite($fp, "\t</url>\n");
		
		$units = glob(PLX_ROOT.'data/articles/*.xml');
		foreach($units as $unit) {
			$unittime = date ("Y-m-d", filemtime($unit));
			$unit = explode('/',$unit);
			$unit = explode('.',$unit[4]);
			fwrite($fp, "\t<url>\n");
				fwrite($fp, "\t\t<loc>");
				fwrite($fp, $host.'?article'.round($unit[0]).'/'.$unit[3]);
				fwrite($fp, "</loc>\n");
				fwrite($fp, "\t\t<lastmod>".$unittime."</lastmod>\n");
				fwrite($fp, "\t\t<priority>0.3</priority>\n");
			fwrite($fp, "\t</url>\n");
		}
		
		$units = glob(PLX_ROOT.'data/galerie/*.php');
		foreach($units as $unit) {
			$unit = explode('/',$unit);
			$unit = explode('.',$unit[4]);
			fwrite($fp, "\t<url>\n");
				fwrite($fp, "\t\t<loc>");
				fwrite($fp, $host.'?galeria'.round($unit[0]).'/'.$unit[1]);
				fwrite($fp, "</loc>\n");
				fwrite($fp, "\t\t<priority>0.5</priority>\n");
			fwrite($fp, "\t</url>\n");
		}
		
		$units = glob(PLX_ROOT.'data/statiques/*.php');
		foreach($units as $unit) {
			$unit = explode('/',$unit);
			$unit = explode('.',$unit[4]);
			fwrite($fp, "\t<url>\n");
				fwrite($fp, "\t\t<loc>");
				fwrite($fp, $host.'?static'.round($unit[0]).'/'.$unit[1]);
				fwrite($fp, "</loc>\n");
				fwrite($fp, "\t\t<priority>0.3</priority>\n");
			fwrite($fp, "\t</url>\n");
		}
		
		fwrite($fp, '</urlset>');
		fclose($fp);
	}
	
}
?>
