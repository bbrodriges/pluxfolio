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
	include_once(PLX_CORE.'lib/class.plx.glob.php');
	include_once(PLX_CORE.'lib/class.plx.record.php');
	include_once(PLX_CORE.'lib/class.plx.motor.php');
	include_once(PLX_CORE.'lib/class.plx.feed.php');
	
if(empty($_GET) || $_GET[link]=='' || $_GET[title]=='') {	
	# Creation de l'objet principal et lancement du traitement
	$plxFeed = new plxFeed(PLX_CONF);
	$plxFeed->prechauffage();
	$plxFeed->demarrage();
} else {
	$link = explode('/', $_GET[link]);
	if($_GET[link]!='' && $_GET[title]!='' && file_exists('album/'.$link[1])) {
		$plxMotor = new plxMotor(PLX_CONF);
		echo '<?xml version="1.0"?>'."\n";
		echo '<rss version="2.0">'."\n";
		echo '	<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/">';
		echo '	<channel rdf:about="'.$plxMotor->racine.'feed.php?link='.$_GET[link].'&title='.urlencode($_GET[title]).'">'."\n";
		echo '		<title>'.$_GET[title].'</title>'."\n";
		echo '		<link>'.$plxMotor->racine.'?'.$_GET[link].'</link>'."\n";
		$images = glob('album/'.$link[1].'/*.*');
		foreach($images as $image) {
			if(!strpos($image, '.tb')) {
				$exploded = explode('.', $image);
				$exploded = explode('!', plxUtils::hexToStr($exploded[0]));
				echo '			<image rdf:about="'.$plxMotor->racine.$image.'">'."\n";
				echo '				<title>'.$exploded[0].'</title>'."\n";
				if($exploded[1]!='') echo '				<description>'.$exploded[1].'</description>'."\n";
				if($exploded[2]!='') echo '				<tag>'.$exploded[2].'</tag>'."\n";
				echo '				<url>'.$plxMotor->racine.$image.'</url>'."\n";
				echo '			</image>'."\n";
			}
		}
		
		echo '	</channel>'."\n";
		echo '</rss>';
	}
}
?>