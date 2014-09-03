<?php

$db->query ("UPDATE " . PREFIX . "_gallery_config SET value='1' WHERE name='off'");

@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

$tableSchema = array();

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `cat_alt_name` `cat_alt_name` VARCHAR(255) NOT NULL DEFAULT '' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `allow_watermark` `allow_watermark` tinyint(1) NOT NULL DEFAULT '1' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` DROP `thumbs_in_fullimage` ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `size_max` `size_factor` smallint(3) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `allow_carousel` tinyint( 1 ) NOT NULL DEFAULT '1' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `uploadskin` VARCHAR( 50 ) NOT NULL DEFAULT '' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_comments` DROP `is_register` ";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_comments_subscribe (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL DEFAULT '0',
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  `gast_email` varchar(50) NOT NULL DEFAULT '',
  `flag` tinyint(1) NOT NULL DEFAULT '1',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`,`flag`),
  KEY `user_id` (`user_id`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "DROP TABLE " . PREFIX . "_gallery_flood";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_flood (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `member_key` int(12) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `member_key` (`member_key`,`date`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `tags` VARCHAR( 255 ) NOT NULL DEFAULT '' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `preview_filname` VARCHAR( 100 ) NOT NULL DEFAULT '' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` CHANGE `media_type` `media_type` tinyint(2) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `user_id` mediumint(8) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `email` VARCHAR( 50 ) NOT NULL DEFAULT '' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` CHANGE `date` `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` CHANGE `lastdate` `lastdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD INDEX ( `user_id` ) ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` CHANGE `allow_watermark` `allow_watermark` tinyint(1) NOT NULL DEFAULT '1' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` CHANGE `thumbs_in_fullimage` `allow_carousel` tinyint(1) NOT NULL DEFAULT '1' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` CHANGE `size_max` `size_factor` smallint(3) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` ADD `alt_name_tpl` VARCHAR( 100 ) NOT NULL DEFAULT '' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` ADD `uploadskin` VARCHAR( 50 ) NOT NULL DEFAULT '' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_search` CHANGE `date` `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_search` ADD `tag` VARCHAR( 100 ) NOT NULL DEFAULT '' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_search_text` CHANGE `search_id` `search_id` int(10) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_search_text` CHANGE `search_page` `search_page` smallint(5) NOT NULL DEFAULT '0' ";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_tags (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_name` (`tag_name`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_tags_match (
  `mid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(10) NOT NULL DEFAULT '0',
  `file_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mid`),
  KEY `tag_id` (`tag_id`),
  KEY `file_id` (`file_id`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_temp_files` CHANGE `date` `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_users_views (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL DEFAULT '0',
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_id` (`file_id`,`user_id`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_users` ADD `gallery_cs_flag` tinyint(1) NOT NULL DEFAULT '0'";

$insert_config_table = array(
'adminaccess' => '1,2',
'last_cron' => '0',
'statistic_file_onmod' => '-1',
'statistic_file' => '-1',
'statistic_file_day' => '-1',
'statistic_cat' => '-1',
'statistic_cat_week' => '-1',
'statistic_com_day' => '-1',
'statistic_com' => '-1',
'statistic_downloads' => '-1',
'enable_banned' => '1',
'tags_len' => '3-40',
'convert_png_thumb' => '1',
'allow_delete_omcomments' => '0',
'tags_num' => '5',
'file_views' => '1',
'whois_view_file' => '1',
'whois_view_file_day' => '90',
'no_main_watermark' => '0',
'random_filename' => '0',
'admin_user_access' => '0',
'comsubslevel' => '-1',
'thumbs_offset' => '1',
'show_in_fullimage' => '1',
'thumbs_mousewheel' => '1',
'buffer_in_fullimage' => '15',
'thumbs_template' => '<a href="{url}"><img src="{image}" alt="{alt-title}" title="{alt-title}" /></a><br /><div><b>{title}</b></div>',
'thumbs_fx' => 'scroll',
);

foreach ($insert_config_table as $config_name => $config_value){
	$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_gallery_config (name, value, type) VALUES ('{$config_name}', '{$config_value}', 0)";
}

$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='2' WHERE name='allow_cache'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET type='1' WHERE name='admin_num_files'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET type='1' WHERE name='admin_num_cats'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET type='1' WHERE name='admin_user_access'";

$tableSchema[] = "DELETE FROM " . PREFIX . "_gallery_config 
WHERE name IN ('thumbs_in_fullimage',
'max_filesize',
'max_media_filesize',
'admin_type_files',
'admin_type_cats')";

 $tableSchema[] = "ALTER TABLE `" . PREFIX . "_tws_email` ADD UNIQUE (`name`) ";

 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_subscribe', 'E-Mail сообщение, которое отсылается для подтверждения подписки', 'При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%subscribe%}</b> - ссылка на подтверждение подписки на комментарии', 'Уважаемый посетитель,\r\n\r\nвы подписывались на рассылку новых комментариев в галерее на сайте {%site%}.\r\n\r\nДля подтверждения подписки пройдите по следующей ссылке: {%subscribe%}\r\n\r\nЕсли вы никогда не бывали на нашем сайте и не подписывались на комментарии - не обращайте внимание на данное сообщение - возможно кто-то просто ошибся при вводе e-mail адреса. Если сообщения будут приходить постоянно - пожалуйста, обратитесь к администратору сайта - мы постараемся решить вашу проблему.')";
 $tableSchema[] = "UPDATE " . PREFIX . "_tws_email SET description='При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%username_to%}</b> - получатель сообщения\r\n<b>{%username%}</b> - автор комментария\r\n<b>{%date%}</b> - дата написания\r\n<b>{%link%}</b> - ссылка на файл, к которому был оставлен комментарий с параметром продолжения подписки\r\n<b>{%ip%}</b> - IP адрес автора\r\n<b>{%text%}</b> - текст комментария\r\n<b>{%unsubscribe%}</b> - ссылка на отмену подписки на комментарии к данной новости', template='Уважаемый посетитель,\r\n\r\nуведомляем вас о том, что на сайт {%site%} был добавлен комментарий.\r\n\r\n------------------------------------------------\r\nКраткая информация о комментарии\r\n------------------------------------------------\r\n\r\nАвтор: {%username%}\r\nДата добавления: {%date%}\r\nСсылка на фотографию: {%link%}\r\nIP адрес: {%ip%}\r\n------------------------------------------------\r\nТекст комментария\r\n------------------------------------------------\r\n\r\n{%text%}\r\n\r\n------------------------------------------------\r\n\r\nДля продолжения подписки на комментарии к данному файлу необходимо прочитать уже оставленные пользователями комментарии, перейдя по ссылке {%link%}\r\n\r\nЕсли вы не хотите больше получать уведомлений о новых комментариях к данной новости, то проследуйте по данной ссылке: {%unsubscribe%}\r\n\r\nС уважением,\r\nАдминистрация {%site%}' WHERE name='gallery_newcomment'";

$tableSchema[] = "UPDATE " . PREFIX . "_gallery_category SET size_factor='0' WHERE 1";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_category SET icon='' WHERE icon !='' AND icon_picture_id<1";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_profiles SET allow_carousel='1' WHERE 1";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_profiles SET size_factor='0' WHERE 1";

$error_count = 0;
$error_query = array();

foreach($tableSchema as $table){
	$db->mysql_error = '';
	$db->query ($table, false);
	if ($db->mysql_error != ''){ $error_query[] = $table; $error_count++; }
}

$sql = $db->query("SELECT p.picture_id, u.user_id FROM " . PREFIX . "_gallery_picturies p LEFT JOIN " . USERPREFIX . "_users u ON u.name=p.picture_user_name WHERE picture_user_name !=''");

while ($row = $db->get_row($sql))
	$db->query( "UPDATE " . PREFIX . "_gallery_picturies SET user_id='".intval($row['user_id'])."' WHERE picture_id={$row['picture_id']}");

$db->free($sql);

$db->query ("UPDATE " . PREFIX . "_gallery_config SET value='' WHERE name='key'");
$db->query ("UPDATE " . PREFIX . "_gallery_config SET value='{$new_version}' WHERE name='version_gallery'");
//$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_cat','statistic_cat_week','statistic_com','statistic_com_day','statistic_file_day','statistic_file','statistic_file_onmod','statistic_downloads')");

$db->query("TRUNCATE TABLE " . PREFIX . "_gallery_search_text");
$db->query("TRUNCATE TABLE " . PREFIX . "_gallery_search");

@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_cron.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_profiles.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_category.php');

clear_gallery_cache();
clear_gallery_vars();

if ($error_count) $error_info = "Всего запланировано запросов: <b>".$db->query_num."</b> Неудалось выполнить запросов: <b>".$error_count."</b>. Невыполненные запросы (пожалуйста, сообщите о них разработчикам TWS Gallery):<br /><br />".implode("<br />", $error_query)."<br /><br />Возможно они уже выполнены ранее."; else $error_info = "";

msgbox("info","Информация", "<form action=\"index.php\" method=\"GET\">Обновление базы данных с версии <b>{$version_id}</b> до версии <b>{$version_mass[$version_id]}</b> успешно завершено.<br />{$error_info}<br />Нажмите далее для продолжения процессa обновления скрипта<br /><br /><input type=\"hidden\" name=\"next\" value=\"{$version_mass[$version_id]}\"><input class=\"edit\" type=\"submit\" value=\"Далее ...\"></form>");

?>