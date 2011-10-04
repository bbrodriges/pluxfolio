<?php
# ------------------ BEGIN LICENSE BLOCK ------------------
#
# This file is part of Pluxml.
#
# Copyright (c) 2008 Florent MONTHEL and contributors
# Copyright (c) 2006-2008 Anthony GUERIN
# Licensed under the GPL license.
# See http://www.gnu.org/licenses/gpl.html
#
# ------------------- END LICENSE BLOCK -------------------

# Configuration avançée #
define('PLX_ROOT', './');
define('PLX_CORE', PLX_ROOT.'core/');
define('PLX_CONF', PLX_ROOT.'data/configuration/parametres.xml');

# On verifie que Pluxml est installé
if(!file_exists(PLX_CONF)) {
	header('Location: '.PLX_ROOT.'install.php');
	exit;
}

# On inclut les librairies nécessaires
include_once(PLX_ROOT.'config.php');
include_once(PLX_CORE.'lib/class.plx.utils.php');
include_once(PLX_CORE.'lib/class.plx.erreur.php');
include_once(PLX_CORE.'lib/class.plx.glob.php');
include_once(PLX_CORE.'lib/class.plx.record.php');
include_once(PLX_CORE.'lib/class.plx.motor.php');
include_once(PLX_CORE.'lib/class.plx.feed.php');
include_once(PLX_CORE.'lib/class.plx.show.php');
# Creation de l'objet principal et lancement du traitement
$plxMotor = new plxMotor(PLX_CONF);
if ($plxMotor->get =='')
	{
	if ($plxMotor->index_get == 0 || !isset($plxMotor->aGals[$plxMotor->index_get])) $plxMotor->get ='';
	if ($plxMotor->index_get != 0 && isset($plxMotor->aGals[$plxMotor->index_get])) $plxMotor->get = 'galeria'.intval($plxMotor->index_get).'/'.$plxMotor->aGals[$plxMotor->index_get]['url'];		if ($plxMotor->index_get != 0 && isset($plxMotor->aStats[$plxMotor->index_get])) $plxMotor->get = 'static'.intval($plxMotor->index_get).'/'.$plxMotor->aStats[$plxMotor->index_get]['url'];
	}
	
$plxMotor->prechauffage();

$plxMotor->demarrage();
# Creation de l'objet d'affichage
$plxShow = new plxShow($plxMotor);
# On démarre la bufferisation
ob_start();

if(file_exists(PLX_ROOT.'themes/'.$plxMotor->style.'/'.$plxMotor->mode.'.php')) {
	# On impose le charset
	header('Content-Type: text/html; charset='.PLX_CHARSET);
	# Insertion du template
	include(PLX_ROOT.'themes/'.$plxMotor->style.'/'.$plxMotor->mode.'.php');
} else {
	header('Content-Type: text/plain');
	echo 'Le fichier cible Pluxml est introuvable ('.PLX_ROOT.'themes/'.$plxMotor->style.'/'.$plxMotor->mode.'.php) !';
}

# On envoi le buffer sur stdout
ob_end_flush();
?>