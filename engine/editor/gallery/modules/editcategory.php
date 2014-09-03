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
 Файл: editcategory.php
-----------------------------------------------------
 Назначение: Редактирование категория администраторами и модераторами
=====================================================
*/
if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

$metatags['title'] = $langGal['menu_title9'];

if (!in_array($act, array(19,24)) && ($dle_login_hash == "" || $_REQUEST['dle_allow_hash'] != $dle_login_hash))
	$stop = $langGal['unknown'];
elseif (in_array($act, array(24,25,27)) && !check_gallery_access ("addcat", ""))
	$stop = $langGal['access_error'];
elseif ($galConfig['enable_banned'])
	$stop = check_banned();
else
	$stop = false;

if ($stop != false){

	msgbox ($langGal['all_err_1'], $stop."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
	return;

}

$catedit = new gallery_category_edit();

$allow_redirect = true;

switch ($act){
case 19 : 
///********************************************************************************************************
//                                   Создание\редактирование категории - подготовка
//*********************************************************************************************************

	$tpl->load_template('gallery/editcategory.tpl');

	$catedit->edit_prepare();

	if ($catedit->affected_categories){

		$tpl->result['content'] = "<form method=post name=\"entryform\" id=\"entryform\" onsubmit=\"if (!ckeck_title('cat_title', '{$langGal['edit_cat_er1']}')){ return false; }\" action=\"\" enctype=\"multipart/form-data\">".$bb_code.$tpl->result['content']."{$input}<input type=\"hidden\" name=\"do\" value=\"gallery\"><input type=\"hidden\" name=\"act\" value=\"20\"><input type=\"hidden\" name=\"dle_allow_hash\" value=\"{$dle_login_hash}\"></form>";

	}

	$allow_redirect = false;

break;

case 20 :
///********************************************************************************************************
//                                   Создание\редактирование категории - сохранение
//*********************************************************************************************************

	$catedit->edit();

break;

case 21 :
///********************************************************************************************************
//                                   Изменение статуса категории
//*********************************************************************************************************

	$catedit->status(intval($_REQUEST['subact']));

break;

case 24 :
///********************************************************************************************************
//                                   Создание\редактирование категории по профилю
//*********************************************************************************************************

	$tpl->load_template('gallery/addcategory.tpl');

	$catedit->edit_by_profile_prepare();

	if ($catedit->affected_categories){

		$tpl->result['content'] = "<form method=post name=\"entryform\" id=\"entryform\" onsubmit=\"if (!ckeck_title('cat_title', '{$langGal['edit_cat_er1']}')){ return false; }\" action=\"\" enctype=\"multipart/form-data\">".$bb_code.$tpl->result['content']."{$input}<input type=\"hidden\" name=\"do\" value=\"gallery\"><input type=\"hidden\" name=\"act\" value=\"25\"><input type=\"hidden\" name=\"dle_allow_hash\" value=\"{$dle_login_hash}\"></form>";

	}

	$allow_redirect = false;

break;

case 25 :
///********************************************************************************************************
//                                   Создание\редактирование категории по профилю - сохранение
//*********************************************************************************************************

	$catedit->edit_by_profile();

break;

case 27 :
///********************************************************************************************************
//                                   Удаление категории
//*********************************************************************************************************

	$catedit->clear(true);

break;

}

if ($catedit->stat['cat_title'])
	$metatags['title'] = $langGal['menu_title10'] . " &raquo; " . $catedit->stat['cat_title'];

$tpl->result['content'] = str_replace('{title}', $metatags['title'], $tpl->result['content']);

if (count($catedit->error_result))
	msgbox ($langGal['all_err_1'], implode("<br /><br />", $catedit->error_result)."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");
elseif ($catedit->access_error)
	msgbox ($langGal['all_err_1'], $langGal['access_error']."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");
elseif (!$catedit->affected_categories)
	msgbox ($langGal['all_info'], $langGal['mass_denied']."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");
elseif ($allow_redirect)
	$catedit->redirect($galConfig['mainhref'].($catedit->stat['category_id'] ? (($config['allow_alt_url'] == "yes" && $catedit->stat['cat_alt_name']) ? $catedit->stat['cat_alt_name']."/" : "&act=1&cid=".$catedit->stat['category_id']) : ""));

?>