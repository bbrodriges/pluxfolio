<?php

include('prepend.php');

$dh  = opendir('../../themes/'.$plxAdmin->aConf['style']);
while (false !== ($filename = readdir($dh))) {
    if (is_file('../../themes/'.$plxAdmin->aConf['style'].'/'.$filename)) $files[] = $filename;
}

if (empty($_GET)) $current_file = $files[0];
else $current_file = $_GET['file'];

if (!empty($_POST)) {
 $content = $_POST['text'];
 $Saved_File = fopen('../../themes/'.$plxAdmin->aConf['style'].'/'.$current_file, 'w');
 fwrite($Saved_File, stripslashes($content)); 
 fclose($Saved_File);
 header('Location: parametres_theme.php?file='.$current_file); 
}

# On inclut le header
include('top.php');

?>

<h2><?php echo $ADM_themesettings_title; ?></h2>

<div style="float: left; width: 600px;">
<form action="<?php echo 'parametres_theme.php?file='.$current_file;?>" method="post" id="change-theme">
	<fieldset>
		<p class="field">
			<label><?php echo $ADM_themesettings_currenttheme.': <strong>'.$plxAdmin->aConf['style'].'</strong>'; ?></label>
		</p>
		<p class="field"><label><?php echo $ADM_themesettings_editingfile.': '.$current_file; ?></label></p>
		<textarea rows="80" cols="60" name="text"><?php foreach (file('../../themes/'.$plxAdmin->aConf['style'].'/'.$current_file) as $lines) echo $lines; ?></textarea>
		<p class="field">
			<input type="submit" name="preview" value="<?php echo $ADM_savechanges; ?>"/>
		</p>
	</fieldset>
</form>
</div>

<div style="float: left;">
<?php
echo '<strong>'.$ADM_themesettings_fileslist.':</strong><br><br>';
foreach($files as $file) echo '<a href="?file='.$file.'">'.$file.'</a><br>';
?>
</div>

<?php

# On inclut le footer
include('foot.php');

?>
