<?php

/**
 * Edition des paramètres d'affichage
 *
 * @package PLX
 * @author	Florent MONTHEL
 **/

include('prepend.php');

# On édite la configuration
if(!empty($_POST)) {
	$plxAdmin->editConfiguration($plxAdmin->aConf,plxUtils::unSlash($_POST));
	header('Location: parametres_affichage.php');
	exit;
}

# On inclut le header
include('top.php');

# On récupère les templates
$tpl = new plxGlob(PLX_ROOT.'themes', true);
$a_style = $tpl->query('/[a-z0-9-_]+/');
foreach($a_style as $k=>$v)
	$b_style[ $v ] = $v;

# Tableau du tri
$aTri = array('desc'=>$ADM_sortby_desc, 'asc'=>$ADM_sortby_asc);

//Список страницы для главной
$arr_index[0] = $SITE_headermenu_firstnews;
foreach ($plxAdmin->aGals as $key=>$val)
	$arr_index[$key] = $val['name'];
foreach ($plxAdmin->aStats as $key=>$val)
	$arr_index[$key] = $val['name'];

//Выбор вывода категорий
$categ[0] = $ADM_contentsettings_catliststatic;
$categ[1] = $ADM_contentsettings_catlistcollapsable;

//Показывать или нет счетчик работ
$counter[0] = $ADM_no;
$counter[1] = $ADM_yes;

$imgorder_by[0] = $ADM_imgorderby_name;
$imgorder_by[1] = $ADM_imgorderby_date;

$thumb_type[0] = $ADM_thumbtype_scale;
$thumb_type[1] = $ADM_thumbtype_crop;

//Свежесть картинок
$freshtime[604800] = $ADM_week;
$freshtime[1296000] = $ADM_fifteendays;
$freshtime[2592000] = $ADM_month;
?>

<h2><?php echo $ADM_contentsettings_title; ?></h2>

