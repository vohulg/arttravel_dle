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
 Файл: show.carpusel.php
-----------------------------------------------------
 Назначение: Показ карусели миниатюрных фотографий
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	$search_parametrs = '';
	$carousel_active = false;
	if ($galConfig['buffer_in_fullimage'] < 10) $galConfig['buffer_in_fullimage'] = 10;

	if ($_search_code != '' && $_search_i !== false){

		$carousel_active = 0;

		include_once TWSGAL_DIR.'/modules/search.php';

		$fotolimit = $galConfig['foto_tr'] * $galConfig['foto_td'];

		$carousel = search_gallery_cache(array('search_code' => $_search_code), 0, $fotolimit, $_search_i, $galConfig['buffer_in_fullimage'], $left_image_offset);

		if (is_array($carousel)){

			$carousel_active = true;
			$search_parametrs = ','.$carousel['search_code'].',{reali}';

		}

	}

	if ($carousel_active === false && $this_album['id'] && $this_album['allow_carousel']){

		$carousel_active = 0;

		$fsort = foto_sort($this_album);
		$carousel = array('search_code' => '');

		if (!isset($fsort['sort_set'])) $find_files = get_gallery_vars ("foto_ids", $this_album['id']);

		if (isset($fsort['sort_set']) || !is_array($find_files)){

			$find_files = array();

			$db->query("SELECT picture_id FROM " . PREFIX . "_gallery_picturies WHERE category_id='".$this_album['id']."' AND approve='1' ORDER BY {$fsort['sort']} {$fsort['msort']}, picture_id");

			while($row = $db->get_row())
				$find_files[] = $row['picture_id'];

			$db->free();

			if (!isset($fsort['sort_set'])) create_gallery_vars ("foto_ids", $find_files, $this_album['id']);

		}

		$carousel['search_num'] = count($find_files);

		if ($_search_i === false)
			$_search_i = intval(array_search($this_foto['picture_id'], $find_files));

		$carousel['first_i'] = max($_search_i - $left_image_offset, 0);
		$to = min($carousel['first_i'] + $galConfig['buffer_in_fullimage'], $carousel['search_num']);

		if ($to - $carousel['first_i'] < $galConfig['buffer_in_fullimage'] && $carousel['first_i'] > 0)
			$carousel['first_i'] = max($to - $galConfig['buffer_in_fullimage'], 0);

		$carousel['start'] = max($_search_i - $carousel['first_i'] - $left_image_offset, 0);

		for ($i = $carousel['first_i']; $i < $to; $i++) $carousel['find_files'][] = $find_files[$i];

		$carousel_active = true;

	}

	if (!$carousel_active) return ($carousel_active === false ? ($carousel = 0) : false);

	if (!($num = count($carousel['find_files']))){

		if (defined('AJAX_ACTION') && AJAX_ACTION)
			return ($buffer = "{\"error\":\"0\",\"search_code\":\"{$carousel['search_code']}\",\"allnum\":\"{$carousel['search_num']}\",\"album_id\":\"".intval($this_album['id'])."\",\"length\":\"0\",\"data\":{}}");
		else
			return;

	} else if ((!defined('AJAX_ACTION') || !AJAX_ACTION) && $num < 2) return ($carousel = 0);

	$carousel['find_files'] = implode(",", $carousel['find_files']);

	if ($galConfig['allow_cache'] < 3 || $_search_i > $galConfig['thumbs_in_fullimage']*5 || $is_logged || $fsort['sort_set']) $carousel_cache = false;

	if ($carousel_cache){
		$carousel_cache = 'all_files_carousel_'.md5($carousel['find_files'].$fsort['sort'].$fsort['msort']);
		$buffer = get_gallery_cache ($carousel_cache);
	} else $buffer = false;

	if ($buffer === false){

		$buffer = "";

		$db->query("SELECT p.*, c.id, c.cat_title, c.cat_short_desc, c.cat_alt_name, c.user_name, c.edit_level, c.moderators, c.allow_rating, c.allow_comments, c.allow_user_admin FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE p.picture_id IN ({$carousel['find_files']}) ORDER BY {$fsort['sort']} {$fsort['msort']}, picture_id LIMIT ".$num);

		$i = $carousel['first_i'];

		while($row = $db->get_row()){

			if ($config['allow_alt_url'] == "yes"){

				if ($row['picture_alt_name'])
					$fotourl = $row['cat_alt_name']."/".$row['picture_id'].$search_parametrs."-".$row['picture_alt_name'].".html";
				else
					$fotourl = $row['cat_alt_name']."/".$row['picture_id'].$search_parametrs.".html";

			} else $fotourl = "&act=2&cid=".$row['category_id']."&fid=".$row['picture_id'].$search_parametrs;

			if ($search_parametrs) $fotourl = str_ireplace(array("{reali}", $carousel['search_code']), array($i+1, "{sid}"), $fotourl);

			$row['image_alt_title'] = stripslashes($row['image_alt_title']);
			$row['picture_title'] = stripslashes($row['picture_title']);

			if ($fsort['msort'] != "asc")
				$ii = $i + 1;
			else
				$ii = $carousel['search_num'] - $i + 2;

			if ($row['picture_title'] == '')
				$row['picture_title'] = str_ireplace(array("{%i%}", "{%posi%}", "{%category%}", "{%filename%}"), array($ii, intval($row['posi']), $row['cat_title'], preg_replace("/(.*)\.[a-z0-9\_]+$/i","\\1", $row['picture_filname'])), stripslashes($galConfig['empty_title_template']));

			if (!$row['media_type'] || $row['preview_filname']){

				$thumb_path = thumb_path($row['thumbnails'], 't');

				if ($thumb_path != 'main')
					$thumb_path = '{FOTO_URL}/'.$thumb_path.'/'.$row['category_id'].'/'.$row[($row['preview_filname'] ? 'preview_filname' : 'picture_filname')];
				elseif (!$row['type_upload'])
					$thumb_path = '{FOTO_URL}/main/'.$row['category_id'].'/'.$row['picture_filname'];
				else
					$thumb_path = $row['full_link'];

			} else $thumb_path = "{THEME}/gallimages/extensions/".get_extension_icon ($row['picture_filname'], $row['media_type']);

			$row['picture_title'] = str_replace(array("{", "}"), '', addcslashes($row['picture_title'], "\v\t\n\r\f\"\\/"));
			$row['image_alt_title'] = str_replace(array("{", "}"), '', addcslashes($row['image_alt_title'], "\v\t\n\r\f\"\\/"));

			$buffer .= "\"{$i}\":{\"0\":\"{$thumb_path}\",\"1\":\"{$fotourl}\",\"2\":\"{$row['picture_title']}\",\"3\":\"{$row['image_alt_title']}\"},";

			$i++;

		}

		$db->free();

		if ($buffer) $buffer = substr($buffer, 0, -1);

		if ($carousel_cache)
			create_gallery_cache ($carousel_cache, $buffer);

	}

	$carousel['last_i'] = $i - 1;
	$buffer = "{\"error\":\"0\",\"search_code\":\"{$carousel['search_code']}\",\"allnum\":\"{$carousel['search_num']}\",\"album_id\":\"".intval($this_album['id'])."\",\"last\":\"{$carousel['last_i']}\",\"first\":\"{$carousel['first_i']}\",\"start\":\"{$carousel['start']}\",\"length\":\"".($i-$carousel['first_i'])."\",\"data\":{".$buffer."}}";

	if (defined('AJAX_ACTION') && AJAX_ACTION) return;

	$carousel['jkey'] = 'cr'.md5(microtime());

	$ajax .= "<script language=\"javascript\" type=\"text/javascript\">
<!--
$(function(){
	new gallery_jcarousel('{$carousel['jkey']}', ".intval($galConfig['show_in_fullimage']).", ".($galConfig['thumbs_mousewheel'] ? "true" : "false").", {$buffer}, '".str_replace(array("'", "\n", "\r"), array("\"", "", ""), $galConfig['thumbs_template'])."', '{$galConfig['thumbs_fx']}');
});
//-->
</script>\n";

	unset($buffer);

	if (defined('JCAROUSEL_INIT')) return;

	define('JCAROUSEL_INIT', true);

	if ($galConfig['thumbs_mousewheel']) $js_array[] = "engine/gallery/js/jquery.mousewheel.min.js";
	$js_array[] = "engine/gallery/js/carouFredSel.js";
	$js_array[] = "engine/gallery/js/carouFredSel_handlers.js";

?>