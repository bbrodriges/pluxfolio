<?php

/**
 * Edition des paramètres d'analytics
 *
 * @package PLX
 **/

include('prepend.php');

# On édite la configuration
if(!empty($_POST)) {
	$plxAdmin->editConfiguration($plxAdmin->aConf,plxUtils::unSlash($_POST));
	header('Location: parametres_analytics.php');
	exit;
}

# On inclut le header
include('top.php');

# On récupère les templates
$tpl = new plxGlob(PLX_ROOT.'themes', true);
$a_style = $tpl->query('/[a-z0-9-_]+/');
foreach($a_style as $k=>$v)
	$b_style[ $v ] = $v;

# Tableau du tri
$aTri = array('desc'=>$ADM_sortby_desc, 'asc'=>$ADM_sortby_asc);
?>

<h2><?php echo $ADM_analyticsettings_title; ?></h2>

<form action="parametres_analytics.php" method="post" id="change-cf-file">
	<fieldset class="withlabel">
		<p class="field"><label><?php echo $ADM_googleanalytics_title; ?>:</label></p>
		<?php echo $ADM_googleanalytics_help.' '; plxUtils::printInput('googleanalytics', $plxAdmin->aConf['googleanalytics'], 'text', '10-255'); echo ' '.$ADM_example.': UA-1234567-1';?>
		<p class="field"><label><?php echo $ADM_yandexmetrika_title; ?>:</label></p>
		<?php echo $ADM_yandexmetrika_help.' '; plxUtils::printInput('yandexmetrika', $plxAdmin->aConf['yandexmetrika'], 'text', '10-255'); echo ' '.$ADM_example.': 1234567';?>
	</fieldset>
	<p><input type="submit" value="<?php echo $ADM_savechanges; ?>" /></p>
</form>

<?php
# On inclut le footer
include('foot.php');
?>
