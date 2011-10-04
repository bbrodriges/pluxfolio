<?php
	if($plxAdmin->version < file_get_contents('http://pluxfolio.googlecode.com/svn/latest/version'))
		$updatecheck = '<a href="http://code.google.com/p/pluxfolio/downloads/list" style="color: red;">'.$ADM_update_available.'</a>';
	else 
		$updatecheck = '<span style="color: green;">'.$ADM_upto_date.'</span>';
?>

</div><div id="navigation2">
		<p id="footer" style="clear: both;"><?php echo $ADM_poweredby_title;?> <a href="http://pluxfolio.ru/">Pluxfolio</a> <?php echo $plxAdmin->version; ?> / <?php echo $updatecheck;?></p>
	</div>
</body>
</html>
