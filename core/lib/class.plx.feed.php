<?php

/**
 * Classe plxFeed responsable du traitement global des flux de syndication
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/
class plxFeed extends plxMotor {

	var $type = false; # Type de flux de syndication (rss ou atom)

	function plxFeed($filename) {
	
		$this->get = plxUtils::getGets();
		
		$this->getConfiguration($filename);
		$this->racine = $this->aConf['racine'];
		$this->bypage = $this->aConf['bypage_feed'];
		$this->tri = $this->aConf['tri'];
		
		$this->plxGlob_arts = new plxGlob(PLX_ROOT.$this->aConf['racine_articles']);
		$this->plxGlob_coms = new plxGlob(PLX_ROOT.$this->aConf['racine_commentaires']);
		
		$this->getCategories(PLX_ROOT.$this->aConf['categories']);
	}

	function prechauffage() {

		if($this->get AND preg_match('/^(atom|rss)$/',$this->get,$capture)) {
			$this->feed = $capture[1]; # Type de flux
			$this->mode = 'article'; # Mode du flux
			# On modifie le motif de recherche
			$this->motif = '/^[0-9]{4}.([0-9]{3}|home).[0-9]{12}.[a-z0-9-]+.xml$/';
		}
		elseif($this->get AND preg_match('/^(atom|rss)\/commentaires$/',$this->get,$capture)) {
			$this->feed = $capture[1]; # Type de flux
			$this->mode = 'commentaire'; # Mode du flux
		}
		elseif($this->get AND preg_match('/^(atom|rss)\/commentaires\/article([0-9]+$)/',$this->get,$capture)) {
			$this->feed = $capture[1]; # Type de flux
			$this->mode = 'commentaire'; # Mode du flux
			# On recupere l'article cible
			$this->cible = str_pad($capture[2],4,'0',STR_PAD_LEFT); # On complete sur 4 caracteres
			# On modifie le motif de recherche
			$this->motif = '/^'.$this->cible.'.([0-9]{3}|home).[0-9]{12}.[a-z0-9-]+.xml$/';
		} else {
			$this->feed = 'atom'; # Type de flux
			$this->mode = 'article'; # Mode du flux
			# On modifie le motif de recherche
			$this->motif = '/^[0-9]{4}.([0-9]{3}|home).[0-9]{12}.[a-z0-9-]+.xml$/';
		}
	}
	
	function demarrage() {

		if($this->mode == 'commentaire' AND $this->cible) { # Flux de commentaires d'un article precis
			$this->getFiles(); # Recuperation du fichier de l'article cible
			$this->getArticles(); # Recuperation de l'article cible (on le parse)
			if(!$this->plxGlob_arts->count OR !$this->plxRecord_arts->size) { # Aucun article, on redirige
				$this->cible = $this->cible + 0;
				header('Location: ./?article'.$this->cible.'/');
				exit;
			} else { # On récupère les commentaires
				$this->getCommentaires('/^'.$this->cible.'.[0-9]{10}-[0-9]+.xml$/','rsort',0,$this->bypage);
			}
		} elseif($this->mode == 'commentaire') { # Flux de commentaires global
			$this->getCommentaires('/^[0-9]{4}.[0-9]{10}-[0-9]+.xml$/','rsort',0,$this->bypage);
		} else { # Flux d'articles
			$this->getFiles(); # Recupération des fichier des articles
			$this->getArticles(); # Recupération des articles (on les parse)
		}
		
		# Selon le mode et le feed on appelle la méthode adéquate...
		switch($this->mode.'-'.$this->feed) {
			case 'article-atom' : $this->getAtomArticles(); break;
			case 'article-rss' : $this->getRssArticles(); break;
			default : break;
		}
	}

	function getAtomArticles() {

		# Initialisation
		$last_updated = '';
		$entry = '';
		# On va boucler sur les articles (si il y'en a)
		if($this->plxRecord_arts) {
			while($this->plxRecord_arts->loop()) {
				# Traitement initial
				$content = $this->plxRecord_arts->f('chapo').$this->plxRecord_arts->f('content');
				$artId = $this->plxRecord_arts->f('numero') + 0;
				$catId = $this->plxRecord_arts->f('categorie');
				# On verifie que la categorie n'est pas "home"
				if($catId != 'home') {
					# On va verifier que la categorie existe
					if(isset($this->aCats[ $catId ]))
						$categorie = $this->aCats[ $catId ]['name'];
					else # La categorie n'existe pas
						$categorie = 'Non class&eacute;';
				} else { # Categorie "home"
					$categorie = 'Accueil';
				}
				# On check la date de publication
				if($this->plxRecord_arts->f('date') > $last_updated)
					$last_updated = $this->plxRecord_arts->f('date');
				# On affiche le flux dans un buffer
				$entry .= '<entry>'."\n";
				$entry .= "\t".'<title>'.htmlspecialchars($this->plxRecord_arts->f('title'),ENT_QUOTES,PLX_CHARSET).'</title> '."\n";
				$entry .= "\t".'<link href="'.$this->aConf['racine'].'?article'.$artId.'/'.$this->plxRecord_arts->f('url').'"/>'."\n";
				$entry .= "\t".'<id>urn:md5:'.md5($this->aConf['racine'].'?article'.$artId.'/'.$this->plxRecord_arts->f('url')).'</id>'."\n";
				$entry .= "\t".'<updated>'.$this->plxRecord_arts->f('date').'</updated>'."\n";
				$entry .= "\t".'<author><name>'.htmlspecialchars($this->plxRecord_arts->f('author'),ENT_QUOTES,PLX_CHARSET).'</name></author>'."\n";
				$entry .= "\t".'<dc:subject>'.htmlspecialchars($categorie,ENT_QUOTES,PLX_CHARSET).'</dc:subject>'."\n";
				$entry .= "\t".'<content type="html">'.htmlspecialchars($content,ENT_QUOTES,PLX_CHARSET).'</content>'."\n";
				$entry .= '</entry>'."\n";
			}
		}
		
		# On affiche le flux
		header('Content-Type: text/xml; charset='.PLX_CHARSET);
		echo '<?xml version="1.0" encoding="'.PLX_CHARSET.'" ?>'."\n";
		echo '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
		echo '<title type="html">'.htmlspecialchars($this->aConf['title'],ENT_QUOTES,PLX_CHARSET).'</title>'."\n";
		echo '<subtitle type="html">'.htmlspecialchars($this->aConf['description'],ENT_QUOTES,PLX_CHARSET).'</subtitle>'."\n";
		echo '<link href="'.$this->aConf['racine'].'feed.php?atom" rel="self" type="application/atom+xml"/>'."\n";
		echo '<link href="'.$this->aConf['racine'].'" rel="alternate" type="text/html"/>'."\n";
		echo '<updated>'.$last_updated.'</updated>'."\n";
		echo '<id>urn:md5:'.md5($this->aConf['racine']).'</id>'."\n";
		echo '<generator uri="http://pluxml.org/">Pluxml '.$this->version.'</generator>'."\n";
		echo $entry;
		echo '</feed>';
	}

	function getRssArticles() {

		# Initialisation
		$last_updated = '';
		$entry_link = '';
		$entry = '';
		# On va boucler sur les articles (si il y'en a)
		if($this->plxRecord_arts) {
			while($this->plxRecord_arts->loop()) {
				# Traitement initial
				$content = $this->plxRecord_arts->f('chapo').$this->plxRecord_arts->f('content');
				$artId = $this->plxRecord_arts->f('numero') + 0;
				$catId = $this->plxRecord_arts->f('categorie');
				# On verifie que la categorie n'est pas "home"
				if($catId != 'home') {
					# On va verifier que la categorie existe
					if(isset($this->aCats[ $catId ]))
						$categorie = $this->aCats[ $catId ]['name'];
					else # La categorie n'existe pas
						$categorie = 'Non class&eacute;';
				} else { # Categorie "home"
					$categorie = 'Accueil';
				}
				# On check la date de publication
				if($this->plxRecord_arts->f('date') > $last_updated)
					$last_updated = $this->plxRecord_arts->f('date');
				# On affiche le résumé dans un buffer
				$entry_link .= "\t\t\t".'<rdf:li rdf:resource="'.$this->aConf['racine'].'?article'.$artId.'/'.$this->plxRecord_arts->f('url').'"/>'."\n";
				# On affiche le flux dans un buffer
				$entry .= '<item rdf:about="'.$this->aConf['racine'].'?article'.$artId.'/'.$this->plxRecord_arts->f('url').'">'."\n";
				$entry .= "\t".'<title>'.htmlspecialchars($this->plxRecord_arts->f('title'),ENT_QUOTES,PLX_CHARSET).'</title> '."\n";
				$entry .= "\t".'<link>'.$this->aConf['racine'].'?article'.$artId.'/'.$this->plxRecord_arts->f('url').'</link>'."\n";
				$entry .= "\t".'<dc:date>'.$this->plxRecord_arts->f('date').'</dc:date>'."\n";
				$entry .= "\t".'<dc:creator>'.htmlspecialchars($this->plxRecord_arts->f('author'),ENT_QUOTES,PLX_CHARSET).'</dc:creator>'."\n";
				$entry .= "\t".'<description>'.htmlspecialchars($content,ENT_QUOTES,PLX_CHARSET).'</description>'."\n";
				$entry .= '</item>'."\n";
			}
		}
		
		# On affiche le flux
		header('Content-Type: text/xml; charset='.PLX_CHARSET);
		echo '<?xml version="1.0" encoding="'.PLX_CHARSET.'" ?>'."\n";
		echo '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns="http://purl.org/rss/1.0/">'."\n";
		echo '<channel rdf:about="'.$this->aConf['racine'].'">'."\n";
		echo "\t".'<title>'.htmlspecialchars($this->aConf['title'],ENT_QUOTES,PLX_CHARSET).'</title>'."\n";
		echo "\t".'<link>'.$this->aConf['racine'].'</link>'."\n";
		echo "\t".'<description>'.htmlspecialchars($this->aConf['description'],ENT_QUOTES,PLX_CHARSET).'</description>'."\n";
		echo "\t".'<lastBuildDate>'.$last_updated.'</lastBuildDate>'."\n";
		echo "\t".'<generator>Pluxml '.$this->version.'</generator>'."\n";
		echo "\t".'<dc:language>fr</dc:language>'."\n";
		echo  "\t".'<items>'."\n";
		echo "\t\t".'<rdf:Seq>'."\n";
		echo $entry_link;
		echo "\t\t".'</rdf:Seq>'."\n";
		echo "\t".'</items>'."\n";
		echo '</channel>'."\n";
		echo $entry;
		echo '</rdf:RDF>';
	}

}
?>