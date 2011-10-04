<?php

/**
 * Gestion des images
 *
 * @package PLX
 * @author	Stephane F. et Florent MONTHEL
 **/
 
include('prepend.php');

# Variables pour le redimensionnement
$width = $plxAdmin->twidth;
$height = $plxAdmin->theight;
$quality = 80;

# Rйcupйration des images du dossier
$folder = $_POST['folder'];
if (empty($folder)) $folder = $_GET['folder'];
if (!empty($folder))  $dirImg = PLX_ROOT.'album/'.$folder.'/';
else $dirImg = PLX_ROOT.$plxAdmin->aConf['images'];

$plxImg = new plxGlob($dirImg);
$aImg = $plxImg->query('/(.+).(gif|jpg|jpeg|png|swf|flv|youtube)$/i');
# Suppression d'une image
if(isset($_GET['del']) AND !empty($_GET['hash']) AND $_GET['hash'] == $_SESSION['hash'] AND isset($aImg[ $_GET['del'] ]) AND file_exists($dirImg.$aImg[ $_GET['del'] ])) {
	# On supprime l'image et sa miniature
	if(!@unlink($dirImg.$aImg[ $_GET['del'] ])) # Erreur de suppression
		{}
	else # Ok
		{}
	@unlink($dirImg.$aImg[ $_GET['del'] ].'.tb');
	$plxAdmin->siteMap($plxAdmin->aConf['racine']);
	header('Location: images.php?folder='.$folder);
	exit;
}

if($_POST['youtube']!='') {
	$youtuberesponse = file_get_contents('http://gdata.youtube.com/feeds/api/videos?max-results=1&alt=json&q='.$_POST['youtube'].'&format=5');
	plxUtils::parseYoutube($youtuberesponse, $_POST['youtube'], $dirImg);
	header('Location: images.php?folder='.$_POST['folder'].'&debug='.$debug);
	exit;
}
# Envoi d'une image
if(!empty($_FILES) && !empty($_FILES['img']['name'])) {
	# On teste l'existence de l'image et on formate le nom du fichier
	$i = 0;
	foreach($_FILES['img']['name'] as $ImgFileKey => $ImgFileValue) {
		$FileExt = substr($ImgFileValue, -4);
		$FileName = substr($ImgFileValue, 0, strlen($ImgFileValue)-4);
		$FileName = substr($FileName, 0, 125); // cutting long filename to 125 chars
		$upFile = $dirImg.plxUtils::strToHex($FileName).$FileExt;
		while(file_exists($upFile)) {
			$upFile = $dirImg.$i.plxUtils::strToHex($FileName).$FileExt;
			$i++;
		}
		if(@getimagesize($_FILES['img']['tmp_name'][$ImgFileKey])) { # C'est une image
			if(!@move_uploaded_file($_FILES['img']['tmp_name'][$ImgFileKey],$upFile)) { # Erreur de copie
			} else { # Ok
				@chmod($upFile,0644);
				@plxUtils::makeThumb($upFile, $upFile.'.tb',$width,$height,$quality);
				@chmod($upFile.'.tb',0644);
			}
		} else { /*Ce n'est pas une image*/ }
	}
	# On redirige
	$plxAdmin->siteMap($plxAdmin->aConf['racine']);
	header('Location: images.php?folder='.$folder.'&debug='.$debug);
	exit;
}

include("top.php");

//Листинг папок
function listing ($url,$mode) 
{
if (is_dir($url)) 
	{
	if ($dir = opendir($url)) 
		{
		while (false !== ($file = readdir($dir))) 
			{
			if ($file != "." && $file != "..") 
				{
				if(is_dir($url."/".$file)) 
					{
					$folders[] = $file;
					}
				else {$files[] = $file;}
				}
			}
		}
	closedir($dir);
	}
if($mode == 1) {return $folders;}
if($mode == 0) {return $files;}
}

$url = '../../album';

$options = '<option value="">'.$ADM_images_defaultfolder.'</option>';
if(listing($url,1))
	{
	foreach(listing($url,1) as $f) 
		{
		if ($f==$folder) $selected = ' selected'; else $selected = '';
		$options.='<option value="'.$f.'"'.$selected.'>'.$f.'</option>';
		}
	}
$select = $ADM_images_intofolder.' <select name="folder">'.$options.'</select>';		

