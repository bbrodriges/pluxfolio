<?php

	define( 'ROOT' , '../../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = $dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	$statics = Database::readDB( 'statics' , true ); //reads galleries
	$errorText = '';
	
	if( !empty( $_POST ) && isset( $_POST['new-static-name'] ) ) {
		$data = Array("title" => Database::clearQuery( $_POST['new-static-name'] ), "text" => $_POST['new-static-text'] , "visible" => 'true');
		$returnCode = Utilities::parseError( CStatic::Modify( $data , $_GET['id'] ) ); //capturing errors
		if( $returnCode == 1 ) {
			header('Location: ./edit.php?id='.Utilities::Translit( $_POST['new-static-name'] ));
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
				<li class="current"><a href="<?php echo $database['address']; ?>/core/admin/statics/"><?php echo $dictionary['statics']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/galleries/"><?php echo $dictionary['galleries']; ?></a></li>
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
			<legend><h3><?php echo $dictionary['edit-static']; ?></h3></legend>
			<form method="post">
				<p><label for="new-static-name"><?php echo $dictionary['new-gallery-name']; ?>: </label><input name="new-static-name" id="new-static-name" size="55" value="<?php echo $statics[ $_GET['id'] ]['title']; ?>"></span> </p>
					
				<p><label for="new-static-text"><?php echo $dictionary['new-static-text']; ?>: </label><br><textarea name="new-static-text" cols="100" rows="12"><?php echo $statics[ $_GET['id'] ]['text']; ?></textarea></p>
					
				<p class="confirm-button"> <?php echo $errorText;?> <input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
			</form>
		</fieldset>
	
		<div class="footer">
			<?php echo $dictionary['poweredby']; ?> <?php echo file_get_contents( ROOT.'core/db/version' ); ?>
		</div>
	</div>
</body>
</html>