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
 Файл: cron.php
-----------------------------------------------------
 Назначение: запуск запланированых заданий
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

	if ($galConfig['file_views'] && $config['cache_count']){

		$sql = $db->query("SELECT COUNT(*) as count, picture_id FROM " . PREFIX . "_gallery_picture_views GROUP BY picture_id");

		while ($row = $db->get_row($sql))
			$db->query( "UPDATE " . PREFIX . "_gallery_picturies SET file_views=file_views+{$row['count']} WHERE picture_id={$row['picture_id']}");

		$db->free($sql);

		$db->query("TRUNCATE TABLE " . PREFIX . "_gallery_picture_views");

	}

	if ($galConfig['mail_foto']){

		$user_report = array();

		$sql = $db->query("SELECT p.picture_user_name, p.ip, p.date, c.cat_title, c.moderators FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE p.approve=0 AND lastdate > '".date ("Y-m-d H:i:s", (TIME-3600*2))."'");

		while($row = $db->get_row($sql)){

			if ($row['moderators'])
				$user_ids = explode (',',$row['moderators']);
			else
				$user_ids = array(1);

			foreach ($user_ids as $user_id){

				if (!isset($user_report[$user_id])) $user_report[$user_id] = array(array(),array(),array(),array());

				$row['date'] = langdate("j F Y H:i", strtotime($row['date']));
				if ($row['picture_user_name'] && !in_array($row['picture_user_name'], $user_report[$user_id][0])) $user_report[$user_id][0][] = $row['picture_user_name'];
				if (!in_array($row['ip'], $user_report[$user_id][1])) $user_report[$user_id][1][] = $row['ip'];
				if (!in_array($row['date'], $user_report[$user_id][2])) $user_report[$user_id][2][] = $row['date'];
				if (!in_array($row['cat_title'], $user_report[$user_id][3])) $user_report[$user_id][3][] = $row['cat_title'];

			}

		}

		$db->free($sql);

		if (count($user_report)){

			include_once TWSGAL_DIR.'/classes/mail.php';

			$INFORM = new Mailer();

			$INFORM->template = 'gallery_newfoto';

			$INFORM->subject = $langGal['subj_new_foto'];
			$INFORM->Mailer_set();

			$names = $db->query("SELECT email, name, user_id, user_group FROM " . USERPREFIX . "_users WHERE user_id IN ('".implode ("','",array_keys($user_report))."')");

			while($name = $db->get_row($names)){

				$INFORM->set('{%username%}', stripslashes(implode(", ", $user_report[$name['user_id']][0])));
				$INFORM->set('{%date%}', implode(", ", $user_report[$name['user_id']][2]));
				$INFORM->set('{%site%}', $config['http_home_url']);
				$INFORM->set('{%images%}', count($user_report[$name['user_id']]));
				$INFORM->set('{%category%}', stripslashes(implode(", ", $user_report[$name['user_id']][3])));
				$INFORM->set('{%name%}', stripslashes($name['name']));

				if ($name['user_group'] == '1'){
					$INFORM->set('{%link%}', $config['http_home_url'].$config['admin_path'].'?mod=twsgallery&act=10&moderate=1');
					$INFORM->set('{%ip%}', " ( IP: ".implode(", ", $user_report[$name['user_id']][1])." ) ");
				} else {
					$INFORM->set('{%link%}', $config['http_home_url']."index.php?do=gallery&act=16");
					$INFORM->set('{%ip%}', '');
				}

				$INFORM->do_send_message ($name['email']);

			}

			$db->free($names);
			$INFORM->clear();

		}

	}

	if ($galcron == 2){

		clear_gallery_vars('search_');

		$db->query("TRUNCATE TABLE " . PREFIX . "_gallery_search_text");
		$db->query("DELETE FROM " . PREFIX . "_gallery_search WHERE date < '".date("Y-m-d H:i:s", (TIME-86400*2))."'");

		if ($galConfig['enable_banned']){

			$db->query("DELETE FROM " . USERPREFIX . "_gallery_banned WHERE date != '0' AND date < '".TIME."'");

			@unlink(ENGINE_DIR.'/cache/system/gallery_banned.php');

			$count = $db->super_query("SELECT COUNT(id) as count FROM " . USERPREFIX . "_gallery_banned");

			if (!$count['count'])
				$db->query("UPDATE " . PREFIX . "_gallery_config SET value='0' WHERE name='enable_banned'");

		}

		if ($galConfig['max_comments_days'])
			$db->query("DELETE FROM " . PREFIX . "_gallery_comments_subscribe WHERE date < '".date("Y-m-d", (TIME-86400*intval($galConfig['max_comments_days'])))."'");
			
		$db->query("DELETE FROM " . PREFIX . "_gallery_flood WHERE date < '".date("Y-m-d H:i:s", (TIME-86400))."'");

		$db->query("SELECT path FROM " . PREFIX . "_gallery_temp_files WHERE date < '".date("Y-m-d H:i:s", (TIME-3600))."'");

		if ($db->num_rows() > 0){

			while($row = $db->get_row()){
				@unlink(FOTO_DIR . $row['path']);
			}

			$db->free();

			$db->query("DELETE FROM " . PREFIX . "_gallery_temp_files WHERE date < '".date("Y-m-d H:i:s", (TIME-3600))."'");

		}

		include_once TWSGAL_DIR.'/classes/editcategory.php';

		$catedit = new gallery_category_edit();

		if (($handle_folder = @opendir(FOTO_DIR . '/temp'))){

			while (false !== ($file = @readdir($handle_folder)))
				if (!in_array($file, array(".", "..", ".htaccess")) && ($ftime = substr($file, 0, 10)) && $ftime < (TIME-86400) && strpos($file, "unzip") !== false && is_dir(FOTO_DIR . '/temp/' . $file))
					$catedit->delete_folder('temp/' . $file);

			@closedir($handle_folder);

		}

		if (($handle_folder = @opendir(FOTO_DIR . '/caticons'))){

			while (false !== ($file = @readdir($handle_folder)))
				if (!in_array($file, array(".", "..", ".htaccess")) && !is_dir(FOTO_DIR . '/caticons/' . $file) && ($ftime = filemtime(FOTO_DIR . '/caticons/' . $file)) && $ftime < (TIME-86400))
					@unlink(FOTO_DIR . '/caticons/' . $file);

			@closedir($handle_folder);

		}

		$auto_delete = $db->super_query("SELECT id FROM " . PREFIX . "_gallery_category WHERE exprise_delete != 0 LIMIT 1");

		if ($auto_delete['id']){

			include_once TWSGAL_DIR.'/classes/editfile.php';
			include_once TWSGAL_DIR.'/classes/editcategory.php';

			$edit = new gallery_file_edit();
			$edit->remove(0, "c.exprise_delete != '0' AND p.date < ( FROM_UNIXTIME( ( UNIX_TIMESTAMP() - ( c.exprise_delete * 86400 )) ,'%Y-%m-%d %H:%i:%s') )");

		}

		$tags = array();

		$db->query("SELECT t.id FROM " . PREFIX . "_gallery_tags t LEFT JOIN " . PREFIX . "_gallery_tags_match m ON t.id=m.tag_id WHERE m.tag_id IS NULL");

		while($row = $db->get_row())
			$tags[] = $row['id'];

		$db->free();

		if (count($tags))
			$db->query("DELETE FROM " . PREFIX . "_gallery_tags WHERE id IN (".implode(",",$tags).")");

	}

	clear_gallery_cache();

	$db->query("UPDATE " . PREFIX . "_gallery_config SET value='".TIME."' WHERE name='last_cron'");
	//	if ($galConfig['show_statistic']) $db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_cat','statistic_cat_week','statistic_com','statistic_com_day','statistic_file_day','statistic_file','statistic_file_onmod','statistic_downloads')");

	@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

?>