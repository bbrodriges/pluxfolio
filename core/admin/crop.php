<?php

include('prepend.php');

if (!empty($_POST)) {

		if($_GET['folder']!='') {
			$filename = '../../album/'.$_GET['folder'].'/'.$_GET['img'];
			$folder = $_GET['folder'];
		}
		else {
			$filename = '../../'.$plxAdmin->aConf['images'].$_GET['img'];
			$folder = $plxAdmin->aConf['images'];
		}
		$quality = 80;
		$filename_out = $filename.'.tb';

		if (file_exists($filename_out)) unlink($filename_out);

		list($width_orig,$height_orig,$type) = getimagesize($filename);
		
		$image_p = imagecreatetruecolor($plxAdmin->twidth,$plxAdmin->theight);
		if($type == 2)
			$image = imagecreatefromjpeg($filename);
		elseif($type == 3)
			$image = imagecreatefrompng($filename);
		elseif($type == 1)
			$image = imagecreatefromgif($filename);	
		imagecopyresampled($image_p, $image, 0, 0, $_POST['x'],$_POST['y'], 200, 150,$_POST['w'],$_POST['h']);
		if($type == 2)
			imagejpeg($image_p, $filename_out, $quality);
		elseif($type == 3)
			imagepng($image_p, $filename_out);
		elseif ($type==1) imagegif($image_p, $filename_out);
		header("Location: images.php?folder=$folder");
}

# Calculating aspect ratio
function GCD($a, $b) {
	while ($b != 0) {
		$remainder = $a % $b;
		$a = $b;
		$b = $remainder;
	}
	return abs ($a);
}

$a = $plxAdmin->twidth; // screen width
$b = $plxAdmin->theight; // screen height
$gcd = GCD($a, $b);
$a = $a/$gcd;
$b = $b/$gcd;
$ratio = $a . " / " . $b;

# On inclut le header
include('top.php');

?>

<script language="Javascript">

jQuery(document).ready(function(){
   jQuery('#cropper').Jcrop({
            onSelect:    showCoords,
	    onChange:    showCoords,
            bgColor:     'black',
            bgOpacity:   .4,
            aspectRatio: <?php echo $ratio;?>
   });
});

function showCoords(c) {
	jQuery('#x').val(c.x);
	jQuery('#y').val(c.y);
	jQuery('#w').val(c.w);
	jQuery('#h').val(c.h);
}

</script>


<h2><?php echo $ADM_imagecrop_title; ?></h2>

		<p class="field">
			<label><?php echo $ADM_imagecrop_help; ?></label>
		</p>

		<p class="field">
			<?php 
				if($_GET['folder']!='') {
					echo '<img src="../../album/'.$_GET['folder'].'/'.$_GET['img'].' "id="cropper" style="border: 1px #cacaca solid;">';
				} 
				else echo '<img src="../../'.$plxAdmin->aConf['images'].$_GET['img'].'" id="cropper" style="border: 1px #cacaca solid;">';
			?>
		</p>

		<form method="post" action="<?php echo 'crop.php?img='.$_GET['img'].'&folder='.$_GET['folder']; ?>">
			<input type="hidden" size="4" id="x" name="x" />
			<input type="hidden" size="4" id="y" name="y" />
			<input type="hidden" size="4" id="w" name="w" />
			<input type="hidden" size="4" id="h" name="h" />
			<input type="hidden" size="4" id="folder" name="folder" value="<?php if($_GET['folder']!='') echo 'notempty'; else echo 'empty'; ?>"/>

		<p class="field">
			<input type="submit" name="preview" value="<?php echo $ADM_savechanges; ?>"/>
		</p>
		</form>

<?php

# On inclut le footer
include('foot.php');

?>
