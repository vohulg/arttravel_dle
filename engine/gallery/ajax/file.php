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
 Файл: file.php
-----------------------------------------------------
 Назначение: Работа с файлами через ajax
=====================================================
*/

@error_reporting ( E_ALL ^ E_WARNING ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE );

if (intval($_REQUEST['act']) == 1 && isset($_SESSION['lastds']) && $_SESSION['lastds'] == time()) die("");

define('DATALIFEENGINE', true);
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -20 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

include ENGINE_DIR.'/data/config.php';

if( $config['http_home_url'] == "" ) {
	
	$config['http_home_url'] = explode( "engine/gallery/ajax/file.php", $_SERVER['PHP_SELF'] );
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

case 1 : // Tags

	$term = convert_unicode($_GET['term'], $config['charset']);

	if (preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $term))
		$term = "";
	else
		$term = $db->safesql(htmlspecialchars(strip_tags(stripslashes(trim($term))), ENT_QUOTES, $config['charset']));

	$buffer = "";

	if (strlen($term) > 2){

		$_SESSION['lastds'] = time();
		$buffer = array();

		$db->query("SELECT tag_name, COUNT(m.file_id) AS count FROM " . PREFIX . "_gallery_tags_match m INNER JOIN  " . PREFIX . "_gallery_tags t ON t.id=m.tag_id WHERE tag_name LIKE '{$term}%' GROUP BY m.tag_id ORDER BY count DESC LIMIT 15");

		while($row = $db->get_row())
			$buffer[] = stripslashes($row['tag_name']);

		$db->free();

		if (count($buffer))
			$buffer = "[\"".implode("\",\"",$buffer)."\"]";
		else
			$buffer = "";

	}

break;

case 3 : // Views

	$id = intval($_REQUEST['id']);

	if (!$id || !$galConfig['whois_view_file']) die ("error");

	$db->query("SELECT u.name FROM " . USERPREFIX . "_users u INNER JOIN  " . PREFIX . "_gallery_users_views v ON v.user_id=u.user_id WHERE v.file_id={$id} ORDER BY v.id");

	$num = $db->num_rows();

	while($row = $db->get_row()){
		$row['name'] = stripslashes($row['name']);
		$buffer .= "[\"{$row['name']}\",\"".urlencode($row['name'])."\",\"".htmlspecialchars($row['name'], ENT_QUOTES, $config['charset'])."\"],";
		$langGal['js_view_titleno'] = "";
	}

	$db->free();

	$buffer = "{\"title\":\"{$langGal['js_view_title']}\",\"title_no\":\"{$langGal['js_view_titleno']}\",\"num\":\"{$num}\",\"alt\":\"{$config['allow_alt_url']}\",\"data\":[".substr($buffer, 0, -1)."]}";

break;

case 5 : // Fields control

	$data = convert_unicode($_GET['data'], $config['charset']);

	if (dle_strlen($data, $config['charset']) < 3) die ("error");

	include_once ENGINE_DIR.'/classes/parse.class.php';

	$parse = new ParseFilter();
	$parse->safe_mode = true;

	$error = array();
	$where = array();

	class gallang {
		var $lang = false;
		function gallang(){
		global $config;
			if ($config["lang_".$config['skin']]){
				include_once ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/gallery.upload.lng';
			} else {
				include_once ROOT_DIR.'/language/'.$config['langs'].'/gallery.upload.lng';
			}
		}
	}

	$gallang = new gallang();

	switch ($_REQUEST['field']){

	case 'name' :

		$data = $db->safesql($parse->process(trim($data)));

		if(($strlen = dle_strlen($data, $config['charset'])) > 20)
			$error[] = $gallang->lang['add_foto_error_33'];
		elseif ($data != "" && ($strlen < 2 || preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\{\+]/", $data)))
			$error[] = $gallang->lang['add_foto_error_36'];

		$data = strtolower($data);
		if ($data) $where[] = "LOWER(name) REGEXP '[[:<:]]".strtr($data, $relates_word)."[[:>:]]' OR name='{$data}'";

	break;

	case 'email' :

		$not_allow_symbol = array("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'" );
		$data = $db->safesql(trim(str_replace($not_allow_symbol, '', strip_tags(stripslashes($data)))));

		if (strlen($data) > 50)
			$error[] = $gallang->lang['add_foto_error_34'];
		elseif ($data != "" && !preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $data))
			$error[] = $gallang->lang['add_foto_error_35'];

		if ($data) $where[] = "email='{$data}'";

	break;

	default : die ("error");
	}

	if (!count($error) && count($where)){

		$user_exists = $db->super_query( "SELECT COUNT(user_id) as count FROM " . USERPREFIX . "_users WHERE " . implode(" OR ", $where));

		if ($user_exists['count'])
			$error[] = $gallang->lang['add_foto_error_37'];

	}

	if (count($error))
		$buffer = implode("<br />", $error);

break;

case 2 : // Rate

	if (!check_gallery_access ("rate")) die ("error");

	$go_rate = intval($_REQUEST['go_rate']);
	$id = intval($_REQUEST['id']);

	if ($go_rate > 5 || $go_rate < 0 || !$id) die ("error");

	$file = $db->super_query("SELECT p.approve, p.allow_rate, p.rating, p.vote_num, c.allow_rating FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE picture_id='{$id}'");

	if (!$file['allow_rate'] || !$file['allow_rating'] || $file['approve'] != 1) die ("error");

	if (!$is_logged){

		$member_id['user_id'] = 0;

		if (count(explode(".", $_SERVER['REMOTE_ADDR'])) == 4)
			$member_id['user_id'] = intval(str_replace(".", "", $_SERVER['REMOTE_ADDR']));

	}

	if ($member_id['user_id']){

		$db->query("INSERT IGNORE INTO " . PREFIX . "_gallery_logs (pic_id, member_key) VALUES ('{$id}', '{$member_id['user_id']}')");

		if ($db->insert_id() > 0){

			$db->query("UPDATE " . PREFIX . "_gallery_picturies set rating=rating+'$go_rate', vote_num=vote_num+1 WHERE picture_id ='{$id}'");

			$file['rating'] = $go_rate;
			$file['vote_num']++;

			clear_gallery_cache('tag_');

		}

	}

	$buffer = ShowGalRating ($id, $file['rating'], $file['vote_num'], false);

break;

case 4 : // Users Admin

	if (!$is_logged || $member_id['user_group'] != 1) die ("error");

	$term = convert_unicode($_GET['term'], $config['charset']);

	if (preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $term))
		$term = "";
	else
		$term = $db->safesql(htmlspecialchars(strip_tags(stripslashes(trim($term))), ENT_QUOTES, $config['charset']));

	$buffer = "";

	if (strlen($term) > 2){

		$buffer = array ();

		$db->query( "SELECT name FROM " . USERPREFIX . "_users WHERE name LIKE '{$term}%' LIMIT 15");

		while($row = $db->get_row())
			$buffer[] = stripslashes($row['name']);

		$db->free();

		if (count($buffer)){
			natsort($buffer);
			$buffer = "[\"".implode("\",\"",$buffer)."\"]";
		} else $buffer="";

	}

break;

default : die ("error");
}


$db->close();

@header("Content-type: text/html; charset=".$config['charset']);
echo $buffer;
exit;

?>