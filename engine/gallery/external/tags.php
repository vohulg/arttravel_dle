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
 Файл: tags.php
-----------------------------------------------------
 Назначение: Файл вывода файлов галереи в тегах
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

require_once ENGINE_DIR.'/gallery/functions/web.php';

$action = totranslit($action, true, false);
$cache = $cache ? $cache : $config['allow_cache'];

switch ($action){
case 'authors' :
	echo gallery_authors($categories, $subcats, $marker, $aviable, $dle_module, $cache, $limit);
break;
case 'tags' :
	include_once TWSGAL_DIR.'/classes/tagscloud.php';
	$tagscloud = new gallery_tags_cloud();
	echo $tagscloud->tags_tags($limit, $cache, $no_button);
break;
default :
	if (!isset($media_type)) $media_type = -1;
	echo galery_foto_tags ($action, $categories, $subcats, $template, $aviable, $start, $dle_module, $vertical, $horizontal, $cache, $search, $member_name, $media_type);
}

?>