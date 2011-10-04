<?php

define('PLX_ROOT', './');
define('PLX_CORE', PLX_ROOT.'core/');
define('PLX_CONF', PLX_ROOT.'data/configuration/parametres.xml');

# On verifie que Pluxml n'est pas déjà installé
if(file_exists(PLX_CONF)) {
	header('Content-Type: text/plain charset=UTF-8');
	echo 'You have everything installed already!';
	exit;
}

# Getting default locale

$langcode = explode(";", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
$langcode = explode(",", $langcode['0']);

if($langcode['0']=='ru') {
	$predicted_lang = 'RU_ru';
	$syslang = 'Русский';
} else {
	$predicted_lang = 'EN_us';
	$syslang = 'English';
}

if (function_exists("gd_info")) $gdcheck = '<span style="color: green;">OK</span>';
else $gdcheck = '<span style="color: red;">ERROR</span>';

# On inclut les librairies nécessaires
include_once(PLX_ROOT.'config.php');
include_once(PLX_CORE.'lib/class.plx.utils.php');
include('core/lang/'.$predicted_lang.'.php');
	
# Configuration de base
$f = file(PLX_ROOT.'version');
$version = $f['0'];
$config = array('title'=>'Project title', 
				'description'=>'and it\'s subtitle',
				'racine'=>plxUtils::getRacine(),
				'intro'=>'This is a very important block, which can help users to understand that it is a John Doe\'s site.',
				'delta'=>'+03:00', 
				'style'=>'pluxpress', 
				'bypage'=>5,
				'bypage_admin'=>10, 
				'bypage_feed'=>10, 
				'tri'=>'date-desc',
				'categ_get'=>'0',
				'images'=>'data/images/', 
				'documents'=>'data/documents/', 
				'racine_articles'=>'data/articles/',
				'gallery'=>'data/galerie/',
				'racine_statiques'=>'data/statiques/',
				'galerie'=>'data/configuration/galerie.xml', 
				'statiques'=>'data/configuration/statiques.xml', 
				'categories'=>'data/configuration/categories.xml', 
				'passwords'=>'data/configuration/passwords.xml',
				'wysiwyg'=>'0',
				'admin_conf'=>'0',
				'counter_enabled'=>'0',
				'site_lang' => $predicted_lang,
				'postas' => '0',
				'images_sets' => '0',
				'freshness' => '0',
				'freshnesstime' => '604800',
				'twitter' => 'pluxfolio',
				'maintence' => '0',
				'templatecheck' => '1',
				'image_caption' => '1',
				'imgorderby' => '0',
				'keywordstag' => 'portfolio, pluxfolio, CMS, XML, php',
				'thumbtype' => 0,
                'watermark' => 0,
                'nonews' => 1
				);

function install($content, $config) {

	# Echappement des caractères
	$content = plxUtils::unSlash($content);
	# Tableau des clés à mettre sous chaîne cdata
	$aCdata = array('title','description','intro','racine');
	
	# Création du fichier de configuration
	$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
	$xml .= '<document>'."\n";
	foreach($config as $k=>$v) {
		if(in_array($k,$aCdata))
			$xml .= "\t<parametre name=\"$k\"><![CDATA[".$v."]]></parametre>\n";
		else
			$xml .= "\t<parametre name=\"$k\">".$v."</parametre>\n";
	}
	$xml .= '</document>';
	plxUtils::write($xml,PLX_CONF);
	
	# Création du fichier de mot de passe
	$xml = '<?xml version="1.0" encoding="'.PLX_CHARSET.'"?>'."\n";
	$xml .= '<document>'."\n";
	$xml .= "\t".'<user login="'.trim($content['name']).'" >'.md5(trim($content['pwd'])).'</user>'."\n";
	$xml .= '</document>';
	plxUtils::write($xml,PLX_ROOT.$config['passwords']);
	
}

if(!empty($_POST['name']) AND !empty($_POST['pwd']) AND $_POST['pwd'] == $_POST['pwd2'] AND function_exists("gd_info")) {
	if(isset($_POST['notify'])) { mail('pluxfolio@gmail.com', 'Successful pluxfolio installation', 'On site '.plxUtils::getRacine()); }
	install($_POST, $config);
	header('Location: '.plxUtils::getRacine());
	exit;
}
?>
<?php header('Content-Type: text/html; charset='.PLX_CHARSET); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo strtolower($predicted_lang);?>" lang="<?php echo strtolower($predicted_lang);?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtolower(PLX_CHARSET) ?>" />
<title><?php echo $INST_title;?></title>
<link rel="stylesheet" type="text/css" href="core/admin/admin.css" media="screen" />
</head>

<body id="install">
<div><h2><?php echo $INST_title;?></h2>
<ul>
	<li><?php echo $INST_engine_version.': '.$version; ?></b></li>
	<li><?php plxUtils::testWrite(dirname(PLX_CONF)); ?></li>
	<li><?php plxUtils::testWrite(PLX_ROOT.$config['racine_articles']); ?></li>
	<li><?php plxUtils::testWrite(PLX_ROOT.$config['racine_statiques']); ?></li>
	<li><?php plxUtils::testWrite(PLX_ROOT.$config['gallery']); ?></li>
	<li><?php echo $INST_php_version.': '.phpversion(); ?></li>
	<li><?php echo $INST_gd_check.': '.$gdcheck;?></li>
	<li><?php echo $INST_default_lang.': '.$syslang; ?></li>
</ul>
<h3><?php echo $INST_creating_hero;?></h3>
<form action="install.php" method="post">
	<fieldset>
		<p class="field"><label><?php echo $INST_username;?>:</label></p>
		<?php plxUtils::printInput('name', '', 'text', '20-255') ?>
		<p class="field"><label><?php echo $INST_password;?>:</label></p>
		<?php plxUtils::printInput('pwd', '', 'password', '20-255') ?>
		<p class="field"><label><?php echo $INST_confirm_password;?>:</label></p>
		<?php plxUtils::printInput('pwd2', '', 'password', '20-255') ?>
		<p class="field"><input type="checkbox" checked="true" id="id_notify" name="notify"> <?php echo $INST_send_notification;?></p>
		<?php plxUtils::printInput('version', $version, 'hidden') ?>
		<p><input type="image" src="images/create.jpg" class="create"  /></p>
	</fieldset>
</form>
</div>
</body>
</html>
