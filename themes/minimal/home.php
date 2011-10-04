<?php include('header.php'); ?>
<div id="inform"><?php $plxShow->introduc(); ?></div>
<div id="page">
<div id="content" class="homo">
		<?php while($plxShow->plxMotor->plxRecord_arts->loop()): # On boucle sur les articles ?>
			<div class="post <?php $plxShow->artCateg(); ?>"> 
            <a class="fwd" href="<?php $plxShow->artAdres(); ?>"><?php $plxShow->artTitle('name'); ?></a>
                <div><h2 class="title"><?php $plxShow->artTitle('link'); ?></h2>
				<p class="post-info"><?php echo $SITE_postedon;?> <?php $plxShow->artDate(); ?>, <?php echo $SITE_category;?>: <?php $plxShow->artCat(); ?> </p>
				<p class="extract"><?php $plxShow->artChapo(); ?></p>
               
                </div>				
			</div>
		<?php endwhile; # Fin de la boucle sur les articles ?>
	</div>
	<?php include('sidebar.php'); ?><p id="pagination"><?php $plxShow->pagination(); ?></p>
</div>
<?php include('footer.php'); ?>
