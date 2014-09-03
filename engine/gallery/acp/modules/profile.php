<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group
 TWS Gallery - by Al-x
-----------------------------------------------------
 Version TWS Gallery 5.2
 Powered by http://wonderfullife.ru/
 Support by http://wonderfullife.ru/, http://inker.wonderfullife.ru/
-----------------------------------------------------
 Copyright (c) 2007,2012 TWS
=====================================================
 Данный код защищен авторскими правами
 This file may no be redistributed in whole or significant part.	
 Файл не может быть изменён или использован без прямого согласия автора
 Запрещается использование файла в любых комменрческих целях
=====================================================
 Файл: profile.php
-----------------------------------------------------
 Назначение: Управление профилями категорий
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

$id = (isset($_POST['id'])) ? intval($_POST['id']) : intval($_GET['id']); // исправляем баг $_REQUEST['id'], временно

$_profiles = array();

$db->query("SELECT * FROM " . PREFIX . "_gallery_profiles ORDER BY id");

while($row = $db->get_row()){
	$_profiles[$row['id']] = array();
	foreach ($row as $key => $value){ $_profiles[$row['id']][$key] = $value; }
}

$db->free();


if ($act == 29){

	include_once TWSGAL_DIR.'/classes/editcategory.php';

	$js_array[] = "engine/skins/tabs.js";

	echoheader("", "");
	galnavigation();

	galHeader($langGal['menu_editpro']);

	if (!$id || !isset($_profiles[$id])){

		$row = array();
		$row['id'] = 0;
		$row['p_id'] = 0;
		$row['profile_name'] = "";
		$row['allow_user'] = "";
		$row['skin'] = "";
		$row['view_level'] = "";
		$row['comment_level'] = "";
		$row['upload_level'] = "";
		$row['mod_level'] = "";
		$row['edit_level'] = "";
		$row['foto_sort'] = "0";
		$row['foto_msort'] = "0";
		$row['icon_max_size'] = 0;
		$row['width_max'] = "0";
		$row['height_max'] = "0";
		$row['com_thumb_max'] = "0";
		$row['thumb_max'] = "0";
		$row['allowed_extensions'] = "";
		$row['allow_comments'] = 1;
		$row['allow_rating'] = 1;
		$row['auto_resize'] = 1;
		$row['allow_watermark'] = 1;
		$row['subcats_td'] = 0;
		$row['subcats_tr'] = 0;
		$row['foto_td'] = 0;
		$row['foto_tr'] = 0;
		$row['allow_carousel'] = 1;
		$row['maincatskin'] = "";
		$row['subcatskin'] = "";
		$row['smallfotoskin'] = "";
		$row['bigfotoskin'] = "";
		$row['uploadskin'] = "";
		$row['allow_user_admin'] = 0;
		$row['alt_name_tpl'] = "";
		$row['size_factor'] = 100;
		$moderators = "";

	} else {

		$row = $_profiles[$id];

		$row['profile_name'] = stripslashes($row['profile_name']);

		if ($row['moderators']){

			$row['moderators'] = explode (',',$row['moderators']);
			$moderators = array();

			$names = $db->query("SELECT name FROM " . USERPREFIX . "_users WHERE user_id IN ('".implode ("','",$row['moderators'])."')");

			while($name = $db->get_row($names)){

				$moderators[] = $name['name'];

			}

			$db->free($names);

			$moderators = implode (',',$moderators);

		} else $moderators = "";

		if (!$row['thumb_max']) $row['thumb_max'] = 0;
		if (!$row['com_thumb_max']) $row['com_thumb_max'] = 0;
		if (!$row['icon_max_size']) $row['icon_max_size'] = 0;

	}

	$skinlist = $twsg->SelectGallSkin($row['skin']);
	$categorylist = CategoryGalSelection($row['p_id']);
	$profile_user = radiomenu(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "allow_user", $row['allow_user']);
	$c_sort = makeDropDownGallery(array(""=>$langGal['sys_global'],"posi"=>$langGal['opt_sys_sort_o'],"date"=>$langGal['opt_sys_sdate'],"rating"=>$langGal['opt_sys_srate'],"file_views"=>$langGal['opt_sys_sview'],"comments"=>$langGal['opt_sys_img_com'],"picture_title"=>$langGal['opt_sys_salph']), "foto_sort", $row['foto_sort']);
	$c_msort = makeDropDownGallery(array(""=>$langGal['sys_global'],"desc"=>$langGal['opt_sys_mminus'],"asc"=>$langGal['opt_sys_mplus']), "foto_msort", $row['foto_msort']);
	$uviewlevel = get_gal_groups(explode(',', $row['view_level']), 1);
	$ucomlevel = get_gal_groups(explode(',', $row['comment_level']), 1);
	$uuploadlevel = get_gal_groups(explode(',', $row['upload_level']), 1);
	$umodlevel = get_gal_groups(explode(',', $row['mod_level']), 1, 1, array(1,2));
	$ueditlevel = get_gal_groups(explode(',', $row['edit_level']), 1, 0, array(4,5));
	$ifcomms = ($row['allow_comments']) ? " checked" : "";
	$ifrate = ($row['allow_rating']) ? " checked" : "";
	$ifresize = ($row['auto_resize']) ? " checked" : "";
	$ifwm = ($row['allow_watermark']) ? " checked" : "";
	$ifcr = ($row['allow_carousel']) ? " checked" : "";
	$allow_user_admin = radiomenu(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "allow_user_admin", $row['allow_user_admin']);

	$allowed_extensions = "";
	$row['allowed_extensions'] = explode(',',$row['allowed_extensions']);

	if (!is_array($galConfig['extensions'])) $galConfig['extensions'] = array("{$langGal['extact_no']}"=>''); else {

		$allowed_extensions .= "<option value=\"\"";
		if(!isset($row['allowed_extensions'][0]) || !$row['allowed_extensions'][0]){ $allowed_extensions .= " selected "; }
		$allowed_extensions .= " style=\"color:green;\">{$langGal['sys_global']}</option>\n";

	}

	foreach($galConfig['extensions'] as $value=>$description){
		$allowed_extensions .= "<option value=\"{$value}\"";
		if(in_array($value, $row['allowed_extensions'])){ $allowed_extensions .= " selected "; }
		$allowed_extensions .= ">{$value}</option>\n";
	}

echo <<<HTML
<form action="{$PHP_SELF}?mod=twsgallery&act=30&id={$row['id']}" method="post" enctype="multipart/form-data">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
<div id="dle_tabView1">
<div class="dle_aTab" style="display:none;">
<table width="100%">
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td width="360" style="padding:4px;">{$lang['cat_name']}</td>
        <td><input class="edit" value="{$row['profile_name']}" type="text" name="profile_name"></td>
    </tr>
	<tr>
		<td style="padding:4px;">{$langGal['profile_user']}</td>
		<td>{$profile_user}</td>
	</tr>
    <tr>
        <td style="padding:4px;">{$lang['cat_parent']}<br /><span class=small>{$langGal['cat_parent_d']}</span></td>
        <td><select name="p_id" >{$categorylist}</select></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['alt_name_tpl']}<br /><span class=small>{$langGal['alt_name_tpl_d']}</span></td>
        <td><input class="edit" value="{$row['alt_name_tpl']}" type="text" size="40" name="alt_name_tpl"></td>
    </tr>
