<?php

@session_start();
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', "..");
define('ENGINE_DIR', ROOT_DIR.'/engine');
define('TWSGAL_DIR', ENGINE_DIR.'/gallery');
define('FOTO_DIR', ROOT_DIR.'/uploads/gallery');

$_D = ROOT_DIR; $_F = ENGINE_DIR;

require_once(ENGINE_DIR.'/data/config.php');

$js_array = array ();
$theme = ENGINE_DIR;

require_once(ENGINE_DIR.'/classes/mysql.php');
require_once(ENGINE_DIR.'/data/dbconfig.php');
require_once(ENGINE_DIR.'/inc/include/functions.inc.php');

$galConfig = false;

$sql = $db->query("SELECT value FROM " . PREFIX . "_gallery_config WHERE name='version_gallery'", false);
if ($sql) $galConfig = $db->get_row();
$db->free();	

if ($galConfig['value']) $galConfig = array('version_gallery' => $galConfig['value']);

if (!$galConfig){
	@include (ENGINE_DIR . '/data/gallery.config.php');
}

extract($_REQUEST, EXTR_SKIP);

require_once(dirname (__FILE__).'/template.php');

require_once (ENGINE_DIR.'/gallery/functions/web.php');

$new_version = "5.2";

$version_mass = array(
'5.1' => $new_version,
'5.0' => '5.1',
'4.2' => '5.0',
);

$version_id = $galConfig['version_gallery'];

if (!$galConfig['version_gallery'] or version_compare($galConfig['version_gallery'], "4.0", "<")) die("TWS Gallery не установлена или обновление произведено некорректно. Выполните необходимые действия, при необходимости обратитесь на форум поддержки.");

$last_news = @file_get_contents("http://inker.wonderfullife.ru/extras/updates.php?script=twsg&install=3&dle=".$config['version_id']."&version=".$new_version."&host=".$_SERVER['HTTP_HOST']);

switch ($version_id) {

case $new_version :
	msgbox("info","Обновление завершено", "Обновление скрипта до актуальной версии <b>$version_id</b> было успешно завершено.<br /><br />В целях безопасности во время обновления галерея была отключена. Не забудьте включить модуль в главных настройках галереи!<br /><br /> Удалите папку <b>/galupdate/</b> с вашего сервера!");
	break;

case "4.0" :
	include dirname (__FILE__).'/4.0.php';
	break;

case "4.0.5" :
	include dirname (__FILE__).'/4.0.5.php';
	break;

case "4.0.7" :
	include dirname (__FILE__).'/4.0.7.php';
	break;

case "2009" :
	include dirname (__FILE__).'/4.1.php';
	break;

case "4.1" :
	include dirname (__FILE__).'/4.1.php';
	break;

case "4.2" :
	include dirname (__FILE__).'/4.2.php';
	break;

case "5.0" :
	include dirname (__FILE__).'/5.0.php';
	break;

case "5.1" :
	include dirname (__FILE__).'/5.1.php';
	break;

default:
	msgbox("info","Ошибка", "Не удалось определить установленную версию скрипта, возможно поддержка этой версии была прекращена или в базе данных содержаться критические ошибки. Установка обновления остановлена.");
}

?>