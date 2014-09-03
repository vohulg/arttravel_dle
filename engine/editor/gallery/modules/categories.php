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

	$this_url = $galConfig['mainhref'];
	$count_cats = 0;
	$gal_user = "";
	$main_sql_where = array();

	if (isset($_REQUEST['gal_user'])){

		$gal_user = @strip_tags(str_replace('/', '', trim(urldecode($_REQUEST['gal_user']))));

		if ($config['charset'] == "windows-1251" && md5($gal_user) != md5(iconv('windows-1251', 'windows-1251', $gal_user)))
			$gal_user = iconv( "UTF-8", "windows-1251//IGNORE", $gal_user);

		$gal_user = $db->safesql($gal_user);

	}

	if ($gal_user != ''){

		$main_sql_where[] = "user_name='{$gal_user}'";

		$gal_user = stripslashes($gal_user);

		if ($config['allow_alt_url'] == "yes")
			$this_url .= "users/".urlencode($gal_user)."/";
		else 
			$this_url .= "&act={$act}&gal_user=".urlencode($gal_user);

		$metatags['title'] = str_ireplace('{user}', $gal_user, $langGal['menu_title101']);

	}

	$cache_category_id = '';

	if (!$is_logged && $galConfig['allow_cache'] > 1 && $cstart < 6){

		$cache_category_id = 'all_categories_'.md5($cstart.$gal_user);

		if (($tpl->result['content'] = get_gallery_cache ($cache_category_id)) !== false){

			$tpl->result['content'] = unserialize($tpl->result['content']);

			if (!$tpl->result['content'][1]){
				@header("HTTP/1.0 404 Not Found");
				msgbox ($langGal['all_err_1'], $langGal['no_that_cats']."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
				return;
			}

			$metatags['title'] = $tpl->result['content'][2];
			$s_navigation = $tpl->result['content'][3];
			$tpl->result['content'] = $tpl->result['content'][0];			

			return;
		}

	}

	include TWSGAL_DIR.'/modules/show.cats.php';

	if (!$count_cats){
		@header("HTTP/1.0 404 Not Found");
		msgbox ($langGal['all_err_1'], $langGal['no_that_cats']."<br /><br /><a href=\"{$galConfig['mainhref']}\">".$langGal['all_prev']."</a>");
		return;
	}

	$tpl->load_template('gallery/categories.tpl');

	if ($galConfig['dinamic_symbols'] && strpos($tpl->copy_template, "{symbols}" ) !== false) $tpl->set('{symbols}', gallery_symbols());

	$tpl->set('{categories}', $tpl->result['listrow']);
	$tpl->result['listrow'] = "";
	$tpl->set('{category_pages}', $pageslist);
	$pageslist = "";

	$tpl->set('{title}', $metatags['title']);
	$tpl->set('{navigator}', $s_navigation . " &raquo; <a href=\"".$this_url."\">".$metatags['title']."</a>");

	if ($cstart > 1) $metatags['title'] .= ' &raquo; '.$lang['news_site'].' '.$cstart;

	$s_navigation .= " &raquo; " . $metatags['title'];

	if (check_gallery_access ("edit", "")){

		$tpl->set('[create]', '<a href="'.$galConfig['PHP_SELF'].'&act=19&dle_allow_hash='.$dle_login_hash.'">');
		$tpl->set('[/create]', '</a>');

	} elseif (check_gallery_access ("addcat", "")){

		$tpl->set('[create]', '<a href="'.$galConfig['PHP_SELF'].'&act=24">');
		$tpl->set('[/create]', '</a>');

	} else $tpl->set_block("'\\[create\\](.*?)\\[/create\\]'si","");

	$tpl->set('[upload]', '<a href="'.$galConfig['PHP_SELF'].'&act=26">');
	$tpl->set('[/upload]', '</a>');

	if ($config['allow_alt_url'] == "yes")
		$tpl->set('[foto]', '<a href="'.$galConfig['mainhref'].'all/sort-new/">');
	else
		$tpl->set('[foto]', '<a href="'.$galConfig['PHP_SELF'].'&act=15&p=sort-new/">');

	$tpl->set('[/foto]','</a>');

	if ($galConfig['allow_comments']){

		$tpl->set('[comments]', '<a href="'.$galConfig['PHP_SELF'].'&act=4">');
		$tpl->set('[/comments]', '</a>');

	} else $tpl->set_block("'\\[comments\\](.*?)\\[/comments\\]'si","");

	$tpl->compile('content');
	$tpl->clear();

	if ($cache_category_id)
		create_gallery_cache ($cache_category_id, serialize(array($tpl->result['content'], $count_cats, $metatags['title'], $s_navigation)));

?>