</table>
</div>
<div class="dle_aTab" style="display:none;">
<table width="100%">
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td style="padding:4px;">{$lang['cat_skin']}<br /><span class=small>{$langGal['not_ness']}</span></td>
        <td>{$skinlist}</td>
    </tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
    <tr>
        <td style="padding:4px;">{$langGal['subcats_td']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{$row['subcats_td']}" type="text" size="5" name="subcats_td"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['subcats_tr']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{$row['subcats_tr']}" type="text" size="5" name="subcats_tr"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['foto_td']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{$row['foto_td']}" type="text" size="5" name="foto_td"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['foto_tr']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{$row['foto_tr']}" type="text" size="5" name="foto_tr"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_maincat']} (category.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, category.tpl</span></td>
        <td><input class="edit" type="text" name="maincatskin" value="{$row['maincatskin']}">.tpl</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_subcat']} (short_category.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, short_category.tpl</span></td>
        <td><input class="edit" type="text" name="subcatskin" value="{$row['subcatskin']}">.tpl</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_smallfoto']} (short_image.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, short_image.tpl</span></td>
        <td><input class="edit" type="text" name="smallfotoskin" value="{$row['smallfotoskin']}">.tpl</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_bigfoto']} (full_image.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, full_image.tpl</span></td>
        <td><input class="edit" type="text" name="bigfotoskin" value="{$row['bigfotoskin']}">.tpl</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['skin_upload']} (upload.tpl)<br /><span class=small>{$langGal['not_nesstpl']}, upload.tpl</span></td>
        <td><input class="edit" type="text" name="uploadskin" value="{$row['uploadskin']}">.tpl</td>
    </tr>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
	<tr>
        <td style="padding:4px;">{$langGal['c_sort']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td>{$c_sort}</td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['c_msort']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td>{$c_msort}</td>
    </tr>
</table>
</div>
<div class="dle_aTab" style="display:none;">
<table width="100%">
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_uviewlevel']}</td>
        <td><select name="view_level[]" class="cat_select" multiple="multiple">{$uviewlevel}</select></td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_ucomlevel']}</td>
        <td><select name="comment_level[]" class="cat_select" multiple="multiple">{$ucomlevel}</select></td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_uuploadlevel']}</td>
        <td><select name="upload_level[]" class="cat_select" multiple="multiple">{$uuploadlevel}</select></td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_umodlevel']}<br /><span class=small>{$langGal['umodlevel_d']}</span></td>
        <td><select name="mod_level[]" class="cat_select" multiple="multiple">{$umodlevel}</select></td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['cat_ueditlevel']}</td>
        <td><select name="edit_level[]" class="cat_select" multiple="multiple">{$ueditlevel}</select></td>
    </tr>
	<tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	<tr>
		<td style="padding:4px;">{$langGal['profile_allow_user_admin']}</td>
		<td>{$allow_user_admin}</td>
	</tr>
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	<tr>
        <td width="240" style="padding:4px;">{$langGal['moderators']}<br /><span class=small>{$langGal['moderators_d']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input class="edit" type="text" size="40" name="moderators" value="{$moderators}"></td>
    </tr>
</table>
</div>
<div class="dle_aTab" style="display:none;">
<table width="100%">
    <tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_global_width_max']}<br /><span class=small>{$langGal['not_ness2']} {$langGal['c_global_max_s_d']}</span></td>
        <td><input class="edit" value="{$row['width_max']}" type="text" size="5" name="width_max"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_global_height_max']}<br /><span class=small>{$langGal['not_ness2']} {$langGal['c_global_max_s_d']}</span></td>
        <td><input class="edit" value="{$row['height_max']}" type="text" size="5" name="height_max"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_fotoop']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{$row['com_thumb_max']}" type="text" size="8" name="com_thumb_max"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_iubw']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{$row['thumb_max']}" type="text" size="8" name="thumb_max"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_icon_res']}<br /><span class=small>{$langGal['not_ness2']}</span></td>
        <td><input class="edit" value="{$row['icon_max_size']}" type="text" size="8" id="icon_max_size" name="icon_max_size"></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_allowed_ext']}<br /><span class=small>{$langGal['not_ness2']} {$langGal['c_allowed_ext_d']}</span></td>
        <td><select name="extensions[]" class="cat_select" multiple="multiple">{$allowed_extensions}</select></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$langGal['c_size_factor']}<br /><span class=small>{$langGal['c_size_factor_d']}</span></td>
        <td><input class="edit" value="{$row['size_factor']}" type="text" id="size_factor" size="8" name="size_factor">%</td>
    </tr>
