<?php

	define( 'ROOT' , '../../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = $dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	$article = Utilities::getById( 'articles' , $_GET['id'] ); //reads article
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
				<li><a href="<?php echo $database['address']; ?>/core/admin/language/"><?php echo $dictionary['lang']; ?></a></li>
				<li class="current"><a href="<?php echo $database['address']; ?>/core/admin/blog/"><?php echo $dictionary['blog']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/statics/"><?php echo $dictionary['statics']; ?></a></li>
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
			<legend><h3><?php echo $dictionary['edit-article']; ?></h3></legend>
			<form method="post">
				<p><label for="new-article-name"><?php echo $dictionary['new-article-name']; ?>: </label><input name="new-article-name" id="new-article-name" size="55" value="<?php echo $article['title']; ?>"></span> <span class="help">(<?php echo $dictionary['article-name-help']; ?>)</span></p>
				
				<p><label for="new-article-pretext"><?php echo $dictionary['new-article-pretext']; ?>:  <span class="help">(<?php echo $dictionary['article-pretext-help']; ?>)</span></label><br><textarea name="new-article-pretext" id="new-article-pretext" cols="100" rows="6"><?php echo $article['pretext']; ?></textarea></p>
				
				<p><label for="new-article-text"><?php echo $dictionary['new-article-text']; ?>:</label><br><textarea name="new-article-text" id="new-article-text" cols="100" rows="12"><?php echo $article['text']; ?></textarea></p>
				
				<p><label for="new-article-tags"><?php echo $dictionary['new-article-tags']; ?>: </label><input name="new-article-tags" id="new-article-tags" size="55" value="<?php echo $article['tags']; ?>"> <span class="help">(<?php echo $dictionary['article-tags-help']; ?>)</span></p>
				
				<p><label for="new-article-visibility"><?php echo $dictionary['new-article-visibility']; ?>? </label><select name="new-article-visibility" id="new-article-visibility"><?php echo Utilities::onOffList( 'visible' , 'articles' , $_GET['id'] );?></select></p>
				
				<p class="confirm-button"> <?php echo $errorText;?> <input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
			</form>
		</fieldset>
	
		<div class="footer">
			<?php echo $dictionary['poweredby']; ?> <?php echo file_get_contents( ROOT.'core/db/version' ); ?>
		</div>
	</div>
</body>
</html>