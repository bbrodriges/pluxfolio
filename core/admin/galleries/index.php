<?php

	define( 'ROOT' , '../../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = $dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	$galleries = Database::readDB( 'galleries' , true ); //reads galleries
	$errorText = '';
	
	
	if( !empty( $_POST ) && isset( $_POST['new-gallery-name'] ) ) {
		$data = Array("name" => Database::clearQuery( $_POST['new-gallery-name'] ), "folder" => Utilities::Translit( Database::clearQuery( $_POST['new-gallery-name'] ) ), "visible" => 'true');
		$returnCode = Utilities::parseError( CGallery::Modify( $data ) ); //capturing errors
		if( $returnCode == 1 ) {
			header('Location: ./');
		} else {
			$errorText = $returnCode.'. '.$dictionary['error-table'];
		}
	}
	
?>

<!doctype html>
<html>
<head>

	<title><?php echo $database['title']; ?> - <?php echo $dictionary['adminpanel']; ?></title>
	
	<meta http-equiv="Content-Language" content="<?php echo $database['language']; ?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<link rel="stylesheet" href="<?php echo $database['address']; ?>/core/admin/css/style.css" type="text/css">
	
</head>
<body>

	<div class="header">
		<div class="container">
			<div class="header-title">
				<div class="title"><?php echo $database['title']; ?></div>
				<div class="subtitle"><?php echo $dictionary['adminpanel']; ?></div>
			</div>
			<div class="main-menu">
				<li><a href="<?php echo $database['address']; ?>/core/admin/blog/"><?php echo $dictionary['blog']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/statics/"><?php echo $dictionary['statics']; ?></a></li>
				<li class="current"><a href="<?php echo $database['address']; ?>/core/admin/galleries/"><?php echo $dictionary['galleries']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/"><?php echo $dictionary['settings']; ?></a></li>
			</div>
		</div>
	</div>
	
	<div class="informer">
		<div class="container">
		</div>
	</div>
	
	<div class="container">
	
		<fieldset>
			<legend><h3><?php echo $dictionary['new-gallery']; ?></h3></legend>
			<form method="post">
				<p><label for="new-gallery-name"><?php echo $dictionary['new-gallery-name']; ?>: </label><input name="new-gallery-name" id="new-gallery-name" size="55"></span> <?php echo $errorText;?></p>
				<p class="confirm-button"><input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
			</form>
		</fieldset>
		
		<fieldset>
			<legend><h3><?php echo $dictionary['existing-galleries']; ?></h3></legend>
			
			<?php
			
				foreach( $galleries as $galleryid => $gallery ) {
					echo '<li class="gallery-item"><a href="edit.php?id='.$galleryid.'">';
					foreach( $gallery['images'] as $filename => $fileinfo ) {
						echo '<img src="'.$database['address'].'/galleries/'.$gallery['folder'].'/'.$filename.'.tb">';
						break;
					}
					echo '<br>'.$gallery['name'].'</a></li>';
				}
				
			?>
			
		</fieldset>
	
		<div class="footer">
			<?php echo $dictionary['poweredby']; ?> <?php echo file_get_contents( ROOT.'core/db/version' ); ?>
		</div>
	</div>
</body>
</html>