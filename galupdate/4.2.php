<?php

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `cat_short_desc` `cat_short_desc` text NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `disable_upload` tinyint(1) NOT NULL default '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `icon_picture_id` int(10) NOT NULL default '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `allow_user_admin` tinyint(1) NOT NULL default '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` DROP INDEX `cat_order` ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD INDEX ( `position` ) ";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_category SET foto_sort='file_views' WHERE foto_sort='view_count'";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_config` ADD UNIQUE (`name`) ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `text` text NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `logs` text NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` CHANGE `view_count` `file_views` mediumint(8) NOT NULL default '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` CHANGE `picture_alt_title` `image_alt_title` varchar(255) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD INDEX ( `posi` ) ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD INDEX ( `symbol` )";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD FULLTEXT (`picture_title` , `text` ) ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` ADD `allow_user_admin` tinyint(1) NOT NULL default '0' ";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_search (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_id` varchar(32) NOT NULL,
  `find_files` text NOT NULL,
  `search_num` smallint(5) NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL,
  `date` datetime NOT NULL,
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  `actual` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `search_id` (`search_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_tws_email` ADD UNIQUE (`prefix`, `name`) ";
$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_admin_sections (name, title, descr, icon, allow_groups) VALUES ('twsgallery', 'Фотогалерея', 'Общие настройки, категории и изображения галереи', 'iPhoto.png', '1')";

$insert_config_table = array(
'advance_default' => '1',
'disable_advance_upload' => '1',
'max_once_upload' => '10',
'allow_user_admin' => '1',
'max_user_categories' => '1',
'icon_type' => '1',
'timestamp_active' => 'j F H:i',
'key' => '',
);

foreach ($insert_config_table as $config_name => $config_value){
	$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_gallery_config (name, value, type) VALUES ('{$config_name}', '{$config_value}', 0)";
}

$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='5.0' WHERE name='version_gallery'";

$error_count = 0;
$error_query = array();

foreach($tableSchema as $table){
	$db->mysql_error = '';
	$db->query ($table, false);
	if ($db->mysql_error != ''){ $error_query[] = $table; $error_count++; }
}

$sql = $db->query("SELECT pic_id, text, logs FROM " . PREFIX . "_gallery_picturies_text WHERE text != '' OR logs != ''");
while($row = $db->get_row($sql)){
	$db->query ("UPDATE " . PREFIX . "_gallery_picturies SET text='".$db->safesql($row['text'])."', logs='".$db->safesql($row['logs'])."' WHERE picture_id='{$row['pic_id']}'");
}
$db->free($sql);

$db->mysql_error = '';
$db->query ("DROP TABLE " . PREFIX . "_gallery_picturies_text", false);
if ($db->mysql_error != '') $error_count++;

@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_category.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_cron.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');

clear_gallery_cache();
clear_gallery_vars();

if ($error_count) $error_info = "Всего запланировано запросов: <b>".$db->query_num."</b> Неудалось выполнить запросов: <b>".$error_count."</b>. Возможно они уже выполнены ранее."; else $error_info = "";

msgbox("info","Информация", "<form action=\"index.php\" method=\"GET\">Обновление базы данных с версии <b>{$version_id}</b> до версии <b>{$version_mass[$version_id]}</b> успешно завершено.<br />{$error_info}<br />Нажмите далее для продолжения процессa обновления скрипта<br /><br /><input type=\"hidden\" name=\"next\" value=\"{$version_mass[$version_id]}\"><input class=\"edit\" type=\"submit\" value=\"Далее ...\"></form>");

?>