<div id="sidebar">
	<div id="categories">
		<h2><a <?php if ($plxShow->plxMotor->categ_get==1) {?>onclick="toggleDiv('catlist')"<?php };?> id="cathead"><?php echo $SITE_categories_title; ?></a>
		</h2>
		<ul id="catlist">
			<?php $plxShow->catList('','#cat_name'); ?>
		</ul>
	</div>
	<?php $plxShow->artFeed('atom'); ?>
	<?php if ($plxShow->plxMotor->twitter != '') {; ?>
	<br><br>
    <div id="tweets">
		<h2><?php echo $SITE_twitts_title; ?></h2>
		<div id="twitter_update_list"></div>
		<a href="http://twitter.com/<?php echo $plxShow->plxMotor->twitter; ?>"><?php echo $SITE_twitts_mytwitter;?></a>
	</div>
	<?php }; ?>
</div>
<div class="clearer"></div>
