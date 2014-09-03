<?php
/*
=====================================================
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
 Файл: upload.php
-----------------------------------------------------
 Назначение: Загрузка файлов через админ-панель
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

require_once TWSGAL_DIR.'/classes/editfile.php';
require_once TWSGAL_DIR.'/classes/editcategory.php';
require_once TWSGAL_DIR.'/classes/upload.php';
require_once TWSGAL_DIR.'/classes/thumbnailer.php';
require_once TWSGAL_DIR.'/classes/upload.action.php';
require_once ENGINE_DIR.'/classes/parse.class.php';

$galConfig['advance_default'] = 2;

$page = isset($_REQUEST['ap']) ? intval($_REQUEST['ap']) : 0;

switch ($page){
case 0 :

	$temp_upload_dir = 'temp';

	$upload = new gallery_upload_action($_REQUEST['cat']);

	$cat_selected_first = (!$upload->cat) ? true : false;

	$categories = CategoryGalSelection($upload->cat);

	if (!$categories)
		msg("error", $lang['cat_error'], $upload->lang['add_foto_error_22'], "javascript:history.go(-1)"); 

	if (!$upload->cat && $cat_selected_first !== true) $upload->cat = $upload->load_cat_by_id($cat_selected_first);

	if ($upload->cat)
		$foto_title = stripslashes($upload->upload_category[$upload->cat]['cat_title']);
	else
		$foto_title = "";

	$upload_ajax = $upload->js_print($upload->category_rules($upload->cat));

	$count = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies WHERE approve=3 AND user_id='".$member_id['user_id']."' AND date > '".date ("Y-m-d H:i:s", (TIME-3600*2))."'");

	$upload_continue = ($count['count']) ? "&nbsp;&nbsp;<a href=\"{$PHP_SELF}?mod=twsgallery&act=12&ap=1&cat=".$upload->cat."\"><input onclick=\"document.location='{$PHP_SELF}?mod=twsgallery&act=12&ap=1&cat=".$upload->cat."'\" type=\"button\" class=\"buttons\" value=\"{$langGal['upload_continue']}\" style=\"width:230px;\"></a>" : "";

	$js_array[] = "engine/skins/tabs.js";
	$js_array[] = "engine/gallery/js/not_logged.js";

	echoheader("", "");
	galnavigation();

	echo "<script language=\"javascript\" type=\"text/javascript\">
<!--
var dle_root       = '{$config['http_home_url']}';
var dle_admin      = '{$config['admin_path']}';
var dle_login_hash = '{$dle_login_hash}';
var dle_skin       = '{$config['skin']}';
var dle_info       = '{$lang['p_info']}';
var dle_confirm    = '{$lang['p_confirm']}';
var dle_prompt     = '{$lang['p_prompt']}';
var dle_complaint  = '';
var dle_p_send     = '{$lang['p_send']}';
var dle_p_send_ok  = '{$lang['p_send_ok']}';
//-->
</script>
{$upload_ajax}
<form method=post name=\"entryform\" id=\"entryform\" onsubmit=\"return upload_check(0)\" action=\"\" enctype=\"multipart/form-data\"><input type=\"hidden\" name=\"mod\" value=\"twsgallery\"><input type=\"hidden\" name=\"act\" value=\"12\"><input type=\"hidden\" name=\"ap\" value=\"1\">\n";

	galHeader($langGal['menu_add']." - ".$langGal['menu_add_s1']);
	$langGal['upl_type5_c'] = str_replace('{FOTO_DIR}', FOTO_DIR . '/', $langGal['upl_type5_c']);

	echo <<<HTML
<style type="text/css">
fieldset { 
	border:1px solid #CDCDCD;
	padding: 20px 10px;
	margin:0px;
}
fieldset.flash {
	margin: 0px;
	border-color: #CDCDCD;
}
.progressWrapper {
	width: 99%;
	overflow: hidden;
}
.progressContainer {
	margin: 5px;
	padding: 4px;
	border: solid 1px #E8E8E8;
	background-color: #F7F7F7;
	overflow: hidden;
}
/* Message */
.message {
	margin: 1em 0;
	padding: 10px 20px;
	border: solid 1px #FFDD99;
	background-color: #FFFFCC;
	overflow: hidden;
}
/* Error */
.red {
	border: solid 1px #B50000;
	background-color: #FFEBEB;
}
/* Current */
.green {
	border: solid 1px #DDF0DD;
	background-color: #EBFFEB;
}
/* Complete */
.blue {
	border: solid 1px #CEE2F2;
	background-color: #F0F5FF;
}
.progressName {
	font-size: 8pt;
	font-weight: 700;
	color: #555;
	width: 323px;
	height: 14px;
	text-align: left;
	white-space: nowrap;
	overflow: hidden;
}
.progressBarInProgress,
.progressBarComplete,
.progressBarError {
	font-size: 0;
	width: 0%;
	height: 2px;
	background-color: blue;
	margin-top: 2px;
}
.progressBarComplete {
	width: 100%;
	background-color: green;
	visibility: hidden;
}
.progressBarError {
	width: 100%;
	background-color: red;
	visibility: hidden;
}
.progressBarStatus {
	margin-top: 2px;
	width: 99%;
	font-size: 7pt;
	font-family: Arial;
	text-align: left;
	white-space: nowrap;
}
a.progressCancel {
	font-size: 0;
	display: block;
	height: 14px;
	width: 14px;
	background-image: url(/engine/classes/swfupload/cancelbutton.gif);
	background-repeat: no-repeat;
	background-position: -14px 0px;
	float: right;
}
a.progressCancel:hover {
	background-position: 0px 0px;
}
</style>
<input type=hidden name="mod" value="twsgallery"><input type=hidden name="act" value="12"><input type=hidden name="p" value="1">
<div id="dle_tabView1">
	<div class="dle_aTab" style="display:none;">
		<br />
		<div style="position: relative">
			<input id="btnBrowse" type="button" value="{$langGal['upload_init']}" style="width:160px;" class="edit" disabled="disabled" />&nbsp;&nbsp;<input id="btnStart" type="button" value="{$langGal['upload_start']}" onclick="upload_check(1);" style="width:160px;" class="edit" disabled="disabled" />&nbsp;&nbsp;<input id="btnCancel" type="button" value="{$langGal['upload_cancel']}" onclick="swfu.cancelQueue();" disabled="disabled" style="width:160px;" class="edit" />
			<div id="flash_container" style="width:130px; height: 20px;position:absolute;top:0;left:0px;">Flash Container</div>
		</div>
		<br />
		<fieldset class="flash" id="fsUploadProgress">
			<legend>{$lang['upload_queue']}</legend>
		</fieldset>
		<div class="hr_line"></div>
		<div class="navigation">{$langGal['upl_type3_d']}</div>
	</div>
	<div class="dle_aTab" style="display:none;">
		<table id="tblSample" class="upload">
		 <tr id="row">
		  <td>{$langGal['upl_type1_c']}<br /><input type="file" size="81" name="image[]"></td>
		</tr>
		</table>
		<div class="hr_line"></div>
		<input type=button class=buttons value=' - ' style="width:30px;" title='{$lang['images_rem_tl']}' onclick="RemoveImages('tblSample');return false;">
		<input type=button class=buttons value=' + ' style="width:30px;" title='{$lang['images_add_tl']}' onclick="AddImages('tblSample', 'image[]', 'file', '81');return false;">
		<div class="hr_line"></div>
		<div class="navigation">{$langGal['upl_type1_d']}</div>
	</div>
	<div class="dle_aTab" style="display:none;">
		<table id="tblSample2" class="upload">
		 <tr id="row">
		  <td>{$langGal['upl_type2_c']}<br /><input type="text" size="81" name="url[]"></td>
		</tr>
		</table>
		<div class="hr_line"></div>
		<input type=button class=buttons value=' - ' style="width:30px;" title='{$lang['images_rem_tl']}' onclick="RemoveImages('tblSample2');return false;">
		<input type=button class=buttons value=' + ' style="width:30px;" title='{$lang['images_add_tl']}' onclick="AddImages('tblSample2', 'url[]', 'text', '81');return false;">
		<div class="hr_line"></div>
		&nbsp;&nbsp;&nbsp;<input type="checkbox" name="remote_upload" id="remote_upload" value="1" /> <label for="remote_upload">{$langGal['upload_remote']}</label>
		<div class="hr_line"></div>
		<div class="navigation">{$langGal['upl_type2_d']}</div>
	</div>
	<div class="dle_aTab" style="display:none;">
		<table class="upload">
		 <tr id="row">
		  <td>{$langGal['upl_type4_c']}<br /><input type="file" size="81" name="zip_archive"></td>
		</tr>
		</table>
		<div class="hr_line"></div>
		<div class="navigation">{$langGal['upl_type4_d']}</div>
	</div>
	<div class="dle_aTab" style="display:none;">
		<table class="upload">
		 <tr id="row">
		  <td>{$langGal['upl_type5_c']}<br /><input type="text" size="81" name="folder" value="{$temp_upload_dir}"></td>
		</tr>
		</table>
		<div class="hr_line"></div>
		<div class="navigation">{$langGal['upl_type5_d']}</div>
	</div>
	<div id="rules-layer" style="display:none;"></div>
