<?php

	define( 'ROOT' , '../../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = $dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	$galleries = Database::readDB( 'galleries' , true ); //reads galleries
	$categories = Database::readDB( 'categories' , true ); //reads categories
	$errorText = '';
	
	
	if( !empty( $_POST ) ) {
		if( isset( $_POST['new-gallery-name'] ) ) {
			$data = Array("name" => Database::clearQuery( $_POST['new-gallery-name'] ), "folder" => Utilities::Translit( Database::clearQuery( $_POST['new-gallery-name'] ) ) , 'text' => '' , 'description' => '' , "visible" => 'true');
			$returnCode1 = Utilities::parseError( CGallery::Modify( $data ) ); //capturing errors
			$returnCode2 = Utilities::parseError( CCategory::modifyGallery( $_POST['new-gallery-category'] , 'add' , Utilities::Translit( Database::clearQuery( $_POST['new-gallery-name'] ) ) ) ); //capturing errors
			if( $returnCode1 == 1 && $returnCode2 == 1 ) {
				header('Location: ./');
			} else {
				$errorText = $returnCode.'; '.$returnCode2.' '.$dictionary['error-table'];
			}
		}
		if( isset( $_POST['new-category-name'] ) ) {
			/* $data = Array( "name" => string, "description" => string, "visible" => 'true'/'false' ); */
			$data = Array("name" => Database::clearQuery( $_POST['new-category-name'] ), "description" => $_POST['new-category-description'] , "visible" => 'true');
			$returnCode = Utilities::parseError( CCategory::Modify( $data ) ); //capturing errors
			if( $returnCode == 1 ) {
				header('Location: ./');
			} else {
				$errorText = $returnCode.'. '.$dictionary['error-table'];
			}
		}
	}
	
?>

<!doctype html>
<html>
<head>

	<title><?php echo $database['title']; ?> - <?php echo $dictionary['adminpanel']; ?></title>
	
	<meta http-equiv="Content-Language" content="<?php echo $database['language']; ?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<link rel="stylesheet" href="<?php echo $database['address']; ?>/core/admin/files/style.css" type="text/css">
	<script src="<?php echo $database['address']; ?>/core/admin/files/nicEdit.js" type="text/javascript"></script>
	
</head>
<body>

	<div class="header">
		<div class="container">
			<div class="header-title">
				<div class="title"><?php echo $database['title']; ?></div>
				<div class="subtitle"><?php echo $dictionary['adminpanel']; ?></div>
			</div>
			<div class="main-menu">
				<li><a href="<?php echo $database['address']; ?>/core/admin/update/"><?php echo $dictionary['update']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/language/"><?php echo $dictionary['lang']; ?></a></li>
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
	
		<p class="errortitle"><?php echo $errorText;?></p>
	
		<fieldset>
			<legend><h3><?php echo $dictionary['new-gallery']; ?></h3></legend>
			<form method="post">
				<p><label for="new-gallery-name"><?php echo $dictionary['new-gallery-name']; ?>: </label><input name="new-gallery-name" id="new-gallery-name" size="55"></span> </p>
				<p><label for="new-gallery-category"><?php echo $dictionary['category-name']; ?>: <select name="new-gallery-category">
					<?php
						foreach( $categories as $categoryid => $category ) {
							echo '<option value="'.$categoryid.'">'.$category['name'].'</option>';
						}
					?>
				</select></p>
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
		
		<fieldset>
			<legend><h3><?php echo $dictionary['new-category']; ?></h3></legend>
			<form method="post">
				<p><label for="new-category-name"><?php echo $dictionary['new-gallery-name']; ?>: </label><input name="new-category-name" id="new-category-name" size="55"></span></p>
				<p><label for="new-category-description"><?php echo $dictionary['category-description']; ?>: </label><br><textarea name="new-category-description" id="new-category-description" cols="100"rows="7"></textarea></p>
				<p class="confirm-button"><input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
			</form>
		</fieldset>
		
		<fieldset>
			<legend><h3><?php echo $dictionary['existing-categories']; ?></h3></legend>
			<?php
			
				foreach( $categories as $categoryid => $category ) {
					echo '<li class="gallery-item"><a href="category.php?id='.$categoryid.'">'.$category['name'].'</a></li>';
				}
				
			?>
		</fieldset>
	
		<div class="footer">
			<?php echo $dictionary['poweredby']; ?> <?php echo file_get_contents( ROOT.'core/db/version' ); ?>
		</div>
	</div>
</body>
</html>