<form action="parametres_affichage.php" method="post" id="change-cf-file">
	<fieldset class="withlabel">
		<legend><?php echo $ADM_contentsettings_legend; ?>:</legend>
        <p class="field floated"><label><?php echo $ADM_contentsettings_displaynews; ?>?</label>
		<?php plxUtils::printSelect('nonews', $counter, $plxAdmin->aConf['nonews']); ?></p>
		<p class="field floated"><label><?php echo $ADM_contentsettings_frontdisplay; ?>:</label>
		<?php plxUtils::printSelect('index_get', $arr_index, $plxAdmin->aConf['index_get']); ?></p>
		<p class="field floated"><label><?php echo $ADM_contentsettings_sitetemplate; ?>:</label>
		<?php plxUtils::printSelect('style', $b_style, $plxAdmin->aConf['style']); ?></p>
	<?php if ($plxAdmin->aConf['admin_conf']==0) { ?>
		<p class="field floated"><label><?php echo $ADM_contentsettings_articlesort; ?>:</label>
		<?php plxUtils::printSelect('tri', $aTri, $plxAdmin->aConf['tri']); ?></p>
		<p class="field floated"><label><?php echo $ADM_contentsettings_catlistcollapse; ?>:</label>
		<?php plxUtils::printSelect('categ_get', $categ, $plxAdmin->aConf['categ_get']); ?></p>
		<p class="field floated"><label><?php echo $ADM_contentsettings_articlesperpage; ?>:</label>
		<?php plxUtils::printInput('bypage', $plxAdmin->aConf['bypage'], 'text', '10-10'); ?></p>
		<p class="field floated"><label><?php echo $ADM_contentsettings_articlesperadminpage; ?>:</label>
		<?php plxUtils::printInput('bypage_admin', $plxAdmin->aConf['bypage_admin'], 'text', '10-10'); ?></p>
		<p class="field floated"><label><?php echo $ADM_contentsettings_articlesperatom; ?>:</label>
		<?php plxUtils::printInput('bypage_feed', $plxAdmin->aConf['bypage_feed'], 'text', '10-10'); ?></p>
	<?php }; ?>
		<p style="clear: both;"></p>
		<p class="field"><label><?php echo $ADM_twitter_translation; ?>:</label></p>
		<?php plxUtils::printInput('twitter', $plxAdmin->aConf['twitter'], 'text', '10-255'); echo '<i>'.$ADM_twitter_translationhelp.'</i>'; ?>
	</fieldset>
	<fieldset class="withlabel">
		<legend><?php echo $ADM_worksnthumbs_legend; ?>:</legend>
		<p class="field floated"><label><?php echo $ADM_contentsettings_displaycounter; ?>?</label>
		<?php plxUtils::printSelect('counter_enabled', $counter, $plxAdmin->aConf['counter_enabled']); ?></p>
		<p class="field floated"><label><?php echo $ADM_contentsettings_enableimgsets; ?>?</label>
		<?php plxUtils::printSelect('images_sets', $counter, $plxAdmin->aConf['images_sets']); ?></p>
		<p class="field floated"><label><?php echo $ADM_contentsettings_enablefreshness; ?>?</label>
		<?php plxUtils::printSelect('freshness', $counter, $plxAdmin->aConf['freshness']); ?></p>
	<?php if ($plxAdmin->aConf['freshness']==1) { ?>		
		<p class="field floated"><label><?php echo $ADM_contentsettings_freshnesstime; ?>:</label>
		<?php plxUtils::printSelect('freshnesstime', $freshtime, $plxAdmin->aConf['freshnesstime']); ?></p>
	<?php }; ?>
		<p class="field floated"><label><?php echo $ADM_images_caption; ?>?</label>
		<?php plxUtils::printSelect('image_caption', $counter, $plxAdmin->aConf['image_caption']); ?></p>
	<?php if ($plxAdmin->aConf['admin_conf']==0) { ?>
		<p class="field floated"><label><?php echo $ADM_images_order; ?>:</label>
		<?php plxUtils::printSelect('imgorderby', $imgorder_by, $plxAdmin->aConf['imgorderby']); ?></p>
		<p style="clear: both;"></p>
		<p class="field"><label><?php echo $ADM_thumbtype_title; ?>:</label></p>
		<?php plxUtils::printSelect('thumbtype', $thumb_type, $plxAdmin->aConf['thumbtype']); ?>
		<p class="field"><label><?php echo $ADM_contentsettings_thumbssizes; ?>:</label></p>
		<?php echo $ADM_contentsettings_thumbwidth; ?><?php plxUtils::printInput('twidth', $plxAdmin->twidth, $type='text', $size='10-4'); ?>
		<?php echo $ADM_contentsettings_thumbheight; ?><?php plxUtils::printInput('theight', $plxAdmin->theight, $type='text', $size='10-4'); echo ' '.$ADM_contentsettings_thumboffesthelp; ?>
	<?php }; ?>
	</fieldset>
	<fieldset class="withlabel">
		<legend><?php echo $ADM_watermark_legend; ?>:</legend>
		<p class="field floated"><label><?php echo $ADM_watermark_enable; ?>?</label>
		<?php plxUtils::printSelect('watermark', $counter, $plxAdmin->aConf['watermark']); ?></p>
	<?php if ($plxAdmin->aConf['watermark']==1) { ?>
		<p class="field floated"><label><?php echo $ADM_watermark_text; ?>:</label>
		<?php plxUtils::printInput('watermark_text', $plxAdmin->aConf['watermark_text'], 'text', '10-255');?></p>
		<p style="clear: both;"><?php echo $ADM_contentsettings_watermarkhelp; ?></p>
	<?php }; ?>
	</fieldset>
	<p><input type="submit" value="<?php echo $ADM_savechanges; ?>" /></p>
</form>

<?php
# On inclut le footer
include('foot.php');
?>
