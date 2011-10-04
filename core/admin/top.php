<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo strtolower(substr($plxAdmin->site_lang, 0, 2));?>" lang="<?php echo strtolower(substr($plxAdmin->site_lang, 0, 2));?>">
<head>

<?php include('../lang/'.$plxAdmin->aConf['site_lang'].'.php'); ?>

<title><?php echo $plxAdmin->aConf['title']; ?> - <?php echo $ADM_title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../js/shadowbox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="admin.css" media="screen" />
<link rel="stylesheet" type="text/css" href="jquery.Jcrop.css" media="screen" />
<script type="text/javascript" src="../lib/functions.js"></script>
<script type="text/javascript" src="../../js/wysiwyg.js"></script>
<script src="../../js/jquery.min.js"></script>
<script src="../../js/jquery.Jcrop.js"></script>

<script type="text/javascript" src="../../js/shadowbox.js"></script> 
<script type="text/javascript"> 
Shadowbox.init({modal: true});
</script> 

</head>

<body id="<?php $self = basename($_SERVER['PHP_SELF']); echo substr($self,0,-4); ?>">
<div id="main">

	<div id="header"><h1><?php echo $plxAdmin->aConf['title']; ?> - <?php echo $ADM_title; ?></h1>
		<p><?php echo $ADM_authorized_as; ?> <strong><?php echo (!empty($_SESSION['author']))?htmlspecialchars($_SESSION['author'],ENT_QUOTES,PLX_CHARSET):'invit&eacute;'; ?></strong> :: <a href="auth.php?d=1" id="logout"><?php echo $ADM_logout; ?></a></p>
	</div>

	<?php  
		if ($plxAdmin->aConf['templatecheck'] == '1') {
		    if (file_exists('../../themes/'.$plxAdmin->aConf['style'].'/version')) {
			  $template = file('../../themes/'.$plxAdmin->aConf['style'].'/version'); 
			  $templateversion = $template[0];
			  if ($templateversion < $plxAdmin->version) echo '<div id="warning">'.$ADM_template_warning.'</div>'; 
		    }
	        else echo '<div id="warning">'.$ADM_template_warning.'</div>';
	    }
	?>

	<div id="navigation">
		<ul>
		<li><a href="index.php" id="link_control"><?php echo $ADM_top_gallery_title; ?></a></li>
    <?php if ($plxAdmin->aConf['nonews'] == 1) { ?>
		<li><a href="article.php" id="link_article-new"><?php echo $ADM_top_postarticle_title; ?></a></li>
		<li><a href="news.php" id="link_articles"><?php echo $ADM_top_articles_title; ?></a></li>
    <?php } ?>
		<li><a href="statiques.php" id="link_statiques"><?php echo $ADM_top_staticpages_title; ?></a></li>
		<li><a href="categories.php" id="link_categories"><?php echo $ADM_top_articlescategories_title; ?></a></li>
		<li><a href="parametres_base.php" id="link_config"><?php echo $ADM_top_settings_title; ?></a></li>
		<li><a href="images.php" id="link_images"><?php echo $ADM_top_pictures_title; ?></a></li>
		<li><a href="documents.php" id="link_docs"><?php echo $ADM_top_files_title; ?></a></li>
		<li style="position:absolute; left:5%; top:20px;"><a href="<?php echo PLX_ROOT; ?>" class="back"><?php echo $ADM_top_backtosite_title; ?></a></li>
		</ul>
	</div>

<?php if(file_exists(plxUtils::getSousNav())) : ?>
	<div id="sous_navigation"><?php include(plxUtils::getSousNav()); ?></div>
<?php endif; ?>

<?php (!empty($_GET['msg']))?plxUtils::showMsg(plxUtils::unSlash(urldecode(trim($_GET['msg'])))):''; ?>
