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
 Назначение: Работа с категориями через ajax
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
	
	$config['http_home_url'] = explode( "engine/gallery/ajax/category.php", $_SERVER['PHP_SELF'] );
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

$is_logged = false;
$member_id = array ();

if (intval($_REQUEST['act'])%2 == 0){ // Авторизацию подключаем для модулей с чётным $_REQUEST['act'].

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

define('AJAX_ACTION', true);

require_once ENGINE_DIR.'/gallery/functions/web.php';

if (intval($_REQUEST['act'])%2 == 0 && $config['allow_registration'] == "yes"){

	include_once ENGINE_DIR.'/modules/sitelogin.php';

	if (!$is_logged) $member_id['user_group'] = 5;
	if ($member_id['banned'] == "yes") die("error");
	if ($member_id['user_group'] != '1' && !$user_group[$member_id['user_group']]['allow_offline'] && ($galConfig['off'] == "1" || $config['site_offline'] == "yes")) die("error");

}

$buffer = "";

switch(intval($_REQUEST['act'])){

case 1 : // Moderators

	$_album_id = intval($_REQUEST['id']);
	if (!$_album_id) die ("error");

	$this_album = $db->super_query("SELECT moderators FROM " . PREFIX . "_gallery_category WHERE id='{$_album_id}'");

	if (!$this_album['moderators']) break;

	$db->query("SELECT name, user_id FROM " . USERPREFIX . "_users WHERE user_id IN ({$this_album['moderators']}) ORDER BY user_id");

	$num = $db->num_rows();

	$moderators = array();

	while($row = $db->get_row()){
		$moderators[] = $row['user_id'];
		$row['name'] = stripslashes($row['name']);
		$buffer .= "[\"{$row['name']}\",\"".urlencode($row['name'])."\",\"".htmlspecialchars($row['name'], ENT_QUOTES, $config['charset'])."\"],";
	}

	$db->free();

	$this_album['moderators'] = explode(',',$this_album['moderators']);

	if ($num != count($this_album['moderators']))
		$db->query("UPDATE " . PREFIX . "_gallery_category SET moderators='".implode(',',$moderators)."' WHERE id='{$_album_id}'");

	$buffer = "{\"title\":\"{$langGal['js_cat_moderators']}\",\"num\":\"{$num}\",\"alt\":\"{$config['allow_alt_url']}\",\"data\":[".substr($buffer, 0, -1)."]}";

break;

default : die ("error");
}


$db->close();

@header("Content-type: text/html; charset=".$config['charset']);
echo $buffer;
exit;

?>