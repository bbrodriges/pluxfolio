<?php include('header.php'); # On insere le header ?>
<div id="page" class="article">
<div id="content" class="artikol">
<div class="post articul">
<h2 class="title"><?php $plxShow->artTitle(); ?></h2>
<p class="post-info"><?php echo $SITE_postedon; ?> <?php $plxShow->artDate(); ?> <?php $plxShow->artHour(); ?> |  <?php echo $SITE_category; ?>: <?php $plxShow->artCat(); ?></p>
			<?php $plxShow->artContent(); ?>
		</div>
		
	</div>
	<?php include('sidebar.php'); # On insere la sidebar ?>
</div>
<?php include('footer.php'); # On insere le footer ?>
