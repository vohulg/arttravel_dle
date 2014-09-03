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
 Файл: fullimage.php
-----------------------------------------------------
 Назначение: Показ полной фотографии
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	$_search_code = isset($_foto_id[1]) ? totranslit($_foto_id[1], true, false) : false;
	$_search_i = isset($_foto_id[2]) ? intval($_foto_id[2]) : false;
	$_foto_id = intval($_foto_id[0]);

	require_once TWSGAL_DIR.'/classes/comments.php';

	$is_admin = ($this_album['id'] && check_gallery_access ("edit", $this_album['edit_level'], $this_album['moderators']));

	if (!$this_foto['picture_id'] || !check_gallery_access ("read", $this_album['view_level'], "", $this_album['locked']) || (!$is_admin && (!$is_logged || $this_foto['user_id'] != $member_id['user_id']) && !in_array($this_foto['approve'], array(1, 2)))){

		@header("HTTP/1.0 404 Not Found");
		msgbox ($langGal['all_info'], $langGal['no_that_foto']."<br /><br /><a href=\"javascript:history.go(-1)\">".$langGal['all_prev']."</a>");
		return;

	}

	$this_foto['date'] = strtotime($this_foto['date']);

	$addcomments = ($this_foto['allow_comms'] && $this_album['allow_comments'] && $this_foto['approve'] == '1' && !$this_album['locked'] && check_gallery_access ("comms", $this_album['comment_level']) && (!$galConfig['max_comments_days'] || $this_foto['date'] > (TIME - $galConfig['max_comments_days']*3600*24)) && (!$is_logged || !$config['comments_restricted'] || ((TIME - $member_id['reg_date']) >= ($config['comments_restricted'] * 86400))));

	if ($config['allow_alt_url'] == "yes" && $this_foto['picture_alt_name'])
		$full_fotourl = $galConfig['mainhref'].$this_album['cat_alt_name']."/".$this_foto['picture_id']."-".$this_foto['picture_alt_name'].".html";
	elseif ($config['allow_alt_url'] == "yes")
		$full_fotourl = $galConfig['mainhref'].$this_album['cat_alt_name']."/".$this_foto['picture_id'].".html";
	else
		$full_fotourl = $galConfig['mainhref']."&act=2&cid=".$this_foto['category_id']."&fid=".$this_foto['picture_id'];

	if ($doaction == "addcomment" && $addcomments){

		$COMMENTS = new UnComments();
		$COMMENTS->comments_num = $this_foto['comments'];
		$COMMENTS->id = $this_foto['picture_id'];
		$COMMENTS->update_news_sql =  PREFIX . "_gallery_picturies SET comments=comments+1 WHERE picture_id ";
		$COMMENTS->allow_cmod = $galConfig['comments_mod'];
		$COMMENTS->url = $full_fotourl;

		$com_result = $COMMENTS->AddComment();

		if ($com_result !== true) msgbox($langGal['all_err_1'], implode("<br />", $com_result));

	}

	$search_title = "";
	$carousel = false;

	if ($galConfig['buffer_in_fullimage'] > 1 && ($_search_code != '' && $_search_i > 0 || $this_album['allow_carousel'])){

		$carousel_cache = ($this_foto['approve'] == '1' && !$this_album['locked'] && $this_foto['date'] > TIME - 30*86400);

		if ($galConfig['thumbs_offset'] && strpos($galConfig['thumbs_offset'], "%" ) !== false){
			$left_image_offset = floor($full_image_preload*intval($galConfig['thumbs_offset'])/100);
			if ($left_image_offset < 1) $left_image_offset = 1;
		} elseif ($galConfig['thumbs_offset']) $left_image_offset = intval($galConfig['thumbs_offset']);
		else $left_image_offset = 0;

		include TWSGAL_DIR.'/modules/show.carousel.php';

		if (is_array($carousel)){

			if ($carousel['search_code']) {

				$search_title = array();

				if ($carousel['cat'])	$search_title[] = str_ireplace('{cat}', stripslashes($this_album['cat_title']), $langGal['menu_title18']);
				if ($carousel['symbol']) $search_title[] = str_ireplace('{symbol}', stripslashes($carousel['symbol']), $langGal['menu_title17']);
				if ($carousel['user']) $search_title[] = str_ireplace('{user}', stripslashes($carousel['user']), $langGal['menu_title16']);
				if ($carousel['story']) $search_title[] = str_ireplace('{find}', stripslashes($carousel['story']), $langGal['menu_title15']);
				if ($carousel['tag']) $search_title[] = str_ireplace('{tag}', stripslashes($carousel['tag']), $langGal['menu_title19']);

				$search_title = count($search_title) ? implode(" &raquo; ", $search_title) : $langGal['menu_title7'];

			}

		} elseif ($carousel > 0){

			$error_text = ($carousel != 2) ? $langGal['search_err_2'] : str_ireplace('{group}', $user_group[$member_id['user_group']]['group_name'], $lang['search_denied']);
			@header("HTTP/1.0 404 Not Found");
			msgbox($langGal['all_info'], $error_text."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");

		}

	}

	if ($this_album['bigfotoskin'])
		$template = $this_album['bigfotoskin'];
	else
		$template = 'full_image';

	$tpl->load_template('gallery/'.$template.'.tpl');

	if ($galConfig['dinamic_symbols'] && strpos($tpl->copy_template, "{symbols}" ) !== false) $tpl->set('{symbols}', gallery_symbols());

	$this_foto['picture_title'] = stripslashes($this_foto['picture_title']);
	$this_foto['image_alt_title'] = stripslashes($this_foto['image_alt_title']);
	$this_album['cat_title'] = stripslashes($this_album['cat_title']);
	$this_foto['lastdate'] = strtotime($this_foto['lastdate']);

	if ($this_foto['image_alt_title'] == '') $this_foto['image_alt_title'] = $this_foto['picture_title'];

	if ($this_foto['picture_title'] == '')
		$this_foto['picture_title'] = str_ireplace(array("{%posi%}", "{%category%}", "{%filename%}"), array(intval($this_foto['posi']), $this_album['cat_title'], preg_replace("/(.*)\.[a-z0-9\_]+$/i","\\1", $this_foto['picture_filname'])), stripslashes($galConfig['empty_title_template']));

	$metatags['title'] = $this_foto['picture_title'] != '' ? $this_foto['picture_title'] : $langGal['view_no_title'];
	$metatags['title'] .= ' &raquo; ' . ($this_album['metatitle'] != '' ? stripslashes($this_album['metatitle']) : $this_album['cat_title']);

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

	if ($cstart > 1) $metatags['title'] .= ' &raquo; '.$lang['news_site'].' '.$cstart;

	$s_navigation .= " &raquo; " . $path . " &raquo; " . ($this_foto['picture_title'] != '' ? $this_foto['picture_title'] : $langGal['view_no_title']);

	if (is_array($carousel)){

		$tpl->set('{carousel-id}', $carousel['jkey']);
		unset($carousel);

		$tpl->set('[carousel]', '');
		$tpl->set('[/carousel]', '');

	} else $tpl->set_block("'\\[carousel\\](.*?)\\[/carousel\\]'si","");

	if ($search_title){

		$tpl->set('{search}', $search_title);
		$tpl->set('[search]', '');
		$tpl->set('[/search]', '');

	} else $tpl->set_block("'\\[search\\](.*?)\\[/search\\]'si","");

	if (($tdate = date('Ymd', $this_foto['date'])) == date('Ymd', TIME))
		$tpl->set('{created}', $lang['time_heute'].langdate(", H:i", $this_foto['date']));
	elseif ($tdate == date('Ymd', (TIME - 86400)))
		$tpl->set('{created}', $lang['time_gestern'].langdate(", H:i", $this_foto['date']));
	else
		$tpl->set( '{created}', langdate($galConfig['timestamp_active'], $this_foto['date']));

	if (($tdate = date('Ymd', $this_foto['lastdate'])) == date('Ymd', TIME))
		$tpl->set('{updated}', $lang['time_heute'].langdate(", H:i", $this_foto['lastdate']));
	elseif ($tdate == date('Ymd', (TIME - 86400)))
		$tpl->set('{updated}', $lang['time_gestern'].langdate(", H:i", $this_foto['lastdate']));
	else
		$tpl->set( '{updated}', langdate($galConfig['timestamp_active'], $this_foto['lastdate']));

	$tpl->set('{alt_title}', $this_foto['image_alt_title']);

	if ($galConfig['allow_rating'] && ($this_album['allow_rating'] && $this_foto['allow_rate'] || $this_foto['vote_num'])){
		$tpl->set('{rating}', ShowGalRating ($this_foto['picture_id'], $this_foto['rating'], $this_foto['vote_num'], ($this_album['allow_rating'] && $this_foto['allow_rate'] && $this_foto['approve'] == 1 && check_gallery_access ("rate"))));
		$tpl->set('{vote-num}', $this_foto['vote_num']);
	} else {
		$tpl->set('{rating}', '');
		$tpl->set('{vote-num}', '');
	}

	if (!$this_foto['type_upload'])
		$this_foto['full_link'] = FOTO_URL.'/main/'.$this_foto['category_id'].'/'.$this_foto['picture_filname'];

	if ($galConfig['allow_comments'] && ($this_album['allow_comments'] && $this_foto['allow_comms'] || $this_foto['comments'])){

		$tpl->set('[com-link]',"<a href=\"{$full_fotourl}#comment\">");
		$tpl->set('[/com-link]',"</a>");

	} else	$tpl->set_block("'\\[com-link\\](.*?)\\[/com-link\\]'si","");

	if ($this_foto['text'] != ''){

		if ($user_group[$member_id['user_group']]['allow_hide'])
			$this_foto['text'] = str_ireplace(array("[hide]", "[/hide]"), "", stripslashes($this_foto['text']));
		else
			$this_foto['text'] = preg_replace("#\[hide\](.+?)\[/hide\]#is", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", stripslashes($this_foto['text']));

		$tpl->set('[description]', '');
		$tpl->set('[/description]', '');
		$tpl->set('{description}', $this_foto['text']);

	} else $tpl->set_block("'\\[description\\](.*?)\\[/description\\]'si","");

	$allow_download = $user_group[$member_id['user_group']]['allow_files'];

	if (!$this_foto['media_type']){

		if ($galConfig['comms_foto_size']){

			if (!$smartphone_detected && ($this_foto['text'] != '' || $this_foto['picture_title'] != '')){
				$caption = '<span class="highslide-caption">'.$this_foto['picture_title'];
				if ($this_foto['text'] != '') $caption .= '<div class"gallery_foto_descr">' . $this_foto['text'] . "</div>";
				$caption .= '</span>';
			} else $caption = '';

			$thumb_path = thumb_path($this_foto['thumbnails'], 'c');

			if ($thumb_path != 'main')
				$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$this_foto['category_id'].'/'.$this_foto[($this_foto['preview_filname'] ? 'preview_filname' : 'picture_filname')];
			else
				$thumb_path = $this_foto['full_link'];

			$tpl->set('[fullfotourl]', '<a href="'.$this_foto['full_link'].'" onclick="return hs.expand(this)">');

		} else {

			$caption = '';
			$thumb_path = FOTO_URL.'/main/'.$this_foto['category_id'].'/'.$this_foto['picture_filname'];
			$tpl->set('[fullfotourl]', '<a href="'.$this_foto['full_link'].'">');

		}

		$alt_title = $this_foto['image_alt_title'] ? ' alt="'.$this_foto['image_alt_title'].'" title="'.$this_foto['image_alt_title'].'"' : '';

		$tpl->set('{comm-thumb}', '<img src="'.$thumb_path.'"  border="1" style="border-color:#000000;padding:1px;"'.$alt_title.' />');
		$tpl->set('[/fullfotourl]', '</a>'.$caption);

		$minithumb_path = thumb_path($this_foto['thumbnails'], 't');

		if ($minithumb_path != 'main')
			$minithumb_path = FOTO_URL.'/'.$minithumb_path.'/'.$this_foto['category_id'].'/'.$this_foto[($this_foto['preview_filname'] ? 'preview_filname' : 'picture_filname')];
		else
			$minithumb_path = $this_foto['full_link'];

		$tpl->set('[isfoto]', '');
		$tpl->set('[/isfoto]', '');
		$tpl->set_block("'\\[ismedia\\](.*?)\\[/ismedia\\]'si","");

		$tpl->set('{html_thumb_code}', "&lt;a href=&quot;{$full_fotourl}&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;{$minithumb_path}&quot; border=&quot;0&quot; alt=&quot;{$this_foto['picture_title']}&quot; title=&quot;{$this_foto['picture_title']}&quot; /&gt;&lt;/a&gt;");
		$tpl->set('{bb_thumb_code}', "[url={$full_fotourl}][img]{$minithumb_path}[/img][/url]");
		$tpl->set('{html_comm_code}', "&lt;a href=&quot;{$full_fotourl}&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;{$thumb_path}&quot; border=&quot;0&quot; alt=&quot;{$this_foto['picture_title']}&quot; title=&quot;{$this_foto['picture_title']}&quot; /&gt;&lt;/a&gt;");
		$tpl->set('{bb_comm_code}', "[url={$full_fotourl}][img]{$thumb_path}[/img][/url]");

		if ($allow_download){

			$tpl->set('{html_original_code}', "&lt;a href=&quot;{$this_foto['full_link']}&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;{$thumb_path}&quot; border=&quot;0&quot; alt=&quot;{$this_foto['picture_title']}&quot; title=&quot;{$this_foto['picture_title']}&quot; /&gt;&lt;/a&gt;");
			$tpl->set('{bb_original_code}', "[url={$this_foto['full_link']}][img]{$thumb_path}[/img][/url]");

		} else {

			$tpl->set('{html_original_code}', "");
			$tpl->set('{bb_original_code}', "");

		}

	} else {

		if ($this_foto['preview_filname']){
			$thumb_path = thumb_path($this_foto['thumbnails'], 't');
			$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$this_foto['category_id'].'/'.$this_foto['preview_filname'];
		} else $thumb_path = "";

		$tpl->set('[ismedia]', '');
		$tpl->set('[/ismedia]', '');
		$tpl->set('[fullfotourl]', '');
		$tpl->set('[/fullfotourl]', '');
		$tpl->set_block("'\\[isfoto\\](.*?)\\[/isfoto\\]'si","");

		$tpl->set('{comm-thumb}', players($this_foto['full_link'], $this_foto['media_type'], true, $thumb_path, $this_foto['image_alt_title']));

		if ($this_foto['media_type'] > '50' || $this_foto['media_type'] != '10' && !$galConfig['allow_download']) $allow_download = false;

	}

	$tpl->set('[prev-link]',"<a href=\"".$config['http_home_url']."index.php?do=gallery&jump=previous&jc=".$this_foto['category_id']."&j=".$this_foto['picture_id']."\">");
	$tpl->set('[/prev-link]',"</a>");
	$tpl->set('[next-link]',"<a href=\"".$config['http_home_url']."index.php?do=gallery&jump=next&jc=".$this_foto['category_id']."&j=".$this_foto['picture_id']."\">");
	$tpl->set('[/next-link]',"</a>");

	$this_foto['picture_user_name'] = stripslashes($this_foto['picture_user_name']);

	if ($this_foto['user_id']){

		$encoded = urlencode($this_foto['picture_user_name']);

		if ($config['allow_alt_url'] == "yes")
			$author_url = $config['http_home_url']."user/".$encoded."/";
		else
			$author_url = $config['http_home_url']."index.php?subaction=userinfo&amp;user=".$encoded;

		$author_menu = " onclick=\"return dropdownmenu(this, event, GalUserMenu('".htmlspecialchars($author_url, ENT_QUOTES, $config['charset'])."', '".$encoded."'), '220px')\" onMouseout=\"delayhidemenu()\"";

		$news_find = array(
		'{profile_author}'	=> "<a onclick=\"ShowProfile('{$encoded}', '".htmlspecialchars($author_url, ENT_QUOTES, $config['charset'])."', gallery_admin_editusers); return false;\" href=\"".$author_url."\">".$this_foto['picture_user_name']."</a>",
		'{author}'			=> $this_foto['picture_user_name'],
		'[author-link]'		=> "<a".$author_menu." href=\"".$author_url."/\">",
		'[/author-link]'	=> "</a>",
		);

		$tpl->set_block("'\\[author-mail\\](.*?)\\[/author-mail\\]'si", "");

	} elseif ($this_foto['picture_user_name'] != '' || $this_foto['email'] != ''){

		$this_foto['email'] = stripslashes($this_foto['email']);
		$show_name = $this_foto['picture_user_name'];
		if ($show_name == '') $show_name = $this_foto['email'];

		$news_find = array(
		'{author}'			=> $show_name,
		'[author-link]'		=> ($this_foto['picture_user_name'] != '' ? "<a href=\"".$galConfig['mainhref'].($config['allow_alt_url'] == "yes" ? "all/user-".urlencode($this_foto['picture_user_name'])."/" : "&act=15&p=user-".urlencode($this_foto['picture_user_name']))."\">" : ""),
		'[/author-link]'	=> ($this_foto['picture_user_name'] != '' ? "</a>" : ""),
		);

		if ($this_foto['email']){
			$news_find['[author-mail]'] = "<a href=\"mailto:".htmlspecialchars($this_foto['email'], ENT_QUOTES, $config['charset'])."\">";
			$news_find['[/author-mail]'] = "</a>";
			$tpl->set('{profile_author}', "<a href=\"mailto:".htmlspecialchars($this_foto['email'], ENT_QUOTES, $config['charset'])."\">" . $show_name . "</a>");
		} else {
			$tpl->set('{profile_author}', $show_name);
			$tpl->set_block("'\\[author-mail\\](.*?)\\[/author-mail\\]'si", "");
		}

	} else {

		$news_find = array(
		'{profile_author}'	=> "--",
		'{author}'			=> "--",
		'[author-link]'		=> "",
		'[/author-link]'	=> ""
		);

		$tpl->set_block("'\\[author-mail\\](.*?)\\[/author-mail\\]'si", "");

	}

	$tpl->set('',  $news_find);

	$news_find = array(
	'{link-category}'	=> "<a href=\"".$galConfig['mainhref'].($config['allow_alt_url'] == "yes" ? $this_album['cat_alt_name']."/" : "&act=1&cid={$this_album['id']}")."\">".$this_album['cat_title']."</a>",
	'{navigator}'		=> $s_navigation,
	'{title}'			=> ($this_foto['picture_title'] != '' ? $this_foto['picture_title'] : $langGal['send_no_title']),
	'{views}'			=> $this_foto['file_views'],
	'{comments-num}'	=> $this_foto['comments'],
	'{download-num}'	=> $this_foto['downloaded'],
	'{width}'			=> $this_foto['width'],
	'{height}'			=> $this_foto['height'],
	'{size}'			=> formatsize($this_foto['size']),
	'{picture_id}'		=> $this_foto['picture_id'],
	'{rating-num}'		=> $this_foto['rating'],
	'{thumb_path}'		=> $thumb_path,
	);

	if ($this_foto['tags']){

		$this_foto['tags'] = explode(",", stripslashes($this_foto['tags']));

		for ($k=0; $k<count($this_foto['tags']); $k++){

			if ($config['allow_alt_url'] == "yes")
				$this_foto['tags'][$k] = "<a href=\"{$galConfig['mainhref']}all/tag-".urlencode($this_foto['tags'][$k])."/\">".$this_foto['tags'][$k]."</a>";
			else
				$this_foto['tags'][$k] = "<a href=\"{$galConfig['mainhref']}&act=15&p=tag-".urlencode($this_foto['tags'][$k])."\">".$this_foto['tags'][$k]."</a>";

		}

		$tpl->set('{tags}', implode( ", ", $this_foto['tags']));
		$tpl->set('[tags]', "");
		$tpl->set('[/tags]', "");

	} else {

		$tpl->set_block("'\\[tags\\](.*?)\\[/tags\\]'si", "");
		$tpl->set('{tags}', "");

	}

	if ($allow_download){
		$tpl->set('[download]', "<a href=\"".$config['http_home_url']."engine/gallery/external/download.php?id=".$this_foto['picture_id']."\">");
		$tpl->set('[/download]', "</a>");
	} else $tpl->set_block("'\\[download\\](.*?)\\[/download\\]'si","");

	$tpl->set('', $news_find);
	unset($news_find);

	$own_admin_foto = ($this_album['allow_user_admin'] && $member_id['name'] == $this_album['user_name']);

	if ($is_logged && ($is_admin || $own_admin_foto || (($galConfig['allow_edit_picture'] || $galConfig['allow_delete_picture']) && $this_foto['user_id'] == $member_id['user_id']))){

		$a_edit = ($is_admin || $own_admin_foto || $galConfig['allow_edit_picture']) ? 1 : 0;
		$a_delete = ($is_admin || $own_admin_foto || $galConfig['allow_delete_picture']) ? 1 : 0;
		if ($is_admin > 0) $js_options['admin'] = true;
		$a_admin = ($is_admin && $member_id['user_group'] == 1) ? 2 : $is_admin;

		$tpl->set('[edit]',"<a onclick=\"return dropdownmenu(this, event, ShortGalFoto('".$this_foto['picture_id']."', '".$a_admin."', '".$a_edit."', '".$a_delete."', '".($this_foto['user_id'] == $member_id['user_id'] ? 1 : 0)."'), '200px')\" href=\"{$config['http_home_url']}index.php?do=gallery&dle_allow_hash={$dle_login_hash}&act=17&si={$this_foto['picture_id']}\">");
		$tpl->set('[/edit]',"</a>");

		if (!isset($_SESSION['gallery_referrer']) || $_SESSION['gallery_referrer'] != $_SERVER['REQUEST_URI'])
			$_SESSION['gallery_referrer'] = $_SERVER['REQUEST_URI'];

	} else $tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si","");

	switch ($this_foto['approve']){
	case 1 : $tpl->set('{moderation}', ''); break;
	case 0 : $tpl->set('{moderation}', $langGal['mod_title']); break;
	case 2 : $tpl->set('{moderation}', $langGal['recycle_title']); break;
	default : $tpl->set('{moderation}', ''); break;
	}			

	if ($this_foto['edit_reason']){

		$tpl->set( '{editor}', stripslashes($this_foto['editor']));
		$tpl->set( '{edit-reason}', stripslashes($this_foto['edit_reason']));
		$tpl->set( '[edit-reason]', "" );
		$tpl->set( '[/edit-reason]', "" );

	} else $tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );

	if ($galConfig['whois_view_file']){

		$tpl->set( '[whois_view]', "<a href=\"javascript:void(0);\" onclick=\"whois_view({$this_foto['picture_id']});return false;\">" );
		$tpl->set( '[/whois_view]', "</a>" );

	} else $tpl->set_block( "'\\[whois_view\\](.*?)\\[/whois_view\\]'si", "" );

	$tpl->set( '{comments}', "<!--dlecomments-->" );
	$tpl->set( '{addcomments}', "<!--dleaddcomments-->" );
	$tpl->set( '{navigation}', "<!--dlenavigationcomments-->" );

	$allow = explode(',',$galConfig['comsubslevel']);

	if ($addcomments && $galConfig['comsubslevel'] && (in_array($member_id['user_group'], $allow) || $allow[0] == '-1')){
		$tpl->set('[subscribe]','<a href="javascript:void(0)" onclick="subscribe_comments('. $this_foto['picture_id'] .','.intval($member_id['user_id']).')">');
		$tpl->set('[/subscribe]','</a>');
	} else $tpl->set_block("'\\[subscribe\\](.*?)\\[/subscribe\\]'si","");

	$tpl->compile('content'); 
	$tpl->clear();

	$js_options['mode'] = 1;

	$allow_update = (bool)($cstart < 2 && !isset($_SESSION['pic_id']) || $_SESSION['pic_id'] != $this_foto['picture_id']);

	if ($allow_update){

		if ($galConfig['file_views']){

			if (!$config['cache_count'])
				$db->query("UPDATE " . PREFIX . "_gallery_picturies SET file_views=file_views+1 WHERE picture_id='{$this_foto['picture_id']}'");
			else
				$db->query("INSERT INTO " . PREFIX . "_gallery_picture_views (picture_id) VALUES ({$this_foto['picture_id']})");

			$_SESSION['pic_id'] = $this_foto['picture_id'];

		}

		if ($galConfig['whois_view_file'] && $is_logged && (!$galConfig['whois_view_file_day'] || $this_foto['date'] > (TIME - $galConfig['whois_view_file_day']*3600*24))){

			$db->query("INSERT IGNORE INTO " . PREFIX . "_gallery_users_views (file_id, user_id) VALUES ({$this_foto['picture_id']}, {$member_id['user_id']})");

			if (!$galConfig['file_views']) $_SESSION['pic_id'] = $this_foto['picture_id'];

		}

	}

	if ($this_album['meta_descr'] == '' && $this_album['keywords'] == '')
		create_keywords ($this_foto['picture_title']." ".$this_album['cat_title']." ".$this_foto['text']);
	else {
		$metatags['description'] = $this_album['meta_descr'];
		$metatags['keywords'] = $this_album['keywords'];
	}

	if ($galConfig['allow_comments']){

		$COMMENTS = new UnComments();
		$COMMENTS->comments_num = $this_foto['comments'];
		$COMMENTS->id = $this_foto['picture_id'];
		$COMMENTS->this_id = 'Gal';

		if ($this_foto['comments'] > 0){

			if ($config['allow_alt_url'] == "yes")
				$COMMENTS->url = array(substr($full_fotourl, 0, -5)."/com/{INS}/", $full_fotourl);
			else
				$COMMENTS->url = array($full_fotourl."&cstart={INS}", $full_fotourl);

			$COMMENTS->comm_nummers = $config['comm_nummers'];
			$COMMENTS->cstart 		= $cstart;
			$COMMENTS->template 	= 'gallery/comments';
			$COMMENTS->allow_cmod	= $galConfig['comments_mod'];
			$COMMENTS->allow_addc	= $addcomments;
			$COMMENTS->user_mod_id	= ($galConfig['allow_delete_omcomments'] && $addcomments && $is_logged && $own_admin_foto);
			$COMMENTS->doact		= 'do=gallery&act=3&dle_allow_hash='.$dle_login_hash.'&';
			$COMMENTS->allow_ajax	= $galConfig['allow_ajax_comments'];
			$COMMENTS->ShowCommentslist(0, ($addcomments && $galConfig['allow_cache'] > 2 && $this_foto['date'] > TIME - 30*86400));

		}

		if ($addcomments){

			$COMMENTS->template 	= 'gallery/addcomments';
			$COMMENTS->add_sript 	= 'doAddTWSGComments();';
			$COMMENTS->ShowAddform();

		} else {

			switch (true){
			case ($is_logged AND $config['comments_restricted'] AND ((TIME - $member_id['reg_date']) < ($config['comments_restricted'] * 86400)) ) :
				$errors = array('{error}' => $lang['news_info_8'],'{days}' => intval($config['comments_restricted']));
			break;
			case ((isset($member_id['restricted']) and $member_id['restricted'] == 2 || $member_id['restricted'] == 3)) :
				if ($member_id['restricted_days'])
					$errors = array('{error}' => $lang['news_info_2'],'{date}' => langdate( "j F Y H:i", $member_id['restricted_date']));
				else
					$errors = array('{error}' => $lang['news_info_3']);
			break;
			case ($galConfig['max_comments_days'] && $this_foto['date'] < TIME - $galConfig['max_comments_days']*3600*24) :
				$errors = array('{error}' => $lang['news_info_6'],'{days}' => intval($galConfig['max_comments_days']));
			break;
			default :
				$errors = array('{error}' => $lang['news_info_1'],'{group}' => $user_group[$member_id['user_group']]['group_name']);
			}

			$tpl->load_template( 'info.tpl' );
			$tpl->set('',  $errors);
			
			$tpl->set('{title}', $langGal['all_info']);
			$tpl->compile('content');
			$tpl->clear();

		}

	}


?>