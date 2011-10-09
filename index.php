<?php

	define( 'ROOT' , $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
	include('core/includer.php');
	
	
	var_dump( Statics::returnVisible() );
	
?>