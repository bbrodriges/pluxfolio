<?php

/**
 * Classe plxErreur responsable des erreurs de traitement
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/
class plxErreur {

	var $message = false; # Message d'erreur

	function plxErreur($erreur) {

		# Initialisation des variables de classe
		$this->message = $erreur;
	}

	function getMessage() {

		# On retourne le message d'erreur
		return $this->message;
	}
}
?>