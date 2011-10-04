<?php
$folders = glob("../../album/*");
foreach($folders as $folder) {
	if($folder!='.' && $folder!='..'){
		$files = glob("$folder/*.tb");
		foreach($files as $file) unlink($file);
	}		
}
header('Location: parametres_affichage.php');
?>
