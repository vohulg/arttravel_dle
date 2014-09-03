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
 Файл: mainpage.php
-----------------------------------------------------
 Назначение: Главная страница
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	$this_url = $galConfig['mainhref'];
	$_album_id = 0;

	if (!$is_logged && ($tpl->result['listrow'] = get_gallery_cache ('all_category_mainpage'.$cstart)) !== false){

		$tpl->result['listrow'] = unserialize($tpl->result['listrow']);
		$pageslist = $tpl->result['listrow'][1];
		$tpl->result['listrow'] = $tpl->result['listrow'][0];
		$count_cats = true;

	} else {

		$main_sql_where = array("p_id=0");

		include TWSGAL_DIR.'/modules/show.cats.php';

		if ($count_cats && !$is_logged)
			create_gallery_cache ('all_category_mainpage'.$cstart, serialize(array($tpl->result['listrow'], $pageslist)));

	}

	$tpl->load_template('gallery/mainpage.tpl');

	if ($galConfig['dinamic_symbols'] && strpos($tpl->copy_template, "{symbols}" ) !== false) $tpl->set('{symbols}', gallery_symbols());

	if ($count_cats){

		$tpl->set_block("'\\[nocats\\](.*?)\\[/nocats\\]'si","");
		$tpl->set('{categories}', $tpl->result['listrow']);
		$tpl->result['listrow'] = "";
		unset($tpl->result['listrow']);
		$tpl->set('{pages}', $pageslist);
		$pageslist = "";

	} else {

		$tpl->set('[nocats]', '');
		$tpl->set('[/nocats]', '');
		$tpl->set('{categories}', '');
		$tpl->set('{pages}', '');

	}

	if ($galConfig['show_statistic']){

		$statistic_update = array();
		$temp_day = date ('Y-m-d', TIME-3600*24);

		foreach (array('statistic_file_onmod','statistic_file','statistic_file_day','statistic_cat','statistic_cat_week','statistic_com_day','statistic_com','statistic_downloads') as $key){
			if ($galConfig[$key] == '-1'){
				switch ($key){
				case 'statistic_file_onmod' :	$dbsql = "SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies WHERE approve ='0'"; break;
				case 'statistic_file' : 	$dbsql = "SELECT SUM(cat_images) as count FROM " . PREFIX . "_gallery_category WHERE p_id=0"; break;
				case 'statistic_file_day' : 	$dbsql = "SELECT COUNT(picture_id) as count FROM " . PREFIX . "_gallery_picturies WHERE date >= '$temp_day' AND date <= '$temp_day' + INTERVAL 24 HOUR"; break;
				case 'statistic_cat' :		$dbsql = "SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category"; break;
				case 'statistic_cat_week' : 	$dbsql = "SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category WHERE reg_date >= '".date ('Y-m-d', TIME-(3600*24*7))."' AND reg_date <= '".date ('Y-m-d', TIME-(3600*24*7))."' + INTERVAL 1 WEEK"; break;
				case 'statistic_com_day' : 	$dbsql = "SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_comments WHERE date >= '$temp_day'AND date <= '$temp_day' + INTERVAL 24 HOUR"; break;
				case 'statistic_com' : 		$dbsql = "SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_comments"; break;
				case 'statistic_downloads' : 	$dbsql = "SELECT SUM(downloaded) as count FROM " . PREFIX . "_gallery_picturies"; break;
				}
				$row = $db->super_query($dbsql);
				$row['count'] = intval($row['count']);
				$galConfig[$key] = $row['count'];
				$statistic_update[$key] = "WHEN '{$key}' THEN {$row['count']}";
			}
		}

		if (count($statistic_update)){

			$db->query("UPDATE " . PREFIX . "_gallery_config SET value=CASE name ". implode(" ", $statistic_update) ." END WHERE name IN ('". implode("','", array_keys($statistic_update)) ."')");

			@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

		}

		$tpl->set('{foto_moder}',$galConfig['statistic_file_onmod']);
		$tpl->set('{foto}',$galConfig['statistic_file']);
		$tpl->set('{day_foto}',$galConfig['statistic_file_day']);
		$tpl->set('{day_comments}',$galConfig['statistic_com_day']);
		$tpl->set('{comments}',$galConfig['statistic_com']);
		$tpl->set('{week_categories}',$galConfig['statistic_cat_week']);
		$tpl->set('{countcats}',$galConfig['statistic_cat']);
		$tpl->set('{downloads}',$galConfig['statistic_downloads']);

		$found_moderate = $galConfig['statistic_file_onmod'];

	} else $found_moderate = 1;

	if ($config['allow_alt_url'] == "yes")
		$tpl->set('[foto]', '<a href="'.$galConfig['mainhref'].'all/sort-new/">');
	else
		$tpl->set('[foto]', '<a href="'.$galConfig['PHP_SELF'].'&act=15&p=sort-new/">');

	$tpl->set('[/foto]','</a>');

	$tpl->set('[upload]', '<a href="'.$galConfig['PHP_SELF'].'&act=26">');
	$tpl->set('[/upload]', '</a>');

	if ($found_moderate && $is_logged){

		$tpl->set('[moderate]', '<a href="'.$galConfig['PHP_SELF'].'&act=16">');
		$tpl->set('[/moderate]', '</a>');

	} else $tpl->set_block("'\\[moderate\\](.*?)\\[/moderate\\]'si","");

	if (check_gallery_access ("edit", "")){

		$tpl->set('[create]', '<a href="'.$galConfig['PHP_SELF'].'&act=19&dle_allow_hash='.$dle_login_hash.'">');
		$tpl->set('[/create]', '</a>');

	} elseif (check_gallery_access ("addcat", "")){

		$tpl->set('[create]', '<a href="'.$galConfig['PHP_SELF'].'&act=24">');
		$tpl->set('[/create]', '</a>');

	} else $tpl->set_block("'\\[create\\](.*?)\\[/create\\]'si","");

	if ($galConfig['allow_comments']){

		$tpl->set('[comments]', '<a href="'.$galConfig['PHP_SELF'].'&act=4">');
		$tpl->set('[/comments]', '</a>');

	} else $tpl->set_block("'\\[comments\\](.*?)\\[/comments\\]'si","");

	if (strpos ( $tpl->copy_template, "{gallery_authors" ) !== false )
		$tpl->copy_template = preg_replace( "#\\{gallery_authors categories=['\"](.+?)['\"] subcats=['\"](.+?)['\"] marker=['\"](.+?)['\"] aviable=['\"](.+?)['\"] limit=['\"](.+?)['\"] cache=['\"](.+?)['\"]\\}#ies","gallery_authors('\\1', '\\2', '\\3', '\\4', '{$do}', '\\6', '\\5')", $tpl->copy_template);

	if (strpos ( $tpl->copy_template, "{galery_tags" ) !== false ){
		include_once TWSGAL_DIR.'/classes/tagscloud.php';
		$tagscloud = new gallery_tags_cloud();
		$tpl->copy_template = preg_replace( "#\\{galery_tags limit=['\"](.+?)['\"] cache=['\"](.+?)['\"]\\}#ies","\$tagscloud->tags_tags('\\1', '\\2', false)", $tpl->copy_template);
	}

	if (strpos ( $tpl->copy_template, "{galery_foto_tag" ) !== false )
		$tpl->copy_template = preg_replace( "#\\{galery_foto_tag action=['\"](.+?)['\"] categories=['\"](.+?)['\"] subcats=['\"](.+?)['\"] template=['\"](.+?)['\"] aviable=['\"](.+?)['\"] start=['\"](.+?)['\"] vertical=['\"](.+?)['\"] horizontal=['\"](.+?)['\"] cache=['\"](.+?)['\"] search=['\"](.+?)['\"]\\}#ies","galery_foto_tags('\\1', '\\2', '\\3', '\\4', '\\5', '\\6', '{$do}', '\\7', '\\8', '\\9', '\\10')", $tpl->copy_template);

	$tpl->compile('content');

	$tpl->clear();

?>