</table>
</div>
<div class="dle_aTab" style="display:none;">
<table width="100%">
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
	<tr>
        <td><input type="checkbox" name="allow_comments" id="allow_comments" value="1"{$ifcomms}> <label for="allow_comments">{$langGal['allow_comm']}</label><br />
		<input type="checkbox" name="allow_rating" id="allow_rating" value="1"{$ifrate}> <label for="allow_rating">{$langGal['allow_rating']}</label><br />
		<input type="checkbox" name="auto_resize" id="auto_resize" value="1"{$ifresize}> <label for="auto_resize">{$langGal['allow_resize']}</label><br />
		<input type="checkbox" name="allow_watermark" id="allow_watermark" value="1"{$ifwm}> <label for="allow_watermark">{$langGal['allow_wat']}</label><br />
		<input type="checkbox" name="allow_carousel" id="allow_carousel" value="1"{$ifcr}> <label for="allow_carousel">{$langGal['allow_carousel']}</label></td>
		<td>&nbsp;</td>
    </tr>
	<tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr>
	<tr>
        <td style="padding:4px;">{$langGal['c_auto_prune']}<br /><span class=small>{$langGal['not_ness2']} {$langGal['c_auto_prune_d']}</span></td>
        <td width="30%"><input class="edit" value="{$row['exprise_delete']}" type="text" id="exprise_delete" size="5" name="exprise_delete"></td>
    </tr>
