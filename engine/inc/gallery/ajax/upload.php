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
 Файл: upload.php
-----------------------------------------------------
 Назначение: Модуль загрузки через AJAX
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

	$config['http_home_url'] = explode( "engine/gallery/ajax/upload.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require_once ENGINE_DIR.'/modules/functions.php';

if (version_compare($config['version_id'], "9.6", ">")){

	dle_session((isset($_POST["PHPSESSID"]) && $_REQUEST['action'] == 'upload') ? $_POST["PHPSESSID"] : false);

} else {

	if(isset($_POST["PHPSESSID"]) && $_REQUEST['action'] == 'upload') @session_id($_POST["PHPSESSID"]);

	@session_start();

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

$_REQUEST['skin'] = trim(totranslit($_REQUEST['skin'], false, false));

if( $_REQUEST['skin'] == "" OR !@is_dir( ROOT_DIR . '/templates/' . $_REQUEST['skin'] ) ) {
	die( "Hacking attempt!" );
}

if ($config["lang_".$config['skin']]){

	if ( file_exists( ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/website.lng' ) ) {	
		include_once ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/website.lng';
	} else die("Language file not found");

} else {

	 include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';

}

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR.'/modules/sitelogin.php';

if (!$is_logged) $member_id['user_group'] = 5;
if (($member_id['user_group'] != '1' && !$user_group[$member_id['user_group']]['allow_offline'] && ($galConfig['off'] == "1" || $config['site_offline'] == "yes"))) die("error");
if ( isset( $banned_info['ip'] ) ) $blockip = check_ip ( $banned_info['ip'] );  else $blockip = false;
if (($is_logged AND $member_id['banned'] == "yes") OR $blockip) die("error");

define('AJAX_ACTION', true);

require_once ENGINE_DIR.'/gallery/functions/web.php';
require_once TWSGAL_DIR.'/classes/upload.action.php';

switch ($_REQUEST['action']){
case 'rules' :

	$id = intval($_REQUEST['id']);

	if ($id){

		$upload = new gallery_upload_action();
		$buffer = $upload->category_rules($id);
		$buffer = "<script language=\"javascript\" type=\"text/javascript\">
{$buffer[3]}{$buffer[4]}
if (swfu){
swfu.setFileSizeLimit('{$buffer[2]} KB');
swfu.setFileTypes('{$buffer[1]}','{$buffer[1]}');
}
</script>{$buffer[0]}";

	} else $buffer = $langGal['js_menu_chse_nes'];

break;
case 'upload' :

	include_once TWSGAL_DIR.'/classes/editfile.php';
	include_once TWSGAL_DIR.'/classes/editcategory.php';
	include_once TWSGAL_DIR.'/classes/upload.php';
	include_once TWSGAL_DIR.'/classes/thumbnailer.php';

	$upload = new gallery_upload_action($_REQUEST['cat']);
	$upload->gallery_upload_init(1);
	$buffer = $upload->buffer;

break;
case 'complete' :

	include_once ENGINE_DIR.'/classes/templates.class.php';
	include_once TWSGAL_DIR.'/classes/editfile.php';
	include_once TWSGAL_DIR.'/classes/editcategory.php';

	$tpl = new dle_template;
	$tpl->dir = ROOT_DIR.'/templates/'.$_REQUEST['skin'];
	define('TEMPLATE_DIR', $tpl->dir);

	$upload = new gallery_upload_action($_REQUEST['cat']);
	$upload->gallery_upload_init(2);

	if ($upload->buffer != '')
		$buffer = $upload->buffer;
	else
		$buffer = $tpl->result['content'];

break;
default : die("error");
}

$db->close();

$buffer= str_ireplace( '{THEME}', $config['http_home_url'] . 'templates/' . $_REQUEST['skin'], $buffer );

@header("Content-type: text/html; charset=".$config['charset']);
echo $buffer;
exit (1);
?>