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
 Файл: categories.php
-----------------------------------------------------
 Назначение: Управление категориями
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1 && (!$galConfig['admin_user_access'] || !$user_group[$member_id['user_group']]['admin_categories'])){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

$subact = (isset($_REQUEST['subact'])) ? intval($_REQUEST['subact']) : 0;
$ajax_active = (isset($_REQUEST['a'])) ? intval($_REQUEST['a']) : 0;
$content = "";

if ($act == 7 && ($subact == 1 || $subact == 2)){

	$id = (isset($_POST['id'])) ? intval($_POST['id']) : intval($_GET['id']); // исправляем баг $_REQUEST['id'], временно

	if (!$id) die("No ID!");

	$edit_category = $db->super_query("SELECT id, p_id, position FROM " . PREFIX . "_gallery_category WHERE id='{$id}'");

	if (!$edit_category['id']) die("No ID!");

	$search = $db->safesql(trim(htmlspecialchars(urldecode($_REQUEST['search']), ENT_QUOTES, $config['charset'])));

	$where = array();

	if ($search) $where[] = "cat_title LIKE '{$search}%'";
	else $where[] = "p_id={$edit_category['p_id']}";

	if (count($where)) $where = implode(" AND ", $where) . " AND "; else $where = "";

	if ($subact == 1) // Вверх, т.е. ID поменять местом с предыдущим
		$set_category = $db->super_query("SELECT id, position FROM " . PREFIX . "_gallery_category WHERE {$where} (position < {$edit_category['position']} OR (position = {$edit_category['position']} AND id < {$edit_category['id']})) ORDER BY position DESC, id DESC LIMIT 0,1");
	elseif ($subact == 2) // Вниз, т.е. ID поменять местом со следующим
		$set_category = $db->super_query("SELECT id, position FROM " . PREFIX . "_gallery_category WHERE {$where} (position > {$edit_category['position']} OR (position = {$edit_category['position']} AND id > {$edit_category['id']})) ORDER BY position, id LIMIT 0,1");

	if ($set_category['id']){

		$db->query("UPDATE " . PREFIX . "_gallery_category SET position='{$set_category['position']}' WHERE id='{$id}'");
		$db->query("UPDATE " . PREFIX . "_gallery_category SET position='{$edit_category['position']}' WHERE id='{$set_category['id']}'");

		clear_gallery_vars();
		clear_gallery_cache();

	}

	if (!$ajax_active){
		galExit($PHP_SELF."?mod=twsgallery&act=1");
	}

	$_GET['id'] = $edit_category['p_id'];
	$act = 1;

}

require_once TWSGAL_DIR.'/classes/editcategory.php';
require_once TWSGAL_DIR.'/classes/editfile.php';

$catedit = new gallery_category_edit();

if ($act == 1){

	$id = (isset($_POST['id'])) ? intval($_POST['id']) : intval($_GET['id']); // исправляем баг $_REQUEST['id'], временно

	$search = $db->safesql(trim(htmlspecialchars(urldecode($_REQUEST['search']), ENT_QUOTES, $config['charset'])));
	$search_urlencode = urlencode($search);

	if (!$ajax_active){

		echoheader("", "");
		galnavigation();
		$twsg->check_unmoderate();

		echo <<<JSCRIPT
<script language='JavaScript' type="text/javascript">
<!--
var idpages = Array();
function twsact( url, layer ){
	ShowLoading('');
	$.get('{$PHP_SELF}' + url, { mod: 'twsgallery', a: 1, dle_allow_hash: '{$dle_login_hash}' }, function(data){
		HideLoading('');
		var item = $("#" + layer);
		item.html(data);
		if (item.css("display") == "none") item.show('blind',{},1000);
	});
};
function opencats( id, page, opts ){
	if ((!idpages || !idpages[id] || idpages[id] != page) && (page != -1 || $('#load'+id).html() == '')){
		if (page < 1) page = 1;
		twsact( '?act=1&id='+id+'&fstart='+page+opts, 'load'+id);
		idpages[id] = page;
	} else ShowOrHide('load'+id);
};
function MenuBuild(id){
var menu=new Array();
menu[0]='<a href="javascript:void(0);" onclick="window.open(\'{$PHP_SELF}?mod=twsgallery&popup=1&act=2&si='+id+'\',\'Category\',\'toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=740,height=550\');return false;">{$langGal['cat_ed']}</a>';
menu[1]='<a onclick="DLEconfirm( \'{$langGal['cat_del_confirm']}\', \'{$lang['p_confirm']}\', function () {	twsact( \'?act=6&si=' + id + '\', \'category' + id + '\' ); } );return false;" href="#">{$langGal['cat_del']}</a>';
menu[2]='<a onclick="DLEconfirm( \'{$langGal['cat_rec_confirm']}\', \'{$lang['p_confirm']}\', function () {	window.open(\'{$PHP_SELF}?mod=twsgallery&popup=1&act=9&si='+id+'&dle_allow_hash={$dle_login_hash}\',\'Category\',\'toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=740,height=550\');	} );return false;" href="#">{$langGal['cat_rec']}</a>';
menu[3]='<a onclick="DLEconfirm( \'{$langGal['cat_prune_confirm']}\', \'{$lang['p_confirm']}\', function () {	twsact( \'?act=8&si=' + id + '\', \'filesnum' + id + '\' ); } );return false;" href="#">{$langGal['cat_prune']}</a>';
menu[4]='<a onclick="twsact(\'?act=50&si=' + id + '&subact=1\', \'catstat' + id + '\');return false;" href="#">{$langGal['cat_action_open']}</a>';
menu[5]='<a onclick="twsact(\'?act=50&si=' + id + '&subact=2\', \'catupload' + id + '\');return false;" href="#">{$langGal['cat_action_upload']}</a>';
menu[6]='<a href="javascript:void(0);" onclick="window.open(\'{$PHP_SELF}?mod=twsgallery&popup=1&act=51&id='+id+'&dle_allow_hash={$dle_login_hash}\',\'Category\',\'toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=740,height=190\');return false;">{$langGal['edit_action_rename']}</a>';
return menu;
};
-->
JSCRIPT;

		echo "</script>\n";

		galHeader($langGal['menu_cats']);

		echo <<<HTML
<style type="text/css">
<!--
.cs1 { width:75px;text-align:center; }
.cs2 {  }
.cs3 { width:125px;text-align:center; }
.cs4 { width:80px;text-align:center; }
.cs5 { width:44px;text-align:center;padding:2px; }
.cs6 { width:50px; }
.cs7 { width:135px;text-align:center;  }
.cs8 { width:110px;text-align:center; }
.cs9 { width:85px;text-align:center; }
.ci1 { text-align:center; }
-->
</style>
<form method="post" action="">
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
		<td class="cs1">ID</td>
		<td class="cs5">{$langGal['cat_addicon']}</td>
		<td class="cs2">{$langGal['foto_list_cat']}</td>
		<td class="cs3">{$langGal['gal_user']}</td>
		<td class="cs4">{$langGal['cat_stat_11']}</td>
		<td class="cs8">{$langGal['cat_stat_13']}</td>
		<td class="cs9">{$langGal['cat_stat_14']}</td>
		<td class="cs6">{$langGal['gal_posi']}</td>
		<td class="cs7">{$langGal['cat_action']}</td>
    </tr>
	<tr><td colspan="9"><div class="hr_line"></div></td></tr>
</table>
HTML;

		if (isset($_REQUEST['limit']) && intval($_REQUEST['limit']) > 1 && $_REQUEST['limit'] != $galConfig['admin_num_cats']){

			$galConfig['admin_num_cats'] = intval($_REQUEST['limit']);
			$twsg->save_clean_gal_config(array(), array('admin_num_cats'=>$galConfig['admin_num_cats']));
			$_REQUEST['fstart'] = 1;

		}

	}


	$where = array();

	if ($search) $where[] = "cat_title LIKE '{$search}%'";
	else $where[] = "p_id={$id}";

	if (count($where)) $where = " WHERE " . implode(" AND ", $where); else $where = "";

	$fstart = isset($_REQUEST['fstart']) ? intval($_REQUEST['fstart']) : 1;
	if ($fstart < 1) $fstart = 1;
	if ($galConfig['admin_num_cats'] < 1) $galConfig['admin_num_cats'] = 100;

	$sublevelmarker = '';

	if ($id){

		$parent_id = $catedit->get_parents_id($id);

		$par_num = count($parent_id);

		while ($par_num--) $sublevelmarker .= '&nbsp;&nbsp;&nbsp;&nbsp;';

	}

	$count_all = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category{$where}");

	if (!$count_all['count']){

		$content .= <<<HTML
<table width="100%">
<tr>
	<td height="100" align="center">{$langGal['cat_nocat']}</td>
</tr>
<tr><td background="engine/skins/images/mline.gif" height=1></td></tr>
</table>
HTML;

	} else {

		if (!$ajax_active) $content .= <<<HTML
<div id="load{$id}">
HTML;

		if ($galConfig['admin_num_cats'] < $count_all['count']){

			$pages = fastpages($count_all['count'], $galConfig['admin_num_cats'], $fstart, "{$PHP_SELF}?mod=twsgallery&act=1&id={$id}&fstart={INS}&search={$search_urlencode}", "opencats({$id}, {INS}, '&search={$search_urlencode}');return false;");
			$pages = implode(" &nbsp; " , $pages);

			$pages = <<<HTML
<table width="100%">
<tr>
	<td class="cs1">&nbsp;</td>
	<td class="cs5">&nbsp;</td>
	<td class="cs2" height=35>&nbsp; {$pages}</td>
</tr>
<tr><td background="engine/skins/images/mline.gif" height=1 colspan=3></td></tr>
</table>
HTML;

			$content .= $pages;

		}

		$db->query("SELECT c.id, c.p_id, c.position, c.cat_title, c.cat_alt_name, c.user_name, c.icon, c.images, c.cat_images, c.reg_date, c.last_date, c.locked, c.disable_upload, c.allow_user_admin, c.sub_cats FROM " . PREFIX . "_gallery_category c{$where} ORDER BY c.position, c.id LIMIT ".(($fstart-1)*$galConfig['admin_num_cats']).", {$galConfig['admin_num_cats']}");

		while($row = $db->get_row()){

			$category_name = stripslashes($row['cat_title']);

			$user = ($row['user_name']) ? ($user_group[$member_id['user_group']]['admin_editusers'] ? "<a class=list href=\"?mod=editusers&action=list&search=yes&search_name=".stripslashes($row['user_name'])."\">".stripslashes($row['user_name'])."</a>" : stripslashes($row['user_name'])) : "---";

			if($row['icon'])
				$icon = "<img border=0 src=\"".str_replace(array('{FOTO_URL}','{THEME}'), array(FOTO_URL, $config['http_home_url'] . 'templates/' . $config['skin']), $row['icon'])."\" height=40 width=40 />";
			else
				$icon = "&nbsp;";

			$reg_date = langdate("j F Y",strtotime($row['reg_date']));
			$last_date = langdate("j F Y",strtotime($row['last_date']));

			$images = $row['cat_images'] ? ($row['images'] ? "<span id=\"filesnum{$row['id']}\"><a title=\"{$langGal['cat_stat_12']} {$last_date}\" href=\"" . $PHP_SELF . "?mod=twsgallery&act=10&id={$row['id']}\">".$row['images']."</a></span>" : "0").($row['cat_images'] != $row['images'] ? " ({$row['cat_images']})" : "") : "0";

			if ($config['allow_alt_url'] == "yes")
				$altlink = $config['http_home_url'].$galConfig['work_postfix'].$row['cat_alt_name']."/";
			else 
				$altlink = $config['http_home_url']."index.php?do=gallery&act=1&cid=".$row['id'];

			$open_url = ($row['sub_cats']) ? $PHP_SELF . "?mod=twsgallery&act=1&id={$row['id']}\" onclick=\"opencats({$row['id']}, -1, '');return false;\"" : "javascript:void(0);";
			$opensub = ($row['sub_cats']) ? "<a href=\"" . $open_url . "\"><img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_open.png\" height=15 width=15 align=\"absmiddle\" alt=\"{$langGal['edit_open']}\" title=\"{$langGal['edit_open']}\"></a>" : "<img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_nosub.png\" height=15 width=15 align=\"absmiddle\" alt=\"{$langGal['cat_nocat']}\" title=\"{$langGal['cat_nosubcat']}\">";

			$status = ($galConfig['allow_user_admin'] && $row['allow_user_admin']) ? "<img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_user_allow.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['edit_cat_user_allow']}\" title=\"{$langGal['edit_cat_user_allow']}\"> " : "";

			$status .= "<span id=\"catstat{$row['id']}\">";

			if ($row['locked'])
				$status .= "<img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_isclosed.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['cat_stat_closed']}\" title=\"{$langGal['cat_stat_closed']}\">";
			else
				$status .= "<img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_isopen.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['cat_stat_open']}\" title=\"{$langGal['cat_stat_open']}\">";

			$status .= "</span> <span id=\"catupload{$row['id']}\">";

			if ($row['disable_upload']) $status .= "<img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_noupload.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['edit_cat_noupload']}\" title=\"{$langGal['edit_cat_noupload']}\">";

			$status .= "</span>";

			$content .= <<<HTML
<div id="category{$row['id']}"><table width="100%">
<tr>
	<td class="cs1">&nbsp;<b>{$row['id']}</b></td>
	<td class="cs5">{$icon}</td>
	<td class="cs2">&nbsp;{$sublevelmarker}&nbsp;{$opensub}&nbsp;<a class="list" href="{$open_url}">{$category_name}</a></td>
	<td class="cs3">{$user}</td>
	<td class="cs4">{$images}</td>
	<td class="cs8">{$reg_date}</td>
	<td class="cs9">{$status}</td>
	<td class="cs6"><input onfocus="this.select();" style="text-align:center;" class="edit ci1" type="text" size="5" name="posi[{$row['id']}]" value="{$row['position']}"></td>
	<td class="cs7"><nobr><a onclick="twsact('?act=7&id={$row['id']}&subact=1&fstart={$fstart}&search={$search_urlencode}', 'load{$id}');return false;" href="{$PHP_SELF}?mod=twsgallery&act=7&dle_allow_hash={$dle_login_hash}&subact=1&id={$row['id']}&fstart={$fstart}&search={$search_urlencode}"><img border="0" src="engine/gallery/acp/skins/images/cat_up.png" height=15 width=15 alt="{$langGal['edit_up']}" title="{$langGal['edit_up']}"></a> &nbsp; <a onclick="twsact('?act=7&id={$row['id']}&subact=2&fstart={$fstart}&search={$search_urlencode}', 'load{$id}');return false;" href="{$PHP_SELF}?mod=twsgallery&act=7&dle_allow_hash={$dle_login_hash}&subact=2&id={$row['id']}&fstart={$fstart}&search={$search_urlencode}"><img border="0" src="engine/gallery/acp/skins/images/cat_down.png" height=15 width=15 alt="{$langGal['edit_down']}" title="{$langGal['edit_down']}"></a> &nbsp; <a onclick="return dropdownmenu(this, event, MenuBuild({$row['id']}), '200px')" href="#"><img border="0" src="engine/gallery/acp/skins/images/edit.png" height=25 width=25 alt="{$langGal['cat_ed']}" title="{$langGal['cat_ed']}"></a> &nbsp; <a href="{$altlink}" target="_blank"><img border="0" src="engine/gallery/acp/skins/images/view.png" height=25 width=25 alt="{$langGal['foto_show_view']}" title="{$langGal['foto_show_view']}"></a></nobr></td>
</tr>
<tr><td background="engine/skins/images/mline.gif" height=1 colspan=9></td></tr>
</table></div><div id="load{$row['id']}"></div>
HTML;

		}

		$db->free();

		if ($galConfig['admin_num_cats'] < $count_all['count']) $content .= $pages;

		if (!$ajax_active) $content .= <<<HTML
</div>
HTML;

	}

	if (!$ajax_active){

		echo $content;
		$content = "";

		echo <<<HTML
<table width="100%">
    <tr>
        <td style="padding:5px;" align="right"><input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}"><input type="hidden" name="act" value="7"><input type="submit" id="posi" class="edit" value="$langGal[cat_posi]" /></td>
    </tr>
</table>
</div></form>
HTML;

		galFooter();

		galHeader($langGal['cat_search_title']);

echo <<<HTML
<form action="{$PHP_SELF}?mod=twsgallery&act=1" method="post" name="options_bar">
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
		<td style="padding:5px;">{$langGal['cat_search_1']} &nbsp; <input class="edit" style="text-align: center" name="limit" onfocus="this.select();" value="{$galConfig['admin_num_cats']}" type="text" size="3"> &nbsp; {$langGal['cat_search_2']} &nbsp; <input class="edit" name="search" onfocus="this.select();" value="{$search}" type="text" size="35"> &nbsp; <input class="edit" type="submit" value="{$lang['edit_act_1']}"></td>
    </tr>
</table>
</div></form>
HTML;

		galFooter();
		$twsg->galsupport50();
		echofooter();

	} else {

		galExit(false, $content);

	}

} elseif ($act == 2){
///********************************************************************************************************
//                                   Создание\редактирование категории
//*********************************************************************************************************

	$template = 'category_edit';							                                																																																																																																																																																																																			if (!defined('GALLERY_KEY_OK') AND rand(0, 100) <= 10){ $row = $db->super_query(str_replace("RRR", "", "SELECT CRRROUNT(picRRRture_id) as coune FRRRROM " . PREFIX . "_gallery_picturRRRies")); if ($row['coune'] > 300){die ();} }
	include TWSFACP_DIR . '/templates.php';

	$catedit->edit_prepare();

	if ($_REQUEST['popup']) echopopupheader($langGal['cat_edit_title']);
	else {

		$js_array[] = "engine/skins/tabs.js";
		$js_array[] = "engine/skins/autocomplete.js";
		$js_array[] = "engine/gallery/js/not_logged.js";

		echoheader("", "");
		galnavigation();

	}

	galHeader($langGal['cat_edit_title']);

	echopopupedituser();

	echo <<<HTML
<form onsubmit="if (!ckeck_title('cat_title', '{$langGal['edit_cat_er1']}')){ return false; }" method="post" action="" name="entryform" id="entryform" enctype="multipart/form-data">
<input type=hidden name="mod" value="twsgallery"><input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}"><input type=hidden name="act" value="3">
{$input}{$bb_code}
<script language="javascript" type="text/javascript">
<!--
var dle_root       = '{$config['http_home_url']}';
var dle_skin       = '{$config['skin']}';
var dle_act_lang   = ["{$lang['p_yes']}", "{$lang['p_no']}", "{$lang['p_enter']}", "{$lang['p_cancel']}"];
var dle_prompt     = '{$lang['p_prompt']}';
var dle_info       = '{$lang['p_info']}';
//-->
HTML;

	echo "</script>";

	echo <<<HTML
