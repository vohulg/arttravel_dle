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
 Файл: massactions.php
-----------------------------------------------------
 Назначение: Действия над файлами
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1 && (!$galConfig['admin_user_access'] || !$user_group[$member_id['user_group']]['admin_editnews'])){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

require_once TWSGAL_DIR . '/classes/editfile.php';
require_once TWSGAL_DIR.'/classes/editcategory.php';

$edit = new gallery_file_edit();

$allow_redirect = true;

switch ($act){
case 13 :
///********************************************************************************************************
//                                   Сортировка
//*********************************************************************************************************

	if (isset($_POST['posi']) && is_array($_POST['posi'])){

		foreach ($_POST['posi'] as $k => $v){

			$v = intval($v); $k = intval($k);
			if ($v > 0 && $k > 0)
				$db->query("UPDATE " . PREFIX . "_gallery_picturies SET posi='{$v}' WHERE picture_id='{$k}'");

		}

	}

	clear_gallery_vars();
	clear_gallery_cache();

	$edit->redirect(isset($_SESSION['gallery_admin_referrer']) ? $_SESSION['gallery_admin_referrer'] : "{$PHP_SELF}?mod=twsgallery&act=10");

break;
case 24 :
///********************************************************************************************************
//                                   Редактирование файлов - подготовка
//*********************************************************************************************************

	$template = 'file_edit';
	include TWSFACP_DIR . '/templates.php';

	$edit->edit_prepare();

	$tpl->clear();

	if (!$edit->affected_files)
		msg("error", $lang['cat_error'], $langGal['mass_denied'], "javascript:history.go(-1)");

	$js_array[] = "engine/skins/autocomplete.js";
	$js_array[] = "engine/gallery/js/not_logged.js";

	echoheader("", "");
	galnavigation();
	galHeader($langGal['menu_edit']);

	echopopupedituser();

	echo <<<HTML
<form method="post" action="" name="entryform" id="entryform" enctype="multipart/form-data">
<input type=hidden name="mod" value="twsgallery"><input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}"><input type=hidden name="act" value="25">
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
<script language="javascript" type="text/javascript">
$(function(){
	gallery_autocomplete($( '.gallery_tags' ), 'engine/gallery/ajax/file.php?act=1');
	gallery_autocomplete($( '.finduser' ), 'engine/gallery/ajax/file.php?act=4', 1);
});
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
	<a href="javascript:history.go(-1)"><input onclick="javascript:history.go(-1)" type="button" class="buttons" value="{$lang['func_msg']}" style="width:130px;"></a> <input type="checkbox" name="send_notice" id="send_notice" value="1" /> <label for="send_notice">{$langGal['edit_foto_select']}</label>
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
	<br /><input type="checkbox" onclick="$('input[id^=\'refresh_thumbs\']').attr('checked', this.checked)" id="catconfig_refresh" /> <label for="catconfig_refresh">{$langGal['allow_refresh_thumbs']}</label>
	<br /><br />
	<select onChange="$('select[name^=\'category\'] option:selected').removeAttr('selected');$('select[name^=\'category\'] option[value=\''+this.value+'\']').attr('selected', 'yes'); return false">{$categories}</select>
</div>
HTML;

		galFooter();

	}

	$twsg->galsupport50();
	echofooter();

	$allow_redirect = false;

break;
case 25 :
///********************************************************************************************************
//                                   Редактирование файлов - сохранение
//*********************************************************************************************************

	$edit->edit();

break;
case 23 :
///********************************************************************************************************
//                                   Удаление файлов
//*********************************************************************************************************

	$edit->remove(1);

	if (!isset($_REQUEST['a']) || !intval($_REQUEST['a'])) $edit->redirect(isset($_SESSION['gallery_admin_referrer']) ? $_SESSION['gallery_admin_referrer'] : "{$PHP_SELF}?mod=twsgallery&act=10");

	galExit(false, "<img border=\"0\" src=\"engine/gallery/acp/skins/images/file_stat_2.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['add_foto_ok2']}\" title=\"{$langGal['add_foto_ok2']}\">");

break;
case 15 :
///********************************************************************************************************
//                                   Перемещение файлов
//*********************************************************************************************************

	$edit->move();

