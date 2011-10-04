<?php

/**
 * Classe plxShow responsable de l'affichage sur stdout
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/
class plxShow {

	var $plxMotor = false; # Objet plxMotor

	function plxShow(&$plxMotor) {

		$this->plxMotor = &$plxMotor;
	}

	function charset($casse='min') {

		if($casse != 'min') # En majuscule
			echo strtoupper(PLX_CHARSET);
		else # En minuscule
			echo strtolower(PLX_CHARSET);
	}

	function version() {

		echo $this->plxMotor->version;
	}

	function get() {

		echo $this->plxMotor->get;
	}

	function template() {

		echo PLX_ROOT.'themes/'.$this->plxMotor->style;
	}

	function introduc() {

		echo $this->plxMotor->intro;
	}

	function pageTitle() {

		if($this->plxMotor->mode == 'home') {
			echo htmlspecialchars($this->plxMotor->aConf['title'].' - '.$this->plxMotor->aConf['description'],ENT_QUOTES,PLX_CHARSET);
			return;
		}
		if($this->plxMotor->mode == 'categorie') {
			echo htmlspecialchars($this->plxMotor->aConf['title'].' - '.$this->plxMotor->aCats[ $this->plxMotor->cible ]['name'],ENT_QUOTES,PLX_CHARSET);
			return;
		}
		if($this->plxMotor->mode == 'article') {
			echo htmlspecialchars($this->plxMotor->plxRecord_arts->f('title').' - '.$this->plxMotor->aConf['title'],ENT_QUOTES,PLX_CHARSET);
			return;
		}
		if($this->plxMotor->mode == 'static') {
			echo htmlspecialchars($this->plxMotor->aConf['title'].' - '.$this->plxMotor->aStats[ $this->plxMotor->cible ]['name'],ENT_QUOTES,PLX_CHARSET);
			return;
		}
		if($this->plxMotor->mode == 'galeria') {
			echo htmlspecialchars($this->plxMotor->aConf['title'].' - '.$this->plxMotor->aGals[ $this->plxMotor->cible ]['name'],ENT_QUOTES,PLX_CHARSET);
			return;
		}
		if($this->plxMotor->mode == 'fonts') {
			echo htmlspecialchars($this->plxMotor->aConf['title'].' - '.$this->plxMotor->aStats[ $this->plxMotor->cible ]['name'],ENT_QUOTES,PLX_CHARSET);
			return;
		}				
		if($this->plxMotor->mode == 'erreur') {
			echo htmlspecialchars($this->plxMotor->aConf['title'],ENT_QUOTES,PLX_CHARSET).' - '.$this->plxMotor->plxErreur->getMessage();
			return;
		}
	}

	function mainTitle($type='') {

		$title = htmlspecialchars($this->plxMotor->aConf['title'],ENT_QUOTES,PLX_CHARSET);
		if($type == 'link') # Type lien
			echo '<a href="./" title="'.$title.'">'.$title.'</a>';
		else # Type normal
			echo $title;
	}

	function subTitle() {

		echo htmlspecialchars($this->plxMotor->aConf['description'],ENT_QUOTES,PLX_CHARSET);
	}

	function staticList($extra='') {
	if ($this->plxMotor->index_get==0) $href=''; else $href="?news";
		# Si on a la variable extra, on affiche un lien vers la page d'accueil
		if($extra != '') {
			$title = htmlspecialchars($this->plxMotor->aConf['title'],ENT_QUOTES,PLX_CHARSET);
			if($this->plxMotor->mode == 'home' AND $this->plxMotor->cible == '')
				echo '<li class="active"><a class="'.$title.'" href="./'.$href.'" title="'.$title.'">'.$extra.'</a></li>';
			else
				echo '<li><a class="'.$title.'" href="./'.$href.'" title="'.$title.'">'.$extra.'</a></li>';
		}
        if($this->plxMotor->aGals) {
			foreach($this->plxMotor->aGals as $k=>$v) {
				if($v['readable'] == 1 AND $v['active'] == 1 AND $v['hidden'] == 0) { # La page existe bien et elle est active
					$name = htmlspecialchars($v['name'],ENT_QUOTES,PLX_CHARSET);
					$url = './?galeria'.intval($k).'/'.$v['url'];
					if($this->plxMotor->mode == 'galeria' AND $this->plxMotor->cible == $k)
						echo '<li class="active"><a class="'.$name.'" href="'.$url.'" title="'.$name.'">'.$name.'</a></li>';
					else
						echo '<li><a class="'.$name.'" href="'.$url.'" title="'.$name.'">'.$name.'</a></li>';
				}
			} # Fin du while
		}

		# On verifie qu'il y a des pages statiques
		if($this->plxMotor->aStats) {
			foreach($this->plxMotor->aStats as $k=>$v) {
				if($v['readable'] == 1 AND $v['active'] == 1) { # La page existe bien et elle est active
					$name = htmlspecialchars($v['name'],ENT_QUOTES,PLX_CHARSET);
					$url = './?static'.intval($k).'/'.$v['url'];
					if($this->plxMotor->mode == 'static' AND $this->plxMotor->cible == $k)
						echo '<li class="active"><a class="'.$name.'" href="'.$url.'" title="'.$name.'">'.$name.'</a></li>';
					else
						echo '<li><a class="'.$name.'" href="'.$url.'" title="'.$name.'">'.$name.'</a></li>';
				}
			} # Fin du while
		}
		
				
		
	}

	function catList($extra='', $format='#cat_name') {

		# Si on a la variable extra, on affiche un lien vers la page d'accueil (avec $extra comme nom)
		if($extra != '') {
			$title = htmlspecialchars($this->plxMotor->aConf['title'],ENT_QUOTES,PLX_CHARSET);
			if($this->plxMotor->mode == 'home' AND $this->plxMotor->cible == '')
				echo '<li class="active"><a href="./" title="'.$title.'">'.$extra.'</a></li>';
			elseif($this->plxMotor->mode == 'article' AND $this->plxMotor->plxRecord_arts->f('categorie') == 'home')
				echo '<li class="active"><a href="./" title="'.$title.'">'.$extra.'</a></li>';
			else
				echo '<li><a href="./" title="'.$title.'">'.$extra.'</a></li>';
		}
		# On verifie qu'il y a des categories
		if($this->plxMotor->aCats) {
			foreach($this->plxMotor->aCats as $k=>$v) {
				if($v['articles'] > 0) { # On a des articles
					$v['name'] = htmlspecialchars($v['name'],ENT_QUOTES,PLX_CHARSET);
					# On modifie nos motifs
					$name = str_replace('#cat_id',intval($k),$format);
					$name = str_replace('#cat_name',$v['name'],$name);
					$name = str_replace('#art_nb',$v['articles'],$name);
					# Ok
					$url = './?categorie'.intval($k).'/'.$v['url'];
					if($this->plxMotor->mode == 'categorie' AND $this->plxMotor->cible == $k)
						echo '<li class="active"><a href="'.$url.'" title="'.$v['name'].'">'.$name.'</a></li>';
					elseif($this->plxMotor->mode == 'article' AND $this->plxMotor->plxRecord_arts->f('categorie') == $k)
						echo '<li class="active"><a href="'.$url.'" title="'.$v['name'].'">'.$name.'</a></li>';
					else
						echo '<li><a href="'.$url.'" title="'.$v['name'].'">'.$name.'</a></li>';
				}
			} # Fin du while
		}
	}

	function catId() {

		# On va verifier que la categorie existe en mode categorie
		if($this->plxMotor->mode == 'categorie' AND isset($this->plxMotor->aCats[ $this->plxMotor->cible ]))
			echo intval($this->plxMotor->cible);
		# On va verifier que la categorie existe en mode article
		elseif($this->plxMotor->mode == 'article' AND isset($this->plxMotor->aCats[ $this->plxMotor->plxRecord_arts->f('categorie') ]))
			echo intval($this->plxMotor->plxRecord_arts->f('categorie'));
		else
			echo '0';
	}

	function catName($type='') {

		# On va verifier que la categorie existe en mode categorie
		if($this->plxMotor->mode == 'categorie' AND isset($this->plxMotor->aCats[ $this->plxMotor->cible ])) {
			# On recupere les infos de la categorie
			$id = $this->plxMotor->cible;
			$name = htmlspecialchars($this->plxMotor->aCats[ $id ]['name'],ENT_QUOTES,PLX_CHARSET);
			$url = $this->plxMotor->aCats[ $id ]['url'];
			# On effectue l'affichage
			if($type == 'link')
				echo '<a href="./?categorie'.intval($id).'/'.$url.'" title="'.$name.'">'.$name.'</a>';
			else
				echo $name;
		}
		# On va verifier que la categorie existe en mode article
		elseif($this->plxMotor->mode == 'article' AND isset($this->plxMotor->aCats[ $this->plxMotor->plxRecord_arts->f('categorie') ])) {
			# On recupere les infos de la categorie
			$id = $this->plxMotor->plxRecord_arts->f('categorie');
			$name = htmlspecialchars($this->plxMotor->aCats[ $id ]['name'],ENT_QUOTES,PLX_CHARSET);
			$url = $this->plxMotor->aCats[ $id ]['url'];
			# On effectue l'affichage
			if($type == 'link')
				echo '<a href="./?categorie'.intval($id).'/'.$url.'" title="'.$name.'">'.$name.'</a>';
			else
				echo $name;
		}
		# Mode home
		elseif($this->plxMotor->mode == 'home') {
			if($type == 'link')
				echo '<a href="./" title="'.htmlspecialchars($this->plxMotor->aConf['title'],ENT_QUOTES,PLX_CHARSET).'">Accueil</a>';
			else
				echo 'Accueil';
		} else {
			echo 'Non class&eacute;';
		}
	}

	function artId() {

		return intval($this->plxMotor->plxRecord_arts->f('numero'));
	}

	function artTitle($type='') {

		if($type == 'link') { # Type lien
			# On recupere les infos de l'article
			$num = $this->artId();
			$title = htmlspecialchars($this->plxMotor->plxRecord_arts->f('title'),ENT_QUOTES,PLX_CHARSET);
			$url = $this->plxMotor->plxRecord_arts->f('url');
			# On effectue l'affichage
			echo '<a href="./?article'.$num.'/'.$url.'" title="'.$title.'">'.$title.'</a>';
		} else { # Type normal
			echo htmlspecialchars($this->plxMotor->plxRecord_arts->f('title'),ENT_QUOTES,PLX_CHARSET);
		}
	}

	function artAuthor() {

		echo htmlspecialchars($this->plxMotor->plxRecord_arts->f('author'),ENT_QUOTES,PLX_CHARSET);
	}

	function artDate() {

		echo plxUtils::dateIsoToHum($this->plxMotor->plxRecord_arts->f('date'));
	}

	function artHour() {

		echo plxUtils::heureIsoToHum($this->plxMotor->plxRecord_arts->f('date'));
	}

	function artCat() {

		# Initialisation de notre variable interne
		$catId = $this->plxMotor->plxRecord_arts->f('categorie');
		# On verifie que la categorie n'est pas "home"
		if($catId != 'home') {
			# On va verifier que la categorie existe
			if(isset($this->plxMotor->aCats[ $catId ])) {
				# On recupere les infos de la categorie
				$name = htmlspecialchars($this->plxMotor->aCats[ $catId ]['name'],ENT_QUOTES,PLX_CHARSET);
				$url = $this->plxMotor->aCats[ $catId ]['url'];
				# On effectue l'affichage
				echo '<a href="./?categorie'.intval($catId).'/'.$url.'" title="'.$name.'">'.$name.'</a>';
			} else { # La categorie n'existe pas
				echo 'Non class&eacute;';
			}
		} else { # Categorie "home"
			echo '<a href="./" title="'.htmlspecialchars($this->plxMotor->aConf['title'],ENT_QUOTES,PLX_CHARSET).'">Последние известия</a>';
		}
	}
	
		function artCateg() {

		$catId = $this->plxMotor->plxRecord_arts->f('categorie');
		if($catId != 'home') {
			if(isset($this->plxMotor->aCats[ $catId ])) {
				$url = $this->plxMotor->aCats[ $catId ]['url'];
				echo ''.$url.'';
			} else {
				echo 'none';
			}
		} else { 
			echo '';
		}
	}

	function artChapo() {
	
		include('core/lang/'.$this->plxMotor->aConf['site_lang'].'.php');

		# On verifie qu'un chapo existe
		if($this->plxMotor->plxRecord_arts->f('chapo') != '') {
			# On recupere les infos de l'article
			$num = $this->artId();
			$title = htmlspecialchars($this->plxMotor->plxRecord_arts->f('title'),ENT_QUOTES,PLX_CHARSET);
			$url = $this->plxMotor->plxRecord_arts->f('url');
			# On effectue l'affichage
			echo $this->plxMotor->plxRecord_arts->f('chapo') ;
			echo ' <a class="more" href="./?article'.$num.'/'.$url.'" title="full text : '.$title.'">'.$SITE_article_more."</a>\n";
		} else { # Pas de chapo, affichage du contenu
			echo $this->plxMotor->plxRecord_arts->f('content')."\n";
		}
	}

	function artContent() {

		echo $this->plxMotor->plxRecord_arts->f('chapo')."\n"; # Chapo
		echo $this->plxMotor->plxRecord_arts->f('content')."\n"; # Contenu
	}

	function artAdres() {
		echo $this->plxMotor->plxRecord_arts->f('adres')."\n"; # Chapo
	}

	function artFeed($type='atom') {

		if($type == 'rss') # Type RSS
			echo '<a href="./feed.php?rss" title="RSS">News RSS</a>';
		else # Type ATOM
			include('core/lang/'.$this->plxMotor->aConf['site_lang'].'.php');
			echo '<a href="./feed.php?atom" title="Atom">'.$SITE_atomfeed.'</a>';
	}

	function staticTitle() {

		echo htmlspecialchars($this->plxMotor->aStats[ $this->plxMotor->cible ]['name'],ENT_QUOTES,PLX_CHARSET);
	}

	function staticContent() {

		# On genere le nom du fichier a inclure
		$file = PLX_ROOT.$this->plxMotor->aConf['racine_statiques'].$this->plxMotor->cible;
		$file .= '.'.$this->plxMotor->aStats[ $this->plxMotor->cible ]['url'].'.php';
		# Inclusion du fichier
		require $file;
	}

    function galTitle($name) {
		return htmlspecialchars($this->plxMotor->aGals[ $this->plxMotor->cible ]['name'],ENT_QUOTES,PLX_CHARSET);
	}
    
	function visualis() {

		# On genere le nom du fichier a inclure
		$file = PLX_ROOT.$this->plxMotor->aConf['gallery'].$this->plxMotor->cible;
		$file .= '.'.$this->plxMotor->aGals[ $this->plxMotor->cible ]['url'].'.php';
		# Inclusion du fichier
		require $file;
	}

	function pagination() {
	
		include('core/lang/'.$this->plxMotor->aConf['site_lang'].'.php');

		# On verifie que la variable bypage n'est pas nulle
		if($this->plxMotor->bypage) {
			# Calcul des pages
			$prev_page = $this->plxMotor->page - 1;
			$next_page = $this->plxMotor->page + 1;
			$last_page = ceil($this->plxMotor->plxGlob_arts->count/$this->plxMotor->bypage);
			if($this->plxMotor->mode == 'home') { # En mode home
				# Generation des URLs
				$p_url = './?page'.$prev_page; # Page precedente
				$n_url = './?page'.$next_page; # Page suivante
				$l_url = './?page'.$last_page; # Derniere page
				$f_url = './'; # Premiere page
			} elseif($this->plxMotor->mode == 'categorie') { # En mode categorie
				# Generation des URLs
				$get = explode('/',$this->plxMotor->get);
				$p_url = './?'.$get[0].'/'.$get[1].'/page'.$prev_page; # Page precedente
				$n_url = './?'.$get[0].'/'.$get[1].'/page'.$next_page; # Page suivante
				$l_url = './?'.$get[0].'/'.$get[1].'/page'.$last_page; # Derniere page
				$f_url = './?'.$get[0].'/'.$get[1]; # Premiere page
			}
			# On effectue l'affichage
			if($this->plxMotor->page > 2) # Si la page active > 2 on affiche un lien 1ere page
				echo '<a href="'.$f_url.'" title="Aller а la premi&egrave;re page">&lt;&lt;</a> | ';
			if($this->plxMotor->page > 1) # Si la page active > 1 on affiche un lien page precedente
				echo '<a class="pagi" href="'.$p_url.'" title="back">'.$prev_page.'</a> | ';
			# Affichage de la page courante
			echo $ADM_page.' '.$this->plxMotor->page.' '.$ADM_of.' '.$last_page;
			if($this->plxMotor->page < $last_page) # Si la page active < derniere page on affiche un lien page suivante
				echo ' | <a class="pagi" href="'.$n_url.'" title="next">'.$next_page.'</a>';
			if(($this->plxMotor->page + 1) < $last_page) # Si la page active++ < derniere page on affiche un lien derniere page
				echo ' | <a href="'.$l_url.'" title="Aller а la derni&egrave;re page">&gt;&gt;</a>';
		}
	}

	function erreurMessage() {

		echo $this->plxMotor->plxErreur->message;
	}	

}
?>