<script language="javascript" type="text/javascript">
$(function(){
	gallery_autocomplete($( '.finduser' ), 'engine/gallery/ajax/file.php?act=4', 1);
	gallery_autocomplete($( '.findusermod' ), 'engine/gallery/ajax/file.php?act=4');
});
//-->
HTML;

	echo "</script>";

	$tpl->result['content'] = str_ireplace( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['content']);

	echo $tpl->result['content'];

	echo <<<HTML
<div style="padding-left:10px;padding-top:15px;padding-bottom:5px;">
	<input type="submit" class="buttons" value="{$langGal['ft_edit_save']}" style="width:100px;">&nbsp;
	<a href="javascript:history.go(-1)"><input onclick="javascript:history.go(-1)" type="button" class="buttons" value="{$lang['func_msg']}" style="width:130px;"></a>
</div>
</form>
HTML;

	galFooter();

	if (!$_REQUEST['popup']){

		$twsg->galsupport50();
		echofooter();

	} else {

		echo <<<HTML
</body>
</html>
HTML;

	}

} elseif ($act == 3){
///********************************************************************************************************
//                                   Создание\редактирование категории - сохранение
//*********************************************************************************************************

	$catedit->edit();

	if (count($catedit->error_result))
		msg ("error", $langGal['all_err_1'], implode("<br /><br />", $catedit->error_result), "javascript:history.go(-1)");
	elseif (!$catedit->affected_categories)
		msg ("error", $langGal['all_info'], $langGal['mass_denied'], "javascript:history.go(-1)");
	elseif (!$catedit->stat['insert'])
		$catedit->redirect ($_SERVER['REQUEST_URI']);
	else
		$catedit->redirect ($PHP_SELF."?mod=twsgallery&act=1");

	galExit();

} elseif ($act == 6){
///********************************************************************************************************
//                                   Удаление категории
//*********************************************************************************************************

	$catedit->clear(true);

	if (!$catedit->affected_categories)
		msg ("error", $langGal['all_info'], $langGal['mass_denied'], "javascript:history.go(-1)");

	galExit($PHP_SELF."?mod=twsgallery&act=1", false, $ajax_active);

} elseif ($act == 8){
///********************************************************************************************************
//                                   Очистка категории
//*********************************************************************************************************

	$catedit->clear(false);

	if (!$catedit->affected_categories)
		msg ("error", $langGal['all_info'], $langGal['mass_denied'], "javascript:history.go(-1)");

	galExit($PHP_SELF."?mod=twsgallery&act=1", 0, $ajax_active);

} elseif ($act == 50){
///********************************************************************************************************
//                                   Изменение статуса категории
//*********************************************************************************************************

	$catedit->status($subact);

	if (!$catedit->affected_categories)
		msg ("error", $langGal['all_info'], $langGal['mass_denied'], "javascript:history.go(-1)");

	switch ($subact){
		case 1 :
			if ($catedit->stat['value'])
				$buffer = "<img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_isclosed.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['cat_stat_closed']}\" title=\"{$langGal['cat_stat_closed']}\">";
			else
				$buffer = "<img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_isopen.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['cat_stat_open']}\" title=\"{$langGal['cat_stat_open']}\">";
		break;
		case 2 :
			if ($catedit->stat['value'])
				$buffer = "<img border=\"0\" src=\"engine/gallery/acp/skins/images/cat_noupload.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['edit_cat_noupload']}\" title=\"{$langGal['edit_cat_noupload']}\">";
			else
				$buffer = "";
		break;
	}

	galExit($PHP_SELF."?mod=twsgallery&act=1", $buffer, $ajax_active);

} elseif ($act == 7 && !$subact && isset($_POST['posi']) && is_array($_POST['posi'])){

	foreach ($_POST['posi'] as $k => $v){

		$v = intval($v);
		$k = intval($k);
		if ($v > 0 && $k > 0) $db->query("UPDATE " . PREFIX . "_gallery_category SET position='{$v}' WHERE id='{$k}'");

	}

	clear_gallery_cache();

	galExit($PHP_SELF."?mod=twsgallery&act=1");

} elseif ($act == 51){

	$id = (isset($_POST['id'])) ? intval($_POST['id']) : intval($_GET['id']); // исправляем баг $_REQUEST['id'], временно

	if (!$id) die("No ID!");

	if ($_REQUEST['doaction'] == "mass_update"){

		$edit_category = $db->super_query("SELECT id, foto_sort, foto_msort, cat_title FROM " . PREFIX . "_gallery_category WHERE id='{$id}'");
	
		if (!$edit_category['id']) die("No ID!");

		$fsort = array();

		if ($edit_category['foto_sort']){
				$fsort['sort'] = $edit_category['foto_sort'];
		} elseif ($galConfig['foto_sort'] != ""){
			$fsort['sort'] = $galConfig['foto_sort'];
		} else $fsort['sort'] = "date";

		if ($edit_category['foto_msort']){
			$fsort['msort'] = $edit_category['foto_msort'];
		} elseif ($galConfig['foto_msort'] != ""){
			$fsort['msort'] = $galConfig['foto_msort'];
		} else $fsort['msort'] = "desc";

		$i = 1;

		include_once ENGINE_DIR.'/classes/parse.class.php';
		$parse = new ParseFilter();

		$template = $db->safesql($parse->process(trim($_POST['template'])));

		$sql = $db->query("SELECT picture_id, picture_filname, posi FROM " . PREFIX . "_gallery_picturies WHERE category_id='{$id}' ORDER BY {$fsort['sort']} {$fsort['msort']}");

		$all = $db->num_rows($sql);

		if ($fsort['msort'] == "desc") $i = $all;

		while($row = $db->get_row($sql)){

			$value = str_ireplace(array("{%i%}", "{%posi%}", "{%category%}", "{%filename%}"), array($i, intval($row['posi']), $edit_category['cat_title'], preg_replace("/(.*)\.[a-z0-9\_]+$/i","\\1", $row['picture_filname'])), $template);

			if ($fsort['msort'] == "desc") $i--; else $i++;

			$db->query("UPDATE " . PREFIX . "_gallery_picturies SET picture_title='{$value}' WHERE picture_id='{$row['picture_id']}'");

		}

		$db->free($sql);

		$db->query("UPDATE " . PREFIX . "_gallery_category SET all_time_images={$all} WHERE id='{$id}'");

		clear_gallery_cache();
		clear_gallery_vars();

		echopopupheader($langGal['menu_rec']);

		galMessage($langGal['edit_info'], $langGal['files_rename_ok'] . "<br /><br><a class=main href=\"javascript:window.close();\">{$langGal['window_close']}</a>", 105);

		echo <<<HTML
</body>
</html>
HTML;

		galExit();

	}

	$content = <<<HTML
<form action="{$PHP_SELF}?mod=twsgallery&doaction=mass_update" id="mass_edit_files" method="post">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
<input type="hidden" name="act" value="51">
<input type="hidden" name="id" value="{$id}">
<br /><br />{$langGal['mass_cat_5']}
<input type="text" name="template" size="45"  class="edit" value="{%category%} {%i%}">
<br /><span class=small>{$langGal['mass_cat_6']}</span>
<br /><br /><input type="submit" value="  {$lang['b_start']}  " class="bbcodes"> &nbsp; <input type=button class=bbcodes value="  {$langGal['window_close']}  " onclick="javascript:window.close()"><br /><br />
</form>
HTML;

	echopopupheader($langGal['mass_cat_4']);

	galMessage($langGal['mass_cat_4'], $content, 60);

	echo <<<HTML
</body>
</html>
HTML;

	galExit();

} elseif ($act == 9){

	if (!$catedit->get_mass_ids())
		msg ("error", $langGal['all_info'], $langGal['mass_denied'], "javascript:history.go(-1)");

	@set_time_limit ( 300 );
	@ini_set('max_execution_time', 300);
	@ini_set("output_buffering", "off");
	@ini_set('4096M');

	include_once ENGINE_DIR.'/gallery/classes/thumbnailer.php';

	echopopupheader($langGal['menu_rec']);
	galHeader($langGal['menu_rec']);

	echo "<table width=\"100%\" border=\"0\">";

	$edit = new gallery_file_edit();

	$sql = $db->query("SELECT id, p_id, cat_title, images, auto_resize, allow_watermark, com_thumb_max, thumb_max, icon_max_size, icon, icon_picture_id FROM " . PREFIX . "_gallery_category WHERE id IN ('".implode ("','",$catedit->ids)."')");

	while($edit_category = $db->get_row($sql)){

		$full_link = array();
		$preview_filname = array();

		$id = $edit_category['id'];

		$catedit->check_new_gallery_dir($id);

		$files = array();
		$bad_file = array();
		$remove_file = array();
		$count = 0;

		foreach (array('main', 'comthumb', 'thumb', 'caticons') as $dir){

			$files[$dir] = array();
			$handle = FOTO_DIR . '/' . $dir . '/' . $id;

			if (is_dir($handle) && ($handle_folder = @opendir($handle))){
				while (false !== ($file = @readdir($handle_folder))){
					if ($file != "." && $file != ".htaccess" && $file != "..") {
						if (!is_dir($handle . '/' . $file)){
							$files[$dir][] = strtolower($file);
						}
					}
				}
				@closedir($handle_folder);
			}

		}

		$sql_files = $db->query("SELECT picture_id, picture_filname, preview_filname, approve, media_type, type_upload, thumbnails, full_link, height, width, size FROM " . PREFIX . "_gallery_picturies WHERE category_id='{$id}'");

		while($row = $db->get_row($sql_files)){
		
			if ($row['approve'] == 1) $count++;

			$row['picture_filname'] = strtolower($row['picture_filname']);
			$row['preview_filname'] = strtolower($row['preview_filname']);

			$bad_file[$row['picture_filname']] = array();

			if (!$row['type_upload']){

				if (($ind = array_search($row['picture_filname'], $files['main'])) === false)
					$bad_file[$row['picture_filname']][] = 0;
				else
					unset($files['main'][$ind]);

			}

			if (!$row['media_type'] || $row['preview_filname']){

				$thumbnails = explode('||', $row['thumbnails']);
				$thumbnails[0] = explode('|', $thumbnails[0]);
				$thumbnails[1] = explode('|', $thumbnails[1]);
				$picture_filname = $row[($row['preview_filname'] ? 'preview_filname' : 'picture_filname')];

				if (($ind = array_search('t', $thumbnails[0])) !== false && $thumbnails[1][$ind]){

					if (($ind = array_search($picture_filname, $files['thumb'])) === false)
						$bad_file[$row['picture_filname']][] = $row['media_type'] ? 9 : 2;
					else
						unset($files['thumb'][$ind]);

				}

				if (($ind = array_search('c', $thumbnails[0])) !== false && $thumbnails[1][$ind]){

					if (($ind = array_search($picture_filname, $files['comthumb'])) === false)
						$bad_file[$row['picture_filname']][] = $row['media_type'] ? 9 : 1;
					else
						unset($files['comthumb'][$ind]);

				}

				if (($ind = array_search('i', $thumbnails[0])) !== false && $thumbnails[1][$ind]){

					if (($ind = array_search($picture_filname, $files['caticons'])) === false)
						$bad_file[$row['picture_filname']][] = $row['media_type'] ? 9 : 7;
					else
						unset($files['caticons'][$ind]);

				}

			}

			if (!$row['type_upload'] && !in_array(0, $bad_file[$row['picture_filname']])){

				$file_path = FOTO_DIR . '/main/' . $id . '/' . $row['picture_filname'];

				$width = @getimagesize($file_path);
				$size = intval(@filesize($file_path));
				$height = intval($width[1]);
				$width = intval($width[0]);

				if ($height && $width && $size && ($row['height'] != $height || $row['width'] != $width || $row['size'] != $size)){

					$db->query("UPDATE " . PREFIX . "_gallery_picturies SET size='{$size}', width='{$width}', height='{$height}' WHERE picture_id='{$row['picture_id']}'");

					$bad_file[$row['picture_filname']][] = 3;

				}

			}

			if (!count($bad_file[$row['picture_filname']]))
				unset($bad_file[$row['picture_filname']]);
			else { 
				if ($row['type_upload'])
					$full_link[$row['picture_filname']] = $row['full_link'];
				$preview_filname[$row['picture_filname']] = $row['preview_filname'];
			}

		}

		$db->free($sql_files);

		foreach (array(4 => 'main', 5 => 'comthumb', 6 => 'thumb') as $error_num => $key){
			foreach ($files[$key] as $t_file){
				if (!isset($bad_file[$t_file])) $bad_file[$t_file] = array();
				$bad_file[$t_file][] = $error_num;
			}
		}

		if (!$edit_category['icon_picture_id'] && $edit_category['icon']){
			$icon_file = explode('/', $edit_category['icon']);
			$icon_file = end($icon_file);
		} else $icon_file = "";

		foreach ($files['caticons'] as $t_file){
			if ($t_file != $icon_file){
				if (!isset($bad_file[$t_file])) $bad_file[$t_file] = array();
				$bad_file[$t_file][] = 8;
			}
		}

		$category_update_id = $catedit->get_parents_id($id, $edit_category['p_id']);
		$last_foto = $catedit->get_last_category_file($id);
		$chlds = $db->super_query("SELECT COUNT(id) AS count, SUM(cat_images) AS cat_images FROM " . PREFIX . "_gallery_category WHERE p_id={$id}");

		$db->query("UPDATE " . PREFIX . "_gallery_category SET ".($galConfig['icon_type'] ? "icon=IF(((icon='' OR icon_picture_id) AND (icon_picture_id<{$last_foto['icon_picture_id']} OR icon_picture_id='{$edit_category['icon_picture_id']}')),'{$last_foto['file']}',icon), icon_picture_id=IF((icon='{$last_foto['file']}'),'{$last_foto['icon_picture_id']}',icon_picture_id), " : ""). "images=IF(id={$id},{$count},images), last_date=IF(id={$id},'{$last_foto['date']}',last_date), last_cat_date=IF((last_cat_date<'{$last_foto['date']}' OR last_cat_date='{$edit_category['last_date']}'),'{$last_foto['date']}',last_cat_date), cat_images=IF(id={$id},{$count}+".intval($chlds['cat_images']).",cat_images), sub_cats=IF(id={$id},{$chlds['count']},sub_cats) WHERE id IN (".implode(",",$category_update_id).")");

		$edit_category['cat_title'] = stripslashes($edit_category['cat_title']);

		$tbhth = 45;

		echo <<<HTML
	<tr>
		<td height="{$tbhth}" valign="middle" colspan="5"><b>{$langGal['foto_list_cat']} "{$edit_category['cat_title']}"<br /></td>
	</tr>
	<tr>
		<td colspan="5"><div class="hr_line"></div></td>
	</tr>
HTML;

		if (!count($bad_file)){

			echo <<<HTML
	<tr>
		<td height="{$tbhth}" valign="middle" colspan="5">{$langGal['cat_recount_ok']}</td>
	</tr>
	<tr>
		<td colspan="5"><div class="hr_line"></div></td>
	</tr>
HTML;

		} else {

			$stat = array(0, 0, 0, 0);

			//$bad_file
			//0 - нет главной фотки
			//1 - нет тумбы комментирования
			//2 - нет миниатюрного изображения
			//3 - пересчитано разрешение, размер
			//4 - бесхозная фотография категории
			//5 - бесхозная тумба комментирования
			//6 - бесхозная миниатюра
			//7 - нет иконки
			//8 - бесхозная иконка
			//9 - удалить файлы предпросмотра

			foreach ($bad_file as $file => $error){

				$report = array();

				if (in_array(3, $error)){

					$report[] = $langGal['rec_er_4'];
					$stat[0]++;

				}

				if (in_array(0, $error)){

					$remove_file[] = $db->safesql($file);
					$report[] = $langGal['rec_er_1'];
					$stat[3]++;

				} elseif (in_array(1, $error) || in_array(2, $error) || in_array(7, $error)){

					$img_arr = explode('.',$file);
					$type = end($img_arr);

					$edit->remove_file($edit_category['id'], $file, $preview_filname[$file], false);

					if ($full_link[$file]){//$row['type_upload']

						$img_arr = explode('.',$file);
						$type = end($img_arr);
						$preview_update = FOTO_DIR . '/temp/' . time().mt_rand(10000000,99999999).'.'.$type;
						@copy($full_link[$file], $preview_update);

						$thumb = $edit->refresh_thumbs($preview_update, $edit_category, $preview_filname[$file]);

						@unlink($preview_update);

					} else $thumb = $edit->refresh_thumbs(FOTO_DIR . '/main/' . $edit_category['id'] .'/' . $file, $edit_category, $preview_filname[$file]);

					if ($thumb === -1){

						$remove_file[] = $db->safesql($file);
						$report[] = $langGal['rec_er_5'];
						$stat[3]++;

					} elseif ($thumb->error()){
	
						$report[] = $langGal['edit_foto_er2'];

					} else {

						if ($thumb->preview == $file) $thumb->preview = '';

						$db->query("UPDATE " . PREFIX . "_gallery_picturies SET thumbnails='".$thumb->thumbnails."', preview_filname='".$thumb->preview."' WHERE picture_filname='".$db->safesql($file)."'");

						$stat[0]++;
						$report[] = $langGal['rec_er_3'];

					}

				} elseif (in_array(9, $error)){

					$edit->remove_file($edit_category['id'], $file, $preview_filname[$file], false);

					$db->query("UPDATE " . PREFIX . "_gallery_picturies SET thumbnails='', preview_filname='' WHERE picture_filname='".$db->safesql($file)."'");

					$stat[0]++;
					$report[] = $langGal['rec_er_11'];

				} else {

					if (in_array(5, $error)){//удалить тумб
						@unlink(FOTO_DIR . '/comthumb/' . $id .'/' . $file);
						$report[] = $langGal['rec_er_7'];
						$stat[2]++;
					}

					if (in_array(6, $error)){//удалить мини-тумб
						@unlink(FOTO_DIR . '/thumb/' . $id .'/' . $file);
						$report[] = $langGal['rec_er_8'];
						$stat[2]++;
					}

					if (in_array(8, $error)){//удалить иконку
						@unlink(FOTO_DIR . '/caticons/' . $id .'/' . $file);
						$report[] = $langGal['rec_er_10'];
						$stat[2]++;
					}

					if (in_array(4, $error)){//переместить в темп

						$in = 2;

						while (file_exists(FOTO_DIR . '/temp/'.$file)){
							$file = explode('.',$file);
							$type = end($file);
							unset($file[(key($file))]);
							$file = implode(".", $file) .'_'.$in++.'.'.$type;
						}

						@rename(FOTO_DIR . '/main/' . $id . '/' . $file, FOTO_DIR . '/temp/'.$file);
						@chmod(FOTO_DIR . '/temp/'.$file, 0666);

						$report[] = $langGal['rec_er_6'];
						$stat[1]++;

					}

				}

				$report = implode("<br />", $report);

				echo <<<HTML
	<tr>
		<td height="{$tbhth}" valign="middle" colspan="5"><b>{$file}:</b><br />{$report}<br /></td>
	</tr>
	<tr>
		<td colspan="5"><div class="hr_line"></div></td>
	</tr>
HTML;

			}

			echo <<<HTML
	<tr>
		<td height="{$tbhth}" valign="middle" colspan="5">{$langGal['rec_rep_1']} <b>{$stat[0]}</b>, {$langGal['rec_rep_2']} <b>{$stat[3]}</b>, {$langGal['rec_rep_3']} <b>{$stat[1]}</b>, {$langGal['rec_rep_4']} <b>{$stat[2]}</b></td>
	</tr>
	<tr>
		<td colspan="5"><div class="hr_line"></div></td>
	</tr>
HTML;

		}

		if (count($remove_file)){

			$edit = new gallery_file_edit();
			$edit->remove(0, "p.picture_filname IN ('".implode("','", $remove_file)."')");

		}

	}

	$db->free($sql);

	if ($galConfig['show_statistic']){

		$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_cat','statistic_cat_week','statistic_file_day','statistic_file','statistic_file_onmod')");
		@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

	}

	clear_gallery_cache();
	clear_gallery_vars();

	echo <<<HTML
	<tr>
		<td height="{$tbhth}" valign="middle" align="center" colspan="5"><input type=button class=bbcodes value="  {$langGal['window_close']}  " onclick="javascript:window.close()"></td>
	</tr>
HTML;

	echo "</table>";

	galFooter();

	echo <<<HTML
</body>
</html>
HTML;

}


?>