?>

	<h2><?php echo $ADM_images_title;?></h2>
                <p><?php echo $ADM_images_help;?></p>
	<?php (!empty($_GET['msg']))?plxUtils::showMsg(plxUtils::unSlash(urldecode(trim($_GET['msg'])))):''; ?>
	<?php if(!empty($_GET['img']) AND file_exists($dirImg.plxUtils::unSlash(rawurldecode($_GET['img'])))) { # Image particuliиre ?>
		<p><a href="images.php" class="backupload">назад</a></p>
		<img src="<?php echo $dirImg.plxUtils::unSlash(rawurldecode($_GET['img'])); ?>" alt="" />
	<?php } else { # Galerie ?>
		<form enctype="multipart/form-data" action="images.php" method="post">
			<fieldset class="withlabel">
				<legend><?php echo $ADM_images_legend; ?></legend>
				<p><?php echo $select?></p>
				<p><input multiple="true" name="img[]" type="file" />
				<input type="submit" value="<?php echo $ADM_images_go; ?>" /></p>
				<p class="help"><?php echo $ADM_images_multiuploadnotice; ?></p> 
			</fieldset>
		</form>
		<?php if($_GET['folder']!='') { ?>
		<form action="images.php" method="post">
			<fieldset class="withlabel">
				<legend><?php echo $ADM_youtube_legend; ?></legend>
				<input name="folder" type="hidden" value="<?php echo $_GET['folder'];?>"/>
				<p><input name="youtube" type="text" />
				<input type="submit" value="<?php echo $ADM_images_go; ?>" /></p>
				<p class="help"><?php echo $ADM_youtube_uploadnotice; ?></p> 
			</fieldset>
		</form>
		<?php } ?>
		<h3 class="subh"><?php echo $ADM_images_previouslyuploaded; ?></h3>
		<div class="imgs">
		<?php
			if(!$aImg) { # Aucune image
				echo $ADM_images_noimages;
			} else { # On affiche les images
				$nb = count($aImg);
				for($i=0; $i < $nb; $i++) 
					{
					echo '<div class="bloc_gal" style="width:'.$width.'px";><p class="thumb">';
					$name = $aImg[$i];
					$ext = end(explode('.', $name)); // Getting file extension
					$filename = substr($name, 0, strlen($name)-4);
					$filename = explode('!', plxUtils::hexToStr($filename));
					$filename = $filename[0];
					if ($ext == 'swf' || $ext == 'flv') {
						if ($ext == 'swf') $flashthumb = '../../images/flash.png';
						else $flashthumb = '../../images/flv.gif';
						$swfdimensions = getimagesize($dirImg.$aImg[$i]); //Getting dimensions
						echo '<a style="background:url(\''.$flashthumb.'\') no-repeat center;display:block; width:'.$width.'px; height:'.$height.'px;" title="'.$filename.'" href="'.$dirImg.$aImg[$i].'" rel="shadowbox;height='.$swfdimensions[1].';width='.$swfdimensions[0].'"/></a><span>'.$filename.'</span>'; 
					} if ($ext == 'youtube') {
						$json = file_get_contents($dirImg.$aImg[$i]);
						$youtubeparse = json_decode($json, TRUE);
						echo '<a style="background:url('.$youtubeparse['thumb'].') no-repeat center;display:block; width:'.$width.'px; height:'.$height.'px;" title="'.$youtubeparse['title'].'" href="'.$youtubeparse['url'].'" rel="shadowbox;width=480;height=360;player=swf"/></a><span>'.$youtubeparse['title'].'</span>';
					} else {
							if(file_exists($dirImg.$aImg[$i].'.tb')) {	# On a une miniature
								echo '<a style="background:url('.$dirImg.$aImg[$i].'.tb'.') no-repeat center;display:block; width:'.$width.'px; height:'.$height.'px;" title="'.$filename.'" href="'.$dirImg.$aImg[$i].'" rel="shadowbox"/></a><span>'.$filename.'</span>';
							} else # Pas de miniatures
								echo '<span>'.$name.'</span><br />';
					}
					# On affiche les diffйrents liens
					echo '</p>';
					echo '<p class="thumb_link">';

					if($plxAdmin->aConf['thumbtype']==1 && $ext != 'swf' && $ext != 'flv' && $ext != 'youtube') echo '<a href="crop.php?img='.$aImg[$i].'&folder='.$folder.'"><img src="img/crop.png" /></a> ';
					if($ext != 'youtube') echo '<a href="rename.php?img='.$aImg[$i].'&folder='.$folder.'"><img src="img/edit-paragraph.png" /></a> <a href="'.$dirImg.$aImg[$i].'"><img src="img/anchor.png" /></a> '; 
					echo '<a href="?folder='.$folder.'&del='.$i.'&amp;hash='.$_SESSION['hash'].'" onclick="Check=confirm(\''.$ADM_check_deletion.'\');if(Check==false) return false;"><img src="img/delete.png" /></a></p>';
					echo '</div>'."\n";
					}
			}
		?>
	<?php } # Fin else galerie ?>
</div>
    <?php
# On inclut le footer
include('foot.php');
?>
</body>
</html>
