<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group
 TWS Gallery - by Al-x
-----------------------------------------------------
 http://dle-news.ru/
 http://inker.wonderfullife.ru/
-----------------------------------------------------
 Copyright (c) 2004,2007 SoftNews Media Group
 Copyright (c) 2007,2008 TWS
=====================================================
 Данный код защищен авторскими правами
 This file may no be redistributed in whole or significant part.	
 Файл не может быть изменён или использован без прямого согласия автора
 Запрещается использование файла в люббых комменрческих целях
=====================================================
 Файл: twsgallery.php
-----------------------------------------------------
 Назначение: подключение админ-панели
=====================================================
*/
if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

define('TWSGAL_DIR', ENGINE_DIR.'/gallery');
define('TWSFACP_DIR', TWSGAL_DIR.'/acp');
define('FOTO_DIR', ROOT_DIR.'/uploads/gallery');
define('FOTO_URL', $config['http_home_url'].'uploads/gallery');
define('ACP_ACTIVE', true);
define('TIME', (time()  + ($config['date_adjust'] * 60)));
define('DATETIME', date ("Y-m-d H:i:s", TIME));
define('GALLERY_KEY', true);
define('GALLERY_KEY_OK', true);
//define('DEBUG_MODE', true);

unset($_REQUEST['id']); // временно

$is_logged = true;

require_once TWSFACP_DIR.'/functions.admin.php';
require_once ROOT_DIR."/language/".$config['langs']."/gallery.admin.lng";
require_once ENGINE_DIR."/gallery/acp/main.php";

?>