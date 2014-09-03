<?php

if (!is_dir(FOTO_DIR)){

	echo "Папка uploads/gallery не найдена! Если вы используете другое название папки - измените его в константе FOTO_DIR, расположенной в файле galupdate/index.php";
	die();

}

$tableSchema = array();

$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='1' WHERE name='off'";

$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `metatitle` VARCHAR( 255 ) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `position` `position` INT( 10 ) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `reg_date` `reg_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `last_date` `last_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `last_cat_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `cat_images` INT( 10 ) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `icon_max_size` VARCHAR( 10 ) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD `sub_cats` mediumint( 8 ) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `com_thumb_max` `com_thumb_max` varchar(10) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `thumb_max` `thumb_max` varchar(10) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` CHANGE `allowed_extensions` `allowed_extensions` varchar(250) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD INDEX ( `view_level` ) ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD INDEX ( `p_id` ) ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD INDEX ( `cat_title` ) ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_category` ADD INDEX ( `cat_alt_name` ) ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_comments` DROP INDEX `text` ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `edit_reason` VARCHAR( 250 ) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `editor` VARCHAR( 40 ) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `session_id` VARCHAR( 32 ) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `downloaded` mediumint( 8 ) NOT NULL DEFAULT '0' ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` ADD `thumbnails` VARCHAR( 40 ) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_picturies` CHANGE `full_link` `full_link` text NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` CHANGE `com_thumb_max` `com_thumb_max` varchar(10) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` CHANGE `thumb_max` `thumb_max` varchar(10) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` CHANGE `icon_max_size` `icon_max_size` varchar(10) NOT NULL ";
$tableSchema[] = "ALTER TABLE `" . PREFIX . "_gallery_profiles` CHANGE `allowed_extensions` `allowed_extensions` varchar(250) NOT NULL ";
$tableSchema[] = "DROP TABLE " . PREFIX . "_gallery_search";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_picture_views (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `picture_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_search (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_code` varchar(32) NOT NULL,
  `search_num` int(10) NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL,
  `date` datetime NOT NULL,
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  `actual` tinyint(1) NOT NULL DEFAULT '1',
  `symbol` varchar(10) NOT NULL,
  `user` varchar(40) NOT NULL,
  `story` varchar(255) NOT NULL,
  `cat` mediumint(8) NOT NULL DEFAULT '0',
  `search_sort` varchar(18) NOT NULL,
  `search_msort` varchar(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `search_code` (`search_code`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_search_text (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_id` int(10) NOT NULL,
  `search_page` smallint(5) NOT NULL,
  `find_files` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `search_id` (`search_id`,`search_page`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_temp_files (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL,
  `date` datetime NOT NULL,
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
$tableSchema[] = "DROP TABLE " . PREFIX . "_gallery_logs";
$tableSchema[] = "CREATE TABLE " . PREFIX . "_gallery_logs (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pic_id` int(10) NOT NULL DEFAULT '0',
  `member_key` varchar(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pic_id` (`pic_id`,`member_key`)
  ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";

$insert_config_table = array(
'admin_num_files' => '50',
'admin_type_files' => '0',
'admin_type_cats' => '1',
'allow_recycle' => '1',
'allow_recycle_own' => '0',
'files_on_moderation' => '5',
'file_title_control' => '0',
'disable_select_upload' => '0',
'remotelevel' => '-1',
'allow_download' => '1',
'empty_title_template' => 'Файл {%i%}',
'allow_ajax_comments' => '1',
'yrt_full_width' => '420',
'yrt_full_height' => '320',
'yrt_width' => '50',
'yrt_height' => '150',
'yrt_tube_related' => '0',
'flv_watermark_pos' => 'left',
'flv_watermark_al' => '1',
'youtube_q' => 'hd720',
'startframe' => '1',
'preview' => '0',
'autohide' => '0',
'buffer' => '3',
'admin_num_cats' => '50',
);

foreach ($insert_config_table as $config_name => $config_value){
	$tableSchema[] = "INSERT IGNORE INTO " . PREFIX . "_gallery_config (name, value, type) VALUES ('{$config_name}', '{$config_value}', 0)";
}

$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='5.1' WHERE name='version_gallery'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='', type=1 WHERE name='key'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='date' WHERE name='foto_sort' AND value='lastdate'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='file_views' WHERE name='foto_sort' AND value='view_count'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='' WHERE name='foto_sort' AND value='0'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_config SET value='' WHERE name='foto_msort' AND value='0'";

$tableSchema[] = "DELETE FROM " . PREFIX . "_gallery_config 
WHERE name IN ('rand_foto_cats',
'backgroundBarColor',
'btnsColor',
'outputTxtColor',
'outputBkgColor',
'loadingBarColor',
'loadingBackgroundColor',
'volumeStatusBarColor',
'volumeBackgroundColor')";

 $tableSchema[] = "INSERT IGNORE INTO `" . PREFIX . "_tws_email` (`prefix`, `name`, `title`, `description`, `template`) VALUES (1, 'gallery_editfoto', 'PM сообщение, которое отсылается при отсылке уведомления пользователю', 'При написании шаблона для данного сообщения вы можете использовать следующие теги:\r\n<b>{%name%}</b> - имя пользователя, которому отправлено уведомление\r\n<b>{%site%}</b> - название сайта, с которого отправлено уведомление\r\n<b>{%username%}</b> - имя администратора\r\n<b>{%usergroup%}</b> - группа администратора\r\n<b>{%date%}</b> - дата редактирования\r\n<b>{%fileslist%}</b> - список отредактированных файлов и ссылки на них\r\n<b>{%action%}</b> - действие, которое выполнил администратор\r\n<b>{%notice%}</b> - текст сообщения, которое указал адмиинистратор', 'Уважаемый {%name%},\r\n\r\nуведомляем вас о том, что следующие добавленные вами файлы были отредактированы в галерее:\r\n\r\n{%fileslist%}\r\n\r\n[action]Действие: {%action%}[/action]\r\n[notice]Указанная причина: {%notice%}[/notice]\r\n\r\nАдминистратор: {%username%}\r\nГруппа: {%usergroup%}\r\nДата: {%date%}')";

$tableSchema[] = "UPDATE " . PREFIX . "_gallery_picturies SET thumbnails='m|c|t||1|1|1' WHERE 1";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_category SET foto_sort='date' WHERE foto_sort='lastdate'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_profiles SET foto_sort='date' WHERE foto_sort='lastdate'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_profiles SET foto_sort='file_views' WHERE foto_sort='view_count'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_category SET foto_sort='' WHERE foto_sort='0'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_category SET foto_msort='' WHERE foto_msort='0'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_profiles SET foto_sort='' WHERE foto_sort='0'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_profiles SET foto_msort='' WHERE foto_msort='0'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_category SET icon='' WHERE icon='no'";
$tableSchema[] = "UPDATE " . PREFIX . "_gallery_category SET icon='', icon_picture_id=0 WHERE icon_picture_id!=0 AND icon_picture_id!=''";

$error_count = 0;
$error_query = array();

foreach($tableSchema as $table){
	$db->mysql_error = '';
	$db->query ($table, false);
	if ($db->mysql_error != ''){ $error_query[] = $table; $error_count++; }
}

$sql = $db->query("SELECT id FROM " . PREFIX . "_gallery_category");

while ($row = $db->get_row($sql)){
	$images = $db->super_query("SELECT COUNT(picture_id) AS count FROM " . PREFIX . "_gallery_picturies WHERE category_id={$row['id']} AND approve=1");
	$db->mysql_error = '';
	$db->query( "UPDATE " . PREFIX . "_gallery_category SET cat_images={$images['count']}, images={$images['count']} WHERE id={$row['id']}", false);
	if ($db->mysql_error != ''){ $error_query[] = "UPDATE " . PREFIX . "_gallery_category SET cat_images={$images['count']}, images={$images['count']} WHERE id={$row['id']}"; $error_count++; }
}

$db->free($sql);

$sql = $db->query("SELECT COUNT(id) as count, SUM(cat_images) AS cat_images, p_id FROM " . PREFIX . "_gallery_category WHERE p_id > 0 GROUP BY p_id");

while ($row = $db->get_row($sql)){
	$db->mysql_error = '';
	$db->query( "UPDATE " . PREFIX . "_gallery_category SET sub_cats={$row['count']}, cat_images=cat_images+".intval($row['cat_images'])." WHERE id={$row['p_id']}", false);
	if ($db->mysql_error != ''){ $error_query[] = "UPDATE " . PREFIX . "_gallery_category SET sub_cats={$row['count']}, cat_images=cat_images+".intval($row['cat_images'])." WHERE id={$row['p_id']}"; $error_count++; }
}

$db->free($sql);

$sql = $db->query("SELECT id, icon FROM " . PREFIX . "_gallery_category WHERE icon !='' AND icon_picture_id<1");

while ($row = $db->get_row($sql)){

	if (!file_exists(FOTO_DIR.'/caticons/' . $row['icon'])){
		$db->mysql_error = '';
		$db->query( "UPDATE " . PREFIX . "_gallery_category SET icon='' WHERE id={$row['id']}", false);
		if ($db->mysql_error != ''){ $error_query[] = "UPDATE " . PREFIX . "_gallery_category SET icon='' WHERE id={$row['id']}"; $error_count++; }
		continue;
	}

	$sub_dir = FOTO_DIR.'/caticons/' . $row['id'];

	if (!is_dir($sub_dir)){

		@mkdir($sub_dir, 0777) or $error = true;
		@chmod($sub_dir, 0777);

	}

	$salt = "abchefghjkmnpqrstuvwxyz0123456789";
	srand( ( double ) microtime() * 1000000 );
	$lenght = strlen($salt);

	$image_name = "fixed";

	for($i=0;$i < 4; $i++){
		$image_name .= $salt{rand(1,$lenght)};
	}

	$img_name_arr = explode(".", $row['icon']);
	$type = end($img_name_arr);
	$image_name .= ".".$type;

	$icon_path = '/caticons/'.$row['id'].'/'.$image_name;
	@rename(FOTO_DIR . '/caticons/'.$row['icon'], FOTO_DIR . $icon_path);
	@chmod (FOTO_DIR . $icon_path, 0666);

	if (!file_exists(FOTO_DIR . $icon_path)){
		$error_query[] = "UPDATE " . PREFIX . "_gallery_category SET icon='{FOTO_URL}{$icon_path}', icon_picture_id=0 WHERE id={$row['id']}";
		$error_count++;
		continue;
	}

	$db->mysql_error = '';
	$db->query("UPDATE " . PREFIX . "_gallery_category SET icon='{FOTO_URL}{$icon_path}', icon_picture_id=0 WHERE id={$row['id']}", false);
	if ($db->mysql_error != ''){ $error_query[] = "UPDATE " . PREFIX . "_gallery_category SET icon='{FOTO_URL}{$icon_path}', icon_picture_id=0 WHERE id={$row['id']}"; $error_count++; }

}

$db->free($sql);

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