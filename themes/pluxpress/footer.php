<div id="footer">
	<?php echo $SITE_footer; ?><a href="http://rodriges.org/">pluxpress</a>.</p><?php if ($plxShow->plxMotor->maintence==1 && isset($_COOKIE["PHPSESSID"])) echo '<span style="color:red">'.$SITE_maintence_label.'</span>'; ?></div>

<?php if ($plxShow->plxMotor->twitter != '') {?> 
	<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
	<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/<?php echo $plxShow->plxMotor->twitter; ?>.json?callback=twitterCallback2&count=3"></script>
<?php };?>

<?php if ($plxShow->plxMotor->googleanalytics != '') {?>
	<script type="text/javascript"> 
 
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?php echo $plxShow->plxMotor->googleanalytics; ?>']);
	  _gaq.push(['_trackPageview']);
	 
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
 
</script> 
<?php };?>

<?php if ($plxShow->plxMotor->yandexmetrika != '') {?> 
	<!-- Yandex.Metrika counter -->
	<div style="display:none;"><script type="text/javascript">
	(function(w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter<?php echo $plxShow->plxMotor->yandexmetrika; ?> = new Ya.Metrika(<?php echo $plxShow->plxMotor->yandexmetrika; ?>);
				 yaCounter<?php echo $plxShow->plxMotor->yandexmetrika; ?>.clickmap(true);
				 yaCounter<?php echo $plxShow->plxMotor->yandexmetrika; ?>.trackLinks(true);
			
			} catch(e) { }
		});
	})(window, 'yandex_metrika_callbacks');
	</script></div>
	<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
	<noscript><div><img src="//mc.yandex.ru/watch/<?php echo $plxShow->plxMotor->yandexmetrika; ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->
<?php };?>

</body>
</html>
