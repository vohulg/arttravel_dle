<?PHP
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
 Файл: search.php
-----------------------------------------------------
 Назначение: Поиск
=====================================================
*/

define('SEARCH_CACHE_PAGE', 3);
define('DB_SEARCH_CACHE', true);

function search_gallery_cache($search_info, $fstart, $fotolimit, $full_image_i = false, $full_image_preload = 10, $left_image_offset = 0){
global $db, $config, $langGal, $member_id, $user_group, $is_logged, $fsort;

	$sql_dep = array();
	$join = "";

	$sql_dep[] = "(c.view_level".(check_gallery_access ("read", "", "") ? " IN ('-1','')" : "='-1'")." OR c.view_level regexp '[[:<:]]({$member_id['user_group']})[[:>:]]')";
	if (!check_gallery_access ("edit", "", "")) $sql_dep[] = "c.locked=0";
	$sql_dep[] = "p.approve=1";

	$search_code = $search_info['search_code'] && !defined('USER_SET_SORT') ? totranslit($search_info['search_code']) : md5($search_info['symbol'].$search_info['user'].$search_info['cat'].$search_info['tag'].$search_info['story'].implode("", $sql_dep).$fsort['sort'].$fsort['msort']);

	$db->query("SELECT * FROM " . PREFIX . "_gallery_search WHERE search_code='{$search_code}' LIMIT 1");

	if ($db->num_rows())
		$search_info = $db->get_row();
	elseif ($full_image_i !== false)
		return 3;

	$db->free();

	if ($search_info['symbol'] != '') $sql_dep[] = "p.symbol='{$search_info['symbol']}'";
	if ($search_info['user'] != '') $sql_dep[] = "p.picture_user_name='{$search_info['user']}'";
	if ($search_info['cat']) $sql_dep[] = "c.id='{$search_info['cat']}'";
	if ($search_info['tag']){
		$tag_id = $db->super_query("SELECT id FROM " . PREFIX . "_gallery_tags WHERE tag_name='{$search_info['tag']}'");
		if (!$tag_id['id']) return 3;
		$join = "INNER JOIN " . PREFIX . "_gallery_tags_match m ON m.file_id=p.picture_id";
		$sql_dep[] = "m.tag_id='{$tag_id['id']}'";
	}

	if ($search_info['story'] != ''){

		$sql_search = array();

		$storywords = explode("__OR__", $db->safesql($search_info['story']));

		foreach ($storywords as $words){
			$words = preg_replace( "#\s+#i", '%', $words );
			$sql_search[] = "p.picture_title LIKE '%{$words}%' OR p.text LIKE '%{$words}%'";
		}

		$sql_dep[] = "(" . implode(" OR ", $sql_search) . ")";

	}

	$sql_dep = $join . " WHERE " . implode(" AND ", $sql_dep);

	if ($search_info['id']){

		$search_info['story'] = stripslashes($search_info['story']);
		$fsort['sort'] = $search_info['search_sort'];
		$fsort['msort'] = $search_info['search_msort'];

	} else {

		if ($search_info['story'] != '' && !$user_group[$member_id['user_group']]['allow_search']) return 2;

		$search_info['search_code'] = $search_code;

	}

	//$fstart только в режиме списка, $full_image_i в режиме карусели
	if (!$fstart) $fstart = ceil($full_image_i/$fotolimit); // Определяем страницу, видимую пользователем, которую кэшируем
	if ($fstart < 1) $fstart = 1;
	$search_block_limit = $fotolimit*((defined('SEARCH_CACHE_PAGE') && SEARCH_CACHE_PAGE > 0) ? SEARCH_CACHE_PAGE : 1); // Определяем количество файлов для одного блока кэширования
	$search_block = $search_block_limit > 0 ? (floor(($fstart-1) * $fotolimit / $search_block_limit)) : 0; // нумерация с 0. Определяем блок кэширования
	$search_block_from = $search_block*$search_block_limit;

	if (!$search_info['id'] || !$search_info['actual']){ // Если поиск не найден или он устарел - ищем снова

		$found_num = $db->super_query("SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id {$sql_dep}");

		if (!$found_num['count']) return 3;

		if (!$search_info['id']){

			$user_id = $is_logged ? $member_id['user_id'] : 0;

			$db->query("INSERT INTO " . PREFIX . "_gallery_search (search_code, search_num, ip, date, user_id, actual, symbol, user, story, cat, tag, search_sort, search_msort) VALUES ('{$search_info['search_code']}', {$found_num['count']}, '".$db->safesql($_SERVER['REMOTE_ADDR'])."', '".DATETIME."', '{$user_id}', 1, '{$search_info['symbol']}', '{$search_info['user']}', '".$db->safesql($search_info['story'])."', '{$search_info['cat']}', '{$search_info['tag']}', '{$fsort['sort']}', '{$fsort['msort']}')");

			$search_info['id'] = $db->insert_id();

		} elseif (!$search_info['actual'] || $search_info['search_num'] != $found_num['count'] || strtotime($search_info['date']) < (TIME-600)) $db->query("UPDATE " . PREFIX . "_gallery_search SET search_num={$found_num['count']}, date='".DATETIME."', actual=1 WHERE id={$search_info['id']}");

		$search_info['search_num'] = $found_num['count'];

		if ($search_info['id'] && defined('DB_SEARCH_CACHE'))
			$db->query("DELETE FROM " . PREFIX . "_gallery_search_text WHERE search_id={$search_info['id']}");

		if ($search_block_from > $search_info['search_num'] || $full_image_i > $search_info['search_num']) return 4;

	} else {

		if ($search_block_from > $search_info['search_num'] || $full_image_i > $search_info['search_num']) return 4;

		if (strtotime($search_info['date']) < (TIME-600)) $db->query("UPDATE " . PREFIX . "_gallery_search SET date='".DATETIME."' WHERE id={$search_info['id']}");

	}

	$search_info['find_files'] = $find_files = array();
	$flag_back = false;
	$fail = 0; // Защита на экстренный случай (по алгоритму произойти не должно)

	while($fail < 2){ $fail++;

		$temp_find_files = false;

		if ($search_info['actual']){

			if (defined('DB_SEARCH_CACHE')){
				$temp_find_files = $db->super_query("SELECT find_files FROM " . PREFIX . "_gallery_search_text WHERE search_id={$search_info['id']} AND search_page='{$search_block}' LIMIT 1");
				if (isset($temp_find_files['find_files']) && $temp_find_files['find_files']) $temp_find_files = explode(",",$temp_find_files['find_files']);
			} else $temp_find_files = get_gallery_vars ("search", $search_info['id']."_".$search_block);

		}

		if (!$temp_find_files){ // Если поиск не найден или он устарел - ищем снова

			$db->query("SELECT p.picture_id FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id {$sql_dep} ORDER BY {$fsort['sort']} {$fsort['msort']}, picture_id LIMIT {$search_block_from}, {$search_block_limit}");

			while($row = $db->get_row())
				$temp_find_files[] = $row['picture_id'];

			$db->free();

			if (defined('DB_SEARCH_CACHE'))
				$db->query("INSERT INTO " . PREFIX . "_gallery_search_text (search_id, search_page, find_files) VALUES ({$search_info['id']}, '{$search_block}', '".implode(",",$temp_find_files)."')" );
			else
				create_gallery_vars ("search", $temp_find_files, $search_info['id']."_".$search_block);

		}

		if ($full_image_i === false){ // Если условие истинно, то тут всё просто.

			$i = ($fstart-1)*$fotolimit - $search_block_from;
			$to = min(($i+$fotolimit), count($temp_find_files));

			for (;$i<$to; $i++) $search_info['find_files'][] = $temp_find_files[$i];

			return $search_info;
		}

		$find_files = $flag_back ? array_merge($temp_find_files, $find_files) : array_merge($find_files, $temp_find_files);

		$page_i = $full_image_i - $search_block_from;

		if ($search_block && $left_image_offset && $page_i < $left_image_offset){
			$flag_back = true;
			$search_block--;
			$search_block_from = $search_block*$search_block_limit;
			continue;
		}

		if ((($search_info['count'] = count($find_files)) - $page_i) < ($full_image_preload-$left_image_offset) && !$flag_back && ($search_block+1)*$search_block_limit < $search_info['search_num']){
			$search_block++;
			$search_block_from = $search_block*$search_block_limit;
			continue;
		}

		break;

	}

	$i = max($page_i - $left_image_offset - 1, 0);
	$to = min($i + $full_image_preload, $search_info['count']);

	if ($to - $i < $full_image_preload && $i > 0)
		$i = max($to - $full_image_preload, 0);

	$search_info['first_i'] = $i + $search_block_from;
	$search_info['start'] = max($full_image_i - $search_info['first_i'] - $left_image_offset - 1, 0);

	for (; $i < $to; $i++) $search_info['find_files'][] = $find_files[$i];

	return $search_info;
}

?>