</div>
<div class="hr_line"></div>
<table width="100%">
  <tr>
	<td width="250" style="padding:4px;">{$langGal['foto_group_title']}:</td>
	<td style="padding:4px;"><input class="edit bk" type="text" name="foto_title" id="foto_title" size="30" value="{$foto_title}"></td>
  </tr>
  <tr>
	<td style="padding:4px;">{$langGal['foto_list_cat']}:</td>
	<td style="padding:4px;"><select name="cat" id="cat" onChange="show_rules(this.value); return false;">{$categories}</select></td>
  </tr>
  <tr>
	<td style="padding:4px;">{$langGal['foto_list_vt']}:</td>
	<td style="padding:4px;"><input class="edit bk" type="text" name="tags" id="tags" size="30" autocomplete="off"></td>
  </tr>
</table>
<div class="hr_line"></div>
<div style="padding-left:10px;padding-top:5px;padding-bottom:5px;">
	<input type="submit" class="buttons" value="{$langGal['upload_next']}" style="width:100px;">{$upload_continue}
</div>
HTML;

	galFooter();
	galHeader($langGal['upl_type6']);

	$allow_auto_resize = makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "allow_foto_resize", $galConfig['allow_foto_resize']);
	$res_type1 = makeDropDownGallery(array("4"=>$langGal['c_res_type5'],"3"=>$langGal['c_res_type4'],"1"=>$langGal['c_res_type3'],"2"=>$langGal['c_res_type2'],"0"=>$langGal['c_res_type1'],"5"=>$langGal['c_res_type6']), "full_res_type", $galConfig['full_res_type']);
	$res_type2 = makeDropDownGallery(array("4"=>$langGal['c_res_type5'],"3"=>$langGal['c_res_type4'],"1"=>$langGal['c_res_type3'],"2"=>$langGal['c_res_type2'],"0"=>$langGal['c_res_type1'],"5"=>$langGal['c_res_type6']), "comm_res_type", $galConfig['comm_res_type']);
	$res_type3 = makeDropDownGallery(array("4"=>$langGal['c_res_type5'],"3"=>$langGal['c_res_type4'],"1"=>$langGal['c_res_type3'],"2"=>$langGal['c_res_type2'],"0"=>$langGal['c_res_type1'],"5"=>$langGal['c_res_type6']), "thumb_res_type", $galConfig['thumb_res_type']);
	$rewrite = makeDropDownGallery(array("2"=>$langGal['upl_s1_c6_1'],"1"=>$langGal['upl_s1_c6_2'],"0"=>$langGal['upl_s1_c6_3']), "rewrite_mode", $galConfig['rewrite_mode']);
	$double = makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "allow_check_double", $galConfig['allow_check_double']);
	$delete = makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "delete", 1);

	if (!$galConfig['icon_type']) $galConfig['max_icon_size'] = 0;
	$galConfig['min_watermark'] = intval($galConfig['min_watermark']);

	echo <<<HTML
