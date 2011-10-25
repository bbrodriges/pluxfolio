<?php

	define( 'ROOT' , '../../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	$currentVersion = file_get_contents( ROOT.'core/db/version' );
	$updaterData = json_decode( file_get_contents( 'http://pluxfolio.ru/update/descriptor' ) );
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
				<li class="current"><a href="<?php echo $database['address']; ?>/core/admin/update/"><?php echo $dictionary['update']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/language/"><?php echo $dictionary['lang']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/blog/"><?php echo $dictionary['blog']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/statics/"><?php echo $dictionary['statics']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/galleries/"><?php echo $dictionary['galleries']; ?></a></li>
				<li><a href="<?php echo $database['address']; ?>/core/admin/"><?php echo $dictionary['settings']; ?></a></li>
			</div>
		</div>
	</div>
	
	<div class="informer">
		<div class="container">
			<?php echo $dictionary['update-help']; ?>
		</div>
	</div>
	
	<div class="container">
	
		<fieldset>
			<legend><h3><?php echo $dictionary['update']; ?></h3></legend>
			<form action="./update.php" method="post">
				<?php if( $currentVersion >= $updaterData->version ) {?>
				
					<h3 class="update-title"><?php echo $dictionary['latest-version']; ?></h3>
				
				<?php } else { ?>
				<h3 class="update-title"><?php echo $dictionary['new-version-available']; ?> <strong><?php echo $updaterData->version; ?></strong></h3>
				<p><?php echo $dictionary['whats-new']; ?></p>
				<p><?php echo $updaterData->changelog; ?></p>
				<p class="confirm-update">
					<input type="submit" value="<?php echo $dictionary['proceed-update']; ?>">
					<br><br><input type="checkbox" checked name="send-statistics"> <?php echo $dictionary['send-statistics']; ?>
				</p>
				<?php } ?>
			</form>
		</fieldset>
	
		<div class="footer">
			<?php echo $dictionary['poweredby']; ?> <?php echo file_get_contents( ROOT.'core/db/version' ); ?>
		</div>
	</div>
</body>
</html>