<?php

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `images` `images` MEDIUMINT( 8 ) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `all_time_images` MEDIUMINT( 8 ) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` CHANGE `posi` `posi` INT( 10 ) NOT NULL DEFAULT '1' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` CHANGE `symbol` `symbol` VARCHAR( 10 ) CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `picture_alt_title` VARCHAR( 255 ) CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci NOT NULL ";
$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_admin_sections (name, title, descr, icon, allow_groups) VALUES ('twsgallery', 'Фотогалерея', 'Общие настройки, категории и изображения галереи', 'iPhoto.png', '1')";
$tableSchema[] = "CREATE TABLE IF NOT EXISTS " . PREFIX . "_gallery_config (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
  ) TYPE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$insert_config_table = array(
'off' => '0',
'allow_cache' => '1',
'work_postfix' => 'gallery/',
'description' => 'Моя tws галерея картинок',
'keywords' => 'Личные галереи, картинки, обои, tws',
'main_cat_td' => '2',
'main_cat_tr' => '8',
'foto_td' => '4',
'foto_tr' => '8',
'category_sort' => 'position',
'category_msort' => 'asc',
'foto_sort' => 'posi',
'foto_msort' => 'asc',
'max_title_lenght' => '15',
'autowrap_foto' => '20',
'thumbs_in_fullimage' => '3',
'global_max_foto_width' => '1280',
'global_max_foto_height' => '1024',
'full_res_type' => '5',
'comms_foto_size' => '550x450',
'comm_res_type' => '5',
'max_thumb_size' => '150x120',
'thumb_res_type' => '5',
'allow_foto_resize' => '1',
'min_watermark' => '150',
'resize_quality' => '90',
'rewrite_mode' => '1',
'allow_check_double' => '1',
'allow_watermark' => '1',
'max_icon_size' => '20',
'watermark_light' => 'dleimages/watermark_light.png',
'watermark_dark' => 'dleimages/watermark_dark.png',
'allow_edit_picture' => '1',
'allow_delete_picture' => '0',
'dinamic_symbols' => '1',
'allow_comments' => '1',
'allow_rating' => '1',
'show_statistic' => '1',
'rand_foto_cats' => '0',
'comments_mod' => '0',
'mail_comments' => '1',
'mail_foto' => '1',
'extensions' => 'a:12:{s:3:\\"jpg\\";a:2:{s:1:\\"s\\";i:400;s:1:\\"p\\";i:0;}s:4:\\"jpeg\\";a:2:{s:1:\\"s\\";i:400;s:1:\\"p\\";i:0;}s:3:\\"jpe\\";a:2:{s:1:\\"s\\";i:400;s:1:\\"p\\";i:0;}s:3:\\"png\\";a:2:{s:1:\\"s\\";i:400;s:1:\\"p\\";i:0;}s:3:\\"gif\\";a:2:{s:1:\\"s\\";i:400;s:1:\\"p\\";i:0;}s:3:\\"mp3\\";a:2:{s:1:\\"s\\";i:8192;s:1:\\"p\\";i:2;}s:3:\\"avi\\";a:2:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:3;}s:3:\\"wmv\\";a:2:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:4;}s:3:\\"flv\\";a:2:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:2;}s:3:\\"rar\\";a:2:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:0;}s:3:\\"zip\\";a:2:{s:1:\\"s\\";i:10240;s:1:\\"p\\";i:0;}s:3:\\"doc\\";a:2:{s:1:\\"s\\";i:2048;s:1:\\"p\\";i:0;}}',
'viewlevel' => '1,2,3,4,5',
'comlevel' => '1,2,3,4',
'uploadlevel' => '1,2,3,4',
'modlevel' => '4',
'editlevel' => '1,2',
'ratelevel' => '1,2,3,4',
'addlevel' => '3,4',
'allowed_extensions' => 'jpg,jpeg,jpe,png,gif',
'skin_name' => '',
'version_gallery' => '4.2',
'max_filesize' => '3000',
'max_media_filesize' => '10000',
'max_comments_days' => '0',
'allow_check_update' => '1',
'jw_flv_mp_full_width' => '420',
'jw_flv_mp_full_height' => '320',
'jw_flv_mp_width' => '150',
'jw_flv_mp_height' => '150',
'jw_flv_mp_mp3_full_width' => '475',
'jw_flv_mp_mp3_full_height' => '55',
'jw_flv_mp_mp3_width' => '175',
'jw_flv_mp_mp3_height' => '20',
'divx_wp_full_width' => '420',
'divx_wp_full_height' => '320',
'divx_wp_width' => '150',
'divx_wp_height' => '150',
'cms_fp_full_width' => '420',
'cms_fp_full_height' => '320',
'cms_fp_width' => '150',
'cms_fp_height' => '150',
'cms_fp_mp3_full_width' => '420',
'cms_fp_mp3_full_height' => '320',
'cms_fp_mp3_width' => '475',
'cms_fp_mp3_height' => '55',
'cms_flp_full_width' => '420',
'cms_flp_full_height' => '320',
'cms_flp_width' => '150',
'cms_flp_height' => '150',
'cms_ftp_full_width' => '420',
'cms_ftp_full_height' => '320',
'cms_ftp_width' => '50',
'cms_ftp_height' => '150',
'backgroundBarColor' => '0x1A1A1A',
'btnsColor' => '0xFFFFFF',
'outputTxtColor' => '0x999999',
'outputBkgColor' => '0x1A1A1A',
'loadingBarColor' => '0x666666',
'loadingBackgroundColor' => '0xCCCCCC',
'progressBarColor' => '0x000000',
'volumeStatusBarColor' => '0x000000',
'volumeBackgroundColor' => '0x666666',
'play' => '1',
'flv_watermark' => '1',
);

foreach ($insert_config_table as $config_name => $config_value){
	$tableSchema[] = "INSERT INTO " . PREFIX . "_gallery_config (name, value, type) VALUES ('{$config_name}', '{$config_value}', 0)";
}

$insert_config_table = array(
'check_update' => '',
);

foreach ($insert_config_table as $config_name => $config_value){
	$tableSchema[] = "INSERT INTO " . PREFIX . "_gallery_config (name, value, type) VALUES ('{$config_name}', '{$config_value}', 1)";
}

foreach($tableSchema as $table){
	$db->query ($table);
}

@unlink(ENGINE_DIR.'/cache/system/gallery_category.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_cron.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
@rename(ENGINE_DIR.'/data/gallery.config.php', ENGINE_DIR.'/data/gallery.config.backup.php');

clear_gallery_cache();
clear_gallery_vars();

msgbox("info","Информация", "<form action=\"index.php\" method=\"GET\">Обновление базы данных с версии <b>4.1 (2009)</b> до версии <b>4.2</b> успешно завершено. После полного завершения установки обязательно зайдите в админ-панель скрипта галереи и настройте все необходимые параметры (настойки были сброшены во время обновления). В последующих версиях скрипта конфигурационный файл более не требуется, поэтому он был переименован в gallery.config.backup.php. Вы можете удалить его!<br />Нажмите далее для продолжения процессa обновления скрипта<br /><br /><input type=\"hidden\" name=\"next\" value=\"4.2\"><input class=\"edit\" type=\"submit\" value=\"Далее ...\"></form>");

?>