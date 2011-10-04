<?php

include('prepend.php');

if (!empty($_POST)) {
 $content = $_POST['text'];
 $Saved_File = fopen('../lang/'.$plxAdmin->aConf['site_lang'].'.php', 'w');
 fwrite($Saved_File, stripslashes($content)); 
 fclose($Saved_File);
 header('Location: parametres_lang.php'); 
}

# On inclut le header
include('top.php');

?>

<h2><?php echo $ADM_langsettings_title; ?></h2>

<form action="parametres_lang.php" method="post" id="change-lang">
	<fieldset>
		<p class="field">
			<label><?php echo $ADM_langsettings_currentlang.': <strong>'.$plxAdmin->aConf['site_lang'].'</strong>'; ?></label>
		</p>
		<textarea rows="80" cols="60" name="text"><?php foreach (file('../lang/'.$plxAdmin->aConf['site_lang'].'.php') as $lines) echo $lines; ?></textarea>
		<p class="field">
			<input type="submit" name="preview" value="<?php echo $ADM_savechanges; ?>"/>
		</p>
	</fieldset>
</form>

<?php

# On inclut le footer
include('foot.php');

?>
