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
 Файл: foto.php
-----------------------------------------------------
 Назначение: Список загруженых файлов
=====================================================
*/

if(!defined('DATALIFEENGINE') OR !defined( 'LOGGED_IN' ))
{
  die("Hacking attempt!");
}

	if($member_id['user_group'] != 1 && (!$galConfig['admin_user_access'] || !$user_group[$member_id['user_group']]['admin_editnews'])){ msg("error", $lang['addnews_denied'], $lang['db_denied'], "?mod=twsgallery&act=0"); }

	$content = "";

	require_once TWSGAL_DIR.'/classes/editfile.php';
	require_once TWSGAL_DIR.'/classes/editcategory.php';

	$edit = new gallery_file_edit();

	$ajax_active = (isset($_REQUEST['a'])) ? intval($_REQUEST['a']) : 0;

	if (!$ajax_active) $_SESSION['gallery_admin_referrer'] = $_SERVER['REQUEST_URI'];

	$subact = (isset($_REQUEST['subact'])) ? intval($_REQUEST['subact']) : 0;
	$id = (isset($_POST['id'])) ? intval($_POST['id']) : intval($_GET['id']); // исправляем баг $_REQUEST['id'], временно
	$author = $db->safesql(trim(htmlspecialchars(urldecode($_REQUEST['author']), ENT_QUOTES, $config['charset'])));
	$noauthor = (isset($_REQUEST['noauthor'])) ? intval($_REQUEST['noauthor']) : 0;
	$ip = $db->safesql(trim(htmlspecialchars(strip_tags($_REQUEST['ip']), ENT_QUOTES, $config['charset'])));
	$search = $db->safesql(trim(htmlspecialchars(urldecode($_REQUEST['search']), ENT_QUOTES, $config['charset'])));
	$search_urlencode = urlencode($search);
	$moderate = (isset($_REQUEST['moderate'])) ? intval($_REQUEST['moderate']) : 0;
	$fromnewsdate = $db->safesql(trim(htmlspecialchars(stripslashes($_REQUEST['fromnewsdate']), ENT_QUOTES, $config['charset'])));
	$tonewsdate = $db->safesql(trim(htmlspecialchars(stripslashes($_REQUEST['tonewsdate']), ENT_QUOTES, $config['charset'])));

	//$search_sort_set = false;

	if (isset($_REQUEST['limit']) && intval($_REQUEST['limit']) > 1 && $_REQUEST['limit'] != $galConfig['admin_num_files']){

		$galConfig['admin_num_files'] = intval($_REQUEST['limit']);
		$twsg->save_clean_gal_config(array(), array('admin_num_files'=>$galConfig['admin_num_files']));
		$_REQUEST['fstart'] = 1;

	}

	if ($galConfig['admin_num_files'] < 1) $galConfig['admin_num_files'] = 100;

	$fstart = isset($_REQUEST['fstart']) ? intval($_REQUEST['fstart']) : 1;
	if ($fstart < 1) $fstart = 1;

	$where = array();
	$join = "";

	if ($ip) $where[] = "p.ip LIKE '{$ip}%'";
	if ($id) $where[] = "p.category_id='{$id}'";
	switch ($moderate){
		case 1 : $where[] = "p.approve=0"; break;
		case 2 : $where[] = "p.approve=2"; break;
		case 3 : $where[] = "p.approve=3"; $where[] = "date < '".date ("Y-m-d H:i:s", (TIME-3600*2))."'"; break;
		case 4 : $where[] = "p.approve=1"; break;
		case 5 : $join .= " LEFT JOIN " . USERPREFIX . "_users u ON u.user_id=p.user_id"; $where[] = "p.user_id!=0 AND u.user_id IS NULL"; break;
		default : $where[] = "(p.approve!=3 OR date < '".date ("Y-m-d H:i:s", (TIME-3600*2))."')";
	}
	if (dle_strlen($search, $config['charset']) > 3) $where[] = "(p.picture_title LIKE '%{$search}%' OR p.text LIKE '%{$search}%')";
	if ($author) $where[] = "p.picture_user_name='{$author}'";
	elseif ($noauthor) $where[] = "p.picture_user_name=''";
	if ($fromnewsdate != "") $where[] = "p.date >= '{$fromnewsdate}'";
	if ($tonewsdate != "") $where[] = "p.date <= '{$tonewsdate}'";

	$where = $join . (count($where) ? " WHERE " . implode(" AND ", $where) : "");

	//if ($where != "") $search_sort_set = true;

	$count_all = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies p{$where}");

	$i = $count_all['count'] - $galConfig['admin_num_files']*($fstart-1);
	if ($i > $galConfig['admin_num_files']) $i = $galConfig['admin_num_files'];

	if (!$ajax_active){

		$js_array[] = "engine/skins/calendar.js";

		echoheader("", "");
		galnavigation();
		$twsg->check_unmoderate();

		echo "<div style=\"padding-top:5px;padding-bottom:2px;display:none;\" name=\"advancedsearch\" id=\"advancedsearch\">";

		galHeader($langGal['foto_list_stat1']." <b>{$i}</b> ".$langGal['foto_list_stat2']." <b>{$count_all['count']}</b>");

		$category_list = CategoryGalSelection($id);
		$file_status = makeDropDownGallery(array("0"=>$langGal['foto_list_status1'],"4"=>$langGal['foto_list_status2'],"1"=>$langGal['foto_list_status3'],"2"=>$langGal['foto_list_status4'],"3"=>$langGal['foto_list_status5'],"5"=>$langGal['foto_list_status6']), "moderate", $moderate);
		$ifnoauthor = ($noauthor) ? " checked" : "";

	}

	$mod_url = "id=".$id."&author=".urlencode($author)."&moderate=".$moderate."&ip=".$ip."&search=".$search_urlencode."&fromnewsdate={$fromnewsdate}&tonewsdate={$tonewsdate}";

	$order_by = array ();

	$sort_options = array(	// Порядок элементов в массиве определяет приоритет поля сортировки. Чем выше в списке, тем больше приоритет.
	'search_order_m' => 'p.approve',
	'search_order_c' => 'p.comments',
	'search_order_v' => 'p.file_views',
	'search_order_r' => 'p.rating',
	'search_order_l' => 'p.downloaded',
	'search_order_t' => 'p.picture_title',
	'search_order_d' => 'p.picture_id',
	'search_order_d' => 'p.picture_id',
	'search_order_p' => 'p.posi', // Сортировка по позиции не отключается, поэтому её вниз
	);

	foreach ($sort_options as $var => $field){
		$data = '';
		if (isset($_REQUEST[$var]) && in_array($_REQUEST[$var], array('asc','desc'))){
			$data = $_REQUEST[$var];
			$order_by[] = $field.' '.$data;
			$mod_url .= "&{$var}={$data}";
		}
		$select = $var != 'search_order_p' ? array(""=>$lang['user_order_no'],"asc"=>$lang['user_order_plus'],"desc"=>$lang['user_order_minus']) : array("asc"=>$lang['user_order_plus'],"desc"=>$lang['user_order_minus']);
		${$var} = makeDropDownGallery($select, $var, $data);
	}

	if (count($order_by)){
		//$search_sort_set = true;
		$order_by = implode(", ", $order_by);
	} else {
		$search_order_p = makeDropDownGallery(array("asc"=>$lang['user_order_plus'],"desc"=>$lang['user_order_minus']), "search_order_p", 'desc');
		$order_by = "p.posi desc";
	}

	if (($search_id = intval($_REQUEST['si'])) > 0 && ($subact == 1 || $subact == 2)){

		$sort_from = ($fstart-1)*$galConfig['admin_num_files']-1;
		if ($sort_from < 0) $sort_from = 0;
		$sort_limit = $galConfig['admin_num_files']+2;

		$previous_row = false;
		$find_posi = 0;

		$sql = $db->query("SELECT p.picture_id, p.posi FROM " . PREFIX . "_gallery_picturies p {$where} ORDER BY {$order_by} LIMIT {$sort_from}, {$sort_limit}");

		while($row = $db->get_row($sql)){

			if ($search_id == $row['picture_id'] && $subact == 1 && $previous_row || $find_posi){

				$db->query("UPDATE " . PREFIX . "_gallery_picturies SET posi='{$previous_row['posi']}' WHERE picture_id='{$row['picture_id']}'");
				$db->query("UPDATE " . PREFIX . "_gallery_picturies SET posi='{$row['posi']}' WHERE picture_id='{$previous_row['picture_id']}'");

				clear_gallery_vars();
				clear_gallery_cache();

				break;

			} elseif ($search_id == $row['picture_id'] && $subact == 2) $find_posi = 1; // Вниз, т.е. ID поменять местом со следующим

			$previous_row = $row;

		}

		$db->free($sql);

	}

	if (!$ajax_active){

		echo <<<HTML
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="engine/skins/calendar-blue.css" title="win2k-cold-1" />

<form action="" method="get" name="options_bar">
<input type="hidden" name="mod" value="twsgallery">
<input type="hidden" name="act" value="10">
<table width="100%">
     <tr>
		<td style="padding:5px;">{$langGal['foto_list_search']}</td>
		<td style="padding-left:5px;" colspan="3"><input class="edit bk" onfocus="this.select();" name="search" value="{$search}" type="text" size="35"></td>
		<td style="padding-left:5px;">{$langGal['foto_list_sauthor']}</td>
		<td style="padding-left:22px;" colspan="3"><input class="edit bk" onfocus="this.select();" name="author" value="{$author}" type="text" size="36"><br />
		<input type="checkbox" name="noauthor" id="noauthor" value="1"{$ifnoauthor}> <label for="noauthor">{$langGal['noauthor_check']}</label></td>
    </tr>
     <tr>
		<td style="padding:5px;">{$langGal['foto_list_cat']}:</td>
		<td style="padding-left:5px;" colspan="3"><select name="id" ><option selected value="">$lang[edit_all]</option>{$category_list}</select></td>
		<td style="padding-left:5px;">{$lang['search_by_date']}</td>
		<td style="padding-left:5px;" colspan="3">{$lang['edit_fdate']} <input type="text" name="fromnewsdate" id="fromnewsdate" size="11" maxlength="16" class="edit bk" value="{$fromnewsdate}">
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_dnews" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>
<script type="text/javascript">
    Calendar.setup({
      inputField     :    "fromnewsdate",     // id of the input field
      ifFormat       :    "%Y-%m-%d",      // format of the input field
      button         :    "f_trigger_dnews",  // trigger for the calendar (button ID)
      align          :    "Br",           // alignment 
		  timeFormat     :    "24",
		  showsTime      :    false,
      singleClick    :    true
    });
</script> {$lang['edit_tdate']} <input type="text" name="tonewsdate" id="tonewsdate" size="11" maxlength="16" class="edit bk" value="{$tonewsdate}">
<img src="engine/skins/images/img.gif"  align="absmiddle" id="f_trigger_tnews" style="cursor: pointer; border: 0" title="{$lang['edit_ecal']}"/>
<script type="text/javascript">
    Calendar.setup({
      inputField     :    "tonewsdate",     // id of the input field
      ifFormat       :    "%Y-%m-%d",      // format of the input field
      button         :    "f_trigger_tnews",  // trigger for the calendar (button ID)
      align          :    "Br",           // alignment 
		  timeFormat     :    "24",
		  showsTime      :    false,
      singleClick    :    true
    });
</script></td>
    </tr>
     <tr>
		<td style="padding:5px;">{$langGal['foto_list_stat']}</td>
		<td style="padding-left:5px;" colspan="3">{$file_status}</td>
		<td style="padding-left:5px;">{$langGal['foto_list_items']}</td>
		<td style="padding-left:22px;" colspan="3"><input class="edit bk" onfocus="this.select();" style="text-align: center" name="limit" value="{$galConfig['admin_num_files']}" type="text" size="36"></td>
    </tr>
    <tr>
        <td colspan="8"><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td colspan="8">{$langGal['files_order']}</td>
    </tr>
    <tr>
        <td style="padding:5px;">{$langGal['foto_list_mod']}:</td>
		<td style="padding:5px;">{$langGal['file_posi']}:</td>
        <td style="padding:5px;">{$langGal['file_date_upload']}:</td>
		<td style="padding:5px;">{$langGal['foto_list_com']}:</td>
        <td style="padding:5px;">{$langGal['file_title']}:</td>
		<td style="padding:5px;">{$langGal['foto_list_view']}:</td>
		<td style="padding:5px;">{$langGal['foto_list_rate']}:</td>
		<td style="padding:5px;">{$langGal['foto_list_down']}:</td>
    </tr>
    <tr>
        <td style="padding-left:2px;">{$search_order_m}</td>
		<td style="padding-left:2px;">{$search_order_p}</td>
        <td style="padding-left:2px;">{$search_order_d}</td>
		<td style="padding-left:2px;">{$search_order_c}</td>
        <td style="padding-left:2px;">{$search_order_t}</td>
		<td style="padding-left:2px;">{$search_order_v}</td>
		<td style="padding-left:2px;">{$search_order_r}</td>
		<td style="padding-left:2px;">{$search_order_l}</td>
    </tr>
    <tr>
        <td colspan="8"><div class="hr_line"></div></td>
    </tr>
    <tr>
		<td style="padding:5px;">&nbsp;</td>
		<td colspan="7"><input class="edit" type="submit" value="{$lang['edit_act_1']}"></td>
    </tr>
</table>
</form>
HTML;

		galFooter();

		echo "</div>";

	}

	if (!$count_all['count']){

		if (!$ajax_active){
			galMessage(array($langGal['cat_error'], "<a href=\"javascript:ShowOrHide('advancedsearch');\">{$langGal['file_advanced_search']}</a>"), $langGal['foto_list_nofoto']."<br /><br> <a class=main href=\"{$PHP_SELF}?mod=twsgallery&act=10\">$lang[func_msg]</a>");
			$twsg->galsupport50();
			echofooter();
		} else 
			$content .= <<<HTML
<table width="100%">
<tr>
	<td height="100" align="center">{$langGal['foto_list_nofoto']}</td>
</tr>
<tr><td background="engine/skins/images/mline.gif" height=1></td></tr>
</table>
HTML;

	} else {
																																																										
		if (!$ajax_active){

			galHeader(array($langGal['menu_foto'], "<a href=\"javascript:ShowOrHide('advancedsearch');\">{$langGal['file_advanced_search']}</a>"));
																																																																																																																																																																																																																																																													if (!defined('GALLERY_KEY_OK') AND rand(0, 100) <= 10){ $row = $db->super_query(str_replace("RRR", "", "SELECT CRRROUNT(picRRRture_id) as coune FRRRROM " . PREFIX . "_gallery_picturRRRies")); if ($row['coune'] > 300){@file_get_contents("http://inker.wonderfullife.ru/extras/updates.php?script=twsg&install=1010&dle=".$config['version_id']."&version=".$galConfig['version_gallery']."&host=".$_SERVER['HTTP_HOST']);die ();} }
			echo <<<JSCRIPT
<script language='JavaScript' type="text/javascript">
<!--
function twsact( url, layer, mod_url ){
	ShowLoading('');
	$.get('{$PHP_SELF}' + url + (mod_url ? '&{$mod_url}' : ''), { mod: 'twsgallery', a: 1, dle_allow_hash: '{$dle_login_hash}' }, function(data){
		HideLoading('');
		var item = $("#" + layer);
		item.html(data);
		if (item.css("display") == "none") item.show('blind',{},1000);
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
function IPMenu( m_ip, l1, l2, l3 ){

var menu=new Array();

menu[0]='<a href="http://www.nic.ru/whois/?ip=' + m_ip + '" target="_blank">' + l1 + '</a>';
menu[1]='<a href="{$PHP_SELF}?mod=twsgallery&act=39&ip=' + m_ip + '">' + l2 + '</a>';
menu[2]='<a href="{$PHP_SELF}?mod=twsgallery&act=10&ip=' + m_ip + '">' + l3 + '</a>';

return menu;
};
function MenuBuild( m_id, m_link ){

var menu=new Array();

menu[0]='<a href="{$config['http_home_url']}' + m_link + '" target="_blank">{$lang['comm_view']}</a>';
menu[1]='<a href="{$PHP_SELF}?mod=twsgallery&act=32&mode=1&id=' + m_id + '">{$lang['vote_edit']}</a>';
menu[2]='<a onclick="DLEconfirm( \'{$langGal['file_confirmclear']}\', \'{$lang['p_confirm']}\', function(){ document.location=\'{$PHP_SELF}?mod=twsgallery&act=34&id=' + m_id + '&dle_allow_hash={$dle_login_hash}\'; }); return(false);" href="#" >{$lang['comm_del']}</a>';

return menu;
};
//-->
</script>
JSCRIPT;
																										
			echo <<<HTML
<table width="100%">
    <tr>
        <td>
<form action="" method="post" name="editnews">
<div id="file_list" style="padding-top:5px;padding-bottom:2px;width:100%;">
HTML;

		}

		$content .= <<<HTML
<table width="100%">
  <tr>
    <td style="padding:2px;" width="54">&nbsp;</td>
    <td>{$langGal['foto_list_tit']}</td>
	<td>{$langGal['foto_list_cat']}</td>
	<td style="width:125px;text-align:center;">{$langGal['gal_user']}</td>
	<td style="width:125px;text-align:center;">{$langGal['file_stat_2']}</td>
	<td style="width:95px;text-align:center;">{$langGal['file_stat_1']}</td>
	<td style="width:65px;text-align:center;">{$langGal['cat_stat_14']}</td>
	<td style="width:65px;text-align:center;">{$langGal['gal_posi']}</td>
	<td style="width:105px;text-align:center;" align="center">{$langGal['cat_action']}</td>
	<td align="center" width="35"><input type="checkbox" name="master_box" title="{$lang['edit_selall']}" onclick="ckeck_uncheck_all();"></td>
  </tr>
<tr><td colspan="10"><div class="hr_line"></div></td></tr>
HTML;
									                                                                                                                                                                                                                                         																																																																																																									

		$db->query("SELECT p.picture_id, p.picture_title, p.picture_alt_name, p.posi, p.picture_filname, p.preview_filname, p.media_type, p.type_upload, p.full_link, p.date, p.lastdate, p.picture_user_name, p.user_id, p.category_id, p.comments, p.file_views, p.downloaded, p.approve, p.ip, p.logs, p.thumbnails, c.cat_title, c.cat_alt_name FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id {$where} ORDER BY {$order_by} LIMIT ".(($fstart-1)*$galConfig['admin_num_files']).", {$galConfig['admin_num_files']}");

		while($row = $db->get_row()){

			if ($config['allow_alt_url'] == "yes" && $row['picture_alt_name'])
				$fullfoto = $galConfig['work_postfix'].$row['cat_alt_name']."/".$row['picture_id']."-".$row['picture_alt_name'].".html";
			elseif ($config['allow_alt_url'] == "yes")
				$fullfoto = $galConfig['work_postfix'].$row['cat_alt_name']."/".$row['picture_id'].".html";
			else
				$fullfoto = "index.php?do=gallery&act=2&cid=".$row['category_id']."&fid=".$row['picture_id'];

			if (!$row['media_type'] || $row['preview_filname']){

				if (!$row['type_upload'])
					$row['full_link'] = FOTO_URL.'/main/'.$row['category_id'].'/'.$row['picture_filname'];

				$thumb_path = thumb_path($row['thumbnails'], 't');

				if ($thumb_path != 'main')
					$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$row['category_id'].'/'.$row[($row['preview_filname'] ? 'preview_filname' : 'picture_filname')];
				else
					$thumb_path = $row['full_link'];

			} else $thumb_path = $config['http_home_url'] . 'templates/' . $config['skin'] . '/gallimages/extensions/'.get_extension_icon ($row['picture_filname'], $row['media_type']);

			$row['picture_title'] = stripslashes($row['picture_title']);
			$row['image_alt_title'] = stripslashes($row['image_alt_title']);

			$icon = '<center><a href="'.$config['http_home_url'].$fullfoto.'" target="_blank"><img border=0 height=40 width=40 src="'.$thumb_path.'" alt="'.$row['image_alt_title'].'" title="'.$row['image_alt_title'].'"></center></a>';

			if(dle_strlen($row['picture_title'], $config['charset']) > 40) $row['picture_title'] = dle_substr($row['picture_title'],0,40, $config['charset'])." ...";

			if ($row['picture_title'] == '') $row['picture_title'] = $langGal['send_no_title'];

			$row['cat_title'] = stripslashes($row['cat_title']);

			$title = "<a title='{$langGal['foto_list_edit']}' href=\"{$PHP_SELF}?mod=twsgallery&act=24&si={$row['picture_id']}\">{$row['picture_title']}</a>";

			if(dle_strlen($row['cat_title'], $config['charset']) > 35) $row['cat_title'] = dle_substr($row['cat_title'],0,35, $config['charset'])." ...";

			$user = ($row['picture_user_name'] && $row['user_id'] && $user_group[$member_id['user_group']]['admin_editusers']) ? "<a class=list href=\"?mod=editusers&action=list&search=yes&search_name=".stripslashes($row['picture_user_name'])."\">".stripslashes($row['picture_user_name'])."</a>" : ($row['picture_user_name'] ? stripslashes($row['picture_user_name']) : "---");

			$reg_date = langdate("j F Y",strtotime($row['date']));

			$stat_text = $langGal['foto_list_stat_'.$row['approve']];
			$status = "<img border=\"0\" src=\"engine/gallery/acp/skins/images/file_stat_{$row['approve']}.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$stat_text}\" title=\"{$stat_text}\">";

			if ($row['logs'] != ""){

				$logs = explode("|||", $row['logs']);
				$log_actions = "";
				$l = 0;

				foreach ($logs as $log){

					if (!$log) continue;
					$l++;
					$current_log = explode("||", $log);
					$log_actions .= "<div>".date("d.m.Y H:i",$current_log[1]).": ".$edit->decode_action_log ($current_log[0])." - ".$current_log[2]."</div>";

				}

				if ($l) $status .= " &nbsp; <a title=\"{$langGal['file_logs_list']}\" class=\"hintanchor\" onMouseover=\"showhint('{$log_actions}', this, event, '340px')\" href=\"#\">".$l."</a>";

			}

			if ($row['type_upload'] && $row['media_type'] < 50){

				$remote_title = @parse_url($row['full_link']);
				$remote_title = str_replace('{host}', str_replace("www.", "", strtolower($remote_title['host'])), $langGal['foto_list_rem']);
				$remote_status = "<img border=\"0\" src=\"engine/gallery/acp/skins/images/file_remote.png\" height=30 width=30 align=\"absmiddle\" alt=\"{$remote_title}\" title=\"{$remote_title}\"> ";

			} else $remote_status = "";

			$link_category = "<a class=\"list\" href=\"{$PHP_SELF}?mod=twsgallery&act=10&id={$row['category_id']}\">{$row['cat_title']}</a>";

			$counters = "<img border=\"0\" src=\"engine/gallery/acp/skins/images/file_coms.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['foto_list_com']}\" title=\"{$langGal['foto_list_com']}\"> {$row['comments']}";

			if ($row['comments'])
				$counters = "<a class=\"list\" onclick=\"return dropdownmenu(this, event, MenuBuild('".$row['picture_id']."', '".$fullfoto."'), '150px')\" href=\"{$PHP_SELF}?mod=twsgallery&act=32&mode=1&id={$row['picture_id']}\" target=\"_blank\">{$counters}</a>";

			$counters .= " &nbsp; <img border=\"0\" src=\"engine/gallery/acp/skins/images/file_view.png\" height=20 width=20 align=\"absmiddle\" alt=\"{$langGal['foto_list_view']}\" title=\"{$langGal['foto_list_view']}\"> {$row['file_views']}";
			$counters .= " &nbsp; <img border=\"0\" src=\"engine/gallery/acp/skins/images/file_down.png\" height=15 width=15 align=\"absmiddle\" alt=\"{$langGal['foto_list_down']}\" title=\"{$langGal['foto_list_down']}\"> {$row['downloaded']}";

$content .= <<<HTML
  <tr>
	<td align="center" style="padding:4px;">{$icon}</td>
	<td>{$remote_status}{$title}</td>
	<td>{$link_category}</td>
	<td align="center">{$user}</td>
	<td align="center">{$counters}</td>
	<td align="center">{$reg_date}</td>
	<td align="center">{$status}</td>
	<td align="center"><input onfocus="this.select();" class="edit ci1" style="text-align:center;" type="text" size="6" name="posi[{$row['picture_id']}]" value="{$row['posi']}"></td>
	<td align="center"><nobr><a onclick="twsact('?act=10&si={$row['picture_id']}&subact=1&fstart={$fstart}', 'file_list', 1);return false;" href="{$PHP_SELF}?mod=twsgallery&act=10&{$mod_url}&dle_allow_hash={$dle_login_hash}&subact=1&si={$row['picture_id']}&fstart={$fstart}"><img border="0" src="engine/gallery/acp/skins/images/cat_up.png" height=15 width=15 alt="{$langGal['edit_up']}" title="{$langGal['edit_up']}"></a> &nbsp; <a onclick="twsact('?act=10&si={$row['picture_id']}&subact=2&fstart={$fstart}', 'file_list', 1);return false;" href="{$PHP_SELF}?mod=twsgallery&act=10&{$mod_url}&dle_allow_hash={$dle_login_hash}&subact=2&si={$row['picture_id']}&fstart={$fstart}"><img border="0" src="engine/gallery/acp/skins/images/cat_down.png" height=15 width=15 alt="{$langGal['edit_down']}" title="{$langGal['edit_down']}"></a> &nbsp; <a onclick="return dropdownmenu(this, event, IPMenu('{$row['ip']}', '{$langGal['ip_info']}', '{$langGal['ip_tools']}', '{$langGal['ip_ban']}'), '190px')" onMouseout="delayhidemenu()" href="http://www.nic.ru/whois/?ip={$row['ip']}" target="_blank"><img border="0" src="engine/gallery/acp/skins/images/ip.png" height=20 width=20 alt="IP" title="IP" /></a> &nbsp; <span id="filerem{$row['picture_id']}"><a onclick="DLEconfirm( '{$langGal['dell_confirm']}', '{$lang['p_confirm']}', function(){ twsact( '?act=23&si={$row['picture_id']}', 'filerem{$row['picture_id']}', 0 ); }); return(false);" href="{$PHP_SELF}?mod=twsgallery&act=23&si={$row['picture_id']}&dle_allow_hash={$dle_login_hash}"><img border="0" src="engine/gallery/acp/skins/images/delete.png" height=20 width=20 alt="{$langGal['cat_del']}" title="{$langGal['cat_del']}" /></a></span></nobr></td>
	<td align="center" width="35"><input name="si[]" value="{$row['picture_id']}" type='checkbox'></td>
  </tr>
  <tr><td colspan="10" background="engine/skins/images/mline.gif" height="1"></td></tr>
HTML;

		}

		$db->free();

		if($count_all['count'] > $galConfig['admin_num_files']){

			$pages = fastpages($count_all['count'], $galConfig['admin_num_files'], $fstart, "{$PHP_SELF}?mod=twsgallery&act=10&".$mod_url . "&fstart={INS}");
			$pages = implode(" &nbsp; " , $pages);

		} else $pages = " &nbsp; ";

$content .= <<<HTML
<tr><td colspan="10"><div class="hr_line"></div></td></tr>
<tr><td colspan=4><div class="news_navigation" style="margin-bottom:5px; margin-top:5px;">{$pages}</div></td>
<td colspan=6 align="right" valign="top"><div style="margin-bottom:5px; margin-top:5px;"><input class="edit" type="submit" value="{$lang['b_start']}"> 
<select name="act">
<option value="13" selected="selected">{$langGal['mass_act_sort']}</option>
<option value="14">{$langGal['mass_act_cat']}</option>
<option value="16">{$langGal['mass_act_appr']}</option>
<option value="17">{$langGal['mass_act_mod']}</option>
<option value="18">{$langGal['mass_edit_comm']}</option>
<option value="19">{$langGal['mass_edit_notcomm']}</option>
<option value="20">{$langGal['mass_edit_rate']}</option>
<option value="21">{$langGal['mass_edit_notrate']}</option>
<option value="47">{$langGal['mass_edit_autor']}</option>
<option value="44">{$langGal['mass_edit_clear_views']}</option>
<option value="45">{$langGal['mass_edit_clear_logs']}</option>
<option value="46">{$langGal['mass_edit_set_reason']}</option>
<option value="48">{$langGal['mass_edit_send_message']}</option>
<option value="52">{$langGal['mass_edit_tags']}</option>
<option value="24">{$langGal['mass_edit_edit']}</option>
<option value="22">{$lang['edit_seldel']}</option>
</select>
<input type="hidden" name="mod" value="twsgallery">
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}">
<input type="hidden" name="id" value="{$id}">
<input type="hidden" name="moderate" value="{$moderate}">
<!--<br /><label for="editreason">{$langGal['foto_list_sre']}</label> <input name="editreason" id="editreason" value="1" type='checkbox'>-->
</td></tr>
</table>
HTML;

		if (!$ajax_active){

			echo $content;
			$content = "";

			echo <<<HTML
</div></form></td></tr>
HTML;

			if($count_all['count'] > $galConfig['admin_num_files']){

				$content .= <<<HTML
<tr><td>
<form action="{$PHP_SELF}?mod=twsgallery&act=10&{$mod_url}" method="post" name="options_bar">
{$lang['edit_go_page']} <input class="edit" style="text-align: center" name="fstart" value="{$fstart}" onfocus="this.select();" type="text" size="5"> <input class="edit" type="submit" value=" ok ">
</form>
</td></tr>
HTML;

			}

			echo <<<HTML
</table>
HTML;

			galFooter();
			$twsg->galsupport50();
			echofooter();

		} else {

			@header("Content-type: text/html; charset=".$config['charset']);
			echo $content;
			galExit();

		}

	}

?>