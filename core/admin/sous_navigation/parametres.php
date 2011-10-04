<ul>
	<li><a href="parametres_base.php" id="link_config"><?php echo $ADM_nav_basesettings_title; ?></a></li>
	<li><a href="parametres_affichage.php" id="link_config"><?php echo $ADM_nav_contentsettings_title; ?></a></li>
	<li><a href="parametres_compte.php" id="link_user"><?php echo $ADM_nav_usersettings_title; ?></a></li>
	<li><a href="parametres_admin.php" id="link_config"><?php echo $ADM_nav_adminsettings_title; ?></a></li>
<?php if ($plxAdmin->aConf['admin_conf']==0) { ?>
    <li><a href="parametres_theme.php" id="link_config"><?php echo $ADM_nav_themesettings_title; ?></a></li>
    <li><a href="parametres_lang.php" id="link_config"><?php echo $ADM_nav_langsettings_title; ?></a></li>
	<li><a href="parametres_analytics.php" id="link_config"><?php echo $ADM_nav_analyticsettings_title; ?></a></li>
<?php }; ?>
</ul>