<table width="100%" border="0">
  <tr>
	<td width="390" style="padding:4px;"><input class="edit" type="text" name="global_width_max" size="8" value="{$galConfig['global_max_foto_width']}"> - {$langGal['c_global_width_max']}</td>
	<td style="padding:4px;">{$allow_auto_resize} - {$langGal['c_resize']}</td>
  </tr>
  <tr>
	<td style="padding:4px;"><input class="edit" type="text" name="global_height_max" size="8" value="{$galConfig['global_max_foto_height']}"> - {$langGal['c_global_height_max']}</td>
	<td style="padding:4px;">{$res_type1} - {$langGal['c_res_title3']}</td>
  </tr>
  <tr>
	<td style="padding:4px;"><input class="edit" type="text" name="comms_foto_size" size="8" value="{$galConfig['comms_foto_size']}"> - {$langGal['upl_s1_c3']}</td>
	<td style="padding:4px;">{$res_type2} - {$langGal['c_res_title2']}</td>
  </tr>
  <tr>
	<td style="padding:4px;"><input class="edit" type="text" name="max_thumb_size" size="8" value="{$galConfig['max_thumb_size']}"> - {$langGal['upl_s1_c5']}</td>
	<td style="padding:4px;">{$res_type3} - {$langGal['c_res_title1']}</td>
  </tr>
  <tr>
	<td style="padding:4px;"><input class="edit" type="text" name="max_icon_size" size="8" value="{$galConfig['max_icon_size']}"> - {$langGal['upl_s1_c8']}</td>
	<td style="padding:4px;">{$double} - {$langGal['c_check_double']}</td>
  </tr>
  <tr>
	<td style="padding:4px;"><input class="edit" type="text" name="min_watermark" size="8" value="{$galConfig['min_watermark']}"> - {$langGal['upl_s1_c2']}</td>
	<td style="padding:4px;">{$rewrite} - {$langGal['upl_s1_c6']}</td>
  </tr>
  <tr>
	<td style="padding:4px;"><input class="edit" type="text" name="resize_quality" size="8" value="{$galConfig['resize_quality']}"> - {$langGal['upl_s1_c4']}</td>
	<td style="padding:4px;">{$delete} - {$langGal['upl_s1_c7']}</td>
  </tr>
  <tr>
	<td style="padding:4px;" colspan="2"><div class="hr_line"></div></td>
  </tr>
  <tr>
	<td style="padding:4px;" colspan="2"><div class="navigation">{$langGal['upl_type6_d']}</div></td>
  </tr>
