<?php include('header.php'); # On insere le header ?>
<div id="informer">
	<div id="galeria_title"><?php echo $SITE_categories_title; ?></div>
</div>
<div id="page">
	<div id="content" class="cat">
		<?php while($plxShow->plxMotor->plxRecord_arts->loop()): # On boucle sur les articles ?>
			<div class="post  <?php $plxShow->artCateg(); ?>">
               <div><h2 class="title"><?php $plxShow->artTitle('link'); ?></h2>
				<p class="post-info"><?php echo $SITE_postedon; ?> <?php $plxShow->artDate(); ?>, <?php echo $SITE_category; ?>: <?php $plxShow->artCat(); ?> </p>
				<p><?php $plxShow->artChapo(); ?></p>
				</div>
			</div>
		<?php endwhile; # Fin de la boucle sur les articles ?>
	</div>
	<?php include('sidebar.php'); # On insere la sidebar ?><p id="pagination"><?php $plxShow->pagination(); ?></p>
</div>
<?php include('footer.php'); # On insere le footer ?>
