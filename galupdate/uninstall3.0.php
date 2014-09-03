<?php
@session_start();

error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', "..");
define('ENGINE_DIR', ROOT_DIR.'/engine');

require_once(ENGINE_DIR.'/data/config.php');
require_once(ENGINE_DIR.'/classes/mysql.php');
require_once(ENGINE_DIR.'/data/dbconfig.php');
require_once(ENGINE_DIR.'/inc/functions.inc.php');
$_D = ROOT_DIR; $_F = ENGINE_DIR;

$db->query("DROP TABLE `" . PREFIX . "_gal_cat`");
$db->query("DROP TABLE `" . PREFIX . "_gal_com_backup`");
$db->query("DROP TABLE `" . PREFIX . "_gal_log`");
$db->query("DROP TABLE `" . PREFIX . "_gal_pic`");

echo "Старая база данных галереи успешно удалена. Внимательно прочитайте инструкцию по дальнейшему подключению галереи!";

?>