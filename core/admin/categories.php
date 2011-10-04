<?php

/**
 * Edition des catégories
 *
 * @package PLX
 * @author	Stephane F et Florent MONTHEL
 **/

include('prepend.php');

# On édite les catégories
if(!empty($_POST)) {
	$plxAdmin->editCategories(plxUtils::unSlash($_POST));
	header('Location: categories.php');
	exit;
}

# On inclut le header	
include('top.php');

# Tableau du tri
$aTri = array('desc'=>$ADM_sortby_desc, 'asc'=>$ADM_sortby_asc);
?>

<h2><?php echo $ADM_categories_title; ?></h2>
<form action="categories.php" method="post" id="change-cat-file">
	<fieldset>
		<table>
			<tr>
				<td><strong>ID</strong>&nbsp;:</td>
				<td><strong><?php echo $ADM_category_title; ?></strong>&nbsp;:</td>
				<td><strong>URL</strong>&nbsp;:</td>
				<td><strong><?php echo $ADM_category_sorttitle; ?></strong>&nbsp;:</td>
				<td><strong><?php echo $ADM_category_artperpage; ?></strong>&nbsp;:</td>				
				<td><strong><?php echo $ADM_gal_order; ?></strong>&nbsp;:</td>
			</tr>
		<?php
		# Initialisation de l'ordre
		$num = 0;
		# Si on a des catégories
		if($plxAdmin->aCats) {
			foreach($plxAdmin->aCats as $k=>$v) { # Pour chaque catégorie
				echo '<tr><td><label>№ '.$k.'</label></td><td>';		
				plxUtils::printInput($k, htmlspecialchars($v['name'],ENT_QUOTES,PLX_CHARSET), 'text', '20-50');
				echo '</td><td>';
				plxUtils::printInput($k.'_url', $v['url'], 'text', '20-50');
				echo '</td><td>';	
				plxUtils::printSelect($k.'_tri', $aTri, $v['tri']);
				echo '</td><td>';
				plxUtils::printInput($k.'_bypage', $v['bypage'], 'text', '4-3');
				echo '</td><td>';
				plxUtils::printInput($k.'_ordre', ++$num, 'text', '3-3');
				echo '</td></tr>';
			}
			# On récupère le dernier identifiant
			$a = array_keys($plxAdmin->aCats);
			rsort($a);	
		} else {
			$a['0'] = 0;
		}
		?>
		<tr>
		<td><?php echo $ADM_category_newcategory; ?></td><td>
		<?php
		plxUtils::printInput(str_pad($a['0']+1, 3, "0", STR_PAD_LEFT), '', 'text', '20-50');
		echo '</td><td></td><td>';
		plxUtils::printSelect(str_pad($a['0']+1, 3, "0", STR_PAD_LEFT).'_tri', $aTri, $plxAdmin->aConf['tri']);
		echo '</td><td>';
		plxUtils::printInput(str_pad($a['0']+1, 3, "0", STR_PAD_LEFT).'_bypage', $plxAdmin->aConf['bypage'], 'text', '4-3');
		echo '</td><td>';
		plxUtils::printInput(str_pad($a['0']+1, 3, "0", STR_PAD_LEFT).'_ordre', ++$num, 'text', '3-3');
		echo '</td></tr>';
		?>
		</table>
		<p><input type="submit" value="<?php echo $ADM_savechanges; ?>" /></p>
	</fieldset>
</form>

<?php if ($plxAdmin->aConf['admin_conf']==0) { ?>

<div class="help">
	<?php echo $ADM_category_help; ?>
</div>

<?php }; ?>

<?php
# On inclut le footer
include('foot.php');
?>
