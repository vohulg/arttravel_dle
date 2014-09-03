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
 Файл: comments.php
-----------------------------------------------------
 Назначение: Управление комментариями
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1 && (!$galConfig['admin_user_access'] || !$user_group[$member_id['user_group']]['admin_comments'])){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

class UnComments extends db {

	var $data_querry	= "";
	var $cstart			= 0;
	var $comments_num	= 0;
	var $comm_nummers	= 40;
	var $count_querry	= "";
	var $sort_order 	= "desc";
	var $comm_table		= '';
	var $url			= '';
	var $update_user_sql = "";
	var $update_news_sql = "";
	var $item_url 		= '';

	function UnComments(){

		$this->comm_table 	= PREFIX . '_gallery_comments';

	}

	function get_ids(){
	global $lang;

		$selected_comments = isset($_POST['selected_comments']) ? $_POST['selected_comments'] : array();

		if(!count($selected_comments)){ msg("error", $lang['mass_error'], $lang['mass_dcomm'], "javascript:history.go(-1)"); }

		$_ids = array();

		foreach ($selected_comments as $c_id){
			if (intval($c_id)) $_ids[] = intval($c_id);
		}

		if (!count($_ids)){ msg("error", $lang['mass_error'], $lang['mass_dcomm'], "javascript:history.go(-1)"); }

	 return $_ids;
	}

	function full_delete($_id){

		if (!$_id) die("No ID!");

		if ($this -> update_user_sql){

			$result = $this->query("SELECT COUNT(id) as count, user_id FROM " . $this -> comm_table . " WHERE post_id='{$_id}' AND user_id !=0 GROUP BY user_id");

			while($row = $this->get_row($result)){

				$this->query("UPDATE " . USERPREFIX . "_users set ".$this -> update_user_sql."{$row['count']} where user_id='{$row['user_id']}'"); 

			}

		}

		$this->query("DELETE FROM " . $this -> comm_table . " WHERE post_id='{$_id}'");

		if ($this -> update_news_sql)
			$this->query("UPDATE " . $this -> update_news_sql . " = '{$_id}'");

	}

	function delete(){

		$_ids = $this->get_ids();

		foreach ($_ids as $_id){

			$row = $this->super_query("SELECT post_id, user_id, approve FROM " . $this -> comm_table . " WHERE id='{$_id}'");

			if ($this -> update_user_sql && $row['user_id']){
				$this->query("UPDATE " . USERPREFIX . "_users set " . $this -> update_user_sql  . " where user_id='{$row['user_id']}'");
			}

			$this->query("DELETE FROM " . $this -> comm_table . " where id = '{$_id}'");

			if ($row['approve'] && $this -> update_news_sql)
				$this->query("UPDATE " . $this -> update_news_sql . " = '{$row['post_id']}'");

		}

	}

	function approve(){
	global $config, $parse;

		$_ids = $this->get_ids();

		if ($config['allow_comments_wysiwyg'] != "no" && $config['allow_comments_wysiwyg'] != "0") { $parse->wysiwyg = true; $use_html = true; $parse->ParseFilter(Array('div', 'a', 'span', 'p', 'br'), Array(), 0, 1);} else $use_html = false;

		foreach ($_ids as $_id){

			$row = $this->super_query("SELECT post_id, user_id, approve FROM " . $this -> comm_table . " WHERE id='{$_id}'");

			$comments = $this->safesql($parse->BB_Parse($parse->process($_POST['selected_text'][$_id]), $use_html));
			$this->query("UPDATE " . $this -> comm_table . " set text='{$comments}', approve='1' WHERE id='{$_id}'");

			if (!$row['approve'] && $this -> update_news_sql)
				$this->query("UPDATE " . $this -> update_news_sql . " = '{$row['post_id']}'");

		}

	}

	function comments_list(){
	global $lang, $parse, $config, $dle_login_hash, $langGal, $user_id, $id, $mode, $user_group, $member_id;

	if ($this -> cstart < 1) $this -> cstart = 1;

	if ($this -> count_querry != ""){

		$sql_count = $this->super_query("SELECT COUNT(id) as count FROM " . $this -> count_querry);
		$this -> comments_num = $sql_count['count'];

	}

	$this -> comm_nummers = intval($this -> comm_nummers);

	if ($this->comments_num != 0){

		$sql_result = $this->query($this -> data_querry . " LIMIT ".(($this -> cstart-1)*$this -> comm_nummers)."," . $this -> comm_nummers);

		$entries = "";

		while($row = $this->get_row()){

			$row['text'] = $parse->decodeBBCodes($row['text'], false);
			$row['text'] = "<textarea id='edit-comm-{$row['id']}' name=\"selected_text[{$row['id']}]\" style=\"width:98%; height:100px;font-family:verdana; font-size:11px; border:1px solid #E0E0E0\">".$row['text']."</textarea><input type=\"hidden\" name=\"post_id[{$row['id']}]\" value=\"{$row['post_id']}\">";

			$fotourl = "&act=2&cid=".$row['category_id']."&fid=".$row['picture_id'];
			if ($row['picture_title'] == '') $row['picture_title'] = $langGal['view_no_title'];
			$news_title = "<a href=\"".$this -> item_url . $fotourl . "\" target=\"_blank\">".stripslashes($row['cat_title'])." : ".stripslashes($row['picture_title'])."</a>";
			$row['autor'] = stripslashes($row['autor']);
			$user_edit = ($user_group[$member_id['user_group']]['admin_editusers'] && $row['user_id']) ? "<a class=maintitle onclick=\"javascript:popupedit('$row[user_id]'); return(false)\" href=\"#\">{$row['autor']}</a>" : $row['autor'];

			$entries .= "<span id='table-comm-{$row['id']}'><table width=100%><tr><td class=\"list\" style=\"padding:4px;\" width=120>{$user_edit}</td>";
			$entries .= "<td class=\"list\" width=100><a href=\"?mod=blockip&ip=".urlencode($row['ip'])."\" target=\"_blank\">{$row['ip']}</a></td>";
			$entries .= "<td class=\"list\">{$lang['cmod_n_title']} {$news_title}<br />".$row['text']."</td>";
			$entries .= "<td class=\"list\" width=130><input class=\"bbcodes\" type=\"button\" style=\"width:100px;\" onclick=\"ajax_save_comm_edit('{$row['id']}'); return false;\" value=\"$lang[bb_b_approve]\"><br /><br /><input class=\"bbcodes\" type=\"button\" style=\"width:100px;\" onclick=\"ajax_comm_delete('{$row['id']}'); return false;\" value=\"$lang[edit_seldel]\"></td>";
			$entries .= "<td class=\"list\" width=20><input name=\"selected_comments[]\" value=\"{$row['id']}\" type='checkbox'></td>";
			$entries .= "<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=5></td></tr></table></span>";

		}

		$this->free ();

		if ($this->comments_num > $this->comm_nummers){

			$npp_nav = fastpages($this->comments_num, $this->comm_nummers, $this -> cstart, $this -> url . "&cstart={INS}");
			$npp_nav = "<div class=\"news_navigation\" style=\"margin-bottom:5px; margin-top:5px;\">".implode(" &nbsp; " , $npp_nav)."</div>";

		} else $npp_nav = "";

		$cstart = $this -> cstart; //пожалеем пользователей с пхп 4))) (пхп 5 поддерживает вставку в фигурных скобках переменных класса)

		$returned = <<<HTML
<script language='JavaScript' type="text/javascript">
<!--
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
}
function ajax_save_comm_edit( c_id )
{
	var comm_txt = document.getElementById('edit-comm-'+c_id).value;
	ShowLoading('');
	$.post('engine/gallery/ajax/comments.php', { comm_txt: comm_txt, com_id: c_id, action: "do_edit", dle_allow_hash: '{$dle_login_hash}', skin: '{$config['skin']}' }, function(){
		document.getElementById('table-comm-'+c_id).innerHTML = '';
		HideLoading('');
	});
	return false;
}
function ajax_comm_delete( c_id )
{
	ShowLoading('');
	$.post('engine/gallery/ajax/comments.php', { com_id: c_id, action: "delete", dle_allow_hash: '{$dle_login_hash}', skin: '{$config['skin']}' }, function(){
		document.getElementById('table-comm-'+c_id).innerHTML = '';
		HideLoading('');
	});
	return false;
}
//-->
</script>
<form action="" method="post" name="editnews">
<table width="100%">
    <tr>
        <td>
	<table width=100%>
	<tr>
    <td width=120>&nbsp;&nbsp;{$lang['edit_autor']}
    <td width=100>IP:
	<td>{$lang['comm_ctext']}
    <td width=130>{$lang['vote_action']}
    <td width=20 class="list"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="javascript:ckeck_uncheck_all()">
	</tr>
	<tr><td colspan="5"><div class="hr_line"></div></td></tr></table>
	{$entries}
	<table width=100%>
	<tr>
		<td colspan="2"><div class="hr_line"></div></td>
	</tr>
	<tr>
		<td>{$npp_nav}</td>
		<td align="right">
<select name="act">
<option value="">{$lang['edit_selact']}</option>
<option value="35">{$lang['bb_b_approve']}</option>
<option value="33">{$lang['edit_seldel']}</option>
<input type=hidden name="mod" value="twsgallery">
<input type=hidden name="user_id" value="{$user_id}">
<input type=hidden name="id" value="{$id}">
<input type=hidden name="mode" value="{$mode}">
<input type=hidden name="cstart" value="{$cstart}">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
<input class="buttons" type="submit" value=" {$lang['b_start']} ">
</select>
		</td>
	</tr>
	</table>
		</td>
			</tr>
</table>
</form>
HTML;

	return $returned;

	} else return false;

	}

}