</table>
</div>
</div>
<table width="100%">
	<tr>
		<td colspan="2" align="center" style="padding:10px;"><input type="image" border=0 align="absmiddle" src="engine/skins/images/send.png"></td>
	</tr>
</table>
</form>
HTML;

	echo initTabs1(array($langGal['c_cat_title1'], $langGal['c_cat_title2'], $langGal['c_cat_title3'], $langGal['c_cat_title4'], $langGal['c_cat_title5']));

	galFooter();
	$twsg->galsupport50();
	echofooter();

} elseif ($act == 30){

	if ($id){

		$c_id = $db->super_query("SELECT id FROM " . PREFIX . "_gallery_profiles WHERE id='{$id}'");
		if (!$c_id['id']) $id = 0;

	}

	include_once ENGINE_DIR.'/classes/parse.class.php';
	$parse = new ParseFilter();

	$p_id = intval($_POST['p_id']);

	$profile_name = $parse->process($_POST['profile_name']);
	if ($profile_name == ""){ msg("error", $langGal['cat_error'], $langGal['profile_noname'], "javascript:history.go(-1)"); }

	$sql = "";
	$sql2 = "";

	$vars = array("p_id", "allow_user", "allow_rating", "allow_comments", "auto_resize", "allow_watermark", "subcats_td", "subcats_tr", "foto_td", "foto_tr", "width_max", "height_max", "allow_carousel", "exprise_delete", "allow_user_admin", "size_factor");

	for ($i = 0; $i < count($vars); $i++){

		if($id)
			$sql .= $vars[$i]."='".intval($_POST[$vars[$i]])."', ";
		else {
			$sql .= $vars[$i].", ";
			$sql2 .= "'".intval($_POST[$vars[$i]])."', ";
		}

	}

	if (!in_array($_POST['foto_sort'], array('posi','date','rating','file_views','comments','picture_title'))) $_POST['foto_sort'] = "";
	if (!in_array($_POST['foto_msort'], array('desc','asc'))) $_POST['foto_msort'] = "";

	$vars = array("foto_sort", "foto_msort", "maincatskin", "subcatskin", "smallfotoskin", "bigfotoskin", "uploadskin", "icon_max_size", "com_thumb_max", "thumb_max");

	for ($i = 0; $i < count($vars); $i++){

		if($id){
			$sql .= $vars[$i]."='".totranslit(stripslashes(trim($_POST[$vars[$i]])), true, false)."', ";
		} else {
			$sql .= $vars[$i].", ";
			$sql2 .= "'".totranslit(stripslashes(trim($_POST[$vars[$i]])), true, false)."', ";
		}

	}

	$vars = array("view_level", "upload_level", "comment_level", "edit_level", "mod_level");

	for ($i = 0; $i < count($vars); $i++){

		if($id){
			$sql .= $vars[$i]."='" . save_group_info($_POST[$vars[$i]]) . "', ";
		} else {
			$sql .= $vars[$i].", ";
			$sql2 .= "'".save_group_info($_POST[$vars[$i]])."', ";
		}

	}

	$skin_name = trim( totranslit($_POST['skin_name'], false, false) );
	$alt_name_tpl = $db->safesql(trim($_POST['alt_name_tpl']));
	if ($alt_name_tpl != '' && substr($alt_name_tpl, 0, 1) == '/') $alt_name_tpl = substr($alt_name_tpl, 1);

	if ($skin_name != "" && !@is_dir( ROOT_DIR . '/templates/' . $skin_name)){
		die( "Hacking attempt!" );
	}

	$profile_name = $db->safesql($profile_name);
	$get_post_ext = (isset($_POST['extensions'])) ? $_POST['extensions'] : array();

	$count = count($get_post_ext);

	for ($i=0; $i<$count; $i++){
		if (!isset($galConfig['extensions'][$get_post_ext[$i]])) unset($get_post_ext[$i]);
	}

	$allowed_extensions = $db->safesql(implode(',',$get_post_ext));

	$new_moderators = $db->safesql(str_replace($smallquotes , " ", (strip_tags(stripslashes($_POST['moderators'])))));

	if ($new_moderators != ""){

		$moderators_ar = explode(',',$new_moderators);
		$moderators = array();

		$ids = $db->query("SELECT user_id FROM " . USERPREFIX . "_users WHERE name IN ('".implode ("','",$moderators_ar)."')");

		while($user = $db->get_row($ids)){

			$moderators[] = $user['user_id'];

		}

		$db->free($ids);

		$moderators = implode(',',$moderators);

	} else $moderators = "";

	if ($id){

		$db->query("UPDATE " . PREFIX . "_gallery_profiles SET {$sql}profile_name='{$profile_name}', skin='{$skin_name}', moderators='{$moderators}', allowed_extensions='{$allowed_extensions}', alt_name_tpl='{$alt_name_tpl}' WHERE id='{$id}'");

	} else {

		$db->query("INSERT INTO " . PREFIX . "_gallery_profiles ({$sql}profile_name, skin, moderators, allowed_extensions, alt_name_tpl) VALUES ({$sql2}'{$profile_name}', '{$skin_name}', '{$moderators}', '{$allowed_extensions}', '{$alt_name_tpl}')");

	}

	@unlink(ENGINE_DIR.'/cache/system/gallery_profiles.php');

	galExit($PHP_SELF."?mod=twsgallery&act=28");

} elseif ($act == 31){

	$db->query("DELETE FROM " . PREFIX . "_gallery_profiles WHERE id='{$id}'");

	@unlink(ENGINE_DIR.'/cache/system/gallery_profiles.php');

	galExit($PHP_SELF."?mod=twsgallery&act=28");

} elseif ($act == 28){

	echoheader("", "");
	galnavigation();

	galHeader($langGal['menu_cats_pr']);

echo <<<HTML
<script language="javascript" type="text/javascript">
<!--
function MenuBuild( m_id ){

var menu=new Array()

menu[0]='<a href="{$PHP_SELF}?mod=twsgallery&act=29&id=' + m_id + '" >{$lang['group_sel1']}</a>';
menu[1]='<a href="{$PHP_SELF}?mod=twsgallery&act=31&id=' + m_id + '&dle_allow_hash={$dle_login_hash}" >{$lang['group_sel2']}</a>';

return menu;
}
//-->
</script>
<table width="100%">
    <tr>
        <td style="padding:2px;">
<table width="100%">
  <tr>
   <td width=50>&nbsp;&nbsp;ID</td>
   <td>{$langGal['profile_name']}</td>
   <td>{$langGal['profile_user']}</td>
   <td width=70 align="center">&nbsp;</td>
  </tr>
	<tr><td colspan="4"><div class="hr_line"></div></td></tr>
HTML;

	if (count($_profiles)){

		foreach ($_profiles as $profile){

			$profile['profile_name'] = stripslashes($profile['profile_name']);
			$profile['allow_user'] = ($profile['allow_user']) ? $langGal['yes'] : $langGal['no'];

echo <<<HTML
	<tr>
		<td height=22 class="list">&nbsp;&nbsp;<b>{$profile['id']}</b></td>
		<td class="list">{$profile['profile_name']}</td>
		<td class="list">{$profile['allow_user']}</td>
		<td class="list" align="center"><a onclick="return dropdownmenu(this, event, MenuBuild('{$profile['id']}'), '150px')" href="#"><img src="engine/skins/images/browser_action.gif" border="0"></a></td>
	</tr>
	<tr><td background="engine/skins/images/mline.gif" height="1" colspan="4"></td></tr>
HTML;

		}

	} else {

		echo "<tr><td height=\"1\" align=\"center\" colspan=\"4\">{$langGal['profile_no']}</td></tr>";

	}

echo <<<HTML
	<tr><td colspan="4"><div class="hr_line"></div></td></tr>
	<tr><td colspan="4"><a href="{$PHP_SELF}?mod=twsgallery&act=29"><input onclick="document.location='{$PHP_SELF}?mod=twsgallery&act=29'" type="button" class="buttons" value="{$langGal['profile_new']}"></a></td></tr>
	<tr><td colspan="4"><div class="hr_line"></div></td></tr>
<tr><td colspan="4">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$langGal['profile_descr']}</div></td>
    </tr>
</table>
</td></tr>
	<tr><td colspan="4"><div class="hr_line"></div></td></tr>
</table>
	</td>
    </tr>
</table>
HTML;

	galFooter();
	$twsg->galsupport50();
	echofooter();

}

?>