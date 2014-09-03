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
 Файл: editfile.php
-----------------------------------------------------
 Назначение: Редактирование фотографий
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

$metatags['title'] = $langGal['menu_title6'];

if ($act != 17 && ($dle_login_hash == "" || $_REQUEST['dle_allow_hash'] != $dle_login_hash))
	$stop = $langGal['unknown'];
elseif ($galConfig['enable_banned'])
	$stop = check_banned();
else
	$stop = false;

if ($stop != false){

	msgbox ($langGal['all_err_1'], $stop."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
	return;

}

$edit = new gallery_file_edit();

$allow_redirect = true;

switch ($act){
case 17 :
///********************************************************************************************************
//                                   Редактирование файлов - подготовка
//*********************************************************************************************************

	$tpl->load_template('gallery/editpicture.tpl');

	$edit->edit_prepare();

	if (!$edit->access_error && $edit->affected_files){

		if (!$edit->stat['is_admin'])
			$tpl->set_block("'\\[admin\\](.*?)\\[/admin\\]'si","");
		else {
			$tpl->set('[admin]', '');
			$tpl->set('[/admin]', '');
		}

		$tpl->set('{fotolist}', $tpl->result['content']);
		$tpl->set('{actiontitle}', $langGal['menu_title6']);
		$tpl->result['content'] = "";

		$tpl->set('[button]', '');
		$tpl->set('[/button]', '');
		$tpl->set_block("'\\[foto\\](.*?)\\[/foto\\]'si","");

		$tpl->compile('content');
		$tpl->clear();

		if ($edit->stat['allow_tags']) $js_array[] = "engine/skins/autocomplete.js";

		$allow_tags = (!$edit->stat['allow_tags']) ? "" : ("<script language=\"javascript\" type=\"text/javascript\">
$(function(){
	gallery_autocomplete($( '.gallery_tags' ), 'engine/gallery/ajax/file.php?act=1', ".intval($galConfig['tags_num']).", 3);
});
//-->
</script>");

		$check_title = ($galConfig['file_title_control'] && $edit->full_access != 2) ? " onsubmit=\"if (!ckeck_title('title', gallery_lang_user[1])){ return false; }\"" : "";
		$tpl->result['content'] = "<form method=post name=\"entryform\" id=\"entryform\" action=\"\"{$check_title} enctype=\"multipart/form-data\">".$allow_tags.$bb_code.$tpl->result['content']."{$input}<input type=\"hidden\" name=\"do\" value=\"gallery\"><input type=\"hidden\" name=\"act\" value=\"18\"><input type=\"hidden\" name=\"dle_allow_hash\" value=\"{$dle_login_hash}\"></form>";

	}

	$allow_redirect = false;

break;
case 18 :
///********************************************************************************************************
//                                   Редактирование файлов - сохранение
//*********************************************************************************************************

	$edit->edit();

break;
case 13 :
///********************************************************************************************************
//                                   Удаление файлов
//*********************************************************************************************************

	$edit->remove();

break;
case 14 :
///********************************************************************************************************
//                                   Перемещение файлов
//*********************************************************************************************************

	$edit->move();

break;
default:
///********************************************************************************************************
//                                   Статус файлов
//*********************************************************************************************************

	if ($_REQUEST['doaction'] == "dothis"){

		$edit->status($act);

	} else {

		$allow_redirect = false;

		$files_list = $edit->status_prepare();

		if (!$files_list) break;

		$metatags['title'] = $langGal['menu_title2'];

		switch ($act){
			case 5 : $question = $langGal['mass_edit_app_fr1']; break;
			case 6 : $question = $langGal['mass_edit_app_fr2']; break;
			case 7 :
					$act = 14;
					$metatags['title'] = $langGal['menu_title4'];
					$question = $langGal['mass_cat_2']." <select name=\"new_category\">".CategoryGalSelection()."</select>";
				break;
			case 8 :
					$act = 13;
					$metatags['title'] = $langGal['menu_title3'];
					$question = $langGal['mass_confirm'];
				break;
			case 9 : $question = $langGal['mass_edit_comm_fr1']; break;
			case 10 : $question = $langGal['mass_edit_comm_fr2']; break;
			case 11 : $question = $langGal['mass_edit_rate_fr1']; break;
			case 12 : $question = $langGal['mass_edit_rate_fr2']; break;
			default : die("error");
		}

		$re = isset($_REQUEST['re']) ? intval($_REQUEST['re']) : 0;

		$form = <<<HTML
<form method="post" action="" id="mass_edit_files">
<input type="hidden" name="send_notice" id="send_notice" value="0">
<input type="hidden" name="doaction" value="dothis">
<input type="hidden" name="do" value="gallery">
<input type="hidden" name="act" value="{$act}">
<input type="hidden" name="re" value="{$re}">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
{$files_list}<br>
{$question}<br><br><br />
<input type="submit" class="bbcodes_poll" value="{$langGal['mass_yes']}"> &nbsp; <input type=button class=bbcodes value="  {$langGal['mass_confirm_3']}  " onclick="javascript:var f=document.getElementById('mass_edit_files');f.send_notice.value=1;f.submit();"> &nbsp; <input type=button class=bbcodes value="{$langGal['mass_no']}" onclick="javascript:history.go(-1)">
<br /><br /><a href="javascript:ShowOrHide('send_notice_layer')">{$langGal['send_notice_show']}</a><br />
<div id="send_notice_layer" style="display:none;"><br />
{$langGal['send_notice_title']}<br />
<textarea name="send_notice_text" style="width:375px;height:50px;"></textarea>
</div>
</form>
HTML;

		msgbox ($metatags['title'], $form);

	}

break;

}

if (count($edit->error_result))
	msgbox ($langGal['all_err_1'], implode("<br /><br />", $edit->error_result)."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");
elseif ($edit->access_error)
	msgbox ($langGal['all_err_1'], $langGal['access_error']."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");
elseif (!$edit->affected_files)
	msgbox ($langGal['all_info'], $langGal['mass_denied']."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");
elseif ($allow_redirect)
	$edit->redirect(((!isset($_REQUEST['re']) || $_REQUEST['re'] != 1) && isset($_SESSION['gallery_referrer'])) ? $_SESSION['gallery_referrer'] : $galConfig['mainhref'].(($config['allow_alt_url'] == "yes") ? $edit->stat['cat_alt_name']."/" : "&act=1&cid=".$edit->stat['category_id']));

?>