</table>
HTML;

	galFooter();

	echo <<<HTML
</form>
HTML;

	if ($member_id['user_group'] == 1)
		echo initTabs1(array($langGal['upl_type3'], $langGal['upl_type1'], $langGal['upl_type2'], $langGal['upl_type4'], $langGal['upl_type5']));
	else
		echo initTabs1(array($langGal['upl_type3'], $langGal['upl_type1'], $langGal['upl_type2'], $langGal['upl_type4']));

	$twsg->galsupport50();
	echofooter();

break;
case 1 :

	$upload = new gallery_upload_action($_REQUEST['cat']);

	if (!$upload->cat)
		msg("error", $lang['cat_error'], $upload->lang['add_foto_error_4'], "javascript:history.go(-1)"); 

	$parse = new ParseFilter();

	$foto_title = $db->safesql($parse->process(trim($_POST['foto_title'])));

	global $UPL;

	$UPL = new gallery_upload_images($galConfig, $upload->cat, (($upload->upload_category[$upload->cat]['allowed_extensions']) ? $upload->upload_category[$upload->cat]['allowed_extensions'] : $galConfig['allowed_extensions']), true);

	$UPL->size_factor = 10;
	$UPL->remote_upload = intval($_REQUEST['remote_upload']);
	$UPL->insert_data = array(
	'foto_title' => $foto_title,
	'all_time_images' => (intval($upload->upload_category[$upload->cat]['all_time_images']) + 1),
	'p_id' => $upload->upload_category[$upload->cat]['p_id'],
	'approve' => 3,
	'tags' => $_POST['tags']
	);

	if ($member_id['user_group'] == 1)
		$UPL->doupload(array(1,2,3,4,5));
	else
		$UPL->doupload(array(1,2,3,4));

	if ($UPL->global_error && $UPL->upload_result[0][1] != 16)
		msg("error", $lang['cat_error'], $upload->lang['add_foto_error_'.$UPL->upload_result[0][1]], "javascript:history.go(-1)"); 

