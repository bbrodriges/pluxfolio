<?php

	define( 'ROOT' , './' ); //defining root directory for user
	include_once( ROOT.'core/includer.php' );
	
	/* Verifying installation state */
	/* If site address not persists than installation is not complete */
	/* UNCOMMENT ONLY AFTER SUCCESSFUL INSTALLER TEST */
	
	/* if( !Utilities::readSiteData( 'address' ) ) {
		header( 'Location: '.ROOT.'install.php' );
		exit;
	} else {
		@unlink( ROOT.'install.php' );
	} */
	
	/* Rendering current template */
	$template = new Templates();
	$template->render();
	
?>