<?php
@session_start();

error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);
@set_time_limit ( 1500 );
@ini_set('max_execution_time', 1500);
@ini_set("output_buffering", "off");
@ini_set('4096M');

define('DATALIFEENGINE', true);
define('ROOT_DIR', "..");
define('ENGINE_DIR', ROOT_DIR.'/engine');
define('TWSGAL_DIR', ENGINE_DIR.'/gallery');
define('FOTO_DIR', ROOT_DIR.'/uploads/gallery');

require_once(ENGINE_DIR.'/data/config.php');
require_once(ENGINE_DIR.'/classes/mysql.php');
require_once(ENGINE_DIR.'/data/dbconfig.php');
require_once(ENGINE_DIR.'/inc/functions.inc.php');
require_once (ENGINE_DIR.'/gallery/functions/web.php');
require_once (ENGINE_DIR . '/data/gallery.config.php');
$_D = ROOT_DIR; $_F = ENGINE_DIR;

if (!$galConfig['version_gallery'] or version_compare($galConfig['version_gallery'], "4.1", "<")) die("Не установлена необходимая версия скрипта TWS Gallery");

$admin = $db->super_query("SELECT * FROM " . USERPREFIX . "_users WHERE user_id='1'");
$_IP = $db->safesql($_SERVER['REMOTE_ADDR']);

$db->query("TRUNCATE TABLE `" . PREFIX . "_gallery_category`");

$query = $db->query("SELECT * FROM " . PREFIX . "_gal_cat");

$i = 0;
$GalCat = array ();

echo "Начинаем обработку категорий:<br /><br />";

while($row = $db->get_row($query)){ $i++;

	$row['cat_title'] = addslashes($row['cat_title']);
	$row['cat_desc'] = addslashes($row['cat_desc']);
	$row['us_cat'] = addslashes($row['us_cat']);
	if ($row['us_cat'] == "") $row['us_cat'] = $admin['name'];
	$last_date = date ("Y-m-d H:i:s", $row['date']);
	$row['cat_alt_name'] = totranslit($row['cat_alt_name']);
	if ($row['cat_columns'] && $row['perpage'])
		$row['perpage'] = round(($row['perpage']/$row['cat_columns']));
	else
		$row['perpage'] = 0;

     if (($file = ROOT_DIR."/uploads/old_gallery/caticons/".$row['image']) && file_exists($file)){
		@copy($file, FOTO_DIR . '/caticons/' . $row['image']);
		@chmod(FOTO_DIR . '/caticons/' . $row['image'], 0666);
     }

	$db->query("INSERT INTO " . PREFIX . "_gallery_category (id, p_id, cat_alt_name, cat_title, cat_short_desc, meta_descr, keywords, position, user_name, reg_date, last_date, images, icon, skin, moderators, allowed_extensions, view_level, upload_level, comment_level, edit_level, mod_level, allow_rating, allow_comments, allow_watermark, auto_resize, subcatskin, smallfotoskin, bigfotoskin, foto_td, foto_tr, subcats_td, subcats_tr) VALUES ('{$row['cat_id']}', '{$row['pid']}', '{$row['cat_alt_name']}', '{$row['cat_title']}', '{$row['cat_desc']}', '', '', '{$row['cat_order']}', '{$row['us_cat']}', '{$last_date}', '{$last_date}', '0', '{$row['image']}', '{$row['skin_name']}', '', '', '{$row['cat_view_level']}', '{$row['cat_upload_level']}', '{$row['cat_comment_level']}', '{$row['cat_edit_level']}', '{$row['cat_mod_level']}', '{$row['allow_rating']}', '{$row['allow_comm']}', '{$row['allow_wat']}', '{$row['auto_res']}', '', '', '', '{$row['columns_i']}', '{$row['rows']}', '{$row['cat_columns']}', '{$row['perpage']}')");

	echo $i.": ".$row['cat_title']." - добавлена<br />";

	$GalCat[$row['cat_id']] = array ();

	foreach ($row as $key => $value){
	 	$GalCat[$row['cat_id']][$key] = $value;
	}

}

$db->free($query);

echo "<br />";
echo "<br />";

echo "Добавлено {$i} категорий";

echo "<br />";
echo "<br />";

echo "Начинаем обработку фотографий:<br /><br />";

