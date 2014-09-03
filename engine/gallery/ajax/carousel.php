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
 Файл: carousel.php
-----------------------------------------------------
 Назначение: Карусель файлов с загрузкой через ajax
=====================================================
*/

@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );

define('DATALIFEENGINE', true);
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -20 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

include ENGINE_DIR.'/data/config.php';

if( $config['http_home_url'] == "" ) {

	$config['http_home_url'] = explode( "engine/gallery/ajax/carousel.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require_once ENGINE_DIR.'/modules/functions.php';

if (version_compare($config['version_id'], "9.6", ">"))
	dle_session();
else
	@session_start();

$_REQUEST['skin'] = trim(totranslit($_REQUEST['skin'], false, false));

if( $_REQUEST['skin'] == "" OR !@is_dir( ROOT_DIR . '/templates/' . $_REQUEST['skin'] ) ) {
	die( "Hacking attempt!" );
}

if ($config["lang_".$_REQUEST['skin']]) { 

	if (file_exists(ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/website.lng')){
		@include_once ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/website.lng';
	} else die("Language file not found");

	if (file_exists(ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/gallery.web.lng')){
		@include_once ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/gallery.web.lng';
	} else die("Language file not found");

} else {

    include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';
	include_once ROOT_DIR.'/language/'.$config['langs'].'/gallery.web.lng';

}

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];
$is_logged = false;
$member_id = array ();

//################# Определение групп пользователей
$user_group = get_vars ("usergroup");

if (!$user_group) {
  $user_group = array ();

  $db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");

  while($row = $db->get_row()){

   $user_group[$row['id']] = array ();

	 foreach ($row as $key => $value)
	 {
	   $user_group[$row['id']][$key] = stripslashes($value);
	 }

  }
  set_vars ("usergroup", $user_group);
  $db->free();
}

if ($config['allow_registration'] == "yes") {

	require_once ENGINE_DIR.'/modules/sitelogin.php';

	if (!$is_logged) $member_id['user_group'] = 5;

	if ($member_id['user_group'] != '1' && !$user_group[$member_id['user_group']]['allow_offline'] && ($galConfig['off'] == "1" || $config['site_offline'] == "yes")) die("error");

} elseif ($galConfig['off'] == "1" || $config['site_offline'] == "yes") die("error");

define('AJAX_ACTION', true);

require_once ENGINE_DIR.'/gallery/functions/web.php';

if (!$galConfig['buffer_in_fullimage']) die("error");

$buffer = "{\"error\":\"1\"}";

$_search_code = totranslit($_REQUEST['search_code'], true, false);
$_search_i = intval($_REQUEST['search_i']);
$_album_id = intval($_REQUEST['album_id']);

switch (true){
case true :

	if ($_search_i < 0 || ($_album_id < 1 && $_search_code == '')) break;

	if ($_search_code == ''){

		$this_album = $db->super_query("SELECT id, foto_sort, foto_msort, view_level, locked, allow_carousel FROM " . PREFIX . "_gallery_category WHERE id={$_album_id}");

		if (!$this_album['id'] || !check_gallery_access ("read", $this_album['view_level'], "", $this_album['locked'])) break;

	} else $_search_i++;

	$left_image_offset = 0;
	$carousel_cache = false;

	require_once TWSGAL_DIR.'/modules/show.carousel.php'; //include_once

	if (!is_array($carousel)){

		$error_text = ($carousel != 2) ? $langGal['search_err_2'] : str_ireplace('{group}', $user_group[$member_id['user_group']]['group_name'], $lang['search_denied']);

		$buffer = "{\"error\":\"2\",\"error_text\":\"{$error_text}\"}";

	}

break;

}

$db->close();

$buffer = str_ireplace('{THEME}', $config['http_home_url'].'templates/'.$config['skin'], $buffer);

@header("Content-type: text/html; charset=".$config['charset']);
echo $buffer;
exit;

?>