<?php

/**
 * Edition du code source d'une page galeria
 *
 * @package PLX
 * @author	Stephane F. et Florent MONTHEL
 **/
 
include('prepend.php');

# On édite la page galeria
if(!empty($_POST) AND isset($plxAdmin->aGals[ $_POST['id'] ])) {
	$plxAdmin->editFileGaleria(plxUtils::unSlash($_POST));
	$plxAdmin->siteMap($plxAdmin->aConf['racine']);
	header('Location: galeria.php?p='.$_POST['id']);
	exit;
} elseif(!empty($_GET['p'])) { # On affiche le contenu de la page
	$id = $_GET['p'];
	if(!isset($plxAdmin->aGals[ $id ])) {
		header('Location: galerie.php?msg=');
		exit;
	}
	# On récupère le contenu
	$content = trim($plxAdmin->getFileGaleria($id));
	$title = $plxAdmin->aGals[ $id ]['name'];
	$url = $plxAdmin->aGals[ $id ]['url'];
} else { # Sinon, on redirige
	header('Location: galerie.php');
	exit;
}

# On inclut le header
include('top.php');
?>

<h2><?php echo $ADM_galedit_title; ?> «<?php echo htmlspecialchars($title,ENT_QUOTES,PLX_CHARSET); ?>»</h2>

<?php if ($plxAdmin->aConf['wysiwyg']==1) { ?>
	<!-- nicedit -->
	<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
	<script type="text/javascript">
	//<![CDATA[
	bkLib.onDomLoaded(function() {new nicEditor({fullPanel : true}).panelInstance('id_content'); new nicEditor({fullPanel : true}).panelInstance('id_chapo');});
	//]]>
	</script>						
	<!-- /nicedit -->
<?php } ?>

<p><a href="<?php echo PLX_ROOT; ?>?galeria<?php echo intval($id); ?>/<?php echo $url; ?>"><?php echo $ADM_preview;?> «<?php echo htmlspecialchars($title,ENT_QUOTES,PLX_CHARSET); ?>»</a></p>

<form action="galeria.php" method="post" id="change-gallery-content">
	<fieldset>
		<?php plxUtils::printInput('id', $id, 'hidden');?>
		<p class="field"><label><?php echo $ADM_galsourcecode; ?></label></p>
		<?php plxUtils::printArea('content', htmlspecialchars($content,ENT_QUOTES,PLX_CHARSET),60,20) ?>
    	<p><input type="submit" value="<?php echo $ADM_savechanges; ?>"/></p>
	</fieldset>
</form>

<div class="help">
<?php echo $ADM_galedit_help;?>
</div>

<?php
# On inclut le footer
include('foot.php');
?>
