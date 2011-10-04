<?php
$relative = $_SERVER['QUERY_STRING'];
$cutted = substr($relative,9);

$relative = $plxMotor->get;
$cutted = $plxMotor->aGals[$plxMotor->cible]['url'];

$files_path="./album/" . $cutted . "/"; // The key to galerie logic - it points to the folder which name should match the title of the galerie page
$full_server="./album/" . $cutted . "/"; 
$show_files=array("jpg","gif","png","flv","swf","youtube"); // only show these types of files.
$image_files=array("jpg","gif","png");
$flash_files=array("flv","swf","youtube");
$thumb_width = $plxMotor->twidth; // Width of the thumb
$thumb_height = $plxMotor->theight; // Height of the thumb

?>
<?php include('header.php'); ?>
<div id="informer">
</div>
<div id="page">
	<div id="galeria_title"><?php echo $plxShow->galTitle($name); ?> <sup><a href="<?php echo $plxShow->plxMotor->racine.'feed.php?title='.$plxShow->galTitle($name).'&link='.$plxMotor->get; ?>">rss</a></sup></div>
	<div id="content_galeria">
<?php

//Get the current album.

$album=stripslashes(str_replace(".","",$_GET['album'])); //Great security here.. Disallows going up the dir tree.

//Lil bit of security, not much but it may stop some kids from messing!

If(!is_dir($files_path.$album)) {
	$album="";
}

//We don't want ugly _'s or -'s to display with the file or folder names do we? No! So, lets take them out.

$find=array("_","-");
$replace=array(" "," ");

$count=@count($folder);

for($i=0;$i<$count;$i++) {
	If($folder[$i]) {
		
		$path.=$folder[$i]."/";

	}
}

Echo($nav);

//Lets get some images!!

$dir=@opendir($full_server.$album);

//Loop through them all ;).

while($file=@readdir($dir)) {

	//Don't display the stupid directory tree files.

	If($file!= "." AND $file!= "..") {

		//If it's a directory, show the folder image with a link to the new album

		If(is_dir($full_server.$album.$file)) {

		// Else, the file is not a directory, so it must be an image.

		} Else {
			$file_ext=plxUtils::file_ext($file);
			$file_name=plxUtils::file_name($file);
			$display_name=str_replace($find,$replace,$file_name);

			//Hide the thumb files from displaying as regular files and disallow any file types that are not allowed.

			If((!preg_match("/.tb/i",$file)) && (in_array($file_ext,$show_files))) {
					
					//If a thumb file dosen't exists, then try and make one.
					
					If(in_array($file_ext,$image_files)) {
						If(!file_exists($full_server.$file.'.tb')) {
							plxUtils::makeThumb($full_server.$file, $full_server.$file.'.tb',$thumb_width,$thumb_height,'80',$plxShow->plxMotor->thumbtype);
							
							if ($plxShow->plxMotor->watermark==1) { //Appending watermark text if needed
								plxUtils::appendWatermark($full_server.$file,$plxShow->plxMotor->watermark_text);
							}
						}
					}

					//Now, if there is a thumb file, display the thumb, else display the full images but smaller :(.
					If(file_exists($full_server.$file.".tb") && in_array($file_ext,$image_files)) {
						$thumb = $full_server.$file.'.tb';
					}
					
					$swfdimensions = '';
					If (in_array($file_ext,$flash_files)) { //Determining thumb files for flashs
						If ($file_ext=='swf' || $file_ext=='flv') {
							If ($file_ext=='swf') $thumb = './images/flash.png';
							else $thumb = './images/flv.gif';
							$swfdimensions = getimagesize($full_server.$file);
						} else {
							$json = file_get_contents($full_server.$file);
							$youtubeparse = json_decode($json, TRUE);
							$thumb = $youtubeparse['thumb'];
						}
					}

					//Make the html
$remove="";

$opacity = 'filter:alpha(opacity=80); -moz-opacity: 0.8; -khtml-opacity: 0.8; opacity: 0.8;';

					//Parsing filename for name and description
					if ($plxShow->plxMotor->image_caption==1) {
						if ($file_ext!='youtube') {
							$file = plxUtils::hexToStr($file_name);
							if (strpos($file, '!')) {
							  $file = explode("!", $file);
							  $filename = $file[0];
							  $file_description = $file[1];
							  $file_tag = $file[2];
							  $fileuri = $files_path.$album.$file_name;
							} else {
								$file_description = $filename = $file;
								$fileuri = $files_path.$album.$file_name;
							}
						} else {
							$fileuri = $youtubeparse['url'];
							$file_description = $filename = $youtubeparse['title'];
						}
					}

					$rel = 'rel="shadowbox';
					if ($plxShow->plxMotor->images_sets==1 && $file_tag!='' && $file_ext!='youtube') $rel .= '['.$file_tag.']"'; //Checking for image tag
					if ($file_ext=='youtube') $rel .= ';width=480;height=360;player=swf"';
					if ($file_ext=='swf' || $file_ext=='flv') $rel .= ';height="'.$swfdimensions[1].'";width="'.$swfdimensions[0].'"';
					
					
					$filedate = date ("U ", filemtime($files_path.$album.$file_name.'.'.$file_ext));
					
					//Checking for freshness 2592000 - month in unix time
					if ($plxShow->plxMotor->freshness==1) {
						$fresh_label = '';
						$currentdate = date("U");
						if ($filedate >= $currentdate-$plxShow->plxMotor->freshnesstime) $fresh_label = '<div class="freshlabel"></div>';
					}
					
					
					//Checking for sort type
					if ($plxShow->plxMotor->imgorderby==0) { // sort by name
						$images .= "<div class=\"framer ".$file[0]."\">".$fresh_label."<a class=\"galimage\" style=\"background:url(".$thumb.") no-repeat center; width:".$thumb_width."px; height:".$thumb_height."px;".$opacity."\"  href=\"".$fileuri.'.'.$file_ext."\" ".$rel."\" title=\"".$file_description."\"></a>".$filename."</div>\n";
					}
					
					if ($plxShow->plxMotor->imgorderby==1) { // sort by date
						$images[$filedate] .= "<div class=\"framer ".$file[0]."\">".$fresh_label."<a class=\"galimage\" style=\"background:url(".$thumb.") no-repeat center; width:".$thumb_width."px; height:".$thumb_height."px;".$opacity."\"  href=\"".$fileuri.'.'.$file_ext."\" ".$rel."\" title=\"".$file_description."\"></a>".$filename."</div>\n";
					}
					$k++;
			}
		}
	}
}
@closedir($dir);

If($images) { ?>
	<div>
	<?php 
	if ($plxShow->plxMotor->imgorderby==0) echo $images;
	elseif ($plxShow->plxMotor->imgorderby==1){ 
		krsort($images);
		foreach ($images as $image) echo $image;
	}
	?>
	</div>
<?php }; ?>
	<div id="galeria_info"><?php $plxShow->visualis(); ?></div>
	</div>

</div>
<?php include('footer.php'); # On insere le footer ?>