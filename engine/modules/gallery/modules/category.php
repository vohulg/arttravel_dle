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
 Файл: category.php
-----------------------------------------------------
 Назначение: Вывод категорий
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	if (!$this_album || !$this_album['id'] || (($allow_edit = check_gallery_access ("edit", $this_album['edit_level'], $this_album['moderators'])) != 2 && !check_gallery_access ("read", $this_album['view_level'], "", $this_album['locked']))){
		@header("HTTP/1.0 404 Not Found");
		msgbox ($langGal['all_err_1'], $langGal['no_that_cat']."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
		return;
	}

	$fsort = foto_sort($this_album);
	$cache_category_id = '';

	if (!$is_logged && $galConfig['allow_cache'] > 1 && !$fsort['sort_set'] && $fstart < 6 && $cstart < 6){

		$cache_category_id = 'all_files_'.$fstart.$cstart.$this_album['id'];

		if (($tpl->result['content'] = get_gallery_cache ($cache_category_id)) !== false){

			$tpl->result['content'] = unserialize($tpl->result['content']);

			$metatags['title'] = $tpl->result['content'][1];
			$metatags['description'] = $tpl->result['content'][2];
			$metatags['keywords'] = $tpl->result['content'][3];
			$s_navigation = $tpl->result['content'][4];
			$tpl->result['content'] = $tpl->result['content'][0];			

			return;
		}

	}

	$attention = array();

	if ($this_album['locked']) $attention[] = $langGal['attention_2'];
	if ($this_album['disable_upload'] && $allow_edit == 2) $attention[] = $langGal['attention_3'];

	if (count($attention)) msgbox ($langGal['all_info'], str_replace("{errors}", implode(", ", $attention), $langGal['attention_1']));

	if ($config['allow_alt_url'] == "yes")
		$this_url = $galConfig['mainhref'] . $this_album['cat_alt_name']."/";
	else 
		$this_url = $galConfig['mainhref'] . "&act=1&cid=". $this_album['id'];

	$count_cats = 0;

	if ($fstart == 1 && $this_album['sub_cats']){

		$main_sql_where = array("p_id={$_album_id}");

		include TWSGAL_DIR.'/modules/show.cats.php';

	}

	$search_parametrs = '';
	$main_sql = "";
	$main_sql_where = "";
	$compile = '';
	$template = '';
	$fsort = array();
	$_admin = 0;

	include TWSGAL_DIR.'/modules/show.foto.php';

	$tpl->load_template('gallery/'.(($this_album['maincatskin']) ? $this_album['maincatskin'] : 'category').'.tpl');

	if ($galConfig['dinamic_symbols'] && strpos($tpl->copy_template, "{symbols}" ) !== false) $tpl->set('{symbols}', gallery_symbols());

	if ($count_cats){

		$tpl->set('[categories]', '');
		$tpl->set('[/categories]', '');
		$tpl->set('{categories}', $tpl->result['listrow']);
		$tpl->result['listrow'] = "";
		$tpl->set('{category_pages}', $pageslist);
		$pageslist = "";

	} else $tpl->set_block("'\\[categories\\](.*?)\\[/categories\\]'si","");

	if ($count_foto){

		$tpl->set('[images]', '');
		$tpl->set('[/images]', '');
		$tpl->set('{imageslist}', $tpl->result['fotolistrow']);
		$tpl->result['fotolistrow'] = "";
		$tpl->set('{pages}', $tpl->result['fastnav']);
		$tpl->result['fastnav'] = "";

		$sort_array = array();

		foreach (array("posi"=>$langGal['opt_sys_sort_o'],"date"=>$langGal['opt_sys_sdate'],"rating"=>$langGal['opt_sys_srate'],"file_views"=>$langGal['opt_sys_sview'],"comments"=>$langGal['opt_sys_img_com'],"picture_title"=>$langGal['opt_sys_salph']) as $index => $value)
			$sort_array[] = ($index == $fsort['sort'] ? "<img src=\"{THEME}/dleimages/".($fsort['msort'] == "asc" ? "asc" : "desc").".gif\" alt=\"\" />" : "") . "<a href=\"javascript:void(0);\" onclick=\"gallery_change_sort('{$index}','".($index == $fsort['sort'] ? ($fsort['msort'] == "asc" ? "desc" : "asc") : "")."'); return false;\">{$value}</a>";

		$tpl->set('{sort}', $langGal['sort_main'] . "&nbsp;" . implode( " | ", $sort_array ));

	} else $tpl->set_block("'\\[images\\](.*?)\\[/images\\]'si","");

	if ($count_foto || $count_cats){
		$tpl->set_block("'\\[nothing\\](.*?)\\[/nothing\\]'si","");
	} else {
		$tpl->set('[nothing]', '');
		$tpl->set('[/nothing]', '');
	}

	if (!$this_album['allow_user_admin']){
		$tpl->set('[not-usercategory]', '');
		$tpl->set('[/not-usercategory]', '');
		$tpl->set_block("'\\[usercategory\\](.*?)\\[/usercategory\\]'si","");
	} else {
		$tpl->set('[usercategory]', '');
		$tpl->set('[/usercategory]', '');
		$tpl->set_block("'\\[not-usercategory\\](.*?)\\[/not-usercategory\\]'si","");
	}

	$this_album['cat_title'] = stripslashes($this_album['cat_title']);

	if ($this_album['metatitle'] != '')
		$metatags['header_title'] = stripslashes($this_album['metatitle']);
	else
		$metatags['title'] = $this_album['cat_title'];

	if ($config['allow_alt_url'] == "yes")
		$path = "<a href=\"".$galConfig['mainhref'].$this_album['cat_alt_name']."/\">".$this_album['cat_title']."</a>";
	else
		$path = "<a href=\"".$galConfig['mainhref']."&act=1&cid={$this_album['id']}\">".$this_album['cat_title']."</a>";

	$parent_id = $this_album['p_id'];

	while ($parent_id){

		$parent_data = $db->super_query("SELECT p_id, cat_title, cat_alt_name FROM " . PREFIX . "_gallery_category WHERE id='{$parent_id}'");

		if (!$parent_data['cat_title']) break;

		$parent_data['cat_title'] = stripslashes($parent_data['cat_title']);

		if ($config['allow_alt_url'] == "yes")
			$path = "<a href=\"".$galConfig['mainhref'].$parent_data['cat_alt_name']."/\">".$parent_data['cat_title']."</a> &raquo; ".$path;
		else
			$path = "<a href=\"".$galConfig['mainhref']."&act=1&cid={$parent_id}\">".$parent_data['cat_title']."</a> &raquo; ".$path;

		if ($this_album['metatitle'] == '')
			$metatags['title'] .= ' &raquo; ' . $parent_data['cat_title'];

		$parent_id = $parent_data['p_id'];

	}

	if ($fstart > 1 || $cstart > 1) $metatags[($this_album['metatitle'] == '' ? 'title' : 'header_title')] .= ' &raquo; '.$lang['news_site'].' '.max($fstart, $cstart);

	$s_navigation .= " &raquo; " . $path;

	if ($this_album['user_name']){

		$user_name = stripslashes($this_album['user_name']);
		$encoded = urlencode($user_name);

		if ($config['allow_alt_url'] == "yes")
			$url_user =  $config['http_home_url']."user/".$encoded."/";
		else
			$url_user =  $config['http_home_url']."index.php?subaction=userinfo&amp;user=".$encoded;

		$menu = " onclick=\"return dropdownmenu(this, event, GalUserMenu('".htmlspecialchars($url_user, ENT_QUOTES, $config['charset'])."', '".$encoded."'), '220px')\" onMouseout=\"delayhidemenu()\"";

		$tpl->set('[author-link]', "<a{$menu} href=\"".$url_user."\">");
		$tpl->set('[/author-link]', '</a>');
		$tpl->set('{author}', $user_name);
		$tpl->set('{profile_author}', "<a onclick=\"ShowProfile('{$encoded}', '".htmlspecialchars($url_user, ENT_QUOTES, $config['charset'])."', gallery_admin_editusers); return false;\" href=\"".$url_user."\">".$user_name."</a>");

	} else {

		$tpl->set('[author-link]', '');
		$tpl->set('[/author-link]', '');
		$tpl->set('{author}', '--');
		$tpl->set('{profile_author}', '--');

	}

	if ($galConfig['icon_type'] && $this_album['locked'])
		$tpl->set('{icon}', "<img src=\"{THEME}/gallimages/access_den.gif\" border=\"0\" />");
	elseif ($this_album['icon'] != '')
		$tpl->set('{icon}', "<img src=\"".str_replace('{FOTO_URL}', FOTO_URL, $this_album['icon'])."\" border=\"0\" />");
	elseif ($galConfig['icon_type'] && !$this_album['images'])
		$tpl->set('{icon}', "<img src=\"{THEME}/gallimages/no_foto.gif\" border=\"0\" />");
	else
		$tpl->set('{icon}', '');

	$this_album['reg_date'] = strtotime($this_album['reg_date']);
	$this_album['last_date'] = $this_album['last_date'] != '0000-00-00 00:00:00' ? strtotime($this_album['last_date']) : $this_album['reg_date'];

	if (($tdate = date('Ymd', $this_album['reg_date'])) == date('Ymd', TIME))
		$tpl->set('{created}', $lang['time_heute'].langdate(", H:i", $this_album['reg_date']));
	elseif ($tdate == date('Ymd', (TIME - 86400)))
		$tpl->set('{created}', $lang['time_gestern'].langdate(", H:i", $this_album['reg_date']));
	else
		$tpl->set( '{created}', langdate($galConfig['timestamp_active'], $this_album['reg_date']));

	if (($tdate = date('Ymd', $this_album['last_date'])) == date('Ymd', TIME))
		$tpl->set('{updated}', $lang['time_heute'].langdate(", H:i", $this_album['last_date']));
	elseif ($tdate == date('Ymd', (TIME - 86400)))
		$tpl->set('{updated}', $lang['time_gestern'].langdate(", H:i", $this_album['last_date']));
	else
		$tpl->set( '{updated}', langdate($galConfig['timestamp_active'], $this_album['last_date']));

	$tpl->set('{cattitle}', $this_album['cat_title']);
	$tpl->set('{navigator}', $s_navigation);
	$tpl->set('[in_category]', '');
	$tpl->set('[/in_category]', '');

	if ($this_album['cat_short_desc']) {
		$tpl->set('[description]', '');
		$tpl->set('[/description]', '');
		$tpl->set('{description}', stripslashes($this_album['cat_short_desc']));
	} else $tpl->set_block("'\\[description\\](.*?)\\[/description\\]'si","");

	$tpl->set('{images}', $this_album['images']);
	$tpl->set('{subcats}', $this_album['sub_cats']);

	if (!$this_album['locked'])
		$tpl->set_block("'\\[locked\\](.*?)\\[/locked\\]'si","");
	else {
		$tpl->set('[locked]', '');
		$tpl->set('[/locked]', '');
	}

	if (!$this_album['disable_upload'])
		$tpl->set_block("'\\[disupload\\](.*?)\\[/disupload\\]'si","");
	else {
		$tpl->set('[disupload]', '');
		$tpl->set('[/disupload]', '');
	}

	if (check_gallery_access ("edit", "")){

		$tpl->set('[create]', '<a href="'.$galConfig['PHP_SELF'].'&act=19&dle_allow_hash='.$dle_login_hash.'&r='.$_album_id.'">');
		$tpl->set('[/create]', '</a>');

	} elseif (check_gallery_access ("addcat", "")){

		$tpl->set('[create]', '<a href="'.$galConfig['PHP_SELF'].'&act=24">');
		$tpl->set('[/create]', '</a>');

	} else $tpl->set_block("'\\[create\\](.*?)\\[/create\\]'si","");

	if (($adm_access = check_gallery_access ("edit", $this_album['edit_level'], $this_album['moderators'])) == 2 || (!$this_album['locked'] && $is_logged && $this_album['allow_user_admin'] && $member_id['name'] == $this_album['user_name'])){
		if ($adm_access == 2 && $member_id['user_group'] != 1) $adm_access = 1;
		elseif ($adm_access == 1) $adm_access = 0;
		if ($adm_access > 0) $js_options['admin'] = true;
		$tpl->set('[edit]',"<a onclick=\"return dropdownmenu(this, event, MenuGalCat('".$_album_id."', '".$adm_access."'), '220px')\" href=\"".$config['http_home_url']."index.php?do=gallery&act=".($adm_access ? "19" : "24")."&si=".$_album_id."\">");
		$tpl->set('[/edit]',"</a>");
	} else $tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");

	if (!$this_album['disable_upload'] && check_gallery_access ("upload", $this_album['upload_level'], $this_album['moderators'], $this_album['mod_level'], $this_album['locked'], $this_album['user_name'], $this_album['allow_user_admin'])){

		$tpl->set('[upload]', '<a href="'.$galConfig['PHP_SELF'].'&act=26&cat='.$_album_id.'">');
		$tpl->set('[/upload]', '</a>');

	} else $tpl->set_block("'\\[upload\\](.*?)\\[/upload\\]'si","");

	if ($config['allow_alt_url'] == "yes")
		$tpl->set('[foto]', '<a href="'.$galConfig['mainhref'].'all/sort-new/">');
	else
		$tpl->set('[foto]', '<a href="'.$galConfig['PHP_SELF'].'&act=15&p=sort-new/">');

	$tpl->set('[/foto]','</a>');

	if (!$this_album['moderators'])
		$tpl->set('{moderators}', '');
	else
		$tpl->set('{moderators}', "<a href=\"javascript:void(0);\" onclick=\"cat_moderators_show('{$_album_id}'); return false;\">{$langGal['js_cat_moderators']}</a>");

	if ($galConfig['allow_comments'] && $this_album['allow_comments']){

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

	$tpl->compile('content');
	$tpl->clear();

	if ($this_album['keywords'] == '' AND $this_album['meta_descr'] == '') create_keywords ($metatags['title']." ".$this_album['cat_short_desc']);
	else {
		$metatags['keywords'] = $this_album['keywords'];
		$metatags['description'] = $this_album['meta_descr'];
	}

	$metatags['description'] = (dle_strlen($metatags['description'], $config['charset']) > 5) ? $metatags['description'] : $galConfig['description'];
	$metatags['keywords'] = (dle_strlen($metatags['keywords'], $config['charset']) > 5) ? $metatags['keywords'] : $galConfig['keywords'];

	if ($cache_category_id)
		create_gallery_cache ($cache_category_id, serialize(array($tpl->result['content'], $metatags['title'], $metatags['description'], $metatags['keywords'], $s_navigation)));

?>