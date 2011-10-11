<?php
	$app = new Artworks;
	
	echo '<form name="post" class="myFormAnketa" enctype="multipart/form-data" action="" method="post">';
		echo '<input name="img[]" type="file" size="65" ><br/>';
		echo '<input name="img[]" type="file" size="65" ><br/>';
		echo '<input type="hidden" name="op" value="send">';
		echo '<input type="submit" value="SEND">';
	echo '</form>';
	
	
	if ($_POST['op'] == 'send') {
		var_dump($app->Upload(0, $files));
	}
	
?>