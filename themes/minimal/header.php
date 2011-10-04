<?php 
$syslang = strtolower(substr($plxShow->plxMotor->site_lang, 0, 2));
$bodyid = substr($_SERVER['QUERY_STRING'],11);
if (empty($bodyid)) $bodyid = 'homini';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $syslang; ?>" lang="<?php echo $syslang; ?>">
<head>

<meta name="description" content="<?php echo $plxShow->plxMotor->descriptiontag; ?>" />
<meta name="keywords" content="<?php echo $plxShow->plxMotor->keywordstag; ?>" />
<meta name="application-name" content="<?php echo $plxShow->plxMotor->aConf['title']; ?>" /> 
<meta name="msapplication-tooltip" content="<?php echo $plxShow->plxMotor->aConf['title']; ?>" /> 
<meta name="msapplication-starturl" content="<?php echo $plxShow->plxMotor->racine; ?>" />
<link rel="home" href="<?php echo $plxShow->plxMotor->racine; ?>"/>

<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">

	<title><?php $plxShow->pageTitle(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="<?php $plxShow->template(); ?>/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="js/shadowbox.css" media="screen" />
	<link rel="alternate" type="application/atom+xml" title="Atom articles" href="./feed.php?atom" />
	<link rel="alternate" type="application/rss+xml" title="Rss articles" href="./feed.php?rss" />
	<link rel="alternate" type="application/atom+xml" title="Atom commentaires" href="./feed.php?atom/commentaires" />
	<link rel="alternate" type="application/rss+xml" title="Rss commentaires" href="./feed.php?rss/commentaires" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    

<script type="text/javascript" src="<?php echo $plxShow->plxMotor->racine; ?>/js/shadowbox.js"></script>
<script type="text/javascript">
Shadowbox.init({
    modal: true
});
Shadowbox.clearCache();
</script>


<?php if ($plxShow->plxMotor->categ_get==1){ ?>
	<script type="text/javascript" src="js/collapse.js"></script>
<?php };?>

<?php include('core/lang/'.$plxShow->plxMotor->site_lang.'.php'); ?>

</head>
<body id="<?php echo $bodyid; ?>" <?php if ($plxShow->plxMotor->categ_get==1) { ?> hideDiv('catlist')<?php }; ?>>

<?php if ($plxShow->plxMotor->maintence==1 && !isset($_COOKIE["PHPSESSID"])) {?><div id="maintence"><p><?php echo $SITE_maintence_message;?></p></div> <?php };?>

<div id="top">
	<div id="header">
        <h1><?php $plxShow->mainTitle('link'); ?></h1> 
	<p><?php if ($plxShow->plxMotor->counter_enabled==1) { echo $SITE_counter_pretext.' '; include ("scan.php"); echo ' '.$SITE_counter_aftertext; } ?></p>
	</div>	
	<div id="subtitle">
		<span><?php $plxShow->subTitle(); ?></span>
	</div>
	<div id="menu"><ul>
        <?php
            if ($plxShow->plxMotor->nonews==1){
                if ($plxShow->plxMotor->index_get==0) $plxShow->staticList($SITE_headermenu_firstmain);
                else $plxShow->staticList($SITE_headermenu_firstnews);
            } else $plxShow->staticList('');
        ?>
	</ul></div>
</div>
