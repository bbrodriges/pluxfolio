<?php

/**
 * Edition des paramètres de admin
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/

include('prepend.php');

# On édite la configuration
if(!empty($_POST)) {
	$plxAdmin->editConfiguration($plxAdmin->aConf,plxUtils::unSlash($_POST));
	header('Location: parametres_admin.php');
	exit;
}

# On inclut le header
include('top.php');

# WYSIWYG
$wysiwyg[0] = $ADM_no;
$wysiwyg[1] = $ADM_yes;


$admin_conf[0] = $ADM_adminsettings_extended;
$admin_conf[1] = $ADM_adminsettings_simple;

?>

<h2><?php echo $ADM_adminsettings_title; ?></h2>

<form action="parametres_admin.php" method="post" id="change-cf-file">
	<fieldset class="withlabel">
		<legend><?php echo $ADM_adminsettings_legend; ?>:</legend>	
		<p class="field"><label><?php echo $ADM_adminsettings_admintype; ?>:</label></p>
		<?php plxUtils::printSelect('admin_conf', $admin_conf, $plxAdmin->aConf['admin_conf']); ?>
	<?php if ($plxAdmin->aConf['admin_conf']==0) { ?>
		<p class="field"><label><?php echo $ADM_adminsettings_wysiwyg; ?>?</label></p>
		<?php plxUtils::printSelect('wysiwyg', $wysiwyg, $plxAdmin->aConf['wysiwyg']); ?>
   <!-- <p class="field"><label><?php echo $ADM_adminsettings_postas; ?>?</label></p>
		<?php plxUtils::printSelect('postas', $wysiwyg, $plxAdmin->aConf['postas']); ?> -->
		<p class="field"><label><?php echo $ADM_adminsettings_templatecheck; ?>?</label></p>
		<?php plxUtils::printSelect('templatecheck', $wysiwyg, $plxAdmin->aConf['templatecheck']); ?>
	<?php }; ?>
	</fieldset>
	<p><input type="submit" value="<?php echo $ADM_savechanges; ?>" /></p>
</form>

<?php
# On inclut le footer
include('foot.php');
?>