break;
case 49 :
///********************************************************************************************************
//                                   Сообщение пользователю
//*********************************************************************************************************

	$_REQUEST['send_notice'] = 1;

	$edit->message();

break;
default:
///********************************************************************************************************
//                                   Статус файлов
//*********************************************************************************************************

	if ($_REQUEST['doaction'] == "dothis"){

		switch ($act){
			case 16 : $_act = 5; break;
			case 17 : $_act = 6; break;
			case 18 : $_act = 9; break;
			case 19 : $_act = 10; break;
			case 20 : $_act = 11; break;
			case 21 : $_act = 12; break;
			case 44 : $_act = 3; break;
			case 45 : $_act = 2; break;
			case 46 : $_act = 4; break;
			case 47 : $_act = 20; break;
			case 52 : $_act = 22; break;
			default : die("error");
		}

		$edit->status($_act);

	} else {

		$files_list = $edit->status_prepare(false);

		if (!$files_list) break;

		$send_notice_show = true;

		switch ($act){
			case 14 : $title = $langGal['mass_cat_1']; $act = 15; break;
			case 16 : $title = $langGal['mass_edit_app_tl']; $langGal['mass_confirm'] = $langGal['mass_edit_app_fr1']; break;
			case 17 : $title = $langGal['mass_edit_app_tl']; $langGal['mass_confirm'] = $langGal['mass_edit_app_fr2']; break;
			case 18 : $title = $langGal['mass_edit_com_tl']; $langGal['mass_confirm'] = $langGal['mass_edit_comm_fr1']; $langGal['mass_confirm_1'] = $langGal['mass_confirm_2']; break;
			case 19 : $title = $langGal['mass_edit_com_tl']; $langGal['mass_confirm'] = $langGal['mass_edit_comm_fr2']; $langGal['mass_confirm_1'] = $langGal['mass_confirm_2']; break;
			case 20 : $title = $langGal['mass_edit_rate_tl']; $langGal['mass_confirm'] = $langGal['mass_edit_rate_fr1']; $langGal['mass_confirm_1'] = $langGal['mass_confirm_2']; break;
			case 21 : $title = $langGal['mass_edit_rate_tl']; $langGal['mass_confirm'] = $langGal['mass_edit_rate_fr2']; $langGal['mass_confirm_1'] = $langGal['mass_confirm_2']; break;
			case 22 : $title = $langGal['mass_edit_del_tl']; $act = 23; break;
			case 44 : $title = $langGal['mass_edit_views_tl']; $langGal['mass_confirm'] = $langGal['mass_edit_views_fr']; $langGal['mass_confirm_1'] = $langGal['mass_confirm_2']; $send_notice_show = false; break;
			case 45 : $title = $langGal['mass_edit_logs_tl']; $langGal['mass_confirm'] = $langGal['mass_edit_logs_fr']; $langGal['mass_confirm_1'] = $langGal['mass_confirm_2']; $send_notice_show = false; break;
			case 46 : $title = $langGal['mass_edit_reason_tl']; $send_notice_show = false; break;
			case 47 : $title = $langGal['mass_edit_autor_tl']; $langGal['foto_list_edr'] = $langGal['foto_list_eda']; $send_notice_show = false; break;
			case 48 : $title = $langGal['mass_edit_message_tl']; $send_notice_show = false; $act = 49; break;
			case 52 : $title = $langGal['mass_edit_tags_tl']; $langGal['foto_list_edr'] = $langGal['foto_list_edt']; $send_notice_show = false; break;
			default : die("error");
		}

		if ($act == 52 || $act == 47){

			$js_array[] = "engine/skins/autocomplete.js";
			$js_array[] = "engine/gallery/js/not_logged.js";

		}

		echoheader("options", $lang['mass_head']);
		galnavigation();

		if ($act == 52 || $act == 47){

			echo <<<HTML
<script language="javascript" type="text/javascript">
<!--
var dle_root       = '{$config['http_home_url']}';
var dle_skin       = '{$config['skin']}';
var dle_act_lang   = ["{$lang['p_yes']}", "{$lang['p_no']}", "{$lang['p_enter']}", "{$lang['p_cancel']}"];
var dle_prompt     = '{$lang['p_prompt']}';
//-->\n
HTML;

			echo "</script>";

			echo <<<HTML
<script language="javascript" type="text/javascript">
$(function(){
HTML;

			if ($act == 52)
				echo <<<HTML
	gallery_autocomplete($( '#edit_value' ), 'engine/gallery/ajax/file.php?act=1');
HTML;
			elseif ($act == 47)
				echo <<<HTML
	gallery_autocomplete($( '#edit_value' ), 'engine/gallery/ajax/file.php?act=4', 1);
HTML;

			echo <<<HTML
});
//-->\n
HTML;


			echo "</script>";

		}

		$content = <<<HTML
<form action="{$PHP_SELF}" id="mass_edit_files" method="post">
<input type="hidden" name="send_notice" id="send_notice" value="0">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
<input type="hidden" name="act" value="{$act}">
<input type="hidden" name="mod" value="twsgallery">
<input type="hidden" name="doaction" value="dothis">
{$files_list}<br />
HTML;

		$count = count($edit->ids);
		$send_button = ($send_notice_show) ? " &nbsp; <input type=button class=bbcodes value=\"  {$langGal['mass_confirm_3']}  \" onclick=\"javascript:var f=document.getElementById('mass_edit_files');f.send_notice.value=1;f.submit();\">" : "";

		switch ($act){
		case 15 :

			$categorylist = CategoryGalSelection();

			$content .= <<<HTML
{$langGal['mass_cat_2']} (<b>{$count}</b>) {$langGal['mass_cat_3']}
<select name="new_category" align="absmiddle">{$categorylist}</select>
<br /><br /><input type="submit" value="  {$lang['b_start']}  " class="bbcodes">{$send_button} &nbsp; <input type=button class=bbcodes value="  {$lang['func_msg']}  " onclick="javascript:history.go(-1)"><br /><br />
HTML;

		break;
		case 46 :
		case 47 :
		case 52 :

			$content .= <<<HTML
{$langGal['foto_list_edr']}: <input type="text" name="edit_value" id="edit_value" size="45"  class="edit" value=""> &nbsp; <input type="submit" value="  {$lang['b_start']}  " class="bbcodes"> &nbsp; <input type=button class=bbcodes value="  {$lang['func_msg']}  " onclick="javascript:history.go(-1)"><br /><br />
HTML;

		break;
		case 49 :

			$content .= <<<HTML
{$langGal['send_notice_title1']}<br /><br />
<textarea name="send_notice_text" style="width:345px;height:50px;"></textarea><br /><br />
<input type="submit" value="  {$lang['b_start']}  " class="bbcodes"> &nbsp; <input type=button class=bbcodes value="  {$lang['func_msg']}  " onclick="javascript:history.go(-1)"><br /><br />
HTML;

		break;
		default:

			$content .= <<<HTML
{$langGal['mass_confirm']} (<b>{$count}</b>) {$langGal['mass_confirm_1']}<br><br>
<input class=bbcodes type=submit value="   {$lang['mass_yes']}   ">{$send_button} &nbsp; <input type=button class=bbcodes value="  {$lang['mass_no']}  " onclick="javascript:history.go(-1)"><br /><br />
HTML;

		}

		if ($send_notice_show)
$content .= <<<HTML
<a class="list" href="javascript:ShowOrHide('send_notice_layer')">{$langGal['send_notice_show']}</a><br />
<div id="send_notice_layer" style="display:none;"><br />
{$langGal['send_notice_title']}<br />
<textarea name="send_notice_text" style="width:375px;height:50px;"></textarea>
</div>
<br />\n
HTML;

$content .= <<<HTML
</form>\n
HTML;

		galMessage($title, $content, 100);
		$twsg->galsupport50();
		echofooter();

		$allow_redirect = false;

	}

}

if (count($edit->error_result))
	msg ("error", $langGal['all_err_1'], implode("<br /><br />", $edit->error_result), "javascript:history.go(-1)");
elseif ($edit->access_error)
	msg ("error", $langGal['all_err_1'], $langGal['access_error'], "javascript:history.go(-1)");
elseif (!$edit->affected_files)
	msg ("error", $langGal['all_info'], $langGal['mass_denied'], "javascript:history.go(-1)");
elseif ($allow_redirect)
	$edit->redirect(isset($_SESSION['gallery_admin_referrer']) ? $_SESSION['gallery_admin_referrer'] : "{$PHP_SELF}?mod=twsgallery&act=10");


?>