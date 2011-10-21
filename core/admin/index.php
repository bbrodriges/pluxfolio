<?php

	define( 'ROOT' , '../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = $dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	list( $thumbWidth , $thumbHeight ) = explode( 'x' , $database['thumbsize'] );
	
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
				<li><a href="<?php echo $database['address']; ?>/core/admin/galleries/"><?php echo $dictionary['galleries']; ?></a></li>
				<li class="current"><a href="<?php echo $database['address']; ?>/core/admin/"><?php echo $dictionary['settings']; ?></a></li>
			</div>
		</div>
	</div>
	
	<div class="informer">
		<div class="container">
			<?php echo $dictionary['settings-help']; ?>
		</div>
	</div>
	
	<div class="container">
	
		<fieldset>
			<legend><h3><?php echo $dictionary['base-settings']; ?></h3></legend>
			<p><label for="site-address"><?php echo $dictionary['site-address']; ?>: </label><input name="site-address" id="site-address" value="<?php echo $database['address']; ?>" size="35"> <span class="help">(<?php echo $dictionary['site-address-help']; ?>)</span></p>
			
			<p><label for="site-title"><?php echo $dictionary['site-title']; ?>: </label><input name="site-title" id="site-title" value="<?php echo $database['title']; ?>" size="35"> &nbsp; <label for="site-subtitle"><?php echo $dictionary['site-subtitle']; ?>: </label><input name="site-subtitle" id="site-subtitle" value="<?php echo $database['subtitle']; ?>" size="35"></p>
			
			<p><label for="site-description"><?php echo $dictionary['site-description']; ?>:</label><br><textarea name="site-description" id="site-description" rows="7" cols="80"><?php echo $database['sitedescription']; ?></textarea></p>
			
			<p class="confirm-button"><input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
		</fieldset>
		
		<fieldset>
			<legend><h3><?php echo $dictionary['visual-settings']; ?></h3></legend>
			
			<p><strong><?php echo $dictionary['basic-visual-settings']; ?></strong></p>
			<p><label for="site-theme"><?php echo $dictionary['site-theme']; ?>: </label><select name="site-theme" id="site-theme"><?php echo Utilities::themesList(); ?></select> &nbsp; <label for="site-language"><?php echo $dictionary['site-language']; ?>: </label><select name="site-language" id="site-language"><?php echo Utilities::languagesList(); ?></select></p>
			
			<p><strong><?php echo $dictionary['articles-settings']; ?></strong></p>
			<p><label for="articles-per-page"><?php echo $dictionary['articles-per-page']; ?>: </label><input name="articles-per-page" id="articles-per-page" value="<?php echo $database['articlesperpage']; ?>" size="2"></p>
			
			<p><strong><?php echo $dictionary['galleries-settings']; ?></strong></p>
			<p><label for="artworks-vote"><?php echo $dictionary['artworks-vote']; ?>: </label><select name="artworks-vote" id="artworks-vote"><?php echo Utilities::onOffList( 'showvotes' );?></select> &nbsp; <label for="artworks-counter"><?php echo $dictionary['artworks-counter']; ?>: </label><select name="artworks-counter" id="artworks-counter"><?php echo Utilities::onOffList( 'showartworkscounter' );?></select> &nbsp; <label for="latest-artworks"><?php echo $dictionary['latest-artworks']; ?>: </label><select name="latest-artworks" id="latest-artworks"><?php echo Utilities::onOffList( 'showlatestartworks' );?></select></p>
			
			<p><strong><?php echo $dictionary['thumbs-dimensions']; ?></strong></p>
			<p><label for="thumb-width"><?php echo $dictionary['thumb-width']; ?>: </label><input name="thumb-width" id="thumb-width" value="<?php echo $thumbWidth; ?>" size="4"><span class="help">px</span> &nbsp;&nbsp; <label for="thumb-height"><?php echo $dictionary['thumb-height']; ?>: </label><input name="thumb-height" id="thumb-height" value="<?php echo $thumbHeight; ?>" size="4"><span class="help">px</span></p>
			
			<p class="confirm-button"><input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
		</fieldset>
		
		<fieldset>
			<legend><h3><?php echo $dictionary['user-settings']; ?></h3></legend>
			<p><?php echo $dictionary['yourusername']; ?>: <strong><?php echo $database['login']; ?></strong></p>
			<p><label for="old-password"><?php echo $dictionary['oldpassword']; ?>: </label><input name="old-password" id="old-password"></p>
			
			<p><label for="new-password"><?php echo $dictionary['newpassword']; ?>: &nbsp;&nbsp;</label><input name="new-password" id="new-password"> &nbsp; <label for="confirm-password"><?php echo $dictionary['confirmpassword']; ?>: </label><input name="confirm-password" id="confirm-password"></p>
			
			<p class="confirm-button"><input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
		</fieldset>
	
		<div class="footer">
			<?php echo $dictionary['poweredby']; ?> <?php echo file_get_contents( ROOT.'core/db/version' ); ?>
		</div>
	</div>
</body>
</html>