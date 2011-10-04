<?php

/**
 * Classe plxGlob responsable de la récupération des fichiers à traiter
 *
 * @package PLX
 * @author	Anthony GUÉRIN et Florent MONTHEL
 **/
class plxGlob {

	var $dir = false; # Repertoire a checker
	var $onlyfilename = false; # Booleen indiquant si notre resultat sera relatif ou absolu
	var $count = 0; # Le nombre de resultats

	function plxGlob($dir,$rep=false,$onlyfilename=true) {

		# On initialise les variables de classe
		$this->dir = $dir;
		$this->rep = $rep;
		$this->onlyfilename = $onlyfilename;
	}

	function search($motif,$tri,$publi) {

		# On ouvre le repertoire
		$dh = opendir($this->dir);
		
		if($this->onlyfilename) # On recupere uniquement le nom du fichier
			$dirname = '';
		else # On concatene egalement le nom du repertoire
			$dirname = $this->dir;
		
		# On initialise le compteur
		$this->count = 0;
		
		# Pour chaque entree du repertoire
		while (false !== ($file = readdir($dh))) {
			# Recherche de répertoires
			if($this->rep) {
				if(is_dir($this->dir.'/'.$file) && !preg_match('/^(\.|\.\.)/',$file)){
					if(preg_match($motif,$file,$capture)){
						$array[] = $dirname.$capture[0];
						# On incremente le compteur
						$this->count++;
					}
				}
			} else { # Recherche de fichiers
				if(preg_match($motif,$file,$capture) AND !is_dir($this->dir.'/'.$file)) {
					if($tri == 'art') { # Tri selon les dates de publication (article)
						# On decoupe le nom du fichier
						$index = explode('[.]',$capture[0]);
						# On cree un tableau associatif en choisissant bien nos cles et en verifiant la date de publication
						if($publi == 'before' AND $index[2] <= date('YmdHi'))
							$array[ $index[2].$index[0] ] = $dirname.$capture[0];
						if($publi == 'after' AND $index[2] >= date('YmdHi'))
							$array[ $index[2].$index[0] ] = $dirname.$capture[0];
						if($publi == 'all')
							$array[ $index[2].$index[0] ] = $dirname.$capture[0];
						# On verifie que l'index existe
						if(isset($array[ $index[2].$index[0] ]))
							$this->count++; # On incremente le compteur
					} elseif($tri == 'com') { # Tri selon les dates de publications (commentaire)
						# On decoupe le nom du fichier
						$index = explode('[.]',$capture[0]);
						# On cree un tableau associatif en choisissant bien nos cles et en verifiant la date de publication
						if($publi == 'before' AND $index[1] <= time())
							$array[ $index[1].$index[0] ] = $dirname.$capture[0];
						if($publi == 'after' AND $index[1] >= time())
							$array[ $index[1].$index[0] ] = $dirname.$capture[0];
						if($publi == 'all')
							$array[ $index[1].$index[0] ] = $dirname.$capture[0];
						# On verifie que l'index existe
						if(isset($array[ $index[1].$index[0] ]))
							$this->count++; # On incremente le compteur
					} else { # Aucun tri
						$array[] = $dirname.$capture[0];
						# On incremente le compteur
						$this->count++;
					}
				}
			}
		}
		
		# On ferme la ressource sur le repertoire
		closedir($dh);
		
		# On retourne le tableau si celui-ci existe
		if($this->count > 0)
			return $array;
		else
			return false;
	}

	function query($motif,$tri='',$ordre='',$depart='0',$limite=false,$publi='all') {

		# Si on a des resultats
		if($rs = $this->search($motif,$tri,$publi)) {
			# Ordre de tri du tableau
			if($ordre == 'sort' AND $tri != '')
				ksort($rs);
			elseif($ordre == 'rsort' AND $tri != '')
				krsort($rs);
			elseif($ordre == 'sort' AND $tri == '')
				sort($rs);
			else
				rsort($rs);
			# On enleve les cles du tableau
			$rs = array_values($rs);
			# On a une limite, on coupe le tableau
			if($limite)
				$rs = array_slice($rs,$depart,$limite);
			# On retourne le tableau
			return $rs;
		}
		# On retourne une valeur négative
		return false;
	}

}
?>