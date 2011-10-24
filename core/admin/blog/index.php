<?php

	define( 'ROOT' , '../../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	$articles = Database::readDB( 'articles' , true ); //reads articles
	$errorText = '';
	
	
	if( !empty( $_POST ) && isset( $_POST['new-article-name'] ) ) {
		$data = Array("title" => $_POST['new-article-name'], "pretext" => $_POST['new-article-pretext'], "text" => $_POST['new-article-text'], "tags" => $_POST['new-article-tags'], "date" => strtotime( $database['GMT'].' hours' ) , "author" => $database['login'], "visible" => 'true');
		$returnCode = Utilities::parseError( CArticle::Modify( $data ) ); //capturing errors
		if( $returnCode == 1 ) {
			header('Location: ./');
		} else {
			$errorText = $returnCode.'. '.$dictionary['error-table'];
		}
	}
	
	if( !empty( $_GET ) ) {
		if( isset( $_GET['delete'] ) && !empty( $_GET['delete'] ) ) {
			$returnCode = Utilities::parseError( Utilities::Delete( 'articles' , $_GET['delete'] ) );
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
				<li  class="current"><a href="<?php echo $database['address']; ?>/core/admin/blog/"><?php echo $dictionary['blog']; ?></a></li>
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
			<legend><h3><?php echo $dictionary['new-article']; ?></h3></legend>
			<form method="post">
				<p><label for="new-article-name"><?php echo $dictionary['new-article-name']; ?>: </label><input name="new-article-name" id="new-article-name" size="55"></span> <span class="help">(<?php echo $dictionary['article-name-help']; ?>)</span></p>
				
				<p><label for="new-article-pretext"><?php echo $dictionary['new-article-pretext']; ?>:  <span class="help">(<?php echo $dictionary['article-pretext-help']; ?>)</span></label><br><textarea name="new-article-pretext" id="new-article-pretext" cols="100" rows="6"></textarea></p>
				
				<p><label for="new-article-text"><?php echo $dictionary['new-article-text']; ?>:</label><br><textarea name="new-article-text" id="new-article-text" cols="100" rows="12"></textarea></p>
				
				<p><label for="new-article-tags"><?php echo $dictionary['new-article-tags']; ?>: </label><input name="new-article-tags" id="new-article-tags" size="55"> <span class="help">(<?php echo $dictionary['article-tags-help']; ?>)</span></p>
				
				<p class="confirm-button"> <?php echo $errorText;?> <input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
			</form>
		</fieldset>
		
		<fieldset>
			<legend><h3><?php echo $dictionary['existing-articles']; ?></h3></legend>
			<table class="articles">
			<?php
			
				foreach( $articles as $articleid => $article ) {
					echo '<tr><td class="info">'.$dictionary['article-author'].': '.$article['author'].', '.date( 'Y.m.d G:i' , $article['date'] ).'</td><td class="name"><a href="edit.php?id='.$articleid.'">';
					echo $article['title'];
					echo '</a></td><td><a href="?delete='.$articleid.'">'.$dictionary['delete'].'</a></td></tr>';
				}
				
			?>
			</table>
		</fieldset>
	
		<div class="footer">
			<?php echo $dictionary['poweredby']; ?> <?php echo file_get_contents( ROOT.'core/db/version' ); ?>
		</div>
	</div>
</body>
</html>