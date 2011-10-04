<?php

/**
 * Page d'authentification
 *
 * @package PLX
 * @author	Stephane F et Florent MONTHEL
 **/

# Variable pour retrouver la page d'authentification
$auth_page = true;

include('prepend.php');

include('../lang/'.$plxAdmin->aConf['site_lang'].'.php');

if(!empty($_GET['d'])) {
	$_SESSION = array();
	session_destroy();
}

# Authentification
if(!empty($_POST['login']) AND !empty($_POST['pwd'])) {
	# On rйcupиre les comptes
	$pwd = $plxAdmin->getPasswd(PLX_ROOT.$plxAdmin->aConf['passwords']);
	$login = (string)plxUtils::unSlash($_POST['login']);
	if(md5(plxUtils::unSlash($_POST['pwd'])) == @$pwd[$login]) {
		$_SESSION['admin'] = '1';
		$_SESSION['author'] = $login;
		$_SESSION['pass'] = $pwd[$login];
		$_SESSION['hash'] = plxUtils::charAleatoire(10);
		header('Location: ./');
	} else {
		$msg = $ADM_auth_loginerror;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Pluxfolio - <? echo $ADM_auth_title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="admin.css" media="screen" />
</head>

<body id="login">

<div>
  <form action="auth.php" method="post" id="auth">
	<span class="header"><?php echo $ADM_auth_legend; ?></span>
	<fieldset>
		<?php (!empty($msg))?plxUtils::showMsg($msg):''; ?>
		<label><?php echo $ADM_usersettings_login; ?>:</label>
		<?php plxUtils::printInput('login', (!empty($_POST['login']))?plxUtils::unSlash(htmlspecialchars($_POST['login'],ENT_QUOTES,PLX_CHARSET)):'', 'text', '18-255');?><br />
		<label><?php echo $ADM_usersettings_password; ?>:</label>
		<?php plxUtils::printInput('pwd', '', 'password','18-255');?><br />
		<input id="button" type="submit" value="<?php echo $ADM_auth_enter; ?>" />
	</fieldset>
</form>
</div>
<p class="auth_return"><a href="<?php echo PLX_ROOT; ?>"><?php echo $ADM_auth_backtosite; ?></a></p>

</body>
</html>