/* $UPL->upload_result[$i]: $foto[0] - image_name, $foto[1] - error, $foto[2] - upltype, $foto[3] - insert_id, $foto[4] - pic_filname, $foto[5] - media_type, $foto[6] - current_type, $foto[7] - update_id */

	$error_text = array();
	$r_key = count($UPL->upload_result);

	for ($i = 0; $i < $r_key; $i++){

		if ($UPL->upload_result[$i][1] && $UPL->upload_result[$i][1] != 16){

			if ($UPL->upload_result[$i][0] !== false)
				$image = "<font color=red>".(($UPL->upload_result[$i][0] == "") ? $upload->lang['foto_notitle'] : $UPL->upload_result[$i][0])."</font>  &raquo; ";
			else
				$image = "";

			$error_text[] = "<li><b>".$image.stripslashes($upload->lang['add_foto_error_'.$UPL->upload_result[$i][1]] . $UPL->upload_result[$i][2])."</b></li>";

		}

	}

	$template = 'file_edit';
	include TWSFACP_DIR . '/templates.php';

	$edit = new gallery_file_edit();

	$edit->edit_prepare(1);

	if (!$edit->affected_files && !count($error_text)){

		if (!$UPL->upload_stats['update'])
			msg("error", $lang['cat_error'], $upload->lang['add_foto_error_16'], "{$PHP_SELF}?mod=twsgallery&act=12"); 
		else
			msg ("error", $langGal['all_info'], str_ireplace('{num}', $UPL->upload_stats['update'], $langGal['add_foto_ok3']), "{$PHP_SELF}?mod=twsgallery&act=12");

	}

	echoheader("", "");
	galnavigation();

	if (count($error_text)){

		galHeader($langGal['menu_add']." - ".$langGal['menu_add_s2']);

		$text = $upload->lang['add_foto_ok2']."<br /><br />".implode("<br />", $error_text);

		echo <<<HTML
<table width="100%">
<tr>
	<td style="padding:10px;">{$text}</td>
</tr>
<tr>
	<td colspan="2"><div class="hr_line"></div></td>
</tr>
<tr>
	<td style="padding:10px;"><a href="javascript:history.go(-1)"><input onclick="javascript:history.go(-1)" type="button" class="buttons" value="{$lang['func_msg']}" style="width:130px;"></a></td>
</tr>
</table>
HTML;

		galFooter();

	}

	if ($edit->affected_files){

		$tpl->clear();

		galHeader($langGal['menu_add']." - ".$langGal['menu_add_s2']);

		echo <<<HTML
<form method="post" action="" name="entryform" id="entryform" enctype="multipart/form-data">
<input type=hidden name="mod" value="twsgallery"><input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}"><input type=hidden name="act" value="12"><input type="hidden" name="ap" value="2">
{$input}{$bb_code}
<script language="javascript" type="text/javascript">
<!--
var dle_root       = '{$config['http_home_url']}';
var dle_skin       = '{$config['skin']}';
var dle_act_lang   = ["{$lang['p_yes']}", "{$lang['p_no']}", "{$lang['p_enter']}", "{$lang['p_cancel']}"];
var dle_prompt     = '{$lang['p_prompt']}';
//-->
HTML;

		echo "</script>";

		echo <<<HTML
<table width="100%" border="0">
HTML;

		$tpl->result['content'] = str_ireplace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['content']);

		echo $tpl->result['content'];

		echo <<<HTML
</table>
<div style="padding-left:10px;padding-top:5px;padding-bottom:5px;">
	<input type="submit" class="buttons" value="{$langGal['ft_edit_save']}" style="width:100px;">&nbsp;
	<a href="javascript:history.go(-1)"><input onclick="javascript:history.go(-1)" type="button" class="buttons" value="{$lang['func_msg']}" style="width:130px;"></a>
</div>
</form>
HTML;

		galFooter();

		if ($edit->affected_files > 1){

			galHeader($langGal['mass_panel_edit']);

			$categories = CategoryGalSelection();

			echo <<<HTML
<div style="padding-left:5px;padding-top:5px;padding-bottom:5px;">
	<input type="checkbox" onclick="$('input[id^=\'allow_comms\']').attr('checked', this.checked)" id="catconfig_comments" checked /> <label for="catconfig_comments">{$langGal['allow_comm']}</label>
	<input type="checkbox" onclick="$('input[id^=\'allow_rate\']').attr('checked', this.checked)" id="catconfig_rating" checked /> <label for="catconfig_rating">{$langGal['allow_rating']}</label>
	<br /><input type="checkbox" onclick="$('input[id^=\'approve\']').attr('checked', this.checked)" id="catconfig_upload" checked /> <label for="catconfig_upload">{$langGal['allow_approve']}</label>
	<br /><br />
	<select onChange="$('select[name^=\'category\'] option:selected').removeAttr('selected');$('select[name^=\'category\'] option[value=\''+this.value+'\']').attr('selected', 'yes'); return false">{$categories}</select>
</div>
HTML;

			galFooter();

		}

	}

	$twsg->galsupport50();
	echofooter();

break;
case 2 :

	$edit = new gallery_file_edit();
	$edit->edit(1);

	$text = "";

	if ($edit->stat['ok'])
		$text = str_ireplace('{num}', $edit->stat['ok'], $langGal['add_foto_ok']);

	if (count($edit->error_result))
		msg ("error", $langGal['all_err_1'], ($text ? $text."<br /><br />" : "").implode("<br /><br />", $edit->error_result), "javascript:history.go(-1)");
	elseif (!$edit->affected_files)
		msg ("error", $langGal['all_info'], $langGal['mass_denied'], "javascript:history.go(-1)");
	else
		msg ("error", $langGal['all_info'], $text, "{$PHP_SELF}?mod=twsgallery&act=12");

break;
}

?>