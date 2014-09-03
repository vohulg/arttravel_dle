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
 Файл: show.foto.php
-----------------------------------------------------
 Назначение: Показ миниатюрных фотографий
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	$gallery_referrer = false;

	if ($_album_id){

		$count_foto = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies WHERE category_id='{$_album_id}' AND approve='1'");
		$count_foto = $count_foto['count'];

	}

	if ($count_foto){

		if (!isset($fsort) || !is_array($fsort) || !count($fsort)) $fsort = foto_sort($this_album);

		$foto_td = ($this_album && $this_album['foto_td']) ? intval($this_album['foto_td']) : intval($galConfig['foto_td']);
		$foto_tr = ($this_album && $this_album['foto_tr']) ? intval($this_album['foto_tr']) : intval($galConfig['foto_tr']);

		if ($foto_td < 1) $foto_td = 2;
		if ($foto_tr < 1) $foto_tr = 8;

		$fotolimit = $foto_td * $foto_tr;

		$i = 0;

		if (!$main_sql){
			if (!$main_sql_where) $main_sql_where = "p.category_id='{$_album_id}' AND p.approve=1";
			if (!$main_sql_limit) $main_sql_limit = " LIMIT ".(($fstart-1)*$fotolimit).", {$fotolimit}";
			elseif ($main_sql_limit == "all") $main_sql_limit = "";
			$main_sql = "SELECT p.*, c.id, c.cat_title, c.cat_short_desc, c.cat_alt_name, c.user_name, c.edit_level, c.moderators, c.allow_rating, c.allow_comments, c.allow_user_admin FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE {$main_sql_where} ORDER BY {$fsort['sort']} {$fsort['msort']}, picture_id{$main_sql_limit}";
		}

		if ($this_album && $this_album['smallfotoskin'])
			$template = $this_album['smallfotoskin'];
		elseif ($template == '')
			$template = 'short_image';

		$tpl->load_template('gallery/'.$template.'.tpl');

		$db->query($main_sql);

		$num = $db->num_rows();

		while($row = $db->get_row()){ $i++;

			if ($config['allow_alt_url'] == "yes"){

				$cat_url = $galConfig['mainhref'].$row['cat_alt_name']."/";

				if ($row['picture_alt_name'])
					$fotourl = $cat_url.$row['picture_id'].$search_parametrs."-".$row['picture_alt_name'].".html";
				else
					$fotourl = $cat_url.$row['picture_id'].$search_parametrs.".html";

			} else {

				$cat_url = $galConfig['mainhref']."&act=1&cid=".$row['category_id'];
				$fotourl = $galConfig['mainhref']."&act=2&cid=".$row['category_id']."&fid=".$row['picture_id'].$search_parametrs;

			}

			if ($galConfig['allow_comments'] && ($row['allow_comments'] && $row['allow_comms'] || $row['comments'])){

				$tpl->set('[com-link]',"<a href=\"{$fotourl}#comment\">");
				$tpl->set('[/com-link]',"</a>");

			} else $tpl->set_block("'\\[com-link\\](.*?)\\[/com-link\\]'si","");

			if ($galConfig['allow_rating'] && ($row['allow_rating'] && $row['allow_rate'] || $row['vote_num']))
				$tpl->set('{rating}', ShowGalRating ($row['picture_id'], $row['rating'], $row['vote_num'], ($row['allow_rating'] && $row['allow_rate'] && $row['approve'] == 1 && check_gallery_access ("rate"))));
			else
				$tpl->set('{rating}', '');

			if ($fsort['msort'] == "asc")
				$ii = $i+(($fstart-1)*$fotolimit);
			else
				$ii = $count_foto - $i - ($fstart-1)*$fotolimit + 1;

			$tpl->set('{i}', $ii);

			$row['cat_title'] = stripslashes($row['cat_title']);
			$row['image_alt_title'] = stripslashes($row['image_alt_title']);
			$row['picture_title'] = stripslashes($row['picture_title']);

			if ($row['image_alt_title'] == '') $row['image_alt_title'] = $row['picture_title'];

			if ($row['picture_title'] == '')
				$row['picture_title'] = str_ireplace(array("{%i%}", "{%posi%}", "{%category%}", "{%filename%}"), array($ii, intval($row['posi']), $row['cat_title'], preg_replace("/(.*)\.[a-z0-9\_]+$/i","\\1", $row['picture_filname'])), stripslashes($galConfig['empty_title_template']));

			$tpl->set('{alt_title}', $row['image_alt_title']);

			if ($galConfig['max_title_lenght'] && dle_strlen($row['picture_title'], $config['charset']) > $galConfig['max_title_lenght']){
				$title = dle_substr($row['picture_title'], 0, $galConfig['max_title_lenght'], $config['charset']);
				if (($temp_dmax = dle_strrpos($title, " ", $config['charset']))) $title = dle_substr($title, 0, $temp_dmax, $config['charset']);
				if (dle_strlen($title, $config['charset']) > $galConfig['max_title_lenght'])
					$title = dle_substr($title, 0, $galConfig['max_title_lenght'], $config['charset']);
				$title .= "...";
			} else $title = $row['picture_title'];

			if (!$row['type_upload'])
				$row['full_link'] = FOTO_URL.'/main/'.$row['category_id'].'/'.$row['picture_filname'];

			if ($row['text'] != ''){

				if ($user_group[$member_id['user_group']]['allow_hide'])
					$row['text'] = str_ireplace(array("[hide]", "[/hide]"), "", stripslashes($row['text']));
				else
					$row['text'] = preg_replace("#\[hide\](.+?)\[/hide\]#is", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", stripslashes($row['text']));

				$tpl->set('[description]', '');
				$tpl->set('[/description]', '');
				$tpl->set('{description}', $row['text']);

			} else {

				$tpl->set('{description}', '');
				$tpl->set_block("'\\[description\\](.*?)\\[/description\\]'si","");

			}

			if (!$row['media_type']){

				$thumb_path = thumb_path($row['thumbnails'], 't');

				if ($thumb_path != 'main')
					$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$row['category_id'].'/'.$row[($row['preview_filname'] ? 'preview_filname' : 'picture_filname')];
				else
					$thumb_path = $row['full_link'];

				if (!$smartphone_detected && ($row['text'] != '' || $row['picture_title'] != '')){
					$caption = '<span class="highslide-caption">'.$row['picture_title'];
					if ($row['text'] != '') $caption .= '<div class"gallery_foto_descr">' . $row['text'] . "</div>";
					$caption .= '</span>';
				} else $caption = '';

				$alt_title = $row['image_alt_title'] ? ' alt="'.$row['image_alt_title'].'" title="'.$row['image_alt_title'].'"' : '';

				$tpl->set('{thumb}', '<img src="'.$thumb_path.'"'.$alt_title.' />');
				$tpl->set('[fullimageurl]', '<a href="'.$fotourl.'" onclick="return hs.expand(this, { src: \''.$row['full_link'].'\'} )">');
				$tpl->set('[/fullimageurl]', '</a>'.$caption);
				$tpl->set('[isfoto]', '');
				$tpl->set('[/isfoto]', '');
				$tpl->set_block("'\\[ismedia\\](.*?)\\[/ismedia\\]'si","");

			} else {

				if ($row['preview_filname']){
					$thumb_path = thumb_path($row['thumbnails'], 't');
					$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$row['category_id'].'/'.$row['preview_filname'];
				} else $thumb_path = "";

				$tpl->set('[fullimageurl]', '');
				$tpl->set('[/fullimageurl]', '');
				$tpl->set('[ismedia]', '');
				$tpl->set('[/ismedia]', '');
				$tpl->set_block("'\\[isfoto\\](.*?)\\[/isfoto\\]'si","");

				$tpl->set('{thumb}', players($row['full_link'], $row['media_type'], false, $thumb_path, $row['image_alt_title']));

			}

			$row['lastdate'] = strtotime($row['lastdate']);
			$row['date'] = strtotime($row['date']);

			if (($tdate = date('Ymd', $row['date'])) == date('Ymd', TIME))
				$tpl->set('{created}', $lang['time_heute'].langdate(", H:i", $row['date']));
			elseif ($tdate == date('Ymd', (TIME - 86400)))
				$tpl->set('{created}', $lang['time_gestern'].langdate(", H:i", $row['date']));
			else
				$tpl->set( '{created}', langdate($galConfig['timestamp_active'], $row['date']));

			if (($tdate = date('Ymd', $row['lastdate'])) == date('Ymd', TIME))
				$tpl->set('{updated}', $lang['time_heute'].langdate(", H:i", $row['lastdate']));
			elseif ($tdate == date('Ymd', (TIME - 86400)))
				$tpl->set('{updated}', $lang['time_gestern'].langdate(", H:i", $row['lastdate']));
			else
				$tpl->set( '{updated}', langdate($galConfig['timestamp_active'], $row['lastdate']));

			if ($_admin != -1){

				$is_admin = check_gallery_access ("edit", $row['edit_level'], $row['moderators']);

				$own_admin_foto = ($row['allow_user_admin'] && $member_id['name'] == $row['user_name']);

				if ($is_logged && ($is_admin || $own_admin_foto || (($galConfig['allow_edit_picture'] || $galConfig['allow_delete_picture']) && $row['user_id'] == $member_id['user_id']))){

					$a_edit = ($is_admin || $own_admin_foto || $galConfig['allow_edit_picture']) ? 1 : 0;
					$a_delete = ($is_admin || $own_admin_foto || $galConfig['allow_delete_picture']) ? 1 : 0;
					$a_admin = ($is_admin == 2 && $member_id['user_group'] == 1) ? 2 : (bool)$is_admin;

					$tpl->set('[edit]',"<a onclick=\"return dropdownmenu(this, event, ShortGalFoto('".$row['picture_id']."', '".$a_admin."', '".$a_edit."', '".$a_delete."', '".($row['user_id'] == $member_id['user_id'] ? 1 : 0)."'), '200px')\" href=\"{$config['http_home_url']}index.php?do=gallery&dle_allow_hash={$dle_login_hash}&act=17&si={$row['picture_id']}\">");
					$tpl->set('[/edit]',"</a>");

					$gallery_referrer = true;

				} else $tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");

				if ($is_admin){
					$js_options['admin'] = true;
					$_admin = 1;
					$tpl->set('{check}',"<input name='si[]' value='{$row['picture_id']}' type='checkbox'>"); //при изменение данного тега внести изменения в модуль вывода тегов
				} else $tpl->set('{check}','');

			} else {

				$tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");
				$tpl->set('{check}','');

			}

			$row['picture_user_name'] = stripslashes($row['picture_user_name']);

			if ($row['user_id']){

				$encoded = urlencode($row['picture_user_name']);

				if ($config['allow_alt_url'] == "yes")
					$author_url = $config['http_home_url']."user/".$encoded."/";
				else
					$author_url = $config['http_home_url']."index.php?subaction=userinfo&amp;user=".$encoded;

				$author_menu = " onclick=\"return dropdownmenu(this, event, GalUserMenu('".htmlspecialchars($author_url, ENT_QUOTES, $config['charset'])."', '".$encoded."'), '220px')\" onMouseout=\"delayhidemenu()\"";

				$news_find = array(
				'{profile_author}'	=> "<a onclick=\"ShowProfile('{$encoded}', '".htmlspecialchars($author_url, ENT_QUOTES, $config['charset'])."', gallery_admin_editusers); return false;\" href=\"".$author_url."\">".$row['picture_user_name']."</a>",
				'{author}'			=> $row['picture_user_name'],
				'[author-link]'		=> "<a".$author_menu." href=\"".$author_url."/\">",
				'[/author-link]'	=> "</a>",
				);

				$tpl->set_block("'\\[author-mail\\](.*?)\\[/author-mail\\]'si", "");

			} elseif ($row['picture_user_name'] != '' || $row['email'] != ''){

				$row['email'] = stripslashes($row['email']);
				$show_name = $row['picture_user_name'];
				if ($show_name == '') $show_name = $row['email'];

				$news_find = array(
				'{author}'			=> $show_name,
				'[author-link]'		=> ($row['picture_user_name'] != '' ? "<a href=\"".$galConfig['mainhref'].($config['allow_alt_url'] == "yes" ? "all/user-".urlencode($row['picture_user_name'])."/" : "&act=15&p=user-".urlencode($row['picture_user_name']))."\">" : ""),
				'[/author-link]'	=> ($row['picture_user_name'] != '' ? "</a>" : ""),
				);

				if ($row['email']){
					$news_find['[author-mail]'] = "<a href=\"mailto:".htmlspecialchars($row['email'], ENT_QUOTES, $config['charset'])."\">";
					$news_find['[/author-mail]'] = "</a>";
					$tpl->set('{profile_author}', "<a href=\"mailto:".htmlspecialchars($row['email'], ENT_QUOTES, $config['charset'])."\">" . $show_name . "</a>");
				} else {
					$tpl->set('{profile_author}', $show_name);
					$tpl->set_block("'\\[author-mail\\](.*?)\\[/author-mail\\]'si", "");
				}

			} else {

				$news_find = array(
				'{profile_author}'	=> "--",
				'{author}'			=> "--",
				'[author-link]'		=> "",
				'[/author-link]'	=> "",
				);

				$tpl->set_block("'\\[author-mail\\](.*?)\\[/author-mail\\]'si", "");

			}

			$tpl->set('',  $news_find);

			$tpl->set('', array(
			'{categoryurl}'		=> $cat_url,
			'{categorytitle}'	=> $row['cat_title'],
			'{rating-num}'		=> $row['rating'],
			'{vote-num}'		=> $row['vote_num'],
			'{fotourl}'			=> $fotourl,
			'{fulltitle}'		=> $row['picture_title'],
			'{title}'			=> $title,
			'{view-title}'		=> ($title != '' ? $title : $langGal['view_no_title']),
			'{views}'			=> $row['file_views'],
			'{download-num}'	=> $row['downloaded'],
			'{comments-num}'	=> $row['comments'],
			'{width}'			=> $row['width'],
			'{height}'			=> $row['height'],
			'{size}'			=> formatsize($row['size']),
			'{reali}'			=> $i+(($fstart-1)*$fotolimit),
			));

			if($row['tags']){

				$row['tags'] = explode(",", stripslashes($row['tags']));

				for ($k=0; $k<count($row['tags']); $k++){

					if ($config['allow_alt_url'] == "yes")
						$row['tags'][$k] = "<a href=\"{$galConfig['mainhref']}all/tag-".urlencode($row['tags'][$k])."/\">".$row['tags'][$k]."</a>";
					else
						$row['tags'][$k] = "<a href=\"{$galConfig['mainhref']}&act=15&p=tag-".urlencode($row['tags'][$k])."\">".$row['tags'][$k]."</a>";

				}

				$tpl->set('{tags}', implode( ", ", $row['tags']));
				$tpl->set('[tags]', "");
				$tpl->set('[/tags]', "");

			} else {

				$tpl->set_block("'\\[tags\\](.*?)\\[/tags\\]'si", "");
				$tpl->set('{tags}', "");

			}

			if (($i-1)%$foto_td == 0){
				$tpl->set('[subheader]','');
				$tpl->set('[/subheader]','');
			} else $tpl->set_block("'\\[subheader\\](.*?)\\[/subheader\\]'si","");

			if ($i%$foto_td == 0 || $i == $num){
				$tpl->set('[subfooter]','');
				$tpl->set('[/subfooter]','');
			} else $tpl->set_block("'\\[subfooter\\](.*?)\\[/subfooter\\]'si","");

			if (!isset($compile) || $compile == '')
				$tpl->compile('fotolistrow');
			else
				$tpl->compile($compile);

		}

		$db->free();
		$tpl->clear();

		if ($count_foto > $fotolimit){

			if ($config['allow_alt_url'] == "yes")
				fastpages ($count_foto, $fotolimit, $fstart, $this_url."fotopage/{INS}/", $this_url);
			else
				fastpages ($count_foto, $fotolimit, $fstart, $this_url."&amp;fstart={INS}", $this_url);

		}

	}


?>