<?php

/**
 * Gestion des documents
 *
 * @package PLX
 * @author	Stephane F. et Florent MONTHEL
 **/
 
include('prepend.php');

# Récupération des documents du dossier
$dirDoc = PLX_ROOT.$plxAdmin->aConf['documents'];
$plxDoc = new plxGlob($dirDoc);
$aDoc = $plxDoc->query('/^(.+)$/');
# Suppression d'un documents
if(isset($_GET['del']) AND !empty($_GET['hash']) AND $_GET['hash'] == $_SESSION['hash'] AND isset($aDoc[ $_GET['del'] ]) AND file_exists($dirDoc.$aDoc[ $_GET['del'] ])) {
	# On supprime le document
	if(!@unlink($dirDoc.$aDoc[ $_GET['del'] ])) {}# Erreur de suppression
	else # Ok
	header('Location: documents.php');
	exit;
}
# Envoi d'un documents
if(!empty($_FILES) AND !empty($_FILES['doc']['name'])) {
	# On teste l'existence du doc et on formate le nom du fichier
	$i = 0;
	$upFile = $dirDoc.plxUtils::title2filename(plxUtils::unSlash(basename($_FILES['doc']['name'])));
	while(file_exists($upFile)) {
		$upFile = $dirDoc.$i.plxUtils::title2filename(plxUtils::unSlash(basename($_FILES['doc']['name'])));
		$i++;
	}
	if(!@move_uploaded_file($_FILES['doc']['tmp_name'],$upFile)) # Erreur de copie
		{}
	else {}
	# On place les bons droits
	@chmod($upFile,0644);
	# On redirige
	header('Location: documents.php');
	exit;
}
?>

<?php include("top.php"); ?>

	<h2><?php echo $ADM_documents_title; ?></h2>
	<?php (!empty($_GET['msg']))?plxUtils::showMsg(plxUtils::unSlash(urldecode(trim($_GET['msg'])))):''; ?>	
	<form enctype="multipart/form-data" action="documents.php" method="post">
		<fieldset class="withlabel">
			<legend><?php echo $ADM_documents_legend; ?></legend>
			<p><input name="doc" type="file" />
			<input type="submit" name="upload" value="<?php echo $ADM_documents_upload; ?>" /></p>
		</fieldset>
	</form>
	<h3 class="subh"><?php echo $ADM_documents_uploaded; ?></h3>
	<table class="list-articles">
	<thead>
		<tr>
			<th width="65%"><?php echo $ADM_documents_filename; ?></th>
			<th width="15%"><?php echo $ADM_articles_tableactions; ?></th>
		</tr>
	</thead>
	<tbody>
<?php if(!$aDoc) { # Aucun document ?>
		<tr class="line-0">
			<td colspan="2" class="center"><?php echo $ADM_documents_nofiles; ?></td>
		</tr>
<?php } else { # On a des documents
	$nb = count($aDoc);
	for($i=0; $i < $nb; $i++) { ?>
		<tr class="line-<?php echo $i%2; ?>">
			<td class="tc1">&nbsp;<?php echo plxUtils::strCut($aDoc[$i],100); ?></td>
			<td class="tc1">&nbsp;
				<a href="<?php echo PLX_ROOT.$plxAdmin->aConf['documents'].plxUtils::strCut($aDoc[$i],100); ?>"><img src="img/anchor.png" /></a> <a href="documents.php?del=<?php echo $i.'&amp;hash='.$_SESSION['hash'] ?>" title="удалить файл с сайта" onClick="Check=confirm('<?php echo $ADM_check_deletion; ?>');if(!Check) return false;"><img src="img/delete.png" /></a>
			</td>
        </tr>
	<?php } # Fin for liste des documents ?>
<?php } # Fin else on a des documents ?>
	</tbody>
	</table>
    <?php
# On inclut le footer
include('foot.php');
?>
</body>
</html>
