<?php

/**
 * Edition des paramètres d'affichage
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/

include('prepend.php');

# On édite la configuration
if(!empty($_POST)) {
	$plxAdmin->editConfiguration($plxAdmin->aConf,plxUtils::unSlash($_POST));
	header('Location: parametres_affichage.php');
	exit;
}

# On inclut le header
include('top.php');
?>

<h2><?php echo $ADM_info_title; ?></h2>

<p><?php echo $ADM_info_description; ?></p>

<ul>
	<li><strong><?php echo $ADM_info_version.': '.$plxAdmin->version.' ('.$ADM_info_encoding.' '.PLX_CHARSET.')'; ?></strong></li>
	<li><?php plxUtils::testWrite(PLX_CONF); ?></li>
	<li><?php plxUtils::testWrite(PLX_ROOT.$plxAdmin->aConf['categories']); ?></li>
	<li><?php plxUtils::testWrite(PLX_ROOT.$plxAdmin->aConf['statiques']); ?></li>
	<li><?php plxUtils::testWrite(PLX_ROOT.$plxAdmin->aConf['passwords']); ?></li>
	<li><?php plxUtils::testWrite(PLX_ROOT.$plxAdmin->aConf['racine_articles']); ?></li>
	<li><?php plxUtils::testWrite(PLX_ROOT.$plxAdmin->aConf['racine_statiques']); ?></li>
	<li><?php echo $ADM_info_catnumber; ?>: <?php echo count($plxAdmin->aCats); ?></li>
	<li><?php echo $ADM_info_staticnumber; ?>: <?php echo count($plxAdmin->aStats); ?></li>
	<li><?php echo $ADM_info_loginas; ?>: <?php echo $_SESSION['author']; ?></li>
</ul>

<ul>
	<li><?php echo $ADM_info_phpversion; ?>: <?php echo phpversion(); ?></li>
	<li><?php echo $ADM_info_magicquotes; ?>: <?php echo get_magic_quotes_gpc(); ?></li>
</ul>

<?php
# On inclut le footer
include('foot.php');
?>