include_once ENGINE_DIR.'/classes/parse.class.php';

$parse = new ParseFilter( );
$parse->safe_mode = true;
$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
$parse->allow_image = $user_group[$member_id['user_group']]['allow_image'];

$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
$id = (isset($_POST['id'])) ? intval($_POST['id']) : intval($_GET['id']); // исправляем баг $_REQUEST['id'], временно
$mode = isset($_REQUEST['mode']) ? intval($_REQUEST['mode']) : 0;

$COMMENTS = new UnComments();
$COMMENTS->cstart = (isset($_REQUEST['cstart'])) ? intval($_REQUEST['cstart']) : 1;

if ($act == 32){

	echoheader("", "");
	galnavigation();

	$sql[] = "c.post_id=p.picture_id";
	$COMMENTS->url = "{$PHP_SELF}?mod=twsgallery&act=32&mode={$mode}&user_id={$user_id}&id={$id}";

	if (!$mode) $sql[] = "c.approve='0'";
	if ($user_id) $sql[] = "c.user_id='{$user_id}'";
	if ($id) $sql[] = "p.picture_id='{$id}'";

	$COMMENTS->count_querry = $COMMENTS->comm_table . " c, " . PREFIX . "_gallery_picturies p WHERE " . implode (" AND ",$sql);
	$COMMENTS->data_querry = "SELECT c.id, post_id, autor, c.user_id, c.text, c.ip, p.picture_id, p.picture_title, p.category_id, ct.cat_title FROM " . $COMMENTS->comm_table . " c, " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category ct ON ct.id=p.category_id WHERE " . implode (" AND ",$sql) . " ORDER BY c.date ".$COMMENTS->sort_order;
	$COMMENTS->item_url	= $config['http_home_url']."index.php?do=gallery";

	$content = $COMMENTS->comments_list();

	if ($content !== false){

		$twsg->check_unmoderate();
		galHeader($langGal['menu_editcomm']);

		echopopupedituser();

		echo $content;

		galFooter();

	} else {

		galMessage($langGal['menu_editcomm'], $langGal['no_comments']."<br /><br /><a href=\"javascript:history.go(-1)\">".$lang['func_msg']."</a>");

	}

	$twsg->galsupport50();
	echofooter();

} elseif ($act == 33){

	$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments-1 WHERE picture_id ";
	$COMMENTS->delete();

	if ($galConfig['show_statistic']){

		$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_com','statistic_com_day')");
		@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

	}

	clear_gallery_cache();

	msg("info", $lang['mass_head'], $lang['mass_delokc'], "{$PHP_SELF}?mod=twsgallery&act=32&mode={$mode}&user_id={$user_id}&id={$id}&cstart=".$COMMENTS->cstart);

} elseif ($act == 34){

	$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments='0' WHERE picture_id ";
	$COMMENTS->full_delete($id);

	if ($galConfig['show_statistic']){

		$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_com','statistic_com_day')");
		@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

	}

	clear_gallery_cache();

	galExit((isset($_SESSION['gallery_admin_referrer']) ? $_SESSION['gallery_admin_referrer'] : "{$PHP_SELF}?mod=twsgallery&act=10"));

} elseif ($act == 35){

	$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments+1 WHERE picture_id ";
	$COMMENTS->approve();

	if ($galConfig['show_statistic']){

		$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_com','statistic_com_day')");
		@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

	}

	clear_gallery_cache();

	msg("info", $lang['mass_head'], $lang['mass_approve_ok'], "{$PHP_SELF}?mod=twsgallery&act=32&mode={$mode}&user_id={$user_id}&id={$id}&cstart=".$COMMENTS->cstart);

}

?>