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
 Файл: newfoto.php
-----------------------------------------------------
 Назначение: Новые фотографии
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	$parametrs = array('story' => false,'search_code' => false,'symbol' => false,'tag' => false,'user' => false,'cat' => false,'sort' => false);
	$search_parametrs = '';
	$cache_newfoto_id = '';

	if (isset($_REQUEST['p'])){

		if (substr($_REQUEST['p'],-1, 1) == '/') $_REQUEST['p'] = substr($_REQUEST['p'], 0, -1);

		if ($_REQUEST['p'] != "")
			$temp_params = explode("/", $_REQUEST['p']);
		else
			$temp_params = array();

		for($i=0; $i < count($temp_params); $i++){
			$temp_params[$i] = explode("-", $temp_params[$i]);
			if (count($temp_params[$i]) < 2) continue;
			$temp_index_param = totranslit($temp_params[$i][0], true, false);
			unset($temp_params[$i][0]);
			$parametrs[$temp_index_param] = implode('-', $temp_params[$i]);
		}

		unset($temp_params);

	} else $_REQUEST['p'] = "";

	if ($config['allow_alt_url'] == "yes")
		$this_url = $galConfig['mainhref'].'all/';
	else
		$this_url = $galConfig['mainhref'].'&act='.$act.'&p=';

	if ($galConfig['foto_td'] < 1) $galConfig['foto_td'] = 2;
	if ($galConfig['foto_tr'] < 1) $galConfig['foto_tr'] = 8;

	$fotolimit = $galConfig['foto_tr'] * $galConfig['foto_td'];

	$fsort = foto_sort();

	if (!$fsort['sort_set'] || $parametrs['sort'] != 'user' && !defined('USER_SET_SORT')) $fsort = array('sort' => 'date', 'msort' => 'desc');

	if ($parametrs['sort'] == 'user' || defined('USER_SET_SORT')) $this_url .= 'sort-user/';

	switch ($act){
	case 16 :

		$metatags['title'] = $langGal['menu_title8'];

		if (!$is_logged){

			msgbox($langGal['all_info'], $langGal['access_error']."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
			return;

		}

		$main_sql_where = array();

		$main_sql_where[] = "(c.edit_level".(check_gallery_access ("edit", "", "") ? " IN ('-1','')" : "='-1'")." OR c.edit_level regexp '[[:<:]]({$member_id['user_group']})[[:>:]]' OR c.moderators regexp '[[:<:]]({$member_id['user_id']})[[:>:]]')";
		if (!check_gallery_access ("edit", "", "")) $main_sql_where[] = "c.locked=0";
		$main_sql_where[] = "p.approve=0";

		$main_sql_where = implode(" AND ", $main_sql_where);

		$found_num = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE {$main_sql_where}");

		if (!$found_num['count']){

			msgbox($langGal['all_info'], $langGal['no_new_foto2']."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
			return;

		}

		$count_foto = $found_num['count'];

	break;
	default:

		if (!$is_logged && $galConfig['allow_cache'] > 1 && !$fsort['sort_set'] && $fstart < 6){

			$cache_newfoto_id = 'all_files_newfoto_'.md5($_REQUEST['p'].$fotolimit.$fsort['sort'].$fsort['msort'].$fstart);

			if (($tpl->result['content'] = get_gallery_cache ($cache_newfoto_id)) !== false){

				$tpl->result['content'] = unserialize($tpl->result['content']);

				$metatags['title'] = $tpl->result['content'][1];
				$s_navigation = $tpl->result['content'][2];
				$tpl->result['content'] = $tpl->result['content'][0];			

				return;
			}

		}

		foreach (array('symbol', 'user', 'tag') as $key)
			if (trim($parametrs[$key])){

				$parametrs[$key] = @strip_tags(str_replace('/', '', trim(urldecode($parametrs[$key]))));

				if ($config['charset'] == "windows-1251" && md5($parametrs[$key]) != md5(iconv('windows-1251', 'windows-1251', $parametrs[$key])))
					$parametrs[$key] = iconv( "UTF-8", "windows-1251//IGNORE", $parametrs[$key]);

				switch ($key){
				case 'user' : if (preg_match("/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $parametrs['user'])) $parametrs['user'] = ""; break;
				case 'tag' : $parametrs['tag'] = htmlspecialchars($parametrs['tag'], ENT_COMPAT, $config['charset']); break;
				}

				$parametrs[$key] = @$db->safesql($parametrs[$key]); 

			}

		if ($parametrs['cat']) $parametrs['cat'] = intval($parametrs['cat']);

		if (isset($_POST['story']) || isset($_GET['story'])){

			$parametrs['story'] = isset($_POST['story']) ? stripslashes($_POST['story']) : urldecode($_POST['story']);

			$findstory = '';
			$story = "";

			$quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "'", ",", "/", "¬", ";", ":", "@", "~", "[", "]", "{", "}", "=", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"' );
			$goodquotes = array ("-", "+", "#" );
			$repquotes = array ("\-", "\+", "\#" );
			$parametrs['story'] = trim( strip_tags($parametrs['story']) );
			$parametrs['story'] = str_replace( $goodquotes, $repquotes, str_replace( $quotes, '', $parametrs['story'] ) );
			$parametrs['story'] = dle_substr($parametrs['story'], 0, 84, $config['charset']);
			$parametrs['story'] = preg_replace("#^(\s*OR\s+)*#i", '', $parametrs['story']);
			$parametrs['story'] = preg_replace("#(\s+OR\s*)*$#i", '', $parametrs['story']);

			$arr = explode(' ', trim($parametrs['story']));
			$story_maxlen = 0;
			$parametrs['story'] = array();
			
			foreach ($arr as $word)
				if (dle_strlen(trim($word), $config['charset']) >= $config['search_length_min'] ) $parametrs['story'][] = $word;
		
			$parametrs['story'] = implode(" ", $parametrs['story']);

			if (empty($parametrs['story'])){

				msgbox($langGal['all_info'], $langGal['search_err_3']."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
				return;

			}

		}

		include_once TWSGAL_DIR.'/modules/search.php';

		$result = search_gallery_cache($parametrs, $fstart, $fotolimit);

		if (!is_array($result)){

			switch ($result){
			case 2 :
				$error_text = str_ireplace('{group}', $user_group[$member_id['user_group']]['group_name'], $lang['search_denied']);
			break;
			case 3 :
				$error_text = $langGal['search_err_2'];
				@header("HTTP/1.0 404 Not Found");
			break;
			default :
				$error_text = $langGal['unknown'];
			}

			msgbox($langGal['all_info'], $error_text."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
			return;

		}

		$metatags['title'] = array();

		if ($result['search_code']) $this_url .= 'search_code-'.$result['search_code'].'/';

		if ($result['cat']){
			$this_url .= 'cat-'.$result['cat'].'/';
			$cat = $db->super_query("SELECT cat_title FROM " . PREFIX . "_gallery_category WHERE id={$result['cat']}");
			$metatags['title'][] = str_ireplace('{cat}', stripslashes($cat['cat_title']), $langGal['menu_title18']);
		}

		if ($result['symbol']){
			$result['symbol'] = stripslashes($result['symbol']);
			$this_url .= 'symbol-'.urlencode($result['symbol']).'/';
			$metatags['title'][] = str_ireplace('{symbol}', $result['symbol'], $langGal['menu_title17']);
		}

		if ($result['user']){
			$result['user'] = stripslashes($result['user']);
			$this_url .= 'user-'.urlencode($result['user']).'/';
			$metatags['title'][] = str_ireplace('{user}', $result['user'], $langGal['menu_title16']);
		}

		if ($result['story']){
			$result['story'] = stripslashes($result['story']);
			$this_url .= 'story-'.urlencode($result['story']).'/';
			$metatags['title'][] = str_ireplace('{find}', $result['story'], $langGal['menu_title15']);
		}

		if ($result['tag']){
			$result['tag'] = stripslashes($result['tag']);
			$this_url .= 'tag-'.urlencode($result['tag']).'/';
			$metatags['title'][] = str_ireplace('{tag}', $result['tag'], $langGal['menu_title19']);
		}

		$metatags['title'] = !count($metatags['title']) ? $langGal['menu_title7'] : implode(" &raquo; ", $metatags['title']);

		$count_foto = $result['search_num'];

		$main_sql_where = "p.picture_id IN (".implode(',', $result['find_files']).")";
		$main_sql_limit = " LIMIT 0,{$fotolimit}";
		$search_parametrs = ','.$result['search_code'].',{reali}';

	}

//***************************************************************************************
//               Построение запроса закончили, выводим результаты поиска
//***************************************************************************************

	if ($fstart > 1) $metatags['title'] .= ' &raquo; '.$lang['news_site'].' '.$fstart;

	$s_navigation .= " &raquo; " . $metatags['title'];

	$template = 'short_image';

	include TWSGAL_DIR.'/modules/show.foto.php';

	$tpl->load_template('gallery/category.tpl');

	if ($galConfig['dinamic_symbols'] && strpos($tpl->copy_template, "{symbols}" ) !== false) $tpl->set('{symbols}', gallery_symbols());

	$tpl->set('{imageslist}', $tpl->result['fotolistrow']);
	$tpl->result['fotolistrow'] = "";
	$tpl->set('{pages}', $tpl->result['fastnav']);
	$tpl->result['fastnav'] = "";

	$tpl->set_block("'\\[create\\](.*?)\\[/create\\]'si","");
	$tpl->set_block("'\\[categories\\](.*?)\\[/categories\\]'si","");
	$tpl->set_block("'\\[nothing\\](.*?)\\[/nothing\\]'si","");
	$tpl->set_block("'\\[foto\\](.*?)\\[/foto\\]'si","");
	$tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");
	$tpl->set_block("'\\[locked\\](.*?)\\[/locked\\]'si","");
	$tpl->set_block("'\\[disupload\\](.*?)\\[/disupload\\]'si","");
	$tpl->set_block("'\\[in_category\\](.*?)\\[/in_category\\]'si","");
	$tpl->set('[images]', '');
	$tpl->set('[/images]', '');
	$tpl->set('{icon}', '');

	$sort_array = array();

	foreach (array("posi"=>$langGal['opt_sys_sort_o'],"date"=>$langGal['opt_sys_sdate'],"rating"=>$langGal['opt_sys_srate'],"file_views"=>$langGal['opt_sys_sview'],"comments"=>$langGal['opt_sys_img_com'],"picture_title"=>$langGal['opt_sys_salph']) as $index => $value)
		$sort_array[] = ($index == $fsort['sort'] ? "<img src=\"{THEME}/dleimages/".($fsort['msort'] == "asc" ? "asc" : "desc").".gif\" alt=\"\" />" : "") . "<a href=\"javascript:void(0);\" onclick=\"gallery_change_sort('{$index}','".($index == $fsort['sort'] ? ($fsort['msort'] == "asc" ? "desc" : "asc") : "")."'); return false;\">{$value}</a>";

	$tpl->set('{sort}', $langGal['sort_main'] . "&nbsp;" . implode( " | ", $sort_array ));

	$tpl->set('[upload]', '<a href="'.$galConfig['PHP_SELF'].'&act=26">');
	$tpl->set('[/upload]', '</a>');

	if ($galConfig['allow_comments']){

		$tpl->set('[comments]', '<a href="'.$galConfig['PHP_SELF'].'&act=4">');
		$tpl->set('[/comments]', '</a>');

	} else $tpl->set_block("'\\[comments\\](.*?)\\[/comments\\]'si","");

	if ($_admin < 1){
		$tpl->set('[massactions]','');
		$tpl->set('[/massactions]','');
		$tpl->set('{massactions}','');
	} else {
		$tpl->set('[massactions]',"<script language='JavaScript' type=\"text/javascript\">
<!--
function ckeck_uncheck_all() {
var frm = document.editnews;
for (var i=0;i<frm.elements.length;i++) {
var elmnt = frm.elements[i];
if (elmnt.type=='checkbox') {
	if(frm.master_box.checked == false){ elmnt.checked=false; }
	else{ elmnt.checked=true; }
}
}
if(frm.master_box.checked != true){ frm.master_box.checked = false; }
else{ frm.master_box.checked = true; }
}
-->
</script><form action=\"{$galConfig['mainhref']}\" method=\"post\" id=\"editnews\" name=\"editnews\">");
$tpl->set('[/massactions]',"</form>");
$tpl->set('{massactions}',"<div class=\"mass_comments_action\"><label for=\"master_box\">{$langGal['mass_selall']}</label> <input type=\"checkbox\" id=\"master_box\" name=\"master_box\" onclick=\"javascript:ckeck_uncheck_all()\">&nbsp; &nbsp; ".
makeDropDownGallery(array("0"=>$langGal['mass_action'],"5"=>$langGal['mass_act_appr'],"6"=>$langGal['mass_act_notappr'],"7"=>$langGal['mass_act_cat'],"9"=>$langGal['mass_act_comm'],"10"=>$langGal['mass_act_notcomm'],"11"=>$langGal['mass_act_rate'],"12"=>$langGal['mass_act_notrate'],"8"=>$langGal['mass_edit_delete'],"17"=>$langGal['mass_edit_edit']), "act", "0")
."<input type=\"submit\" value=\"{$langGal['mass_act_do']}\" name=\"submit\" class=\"bbcodes_poll\" />
<input type=\"hidden\" name=\"dle_allow_hash\" value=\"".$dle_login_hash."\"></div>");

	}

	if ($gallery_referrer && !isset($_SESSION['gallery_referrer']) || $_SESSION['gallery_referrer'] != $_SERVER['REQUEST_URI'])
		$_SESSION['gallery_referrer'] = $_SERVER['REQUEST_URI'];

	$tpl->set('{cattitle}', $metatags['title']);
	$tpl->set('{navigator}', $s_navigation);

	$tpl->compile('content');
	$tpl->clear();

	if ($cache_newfoto_id)
		create_gallery_cache ($cache_newfoto_id, serialize(array($tpl->result['content'], $metatags['title'], $s_navigation)));

?>