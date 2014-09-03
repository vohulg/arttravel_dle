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
 Файл: tagscloud.php
-----------------------------------------------------
 Назначение: Управление облаком тегов
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1 && (!$galConfig['admin_user_access'] || !$user_group[$member_id['user_group']]['admin_tagscloud'])){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

$ajax_active = (isset($_REQUEST['a'])) ? intval($_REQUEST['a']) : 0;

if (!$ajax_active) $_SESSION['gallery_admin_referrer'] = $_SERVER['REQUEST_URI'];

$ids = array();

if (isset($_REQUEST['si'])){
	if (!is_array($_REQUEST['si'])){
		$_REQUEST['si'] = intval($_REQUEST['si']);
		if ($_REQUEST['si'] > 0) $ids[] = $_REQUEST['si'];
	} elseif (count($_REQUEST['si']))
		foreach ($_REQUEST['si'] as $i => $d){
			$_REQUEST['si'][$i] = intval($_REQUEST['si'][$i]);
			if ($_REQUEST['si'][$i] > 0) $ids[] = $_REQUEST['si'][$i];
		}
}

switch ($act){

case 53 :
///********************************************************************************************************
//                                   Просмотр списка тегов
//*********************************************************************************************************

	$tags = array();

	$db->query("SELECT t.id FROM " . PREFIX . "_gallery_tags t LEFT JOIN " . PREFIX . "_gallery_tags_match m ON t.id=m.tag_id WHERE m.tag_id IS NULL");

	while($row = $db->get_row())
		$tags[] = $row['id'];

	$db->free();

	if (count($tags))
		$db->query("DELETE FROM " . PREFIX . "_gallery_tags WHERE id IN (".implode(",",$tags).")");

	$fstart = isset($_REQUEST['fstart']) ? intval($_REQUEST['fstart']) : 1;
	if ($fstart < 1) $fstart = 1;
	$limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
	$search = $db->safesql(trim(htmlspecialchars(urldecode($_REQUEST['search']), ENT_QUOTES, $config['charset'])));
	$search_urlencode = urlencode($search);

	$where = array();

	if ($search) $where[] = "tag_name LIKE '{$search}%'";

	$where = count($where) ? " WHERE " . implode(" AND ", $where) : "";

	$count_all = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_tags" . $where);

	$mod_url = "&search=".$search_urlencode;

	if (!$ajax_active){

		echoheader("", "");
		galnavigation();
		$twsg->check_unmoderate();

		galHeader($langGal['menu_tags']);

		echo <<<HTML
<table width="100%">
    <tr>
        <td>
<form action="" method="post" name="editnews">
<div id="file_list" style="padding-top:5px;padding-bottom:2px;width:100%;">
HTML;

	}

	if (!$count_all['count']){

		$content = <<<HTML
<table width="100%">
<tr>
	<td height="100" align="center">{$langGal['foto_list_notags']}<br /><br><a class=main href="javascript:history.go(-1)">{$lang['func_msg']}</a></td>
</tr>
<tr><td background="engine/skins/images/mline.gif" height=1></td></tr>
</table>
HTML;

	} else {

		$content = <<<HTML
<table width="100%" id="tagslist">
  <tr class="thead">
    <th style="padding:4px;">{$langGal['tagscloud_name']}</th>
    <th width="180" align="center"><div style="text-align: center;">{$langGal['tagscloud_count']}</div></th>
    <th width="100"><div style="text-align: center;">&nbsp;{$langGal['cat_action']}&nbsp;</div></th>
    <th width="30"><div style="text-align: center;"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()"></div></th>
  </tr>
  <tr class="tfoot"><th colspan="4"><div class="hr_line"></div></th></tr>
HTML;

		$min = $max = 1;
		$tags = array();

		function gallery_compare_tags($a, $b){

			if($a[0] == $b[0]) return 0;
			return strcasecmp($a[0] , $b[0]);

		}

		$db->query("SELECT t.id, t.tag_name, COUNT(m.mid) as count FROM " . PREFIX . "_gallery_tags t INNER JOIN " . PREFIX . "_gallery_tags_match m ON t.id=m.tag_id {$where} GROUP BY m.tag_id LIMIT ".(($fstart-1)*$limit).", {$limit}");

		while($row = $db->get_row()){
			$tags[] = array(stripslashes($row['tag_name']), $row['count'], $row['id']);
			$min = min($min, $row['count']);
			$max = max($max, $row['count']);
		}

		$db->free();

		$range = $max-$min;
		if ($range <= 0) $range = 1;
		if ($min <= 0) $min = 1;

		$count_tags = count($tags);

		usort ($tags, "gallery_compare_tags");

		for ($i=0; $i < $count_tags; $i++){

			$link = $config['http_home_url'] = ($config['allow_alt_url'] == "yes") ? ($galConfig['work_postfix']."all/tag-".urlencode($tags[$i][0])."/") : ("index.php?do=gallery&act=15&p=tag-".urlencode($tags[$i][0]));

			$content .= "<tr>
<td style=\"padding:4px;\" nowrap><div id=\"content_{$tags[$i][2]}\">{$tags[$i][0]}</div></td>
<td align=center><b>{$tags[$i][1]}</b></td>
<td>[&nbsp;<a href=\"{$link}\" target=\"_blank\">{$lang['comm_view']}</a>&nbsp;]&nbsp;&nbsp;[&nbsp;<a uid=\"{$tags[$i][2]}\" class=\"editlink\" href=\"?mod=twsgallery&act=54&si={$tags[$i][2]}\">{$lang['word_ledit']}</a>&nbsp;]&nbsp;&nbsp;[&nbsp;<a uid=\"{$tags[$i][2]}\" class=\"dellink\" href=\"?mod=twsgallery&act=56&dle_allow_hash={$dle_login_hash}&si={$tags[$i][2]}\">{$lang['word_ldel']}</a>&nbsp;]</td>
<td align=center><input name=\"si[]\" value=\"{$tags[$i][2]}\" type=\"checkbox\"></td>
</tr>
<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=4></td></tr>";

		}

		if($count_all['count'] > $limit){

			$pages = fastpages($count_all['count'], $limit, $fstart, "{$PHP_SELF}?mod=twsgallery&act={$act}&limit={$limit}&".$mod_url."&fstart={INS}");
			$pages = implode(" &nbsp; " , $pages);

		} else $pages = " &nbsp; ";

		$content .= <<<HTML
<tr><td colspan="4"><div class="hr_line"></div></td></tr>
<tr><td colspan=2><div class="news_navigation" style="margin-bottom:5px; margin-top:5px;">{$pages}</div></td>
<td colspan=2 align="right" valign="top"><div style="margin-bottom:5px; margin-top:5px;"><input class="edit" type="submit" value="{$lang['b_start']}"> 
<select name="act">
<option value="">{$lang['edit_selact']}</option>
<option value="54">{$langGal['mass_edit_edit']}</option>
<option value="56">{$lang['edit_seldel']}</option>
</select>
<input type="hidden" name="mod" value="twsgallery">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
</td></tr>
</table>
HTML;

	}

	if (!$ajax_active){

		echo $content;
		$content = "";

		echo <<<HTML
</div></form></td></tr>
HTML;

		if($count_all['count'] > $limit){

			echo <<<HTML
<tr><td>
<form action="{$PHP_SELF}?mod=twsgallery&act={$act}&limit={$limit}&{$mod_url}" method="post" name="options_bar">
{$lang['edit_go_page']} <input class="edit" style="text-align: center" name="fstart" value="{$fstart}" onfocus="this.select();" type="text" size="5"> <input class="edit" type="submit" value=" ok ">
</form>
</td></tr>
HTML;

		}

		echo <<<HTML
</table>
<script type="text/javascript">
function twsact( url, layer, text){
	ShowLoading('');
	$.get('{$PHP_SELF}' + url + '&{$mod_url}', { mod: 'twsgallery', a: 1, dle_allow_hash: '{$dle_login_hash}' }, function(data){
		HideLoading('');
		if (data.match(/\[HTML:Ok\](.*?)\[END:HTML:Ok\]/g)){
			var item = $("#" + layer);
			item.html(text);
		} else {
			DLEalert('{$langGal['all_error_code']}'+data, '{$langGal['cat_error']}'); return false;
		}
	});
};
function ckeck_uncheck_all() {
    var frm = document.editnews;
    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='checkbox') {
            if(frm.master_box.checked == true){ elmnt.checked=false; }
            else{ elmnt.checked=true; }
        }
    }
    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
    else{ frm.master_box.checked = true; }
};
$(function(){

$("#tagslist").delegate("tr", "hover", function(){
  $(this).toggleClass("hoverRow");
});

var tag_id = 0, tag_name = '';

$('.dellink').click(function(){
	tag_id = $(this).attr('uid');
	DLEconfirm( '{$langGal['tagscloud_del']} <b>&laquo;'+$('#content_'+tag_id).text()+'&raquo;</b> {$langGal['tagscloud_del_1']}', '{$lang['p_confirm']}', function () {
		twsact('?act=56&si='+tag_id, 'content_'+tag_id, '{$langGal['all_del']}');
	});
	return false;
});

$('.editlink').click(function(){
	tag_id = $(this).attr('uid');
	tag_name = $('#content_'+tag_id).text();
	DLEprompt('{$langGal['tagscloud_edit_1']}', tag_name, '{$langGal['tagscloud_edit']}', function (new_text) {
		if (tag_name != new_text)
			twsact('?act=55&newname='+encodeURIComponent(new_text)+'&si='+tag_id, 'content_'+tag_id, new_text);
	});
	return false;
});

});
HTML;

		echo "</script>\n";

	} else {

		@header("Content-type: text/html; charset=".$config['charset']);
		echo $content;
		galExit();

	}

	galFooter();

	galHeader($langGal['all_search_title']);

	echo <<<HTML
<form action="{$PHP_SELF}" method="get" name="options_bar">
<input type="hidden" name="mod" value="twsgallery">
<input type="hidden" name="act" value="{$act}">
<input type="hidden" name="fstart" value="1">
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
		<td style="padding:5px;">{$langGal['all_search_1']} &nbsp; <input class="edit" style="text-align: center" name="limit" onfocus="this.select();" value="{$limit}" type="text" size="3"> &nbsp; {$langGal['all_search_2']} &nbsp; <input class="edit" name="search" onfocus="this.select();" value="{$search}" type="text" size="35"> &nbsp; <input class="edit" type="submit" value="{$lang['edit_act_1']}"></td>
    </tr>
</table>
</div>
</form>
HTML;

	galFooter();
	$twsg->galsupport50();
	echofooter();

break;

case 54 :
///********************************************************************************************************
//                                   Редактирование тегов
//*********************************************************************************************************

	if (!count($ids))
		msg ("error", $langGal['all_err_1'], $langGal['mass_denied'], "javascript:history.go(-1)");

	$db->query("SELECT * FROM " . PREFIX . "_gallery_tags WHERE id IN (".implode(",", $ids).") ORDER BY id DESC");

	if (!$db->num_rows())
		msg ("error", $langGal['all_err_1'], $langGal['mass_denied'], "javascript:history.go(-1)");

	echoheader("options", $lang['mass_head']);
	galnavigation();
	galHeader($langGal['menu_tags']);

	echo <<<HTML
<script language='JavaScript' type="text/javascript">
<!--
function ckeck_tag_name(){
var frm = document.getElementById('entryform');
for (var i=0;i<frm.elements.length;i++){
var elmnt = frm.elements[i];
if (elmnt.name && elmnt.name.replace(/^(.+?)\[(.+?)$/, "$1") == 'tag_name' && elmnt.value == ''){
DLEalert('{$langGal['tagscloud_noname']}', '{$langGal['cat_error']}'); return false;
}
}
return true;
};
-->
HTML;

	echo "</script>\n";

	echo <<<HTML
<form action="{$PHP_SELF}" id="entryform" method="post" onsubmit="return ckeck_tag_name()">
<table width="100%">
HTML;

	while($row = $db->get_row()){

		$row['tag_name'] = stripslashes($row['tag_name']);

		echo <<<HTML
    <tr>
        <td width="150" style="padding:4px;">{$langGal['tagscloud_name']}</td>
        <td><input class="edit" style="width:351px;" value="{$row['tag_name']}" type="text" name="tag_name[{$row['id']}]"></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
HTML;

	}

	$db->free();

	echo <<<HTML
	<tr><td colspan="4"><div class="hr_line"></div></td></tr>
</table>
<div style="padding-left:10px;padding-top:15px;padding-bottom:5px;">
	<input type="submit" class="buttons" value="{$langGal['ft_edit_save']}" style="width:100px;">&nbsp;
	<a href="javascript:history.go(-1)"><input onclick="javascript:history.go(-1)" type="button" class="buttons" value="{$lang['func_msg']}" style="width:130px;"></a>
</div>
<input type="hidden" name="act" value="55">
<input type="hidden" name="mod" value="twsgallery">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
</form>
HTML;

	galFooter();
	$twsg->galsupport50();
	echofooter();

break;

case 55 :
///********************************************************************************************************
//                                   Редактирование тегов - сохранение
//*********************************************************************************************************

	if ($ajax_active)
		$_POST['tag_name'] = array($_REQUEST['si'] => convert_unicode(urldecode($_GET['newname']), $config['charset']));

	$ids = array();
	$tag_update = array();

	foreach ($_POST['tag_name'] as $id => $name){

		$id = intval($id);

		if (@preg_match("/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $name)) $name = "";
		$ids[$id] = @$db->safesql(htmlspecialchars(strip_tags(stripslashes(trim($name))), ENT_QUOTES, $config['charset']));

		$tag_update[] = "WHEN {$id} THEN '{$ids[$id]}'";

		if ($id < 1 || $ids[$id] == ''){
			if (!$ajax_active)
				msg ("error", $langGal['all_err_1'], $langGal['tagscloud_noname'], "javascript:history.go(-1)");
			else
				die($langGal['tagscloud_noname']);
		}

	}

	update_files_tags(array_keys($ids), $ids);

	$db->query("UPDATE " . PREFIX . "_gallery_tags SET tag_name=CASE id ". implode(" ", $tag_update) ." END WHERE id IN ('". implode("','", array_keys($ids)) ."')");

	clear_gallery_cache();
	clear_gallery_vars();

	galExit((isset($_SESSION['gallery_admin_referrer']) ? $_SESSION['gallery_admin_referrer'] : "{$PHP_SELF}?mod=twsgallery&act=".$act), "[HTML:Ok]ok[END:HTML:Ok]", $ajax_active);

break;

case 56 :
///********************************************************************************************************
//                                   Удаление тегов
//*********************************************************************************************************

	if (!count($ids)){
		if (!$ajax_active)
			msg ("error", $langGal['all_err_1'], $langGal['mass_denied'], "javascript:history.go(-1)");
		else
			die($langGal['mass_denied']);
	}

	update_files_tags($ids);

	$db->query("DELETE FROM " . PREFIX . "_gallery_tags_match WHERE tag_id IN (".implode(",", $ids).")");
	$db->query("DELETE FROM " . PREFIX . "_gallery_tags WHERE id IN (".implode(",", $ids).")");

	clear_gallery_cache();
	clear_gallery_vars();

	galExit((isset($_SESSION['gallery_admin_referrer']) ? $_SESSION['gallery_admin_referrer'] : "{$PHP_SELF}?mod=twsgallery&act=".$act), "[HTML:Ok]ok[END:HTML:Ok]", $ajax_active);

break;

}

function update_files_tags($ids, &$edit = false){
global $db, $ajax_active, $langGal;

	$db->query("SELECT * FROM " . PREFIX . "_gallery_tags WHERE id IN (".implode(",", $ids).")");

	$delete = array();

	while($row = $db->get_row())
		if (($old_name = stripslashes(trim($row['tag_name']))) != ($edit === false ? "" : $edit[$row['id']]))
			$delete[$row['id']] = $old_name;
		else
			unset($ids[array_search($row['id'], $ids)], $edit[$row['id']]);

	$db->free();

	if (!count($delete)){
		if (!$ajax_active)
			msg ("error", $langGal['all_err_1'], $langGal['mass_denied'], "javascript:history.go(-1)");
		else
			die($langGal['mass_denied']);
	}

	if ($edit !== false){

		$control = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_tags WHERE tag_name IN ('".implode("','", $edit)."')");

		if ($control['count']){
			if (!$ajax_active)
				msg ("error", $langGal['all_err_1'], $langGal['tagscloud_ername'], "javascript:history.go(-1)");
			else
				die($langGal['tagscloud_ername']);
		}

	}

	$sql = $db->query("SELECT m.file_id, p.tags FROM " . PREFIX . "_gallery_tags_match m INNER JOIN " . PREFIX . "_gallery_picturies p ON p.picture_id=m.file_id WHERE m.tag_id IN (".implode(",", $ids).") GROUP BY m.file_id");

	while($row = $db->get_row($sql)){

		$row['tags'] = explode(",", $row['tags']);
		$tags = array();

		foreach ($row['tags'] as $value){
			$value = trim($value);
			$key = array_search($value, $delete);
			if ($key !== false){
				if ($edit === false)
					continue;
				else
					$value = $edit[$key];
			}
			$tags[] = $value;
		}

		$db->query("UPDATE " . PREFIX . "_gallery_picturies SET tags='".implode(",", array_unique($tags))."' WHERE picture_id='{$row['file_id']}'");
	}

	$db->free($sql);

}

?>