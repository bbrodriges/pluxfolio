<?php

include('prepend.php');

if (!empty($_POST)) {

		if($_GET['folder']!='') {
			$filename = '../../album/'.$_GET['folder'].'/'.$_GET['img'];
			$filepath = '../../album/'.$_GET['folder'].'/';
			$folder = $_GET['folder'];
		}
		else {
			$filename = '../../'.$plxAdmin->aConf['images'].$_GET['img'];
			$filepath = '../../'.$plxAdmin->aConf['images'];
			$folder = $plxAdmin->aConf['images'];
		}
		
		if(file_exists($filename.'.tb')) unlink($filename.'.tb'); //removing old .tb
		
		//removing ! from filename and description
		$newimgfname = str_replace("!", "", $_POST['fname']);
		$newimgfdesc = str_replace("!", "", $_POST['filedesc']);
		$newimgftag = str_replace("!", "", $_POST['filetag']);
		
		//Checking for description
		if($_POST['filedesc']!='') $newimgname = $newimgfname.'!'.$newimgfdesc.'!'.$newimgftag;
		else $newimgname = $newimgfname;
		
		$newimgname = plxUtils::strToHex($newimgname).$_POST['filedext'];
		
		//renaming and making new thumb
		rename($filename, $filepath.$newimgname);
		if($_POST['filedext']!='.swf' && $_POST['filedext']!='.flv') plxUtils::makeThumb($filepath.$newimgname, $filepath.$newimgname.'.tb',$plxAdmin->twidth,$plxAdmin->theight,'80',$plxShow->plxMotor->thumbtype);
		
		header("Location: images.php?folder=$folder");
}

$explodedfilename = explode('.',$_GET['img']);
$fileext = '.'.$explodedfilename[1];
$fname = plxUtils::hexToStr($explodedfilename[0]);
if(strpos($fname, '!')) {
	$explodedfilename = explode('!',$fname);
	$fname = $explodedfilename[0];
	$filedesc = $explodedfilename[1];
	$filetag = $explodedfilename[2];
}

# On inclut le header
include('top.php');

?>


<h2><?php echo $ADM_imagerename_title; ?></h2>

		<form method="post" action="<?php echo 'rename.php?img='.$_GET['img'].'&folder='.$_GET['folder']; ?>">
		<p class="field"><?php echo $ADM_imagerename_filename; ?></p>
		<p class="field"><input type="text" size="50" id="filename" name="fname" value="<?php echo $fname;?>" /></p>
		<p class="field"><?php echo $ADM_imagerename_filedescription; ?></p>
		<p class="field"><input type="text" size="50" id="filedesc" name="filedesc" value="<?php echo $filedesc;?>" /></p>
		<p class="field"><?php echo $ADM_imagerename_filetag; ?></p>
		<p class="field"><input type="text" size="50" id="filedesc" name="filetag" value="<?php echo $filetag;?>" /></p>
		<p class="field"><input type="hidden" size="4" id="filedext" name="filedext" value="<?php echo $fileext;?>" /></p>
		<p class="field"><?php echo $ADM_imagerename_help; ?></p>
		<p class="field">
			<input type="submit" name="preview" value="<?php echo $ADM_savechanges; ?>"/>
		</p>
		</form>

<?php

# On inclut le footer
include('foot.php');

?>
