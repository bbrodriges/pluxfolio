<?php

	define( 'ROOT' , '../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = $dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	$thumbDimensions = explode( 'x' , $database['thumbsize'] );

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
				<li><a><?php echo $dictionary['blog']; ?></a></li>
				<li><a><?php echo $dictionary['statics']; ?></a></li>
				<li><a><?php echo $dictionary['galleries']; ?></a></li>
				<li class="current"><a><?php echo $dictionary['settings']; ?></a></li>
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
			<?php echo $dictionary['poweredby']; ?> <?php echo $database['version']; ?>
		</div>
	</div>
</body>
</html>