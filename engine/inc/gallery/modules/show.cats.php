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
 Файл: show.cats.php
-----------------------------------------------------
 Назначение: Показ подкатегорий
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	$hide_notallowed_cats = 0;
	$count_cats = 0;

	if ($hide_notallowed_cats){

		$main_sql_where[] = "(view_level".(check_gallery_access ("read", "", "") ? " IN ('-1','')" : "='-1'")." OR view_level regexp '[[:<:]]({$member_id['user_group']})[[:>:]]')";
		if (!check_gallery_access ("edit", "", "")) $main_sql_where[] = "locked=0";

	}

	$main_sql_where = implode (" AND ",$main_sql_where);

	$sort = array('sort' => ($galConfig['category_sort'] != "") ? $galConfig['category_sort'] : "position", 'msort' => ($galConfig['category_msort'] != "") ? $galConfig['category_msort'] : "asc");

	$main_cat_td = ($this_album && $this_album['subcats_td']) ? intval($this_album['subcats_td']) : intval($galConfig['main_cat_td']);
	$main_cat_tr = ($this_album && $this_album['subcats_tr']) ? intval($this_album['subcats_tr']) : intval($galConfig['main_cat_tr']);

	$limit = $main_cat_td * $main_cat_tr;

	$db->query("SELECT id, cat_title, cat_short_desc, cat_alt_name, user_name, locked, reg_date, last_cat_date, images, cat_images, sub_cats, view_level, edit_level, moderators, icon, allow_user_admin FROM " . PREFIX . "_gallery_category WHERE {$main_sql_where} ORDER BY {$sort['sort']} {$sort['msort']} LIMIT ".(($cstart-1)*$limit).", {$limit}");

	if (($num = $db->num_rows()) > 0){

		$i = 0;

		if ($this_album && $this_album['subcatskin'])
			$template = $this_album['subcatskin'];
		elseif ($template == '')
			$template = 'short_category';

		$tpl->load_template('gallery/'.$template.'.tpl');

		while($row = $db->get_row()){ $i++;

			$user_name = stripslashes($row['user_name']);
			$encoded = urlencode($user_name);

			if ($config['allow_alt_url'] == "yes"){

				$tpl->set('{categoryurl}', $galConfig['mainhref'].$row['cat_alt_name']."/");
				$url_user = $config['http_home_url']."user/".$encoded."/";

			} else {

				$tpl->set('{categoryurl}', $galConfig['mainhref']."&act=1&cid={$row['id']}");
				$url_user = $config['http_home_url']."index.php?subaction=userinfo&amp;user=".$encoded;

			}

			if ($user_name){

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

			if ($galConfig['icon_type'] && !check_gallery_access ("read", $row['view_level'], "", $row['locked']))
				$tpl->set('{icon}', "<img src=\"{THEME}/gallimages/access_den.gif\" border=\"0\" />");
			elseif ($row['icon'] != '')
				$tpl->set('{icon}', "<img src=\"".str_replace('{FOTO_URL}', FOTO_URL, $row['icon'])."\" border=\"0\" alt=\"".stripslashes($row['cat_title'])."\" />");
			elseif ($galConfig['icon_type'] && !$row['cat_images'])
				$tpl->set('{icon}', "<img src=\"{THEME}/gallimages/no_foto.gif\" border=\"0\" />");
			else
				$tpl->set('{icon}', '');

			$tpl->set('{images}', $row['cat_images']);
			$tpl->set('{subcats}', $row['sub_cats']);

			$tpl->set('{title}', stripslashes($row['cat_title']));
			$tpl->set('{description}', stripslashes($row['cat_short_desc']));

			if (!$row['locked'])
				$tpl->set_block("'\\[locked\\](.*?)\\[/locked\\]'si","");
			else {
				$tpl->set('[locked]', '');
				$tpl->set('[/locked]', '');
			}

			if (!$row['allow_user_admin']){
				$tpl->set('[not-usercategory]', '');
				$tpl->set('[/not-usercategory]', '');
				$tpl->set_block("'\\[usercategory\\](.*?)\\[/usercategory\\]'si","");
			} else {
				$tpl->set('[usercategory]', '');
				$tpl->set('[/usercategory]', '');
				$tpl->set_block("'\\[not-usercategory\\](.*?)\\[/not-usercategory\\]'si","");
			}

			$row['reg_date'] = strtotime($row['reg_date']);
			$row['last_cat_date'] = $row['last_cat_date'] != '0000-00-00 00:00:00' ? strtotime($row['last_cat_date']) : $row['reg_date'];

			if (($tdate = date('Ymd', $row['reg_date'])) == date('Ymd', TIME))
				$tpl->set('{created}', $lang['time_heute'].langdate(", H:i", $row['reg_date']));
			elseif ($tdate == date('Ymd', (TIME - 86400)))
				$tpl->set('{created}', $lang['time_gestern'].langdate(", H:i", $row['reg_date']));
			else
				$tpl->set( '{created}', langdate($galConfig['timestamp_active'], $row['reg_date']));

			if (($tdate = date('Ymd', $row['last_cat_date'])) == date('Ymd', TIME))
				$tpl->set('{updated}', $lang['time_heute'].langdate(", H:i", $row['last_cat_date']));
			elseif ($tdate == date('Ymd', (TIME - 86400)))
				$tpl->set('{updated}', $lang['time_gestern'].langdate(", H:i", $row['last_cat_date']));
			else
				$tpl->set( '{updated}', langdate($galConfig['timestamp_active'], $row['last_cat_date']));

			if (($adm_access = check_gallery_access ("edit", $row['edit_level'], $row['moderators'])) == 2 || (!$row['locked'] && $is_logged && $row['allow_user_admin'] && $member_id['name'] == $row['user_name'])){
				if ($adm_access == 2 && $member_id['user_group'] != 1) $adm_access = 1;
				elseif ($adm_access == 1) $adm_access = 0;
				if ($adm_access > 0) $js_options['admin'] = true;
				$tpl->set('[edit]',"<a onclick=\"return dropdownmenu(this, event, MenuGalCat('".$row['id']."', '".$adm_access."'), '220px')\" href=\"".$config['http_home_url']."index.php?do=gallery&act=".($adm_access ? "19" : "24")."&si=".$row['id']."\">");
				$tpl->set('[/edit]',"</a>");
			} else $tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");

			if (($i-1)%$main_cat_td == 0){
				$tpl->set('[subheader]','');
				$tpl->set('[/subheader]','');
			} else $tpl->set_block("'\\[subheader\\](.*?)\\[/subheader\\]'si","");

			if ($i%$main_cat_td == 0 || $i == $num){
				$tpl->set('[subfooter]','');
				$tpl->set('[/subfooter]','');
			} else $tpl->set_block("'\\[subfooter\\](.*?)\\[/subfooter\\]'si","");

			$tpl->compile('listrow');

		}

		$db->free();
		$tpl->clear();

		$count_cats = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category WHERE {$main_sql_where}");
		$count_cats = $count_cats['count'];

		if ($count_cats > $limit){

			if ($config['allow_alt_url'] == "yes")
				fastpages ($count_cats, $limit, $cstart, $this_url."page/{INS}/", $this_url);
			else
				fastpages ($count_cats, $limit, $cstart, $this_url."&amp;cstart={INS}", $this_url);

			$pageslist = $tpl->result['fastnav'];
			$tpl->result['fastnav'] = "";

		} else $pageslist = '';

	}

?>