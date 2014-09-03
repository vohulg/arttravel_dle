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
 Файл: redirect.php
-----------------------------------------------------
 Назначение: Перенаправления
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

switch ($jump){

case 'category' :

	$arr_album = explode("/", $_album);

	@header("HTTP/1.1 301 Moved Permanently");
	@header("Location: ".$config['http_home_url'].$galConfig['work_postfix'].end($arr_album)."/");
	echo "Redirect";

break;

case 'seo' :

	if ($config['allow_alt_url'] == "yes"){

		if ($this_foto['category_id'] != $this_album['id'])
			$this_album = $db->super_query("SELECT cat_alt_name FROM " . PREFIX . "_gallery_category WHERE id={$this_foto['category_id']}");

		$fotourl = $galConfig['mainhref'].$this_album['cat_alt_name']."/";

		if ($this_foto['picture_alt_name'])
			$fotourl .= $this_foto['picture_id']."-".$this_foto['picture_alt_name'].".html";
		else
			$fotourl .= $this_foto['picture_id'].".html";

	} else $fotourl = $galConfig['mainhref']."&act=2&cid=".$this_foto['category_id']."&fid=".$this_foto['picture_id'];

	@header("HTTP/1.1 301 Moved Permanently");
	@header("Location: ".$fotourl);
	echo "Redirect";

break;

case 'next' :
case 'previous' :

	$category_id = intval($_REQUEST['jc']);
	$picture_id = intval($_REQUEST['j']);

	if (!$category_id || !$picture_id){
		echo ("Picture or category not found!");
		break;
	}

	$this_cat = $db->super_query("SELECT id, cat_alt_name, foto_sort, foto_msort FROM " . PREFIX . "_gallery_category WHERE id='{$category_id}'");

	if (!$this_cat['id']){
		echo ("Picture or category not found!");
		break;
	}

	$fsort = foto_sort($this_cat);

	if (!isset($fsort['sort_set']))
		$cat_picturies = get_gallery_vars ("foto_ids", $category_id);

	if (isset($fsort['sort_set']) || !is_array($cat_picturies) || !count($cat_picturies)){

		$cat_picturies = array();

		$db->query("SELECT p.picture_id FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE p.category_id='".$category_id."' AND p.approve='1' ORDER BY {$fsort['sort']} {$fsort['msort']}, picture_id");

		while($row = $db->get_row())
			$cat_picturies[] = $row['picture_id'];

		$db->free();

		if (!isset($fsort['sort_set'])) create_gallery_vars ("foto_ids", $cat_picturies, $category_id);

	}

	//PAV (2007)
	
	$is_id = false;
	$tmp_prev = 0;

	for ($ii = 0; $ii < count($cat_picturies); $ii++){

		if ($is_id){
			$next = $cat_picturies[$ii];
			break;
		}
		if ($cat_picturies[$ii] == $picture_id) {
			$previous = $tmp_prev;
			$is_id = true;
		}
		$tmp_prev = $cat_picturies[$ii];

	}

	//PAV (2007)

	$db->free();

	switch ($jump){
	case 'next' :
		$jump_id = $next;
	break;
	case 'previous' :
		$jump_id = $previous;
	break;
	default: $jump_id = 0;
	}

	if ($config['allow_alt_url'] == "yes")
		$cat_url = $config['http_home_url'].$galConfig['work_postfix'].$this_cat['cat_alt_name']."/";
	else
		$cat_url = $config[http_home_url]."index.php?do=gallery&act=1&cid={$category_id}";

	if ($jump_id){

		if ($config['allow_alt_url'] == "yes"){

			$row = $db->super_query("SELECT picture_alt_name FROM " . PREFIX . "_gallery_picturies WHERE picture_id='".$jump_id."' AND picture_alt_name != ''");

			if ($row['picture_alt_name'])
				$cat_url .= $jump_id."-".$row['picture_alt_name'].".html";
			else
				$cat_url .= $jump_id.".html";

		} else $cat_url = $config['http_home_url']."index.php?do=gallery&act=2&cid={$category_id}&fid={$jump_id}";

	}

	@header("Location: ".$cat_url);
	echo "Redirect disabled!<br /><br />Please visit <a href=\"{$cat_url}\">{$cat_url}</a>";

break;

case 'install' :

	if (version_compare($galConfig['version_gallery'], "5.2", "<")){

		if (file_exists(ROOT_DIR . '/galleryinstall.php'))
			@header( "Location: ".str_replace("index.php","galleryinstall.php",$_SERVER['PHP_SELF']) );

	} elseif (file_exists(ROOT_DIR . '/galupdate/index.php'))
		@header( "Location: ".str_replace("index.php","galupdate/index.php",$_SERVER['PHP_SELF']) );

	die("TWS Gallery not installed or installation incorrect! Files version is 5.2, db version is ".($galConfig['version_gallery'] ? $galConfig['version_gallery'] : '--')."! Please, run galleryinstall.php!");

break;

}

$db->close ();
die();

/*

function get_category_files($this_album){

	$fsort = foto_sort($this_album);

	if (!isset($fsort['sort_set'])) $cat_files = get_gallery_vars ("cat_files", $this_album['category_id']);

	if (isset($fsort['sort_set']) || !is_array($cat_files)){

		$cat_files = array();

		$db->query("SELECT picture_id FROM " . PREFIX . "_gallery_picturies WHERE category_id={$category_id} AND approve=1 ORDER BY {$fsort['sort']} {$fsort['msort']}, picture_id LIMIT ".($search_page*$search_page_limit).", ".$search_page_limit);
		while($row = $db->get_row()){
			$cat_picturies[] = $row['picture_id'];
		}
		$db->free();

		if (!isset($fsort['sort_set'])) create_gallery_vars ("cat_files", $cat_files, $category_id);

	}

	return $cat_files;

}

*/

?>