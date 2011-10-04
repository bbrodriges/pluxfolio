<?php

/**
 * Edition du compte rédacteur
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/

include('prepend.php');

# On édite la configuration
if(!empty($_POST)) {
	$plxAdmin->editRedacteur(plxUtils::unSlash($_POST));
	header('Location: parametres_compte.php');
	exit;
}

# On inclut le header
include('top.php');
?>

<h2><?php echo $ADM_usersettings_title; ?></h2>

<form action="parametres_compte.php" method="post" id="change-cf-file">
	<fieldset class="withlabel">
		<legend><?php echo $ADM_usersettings_legend; ?>:</legend>	
		<p class="field"><label><?php echo $ADM_usersettings_login; ?>:</label></p>
		<?php plxUtils::printInput('login', htmlspecialchars($_SESSION['author'],ENT_QUOTES,PLX_CHARSET)); ?>
		<p class="field"><label><?php echo $ADM_usersettings_password; ?>:</label></p>
		<?php plxUtils::printInput('pwd','','password'); ?>
		<p class="field"><label><?php echo $ADM_usersettings_confirmpassword; ?>:</label></p>
		<?php plxUtils::printInput('pwd2','','password');?>
	</fieldset>
	<p><input type="submit" value="<?php echo $ADM_savechanges; ?>" /></p>
</form>

<?php
# On inclut le footer
include('foot.php');
?>
