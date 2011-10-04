<?php

/**
 * Edition des paramètres avancés
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/

include('prepend.php');

# On édite la configuration
if(!empty($_POST)) {
	$plxAdmin->editConfiguration($plxAdmin->aConf,plxUtils::unSlash($_POST));
	header('Location: parametres_avances.php');
	exit;
}

# On inclut le header
include('top.php');
?>

<h2><?php echo $ADM_pathssettings_title; ?></h2>

<form action="parametres_avances.php" method="post" id="change-cf-file">
	<fieldset class="withlabel">
		<legend><?php echo $ADM_pathssettings_legend; ?>:</legend>
		<p class="field"><label><?php echo $ADM_pathssettings_picturesfolder; ?>:</label></p>
		<?php plxUtils::printInput('images', $plxAdmin->aConf['images']); ?>
		<p class="field"><label><?php echo $ADM_pathssettings_filesfolder; ?>:</label></p>
		<?php plxUtils::printInput('documents', $plxAdmin->aConf['documents']); ?>
		<p class="field"><label><?php echo $ADM_pathssettings_articlesfolder; ?>:</label></p>
		<?php plxUtils::printInput('racine_articles', $plxAdmin->aConf['racine_articles']); ?>
		<p class="field"><label><?php echo $ADM_pathssettings_staticsfolder; ?>:</label></p>
		<?php plxUtils::printInput('racine_statiques', $plxAdmin->aConf['racine_statiques']); ?>
		<p class="field"><label><?php echo $ADM_pathssettings_categoriesfile; ?>:</label></p>
		<?php plxUtils::printInput('categories', $plxAdmin->aConf['categories']); ?>
		<p class="field"><label><?php echo $ADM_pathssettings_statictitlesfile; ?>:</label></p>
		<?php plxUtils::printInput('statiques', $plxAdmin->aConf['statiques']); ?>
		<p class="field"><label><?php echo $ADM_pathssettings_loginpassword; ?>:</label></p>
		<?php plxUtils::printInput('passwords', $plxAdmin->aConf['passwords']); ?>
	</fieldset>
	<p><input type="submit" value="<?php echo $ADM_savechanges; ?>" /></p>
</form>

<?php
# On inclut le footer
include('foot.php');
?>
