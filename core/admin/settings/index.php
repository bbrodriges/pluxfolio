<?php

	define( 'ROOT' , '../../../' ); //defining root directory for admin
	include( ROOT.'core/lib/includer.php' );
	
	$dictionary = json_decode( file_get_contents( ROOT.'core/admin/lang/'.Utilities::readSiteData( 'language' ).'.json' ) , TRUE ); //opens dictionary
	$database = Database::readDB( 'site' , true ); //reads site info
	list( $thumbWidth , $thumbHeight ) = explode( 'x' , $database['thumbsize'] );
	
	if( !empty( $_POST ) && isset( $_GET['type'] ) ){
		switch( $_GET['type'] ) {
			case 'main':
				if( filter_var( $_POST['site-address'], FILTER_VALIDATE_URL ) ) { //preparing site address
					if( $_POST['site-address'] != $database['address'] ) {
						$url = parse_url( $_POST['site-address'] );
						$url = $url['scheme'].'://'.$url['host'].$url['path'];
						if( $url[strlen($url)-1] == '/' ) { //removing trailing slash
							$url = substr( $url , 0 , -1 );
						}
						Utilities::writeSiteData( 'address' , $url );
					}
				}
				Utilities::writeSiteData( 'title' , $_POST['site-title'] );
				Utilities::writeSiteData( 'subtitle' , $_POST['site-subtitle'] );
				Utilities::writeSiteData( 'sitedescription' , $_POST['site-description'] );
				Utilities::writeSiteData( 'GMT' , $_POST['site-time'] );
				break;
			case 'visual':
				Utilities::writeSiteData( 'theme' , $_POST['site-theme'] );
				Utilities::writeSiteData( 'language' , $_POST['site-language'] );
				if( empty( $_POST['articles-per-page'] )  || (int)$_POST['articles-per-page'] < 1 || !is_numeric( $_POST['articles-per-page'] ) ) { //if there's something strange in the input field...
					$_POST['articles-per-page'] = 1; //...who you gonna call?
				}
				Utilities::writeSiteData( 'articlesperpage' , (string)$_POST['articles-per-page'] );
				Utilities::writeSiteData( 'showvotes' , $_POST['artworks-vote'] );
				Utilities::writeSiteData( 'showartworkscounter' , $_POST['artworks-counter'] );
				Utilities::writeSiteData( 'showlatestartworks' , $_POST['latest-artworks'] );
				if( empty( $_POST['thumb-width'] )  || (int)$_POST['thumb-width'] < 1 || !is_numeric( $_POST['thumb-width'] ) ) {
					$_POST['thumb-width'] = 200;
				}
				if( empty( $_POST['thumb-height'] )  || (int)$_POST['thumb-height'] < 1 || !is_numeric( $_POST['thumb-height'] ) ) {
					$_POST['thumb-height'] = 150;
				}
				Utilities::writeSiteData( 'thumbsize' , $_POST['thumb-width'].'x'.$_POST['thumb-height'] );
				break;
			case 'user':
				if(	md5( $_POST['old-password'] ) == $database['password'] ) {
					if( $_POST['new-password'] == $_POST['confirm-password'] ) {
						Utilities::writeSiteData( 'password' , md5( $_POST['new-password'] ) );
					}
				}
				break;
		}
		header('Location: ./');
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
			<form action="./?type=main" method="post">
				<p><label for="site-address"><?php echo $dictionary['site-address']; ?>: </label><input name="site-address" id="site-address" value="<?php echo $database['address']; ?>" size="35"> <span class="help">(<?php echo $dictionary['site-address-help']; ?>)</span></p>
				
				<p><label for="site-title"><?php echo $dictionary['site-title']; ?>: </label><input name="site-title" id="site-title" value="<?php echo $database['title']; ?>" size="35"> &nbsp; <label for="site-subtitle"><?php echo $dictionary['site-subtitle']; ?>: </label><input name="site-subtitle" id="site-subtitle" value="<?php echo $database['subtitle']; ?>" size="35"></p>
				
				<p><label for="site-description"><?php echo $dictionary['site-description']; ?>:</label><br><textarea name="site-description" id="site-description" rows="7" cols="139"><?php echo $database['sitedescription']; ?></textarea></p>
				
				<p><label for="site-time"><?php echo $dictionary['site-time']; ?>: </label><select name="site-time" id="site-time">
					<?php
						for( $time = -12; $time < 13; $time++ ) {
							echo '<option value="';
							if( $time >= 0 ) {
								echo '+';
							}
							echo $time;
							if( $time == $database['GMT'] ){
								echo '" selected>';
							} else {
								echo '">';								
							}
							if( $time >= 0 ) {
								echo '+';
							}
							if( $time >= 0 && $time < 10 ) {
								echo '0'.$time.':00</option>';
							} elseif ( $time >= -9 && $time < 0 ) {
								echo '-0'.abs( $time ).':00</option>';
							} else {
								echo $time.':00</option>';
							}
						}
					?>
				</select></p>
				
				<p class="confirm-button"><input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
			</form>
		</fieldset>
		
		<fieldset>
			<legend><h3><?php echo $dictionary['visual-settings']; ?></h3></legend>
			<form action="./?type=visual" method="post">
				<p><strong><?php echo $dictionary['basic-visual-settings']; ?></strong></p>
				<p><label for="site-theme"><?php echo $dictionary['site-theme']; ?>: </label><select name="site-theme" id="site-theme">
					<?php 
						$themesList = '';
						$currentTheme = Utilities::readSiteData( 'theme' );
						$themes = scandir( ROOT.'themes/' );
						unset( $themes[0] , $themes[1] ); //Delete '.' and '..'
						foreach( $themes as $theme ) {
							if( $theme == $currentTheme ) {
								$themesList .= '<option value="'.$theme.'" selected>'.$theme.'</option>';
							} else {
								$themesList .= '<option value="'.$theme.'">'.$theme.'</option>';
							}
						}
						echo $themesList;
					?>
				</select> &nbsp; <label for="site-language"><?php echo $dictionary['site-language']; ?>: </label><select name="site-language" id="site-language">
					<?php 
						$languagesList = '';
						$currentLanguage = self::readSiteData( 'language' );
						$languages = scandir( ROOT.'core/lang/' );
						unset( $languages[0] , $languages[1] ); //Delete '.' and '..'
						foreach( $languages as $language ) {
							$language = substr( $language , 0 , -5 );
							if( $language == $currentLanguage ) {
								$languagesList .= '<option value="'.$language.'" selected>'.$language.'</option>';
							} else {
								$languagesList .= '<option value="'.$language.'">'.$language.'</option>';
							}
						}
						echo $languagesList;
					?>
				</select></p>
				
				<p><strong><?php echo $dictionary['articles-settings']; ?></strong></p>
				<p><label for="articles-per-page"><?php echo $dictionary['articles-per-page']; ?>: </label><input name="articles-per-page" id="articles-per-page" value="<?php echo $database['articlesperpage']; ?>" size="2"></p>
				
				<p><strong><?php echo $dictionary['galleries-settings']; ?></strong></p>
				<p><label for="artworks-vote"><?php echo $dictionary['artworks-vote']; ?>: </label><select name="artworks-vote" id="artworks-vote"><?php echo Utilities::onOffList( 'showvotes' );?></select> &nbsp; <label for="artworks-counter"><?php echo $dictionary['artworks-counter']; ?>: </label><select name="artworks-counter" id="artworks-counter"><?php echo Utilities::onOffList( 'showartworkscounter' );?></select> &nbsp; <label for="latest-artworks"><?php echo $dictionary['latest-artworks']; ?>: </label><select name="latest-artworks" id="latest-artworks"><?php echo Utilities::onOffList( 'showlatestartworks' );?></select></p>
				
				<p><strong><?php echo $dictionary['thumbs-dimensions']; ?></strong></p>
				<p><label for="thumb-width"><?php echo $dictionary['thumb-width']; ?>: </label><input name="thumb-width" id="thumb-width" value="<?php echo $thumbWidth; ?>" size="4"><span class="help">px</span> &nbsp;&nbsp; <label for="thumb-height"><?php echo $dictionary['thumb-height']; ?>: </label><input name="thumb-height" id="thumb-height" value="<?php echo $thumbHeight; ?>" size="4"><span class="help">px</span></p>
				
				<p class="confirm-button"><input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
			</form>
		</fieldset>
		
		<fieldset>
			<legend><h3><?php echo $dictionary['user-settings']; ?></h3></legend>
			<form action="./?type=user" method="post">
				<p><?php echo $dictionary['yourusername']; ?>: <strong><?php echo $database['login']; ?></strong></p>
				<p><label for="old-password"><?php echo $dictionary['oldpassword']; ?>: </label><input name="old-password" id="old-password"></p>
				
				<p><label for="new-password"><?php echo $dictionary['newpassword']; ?>: &nbsp;&nbsp;</label><input name="new-password" id="new-password"> &nbsp; <label for="confirm-password"><?php echo $dictionary['confirmpassword']; ?>: </label><input name="confirm-password" id="confirm-password"></p>
				
				<p class="confirm-button"><input type="submit" value="<?php echo $dictionary['savechanges']; ?>"></p>
			</form>
		</fieldset>
	
		<div class="footer">
			<?php echo $dictionary['poweredby']; ?> <?php echo file_get_contents( ROOT.'core/db/version' ); ?>
		</div>
	</div>
</body>
</html>