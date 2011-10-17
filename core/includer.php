<?php 

	include_once('lib/database.class.php'); //must be first
	include_once('lib/utilities.class.php'); //must be second
	include_once('lib/galleries.class.php');
	include_once('lib/statics.class.php');
	include_once('lib/articles.class.php');
	include_once('lib/mustache.class.php'); //must be before templates.class.php
	include_once('lib/templates.class.php');
	
	/* Defines major objects */
	$Database  = new Database;
	$Utility   = new Utilities;
	$Gallery   = new Galleries;
	$Artwork   = new Artworks;
	$Static    = new Statics;
	$Article   = new Articles;
	$Template  = new Templates;

?>