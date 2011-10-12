<?php

	define( 'ROOT' , './' ); //defining root directory for user
	include_once( ROOT.'core/includer.php' );
	
	/* Verifying installation state */
	/* If site address not persists than installation is not complete */
	if( !Utilities::getSiteData( 'address' ) ) {
		header( 'Location: '.ROOT.'install.php' );
		exit;
	}
	
	/* Rendering template */
	Templates::render();
	
?>