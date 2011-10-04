<?php

/**
 * Edition des paramètres de base
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/

include('prepend.php');

# On édite la configuration
if(!empty($_POST)) {
	$plxAdmin->editConfiguration($plxAdmin->aConf,plxUtils::unSlash($_POST));
	header('Location: parametres_base.php');
	exit;
}

# Tableau du delta
for($i=-12;$i < 14; $i++)
	$delta[ plxUtils::formatRelatif($i,2).':00' ] = plxUtils::formatRelatif($i,2).':00';

# On inclut le header
include('top.php');
include('../lib/language.php');

$yesno[0] = $ADM_no;
$yesno[1] = $ADM_yes;

?>

<h2><?php echo $ADM_basesettings_title; ?></h2>

<form action="parametres_base.php" method="post" id="change-cf-file">
	<fieldset class="withlabel">
		<legend><?php echo $ADM_basesettings_legend; ?>:</legend>	
		<p class="field"><label><?php echo $ADM_basesettings_sitetitle; ?>:</label></p>
		<?php plxUtils::printInput('title', htmlspecialchars($plxAdmin->aConf['title'],ENT_QUOTES,PLX_CHARSET)); ?>
		<p class="field"><label><?php echo $ADM_basesettings_sitedescription; ?>:</label></p>
		<?php plxUtils::printInput('description', htmlspecialchars($plxAdmin->aConf['description'],ENT_QUOTES,PLX_CHARSET)); ?>
		<p class="field"><label><?php echo $ADM_basesettings_sitedefinition; ?>:</label></p>
		<?php plxUtils::printArea('intro', htmlspecialchars($plxAdmin->aConf['intro'],ENT_QUOTES,PLX_CHARSET)); ?>
	<?php if ($plxAdmin->aConf['admin_conf']==0) { ?>
		<p class="field"><label><?php echo $ADM_basesettings_sitekeywordstag; ?>:</label></p>
		<?php plxUtils::printInput('keywordstag', htmlspecialchars($plxAdmin->aConf['keywordstag'],ENT_QUOTES,PLX_CHARSET)); ?>
		<p class="field"><label><?php echo $ADM_basesettings_sitedescriptiontag; ?>:</label></p>
		<?php plxUtils::printInput('descriptiontag', htmlspecialchars($plxAdmin->aConf['descriptiontag'],ENT_QUOTES,PLX_CHARSET)); ?>
		<p class="field"><label><?php echo $ADM_basesettings_sitelanguage; ?>:</label></p>
		<?php plxUtils::printSelect('site_lang', $langpack_list,$plxAdmin->aConf['site_lang']); ?>
		<p class="field"><label><?php echo $ADM_basesettings_maintence; ?>?</label></p>
		<?php plxUtils::printSelect('maintence', $yesno,$plxAdmin->aConf['maintence']); ?>
		<p class="field"><label><?php echo $ADM_basesettings_sitealias; ?>:</label></p>
		<?php plxUtils::printInput('racine', $plxAdmin->aConf['racine']);?>
		<p class="field"><label><?php echo $ADM_basesettings_sitetimezone; ?></label></p>
		<?php plxUtils::printSelect('delta', $delta, $plxAdmin->aConf['delta']); ?>
	<?php }; ?>
	</fieldset>
	<p><input type="submit" value="<?php echo $ADM_savechanges; ?>" /></p>
</form>

<?php
# On inclut le footer
include('foot.php');
?>
