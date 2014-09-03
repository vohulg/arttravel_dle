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
 Файл: download.php
-----------------------------------------------------
 Назначение: Файл закачки фотографии
=====================================================
*/

@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define( 'ROOT_DIR', substr( dirname(  __FILE__ ), 0, -24 ) );
define( 'ENGINE_DIR', ROOT_DIR . '/engine' );

$foto_id = intval($_REQUEST['id']);

include ENGINE_DIR.'/data/config.php';

if( $config['http_home_url'] == "" ) {
	
	$config['http_home_url'] = explode( "engine/gallery/ajax/comments.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR.'/modules/functions.php';
require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';

if (version_compare($config['version_id'], "9.6", ">"))
	dle_session();
else
	@session_start();

if ($config["lang_".$config['skin']]) { 
     include_once ROOT_DIR.'/language/'.$config["lang_".$config['skin']].'/website.lng';
} else {
     include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';
}
$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

//################# Определение групп пользователей
$user_group = get_vars( "usergroup" );

if( ! $user_group ) {
	$user_group = array ();
	
	$db->query( "SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC" );
	
	while ( $row = $db->get_row() ) {
		
		$user_group[$row['id']] = array ();
		
		foreach ( $row as $key => $value ) {
			$user_group[$row['id']][$key] = stripslashes($value);
		}
	
	}
	set_vars( "usergroup", $user_group );
	$db->free();
}

$is_logged = false;
$member_id = array ();

if ($config['allow_registration'] == "yes") {
	require_once ENGINE_DIR.'/modules/sitelogin.php';
}

if( ! $is_logged ) $member_id['user_group'] = 5;

if (!$user_group[$member_id['user_group']]['allow_files']) die("Hacking attempt! You can`t download this file!");

require_once ENGINE_DIR.'/gallery/functions/web.php';

if ($galConfig['off'] == "1") die("Hacking attempt! Gallery offline!");

$this_foto = $db->super_query("SELECT p.picture_id, p.category_id, p.picture_filname, p.full_link, p.type_upload, p.user_id, p.approve, p.media_type, c.view_level, c.locked FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE p.picture_id='{$foto_id}'");

if (!$this_foto['picture_id']) die ("Hacking attempt!");

if ($this_foto['media_type'] && ($this_foto['media_type'] > '50' || $this_foto['media_type'] != '10' && !$galConfig['allow_download'])) die ("Hacking attempt!");

if (!check_gallery_access ("read", $this_foto['view_level'], "", $this_foto['locked']) || ($this_foto['approve'] != '1' && (!$is_logged || $this_foto['user_id'] != $member_id['user_id']))){
	die("Hacking attempt!");
}

$db->query("UPDATE LOW_PRIORITY " . PREFIX . "_gallery_picturies SET downloaded=downloaded+1 WHERE picture_id={$this_foto['picture_id']}");
$db->query("UPDATE " . PREFIX . "_gallery_config SET value=value+1 WHERE name='statistic_downloads'");

@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

$db->close();
session_write_close();

$global_info = array();
$file = array();

function get_user_os() {
  global $global_info, $HTTP_USER_AGENT, $HTTP_SERVER_VARS;

  if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
  }
  elseif (getenv("HTTP_USER_AGENT")) {
    $HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
  }
  elseif (empty($HTTP_USER_AGENT)) {
    $HTTP_USER_AGENT = "";
  }
  if (strpos(strtolower($HTTP_USER_AGENT), "win") !== false)
    $global_info['user_os'] = "WIN";
  elseif (strpos(strtolower($HTTP_USER_AGENT), "mac") !== false)
    $global_info['user_os'] = "MAC";
  else
    $global_info['user_os'] = "OTHER";

  return $global_info['user_os'];
}

function get_browser_info() {
  global $global_info, $HTTP_USER_AGENT, $HTTP_SERVER_VARS;

  if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
  }
  elseif (getenv("HTTP_USER_AGENT")) {
    $HTTP_USER_AGENT = getenv("HTTP_USER_AGENT");
  }
  elseif (empty($HTTP_USER_AGENT)) {
    $HTTP_USER_AGENT = "";
  }
  if (preg_match("/MSIE ([0-9].[0-9]{1,2})/i", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "MSIE";
    $global_info['browser_version'] = $regs[1];
  }
  elseif (preg_match("/Mozilla\/([0-9].[0-9]{1,2})/i", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "MOZILLA";
    $global_info['browser_version'] = $regs[1];
  }
  elseif (preg_match("/Opera(\/| )([0-9].[0-9]{1,2})/i", $HTTP_USER_AGENT, $regs)) {
    $global_info['browser_agent'] = "OPERA";
    $global_info['browser_version'] = $regs[2];
  }
  else {
    $global_info['browser_agent'] = "OTHER";
    $global_info['browser_version'] = 0;
  }
  return $global_info['browser_agent'];
}

function get_remote_file($url) {
  $file_data = "";
  $url = @parse_url($url);
  if (isset($url['path']) && isset($url['scheme']) && strpos(strtolower($url['scheme']), "http") !== false) {
    $url['port'] = (!isset($url['port'])) ? 80 : $url['port'];
    if ($fsock = @fsockopen($url['host'], $url['port'], $errno, $errstr)) {
      @fputs($fsock, "GET ".$url['path']." HTTP/1.1\r\n");
      @fputs($fsock, "HOST: ".$url['host']."\r\n");
      @fputs($fsock, "Connection: close\r\n\r\n");
      $file_data = "";
      while (!@feof($fsock)) {
        $file_data .= @fread($fsock, 1000);
      }
      @fclose($fsock);
      if (preg_match("/Content-Length\: ([0-9]+)[^\/ \n]/i", $file_data, $regs)) {
        $file_data = substr($file_data, strlen($file_data) - $regs[1], $regs[1]);
      }
    }
  }
  return (!empty($file_data)) ? $file_data : 0;
}

function get_file_data($file_path, $remote=0) {
  ob_start();
  @ob_implicit_flush(0);
  @readfile($file_path);
  $file_data = ob_get_contents();
  ob_end_clean();
  if (!empty($file_data)) {
    return $file_data;
  }
  else {
   if (!$remote && file_exists($file_path)) {
      $file_size = @filesize($file_path);
      $fp = @fopen($file_path, "rb");
      if ($fp) {
        $file_data = @fread($fp, $file_size);
        @fclose($fp);
      }
    } elseif ($remote) {
    $file_data = get_remote_file($file_path);
  }
  }
  return (!empty($file_data)) ? $file_data : 0;
}

	$file['file_name'] = $this_foto['picture_filname'];

	if (!$this_foto['type_upload']){

		$file['file_path'] = FOTO_DIR . '/main/'. $this_foto['category_id'] . '/' . $this_foto['picture_filname'];
		$file['file_data'] = get_file_data($file['file_path']);

	} else {

		$file['file_path'] = $this_foto['full_link'];	
		$file['file_data'] = get_file_data($file['file_path'], 1);

	}

	$file['file_size'] = strlen($file['file_data']);

if (!empty($file['file_data'])) {
  if (get_user_os() == "MAC") {
    header("Content-Type: application/x-unknown\n");
	header("Content-Disposition: attachment; filename=\"".$file['file_name']."\"\n");
  }
  elseif (get_browser_info() == "MSIE") {
    header("Content-Disposition: inline; filename=\"".$file['file_name']."\"\n");
    header("Content-Type: application/x-ms-download\n");
  }
  elseif (get_browser_info() == "OPERA") {
    header("Content-Disposition: attachment; filename=\"".$file['file_name']."\"\n");
    header("Content-Type: application/octetstream\n");
  }
  else {
    header("Content-Disposition: attachment; filename=\"".$file['file_name']."\"\n");
    header("Content-Type: application/octet-stream\n");
  }
  header("Content-Length: ".$file['file_size']."\n\n");
  echo $file['file_data'];
}

exit;
?>