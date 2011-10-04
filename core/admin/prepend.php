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
define('PLX_ROOT', '../../');
define('PLX_CORE', '../');
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
include_once(PLX_CORE.'lib/class.plx.admin.php');
include_once(PLX_CORE.'lib/class.plx.show.php');
include_once(PLX_CORE.'lib/class.plx.erreur.php');

# On démarre la session
session_start();

# On impose le charset
header('Content-Type: text/html; charset='.PLX_CHARSET);

# Creation de l'objet principal et premier traitement
$plxAdmin = new plxAdmin(PLX_CONF);
$pwd = $plxAdmin->getPasswd(PLX_ROOT.$plxAdmin->aConf['passwords']);

# Test sur l'identification
if(@!$auth_page AND (empty($_SESSION['admin']) OR empty($pwd[ $_SESSION['author'] ]) OR $pwd[ $_SESSION['author'] ] !== $_SESSION['pass'])) {
	header('Location: auth.php');
	exit;
}
?>
