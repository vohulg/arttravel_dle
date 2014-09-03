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
 Файл: editfile.php
-----------------------------------------------------
 Назначение: Редактирование фотографий через ajax
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
	
	$config['http_home_url'] = explode( "engine/gallery/ajax/editfile.php", $_SERVER['PHP_SELF'] );
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

//####################################################################################################################
//                    Определение забаненных пользователей и IP
//####################################################################################################################
$banned_info = get_vars ( "banned" );

if (! is_array ( $banned_info )) {
	$banned_info = array ();
	
	$db->query ( "SELECT * FROM " . USERPREFIX . "_banned" );
	while ( $row = $db->get_row () ) {
		
		if ($row['users_id']) {
			
			$banned_info['users_id'][$row['users_id']] = array (
																'users_id' => $row['users_id'], 
																'descr' => stripslashes ( $row['descr'] ), 
																'date' => $row['date'] );
		
		} else {
			
			if (count ( explode ( ".", $row['ip'] ) ) == 4)
				$banned_info['ip'][$row['ip']] = array (
														'ip' => $row['ip'], 
														'descr' => stripslashes ( $row['descr'] ), 
														'date' => $row['date']
														);
			elseif (strpos ( $row['ip'], "@" ) !== false)
				$banned_info['email'][$row['ip']] = array (
															'email' => $row['ip'], 
															'descr' => stripslashes ( $row['descr'] ), 
															'date' => $row['date'] );
			else $banned_info['name'][$row['ip']] = array (
															'name' => $row['ip'], 
															'descr' => stripslashes ( $row['descr'] ), 
															'date' => $row['date'] );
		
		}
	
	}
	set_vars ( "banned", $banned_info );
	$db->free ();
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

require_once ENGINE_DIR.'/modules/sitelogin.php';
require_once ENGINE_DIR.'/classes/templates.class.php';

if (!$is_logged) die ("error");
if ($dle_login_hash == "" || $_REQUEST['dle_allow_hash'] != $dle_login_hash) die("error");
//if (!$is_logged) $member_id['user_group'] = 5;
if (isset( $banned_info['ip'] ) ) $blockip = check_ip ( $banned_info['ip'] );  else $blockip = false;
if (($is_logged AND $member_id['banned'] == "yes") OR $blockip) die("error");

define('AJAX_ACTION', true);

require_once ENGINE_DIR.'/gallery/functions/web.php';
require_once TWSGAL_DIR.'/classes/editcategory.php';

if ($member_id['user_group'] != '1' && !$user_group[$member_id['user_group']]['allow_offline'] && ($galConfig['off'] == "1" || $config['site_offline'] == "yes")) die("error");
if ($galConfig['enable_banned'] && check_banned()) die("error");

if (isset($_POST['send_notice_text']))
	$_POST['send_notice_text'] = convert_unicode($_POST['send_notice_text'], $config['charset']);
else
	$_POST['send_notice_text'] = "";

require_once TWSGAL_DIR.'/classes/editfile.php';

$edit = new gallery_file_edit();
$buffer = "";
$js_array = array();

switch (intval($_REQUEST['act'])){
	case 1 :

		$edit->status(5, true);

		if ($edit->stat['value'] == 1)
			$buffer = '[HTML:Ok]'.$langGal['set_foto_ap1'].'[END:HTML:Ok]';
		else
			$buffer = '[HTML:Ok]'.$langGal['set_foto_ap2'].'[END:HTML:Ok]';

	break;
	case 2 :

		$edit->status(9, true);

		if ($edit->stat['value'] == 1)
			$buffer = '[HTML:Ok]'.$langGal['set_foto_com1'].'[END:HTML:Ok]';
		else
			$buffer = '[HTML:Ok]'.$langGal['set_foto_com2'].'[END:HTML:Ok]';

	break;
	case 3 :

		$edit->status(11, true);

		if ($edit->stat['value'] == 1)
			$buffer = '[HTML:Ok]'.$langGal['set_foto_rt1'].'[END:HTML:Ok]';
		else
			$buffer = '[HTML:Ok]'.$langGal['set_foto_rt2'].'[END:HTML:Ok]';

	break;
	case 4 :

		$edit->message();

		$buffer = '[HTML:Ok]'.$langGal['set_foto_mok'].'[END:HTML:Ok]';

	break;
	case 5 :

		$edit->remove();

		if (!$edit->access_error && $edit->affected_files)
			$buffer = ((!isset($_REQUEST['re']) || $_REQUEST['re'] != 1) && isset($_SESSION['gallery_referrer'])) ? $_SESSION['gallery_referrer'] : ($galConfig['mainhref'].(($config['allow_alt_url'] == "yes") ? $edit->stat['cat_alt_name']."/" : "&act=1&cid=".$edit->stat['category_id']));

	break;

}

if (count($edit->error_result))
	$buffer = '[HTML:Errors]'.implode("<br /><br />", $edit->error_result).'[END:HTML:Errors]';
elseif ($edit->access_error)
	$buffer = '[HTML:Errors]'.$langGal['access_error'].'[END:HTML:Errors]';
elseif (!$edit->affected_files)
	$buffer = '[HTML:Errors]'.$langGal['mass_denied'].'[END:HTML:Errors]';

$db->close();

@header("Content-type: text/html; charset=".$config['charset']);
echo $buffer;
exit;

?>