$db->query("TRUNCATE TABLE `" . PREFIX . "_gallery_picturies`");
$db->query("TRUNCATE TABLE `" . PREFIX . "_gallery_picturies_text`");

$query = $db->query("SELECT * FROM " . PREFIX . "_gal_pic");

$i = 0;
$ii = 0;
$posi = array();

while($row = $db->get_row($query)){

	$dirrectory = 'main/'.$row['pic_cat_id'];

	$error = false;

	if (!is_dir(FOTO_DIR.'/' . $dirrectory)){

		@mkdir(FOTO_DIR.'/' . $dirrectory, 0777) or $error = true;

		if (!$error && !is_writable(FOTO_DIR.'/' . $dirrectory)) $error = true;
		if ($error){
			echo "Не удалось создать папку ".$dirrectory.". Обновление прервано.";
			die();
		}

		@chmod(FOTO_DIR.'/' . $dirrectory, 0777);

	}

	if (($file = ROOT_DIR."/uploads/old_gallery/main/".$GalCat[$row['pic_cat_id']]['cat_alt_name']."/".$row['pic_filname']) && file_exists($file)){
		@copy($file, FOTO_DIR . '/' . $dirrectory .'/' . $row['pic_filname']) or $error = true;
	} elseif (($file = ROOT_DIR."/uploads/old_gallery/main/".$row['pic_filname']) && file_exists($file)){
		@copy($file, FOTO_DIR . '/' . $dirrectory .'/' . $row['pic_filname']) or $error = true;
	} else {
		echo $row['pic_filname']." - файл не найден<br />";
		$ii++;
		continue;
	}

	if ($error){
		echo $row['pic_filname']." - файл не удалось переместить в рабочую папку<br />";
		$ii++;
		continue;
	}

 	$i++;

	@chmod (FOTO_DIR . '/' . $dirrectory .'/' . $row['pic_filname'], 0666);

	$md5_hash = @md5_file(FOTO_DIR . '/' . $dirrectory .'/' . $row['pic_filname']);
	$size = @filesize(FOTO_DIR . '/' . $dirrectory .'/' . $row['pic_filname']);
	$img_info = @getimagesize(FOTO_DIR . '/' . $dirrectory .'/' . $row['pic_filname']);
	$width = $img_info[0];
	$height = $img_info[1];

	$picture_alt_name = totranslit(stripslashes($row['pic_title']));
	$row['pic_title'] = addslashes($row['pic_title']);
	$row['pic_desc'] = addslashes($row['pic_desc']);
	if (!isset($posi[$row['pic_cat_id']])) $posi[$row['pic_cat_id']] = 1; else $posi[$row['pic_cat_id']]++;
	$row['pic_user_id'] = addslashes($row['pic_user_id']);
	if ($row['pic_user_id'] == "") $row['pic_user_id'] = $admin['name'];
	$last_date = date ("Y-m-d H:i:s", $row['pic_time']);
	$picture_symbol = totranslit($picture_alt_name{0});
	if (strlen($row['pic_desc']) > 5) $has_text = 1; else $has_text = 0;

	$db->query("INSERT INTO " . PREFIX . "_gallery_picturies (picture_id, picture_title, picture_alt_name, posi, picture_filname, media_type, md5_hash, full_link, type_upload, size, width, height, picture_user_name, ip, date, lastdate, category_id, view_count, allow_comms, allow_rate, comments, rating, vote_num, approve, symbol, has_text)
	VALUES ('{$row['pic_id']}', '{$row['pic_title']}', '{$picture_alt_name}', '{$posi[$row['pic_cat_id']]}', '{$row['pic_filname']}', '0', '{$md5_hash}', '', '0', '{$size}', '{$width}', '{$height}', '{$row['pic_user_id']}', '{$_IP}', '{$last_date}', '{$last_date}', '{$row['pic_cat_id']}', '{$row['pic_view_count']}', '1', '1', '{$row['comm_num']}', '{$row['rating']}', '{$row['vote_num']}', '{$row['approve']}', '{$picture_symbol}', '{$has_text}')");

	$db->query("INSERT INTO " . PREFIX . "_gallery_picturies_text (pic_id, search_title, text) VALUES ('{$row['pic_id']}', '{$row['pic_title']}', '{$row['pic_desc']}')");


	echo $i.": ".$row['picture_title']." - добавлен<br />";

}

$db->free($query);
unset($posi);

echo "<br />";
echo "<br />";

echo "Добавлено {$i} фотографий, потеряно {$ii}";

echo "<br />";
echo "<br />";

echo "Начинаем обработку комментариев:<br /><br />";

$db->query("CREATE TABLE " . PREFIX . "_gal_com_backup (
`id` int( 10 ) unsigned NOT NULL AUTO_INCREMENT ,
`post_id` int( 11 ) NOT NULL default '0',
`cat_id` mediumint( 8 ) NOT NULL default '0',
`date` datetime NOT NULL default '0000-00-00 00:00:00',
`autor` varchar( 100 ) NOT NULL default '',
`email` varchar( 100 ) NOT NULL default '',
`text` text NOT NULL ,
`ip` varchar( 50 ) NOT NULL default '',
`is_register` smallint( 3 ) NOT NULL default '0',
PRIMARY KEY ( `id` ) ,
KEY `post_id` ( `post_id` ) 
) TYPE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */");

$db->query("INSERT INTO `" . PREFIX . "_gal_com_backup` SELECT * FROM `" . PREFIX . "_gal_com`");

$db->query("DROP TABLE `" . PREFIX . "_gallery_comments`");

$db->query("ALTER TABLE `" . PREFIX . "_gal_com` DROP `cat_id`");
$db->query("ALTER TABLE `" . PREFIX . "_gal_com` CHANGE `autor` `autor` varchar( 40 ) NOT NULL");
$db->query("ALTER TABLE `" . PREFIX . "_gal_com` CHANGE `email` `email` varchar( 40 ) NOT NULL");
$db->query("ALTER TABLE `" . PREFIX . "_gal_com` CHANGE `ip` `ip` varchar( 16 ) NOT NULL");
$db->query("ALTER TABLE `" . PREFIX . "_gal_com` CHANGE `is_register` `is_register` TINYINT( 1 ) NOT NULL DEFAULT '1'");
$db->query("ALTER TABLE `" . PREFIX . "_gal_com` ADD `user_id` MEDIUMINT( 8 ) NOT NULL DEFAULT '0'");
$db->query("ALTER TABLE `" . PREFIX . "_gal_com` ADD `approve` TINYINT( 1 ) NOT NULL DEFAULT '1'");
$db->query("ALTER TABLE `" . PREFIX . "_gal_com` ADD INDEX ( `user_id` )");
$db->query("ALTER TABLE `" . PREFIX . "_gal_com` ADD FULLTEXT ( `text` )");


$query = $db->query("SELECT c.id, u.user_id FROM " . PREFIX . "_gal_com c LEFT JOIN " . USERPREFIX . "_users u ON c.autor=u.name WHERE c.is_register='1'");

while($row = $db->get_row($query)){

	if ($row['user_id']){
		$db->query("UPDATE " . PREFIX . "_gal_com SET user_id='{$row['user_id']}' WHERE id = '{$row['id']}'");
	} else {
		$db->query("UPDATE " . PREFIX . "_gal_com SET is_register='0' WHERE id = '{$row['id']}'");
	}

}

$db->free($query);

$db->query("RENAME TABLE `" . PREFIX . "_gal_com` TO `" . PREFIX . "_gallery_comments`");

echo "<br />";
echo "<br />";

echo "Комментарии успешно перенесены";

echo "<br />";
echo "<br />";

echo "Начинаем обработку логов голосований за фотографии:<br /><br />";

$db->query("TRUNCATE TABLE `" . PREFIX . "_gallery_logs`");

$query = $db->query("SELECT l.*, u.user_id FROM " . PREFIX . "_gal_log l LEFT JOIN " . USERPREFIX . "_users u ON l.member=u.name");

while($row = $db->get_row($query)){

	if (!$row['user_id']) $row['user_id'] = 0;

	$db->query("INSERT INTO " . PREFIX . "_gallery_logs (id, pic_id, ip, member_id) values ('{$row['id']}', '{$row['pic_id']}', '{$row['ip']}', '{$row['user_id']}')");

}

$db->free($query);

echo "<br />";
echo "<br />";

echo "Логи успешно перенесены";

echo "<br />";
echo "<br />";

@unlink(ENGINE_DIR.'/cache/system/gallery_category.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_cron.php');
@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');
clear_gallery_cache();
clear_gallery_vars();

echo "Обновление проведено успешно. Внимательно прочитайте инструкцию по дальнейшему подключению галереи!";

?>