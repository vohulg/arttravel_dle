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
 Файл: editfile.php
-----------------------------------------------------
 Назначение: Управление файлами и базой данных файлов
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

class gallery_file_edit {

	var $ids = false;
	var $affected_files = 0;
	var $error_result = array();
	var $category_update = array();
	var $cache_update = false;
	var $access_error = false;
	var $upload_category = false;
	var $full_access = false;
	var $admin_active = false;
	var $stat = array();
	var $lang = false;

	function gallery_file_edit(){
	global $member_id, $is_logged;

		$this->admin_active = defined('ACP_ACTIVE');
		$this->full_access = ($this->admin_active) ? 2 : check_gallery_access ("edit", "", "");

		if ($member_id['user_group'] == 1 && $is_logged)
			$galConfig['file_title_control'] = 0;

	}

	///********************************************************************************************************
	//                                   Функция - получения списка редактируемых файлов
	//*********************************************************************************************************
	function get_mass_ids(){

		$this->category_update = $this->error_result = array();
		$this->affected_files = 0;
		$this->access_error = false;
		$this->ids = array();

		if (!isset($_REQUEST['si'])) return 0;

		if (!is_array($_REQUEST['si'])){
			$_REQUEST['si'] = intval($_REQUEST['si']);
			if ($_REQUEST['si'] > 0) $this->ids[] = $_REQUEST['si'];
		} elseif (count($_REQUEST['si']))
			foreach ($_REQUEST['si'] as $i => $d){
				$_REQUEST['si'][$i] = intval($_REQUEST['si'][$i]);
				if ($_REQUEST['si'][$i] > 0) $this->ids[] = $_REQUEST['si'][$i];
			}

		return count($this->ids);
	}

	///********************************************************************************************************
	//                                   Функция - загрузка активной категории в память из бд
	//*********************************************************************************************************
	function load_cat_by_id($id){
	global $db;

		if (!$id) return 0;

		if (isset($this->upload_category[$id])) return $id;

		$this->upload_category[$id] = $db->super_query("SELECT * FROM " . PREFIX . "_gallery_category WHERE id='{$id}'");

		if (!$this->upload_category[$id]['id']) return 0;

		return intval($this->upload_category[$id]['id']);
	}

	///********************************************************************************************************
	//                                   Функция - проверка прав на выполнение действия
	//*********************************************************************************************************
	function allow($act, &$row){
	global $is_logged, $galConfig, $member_id;

		$allow = 0;

		$row['own_admin_foto'] = ($is_logged && $row['allow_user_admin'] && $member_id['name'] == $row['user_name']);

		if ($this->full_access){

			$row['is_admin'] = 2;
			return 2;

		}

		$row['is_admin'] = check_gallery_access ("edit", $row['edit_level'], $row['moderators']);

		switch ($act){
		case 1 :
			if ($row['is_admin'] || $row['own_admin_foto'] || $is_logged && $galConfig['allow_edit_picture'] && $row['user_id'] == $member_id['user_id']) $allow = 1;
		break;
		case 2 :
			$allow = check_gallery_access ("upload", $row['upload_level'], $row['moderators'], $row['mod_level'], ($row['locked'] || $row['disable_upload']), $row['user_name'], $row['allow_user_admin']);
		break;
		case 3 :
			if ($row['is_admin'] || $row['own_admin_foto'] || $is_logged && $galConfig['allow_delete_picture'] && $row['user_id'] == $member_id['user_id']) $allow = 1;
		break;
		}

		if (!$allow) $this->access_error = true;

	return $allow;
	}

	///********************************************************************************************************
	//                                   Функция - запись действий над файлом в лог
	//*********************************************************************************************************
	function set_mod_log($act, &$row){
	global $member_id, $db;

		$log = (!$row['logs']) ? array() : explode("|||", $row['logs']); 

		$log[] = $act."||".TIME."||".$db->safesql($member_id['name']);
		$row['logs'] = implode("|||", $log);

	}

	///********************************************************************************************************
	//                                   Функция - установка файла в очередь обновления категории
	//*********************************************************************************************************
	function set_category_update($approve, &$row){
	global $galConfig;

		if (!$row['category_id']) return; // Защита от кривых рук различного происхождения

		// Устанавливаем количество файлов, которое прибавилось или убавилось в категории в зависимости от значения $approve: равен 0 - вычитаем, 1 - прибавляем
		if (!isset($this->category_update[$row['category_id']]))
			$this->category_update[$row['category_id']] = array($approve, 1, $row['p_id'], 0, 0, '');
		else
			$this->category_update[$row['category_id']][1]++;

		$date_flag = ($this->category_update[$row['category_id']][4] < $row['date']);

		if ($approve == 0){

			// Если иконка категории совпала с удаляемым фалом, то сохраняем номер файла
			if ($row['icon_picture_id'] == $row['picture_id']) $this->category_update[$row['category_id']][3]  = $row['picture_id'];
			// Если дата обновления категории совпала с удаляемым фалом, то сохраняем дату файла
			if ($row['last_date'] == $row['date']) $this->category_update[$row['category_id']][4] = $row['date'];

		} elseif ($approve == 1 && $date_flag){

			// Сохраняем дату самого нового файла из вновь публикуемых
			$this->category_update[$row['category_id']][4] = $row['date'];
			// Сохраняем ID самого нового файла из вновь публикуемых
			$this->category_update[$row['category_id']][3] = $row['picture_id'];

		} elseif ($approve == 2){

			if ($row['icon_picture_id'] == $row['picture_id']){
				// Если иконка категории совпала с обновляемым фалом, то сохраняем номер файла
				$this->category_update[$row['category_id']][3] = $row['picture_id'];
			} else unset($this->category_update[$row['category_id']]);

		}

		if ($galConfig['icon_type'] && ($approve == 1 && $date_flag || $approve == 2 && $row['icon_picture_id'] == $row['picture_id'])){

			// Сохраняем иконку самого нового файла из вновь публикуемых или обновляемых
			if ($row['media_type'] && !$row['preview_filname'])
				$this->category_update[$row['category_id']][5] = "{THEME}/gallimages/extensions/".get_extension_icon ($row['picture_filname'], $row['media_type']);
			else {
				$thumb_path = thumb_path($row['thumbnails'], 'i');
				if (!$row['type_upload'] || $thumb_path != 'main')
					$this->category_update[$row['category_id']][5] = '{FOTO_URL}/'.$thumb_path.'/'.$row['category_id'].'/'.$row[($row['preview_filname'] && $thumb_path != 'main' ? 'preview_filname' : 'picture_filname')];
				else
					$this->category_update[$row['category_id']][5] = $row['full_link'];
			}

		}

	}

	///********************************************************************************************************
	//                                   Функция - обновление категорий из массива $this->category_update
	//*********************************************************************************************************
	function category_update(){
	global $galConfig, $db, $catedit;

		if (!is_object($catedit))
			$catedit = new gallery_category_edit();

		foreach ($this->category_update as $id => $data){

			$category_update_id = $catedit->get_parents_id($id, $data[2]);

			if ($data[0] == 0){

				$last_foto = ($data[3] || $data[4]) ? $catedit->get_last_category_file($id) : false;

				$db->query("UPDATE " . PREFIX . "_gallery_category SET ".($data[3] ? "icon=IF(icon_picture_id={$data[3]},'{$last_foto['file']}',icon), icon_picture_id=IF(icon_picture_id={$data[3]},'{$last_foto['icon_picture_id']}',icon_picture_id), " : "")."images=IF(id={$id},images-{$data[1]},images), ".($data[4] ? "last_date=IF(id={$id},'{$last_foto['date']}',last_date), " : "").($data[4] ? "last_cat_date=IF(last_cat_date='{$data[4]}', IF(last_date>'{$last_foto['date']}', last_date, '{$last_foto['date']}'),last_cat_date), " : "")."cat_images=cat_images-{$data[1]} WHERE id IN (".implode(",",$category_update_id).")"); // Снятие с публикации

			} elseif ($data[0] == 1) $db->query("UPDATE " . PREFIX . "_gallery_category SET ".($galConfig['icon_type'] ? " icon=IF(((icon='' OR icon_picture_id) AND icon_picture_id<{$data[3]}),'{$data[5]}',icon), icon_picture_id=IF((icon!='' AND icon='{$data[5]}'),'{$data[3]}',icon_picture_id), " : "")."images=IF(id={$id},images+{$data[1]},images), last_date=IF((id={$id} AND last_date<'{$data[4]}'),'{$data[4]}',last_date), last_cat_date=IF(last_cat_date<'{$data[4]}','{$data[4]}',last_cat_date), cat_images=cat_images+{$data[1]} WHERE id IN (".implode(",",$category_update_id).")"); // Установка на публикацию
			elseif ($data[0] == 2) $db->query("UPDATE " . PREFIX . "_gallery_category SET icon=IF(icon_picture_id={$data[3]},'{$data[5]}',icon) WHERE id IN (".implode(",",$category_update_id).")"); // Обновление пути на иконку

		}

		if (count($this->category_update)){

			$db->query("UPDATE " . PREFIX . "_gallery_search SET actual=0 WHERE actual != 0");

			if ($galConfig['show_statistic']){

				$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_file_day','statistic_file','statistic_file_onmod')");
				@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

			}

			$this->cache_update = true;

		}

		if ($this->cache_update){

			clear_gallery_vars();
			$this->cache_update = false;

		}

		clear_gallery_cache();

		$this->category_update = array();

	}

	///********************************************************************************************************
	//                                   Функция - расшифровка сообщений администрирования
	//*********************************************************************************************************
	function decode_action_log ($current_log){
	global $langGal;

		switch ($current_log){
			case 2 :  $field = $langGal['mod_logs10']; break;//Удаление
			case 3 :  $field = $langGal['mod_logs11']; break;//Очистка просмотров 
			case 4 :  $field = $langGal['mod_logs12']; break;//Редактирование причины
			case 5 :  $field = $langGal['mod_logs1']; break;//Публикация
			case 6 :  $field = $langGal['mod_logs2']; break;//Снятие с публикации
			case 9 :  $field = $langGal['mod_logs3']; break;//Разрешение комментариев
			case 10 : $field = $langGal['mod_logs4']; break;//Запрет комментариев
			case 11 : $field = $langGal['mod_logs5']; break;//Разрешение рейтинга
			case 12 : $field = $langGal['mod_logs6']; break;//Запрет рейтинга
			case 13 : $field = $langGal['mod_logs9']; break;//Удаление в корзину
			case 14 : $field = $langGal['mod_logs8']; break;//Изменение категории
			case 18 : $field = $langGal['mod_logs7']; break;//Полное редактирование
			case 20 : $field = $langGal['mod_logs13']; break;//Изменение автора
			case 22 : $field = $langGal['mod_logs14']; break;//Изменение ключевых слов
			default : $field = $langGal['mod_logs0']; break;//Неизвестно
		}

		return $field;
	}

	///********************************************************************************************************
	//                                   Функция - уведомление пользователя о модерации его файла
	//*********************************************************************************************************
	function send_user_messages ($selected, $send_text='', $action = 0){
	global $db, $config, $user_group, $member_id, $langGal, $galConfig;

		if (intval($_REQUEST['send_notice']) != 1 || !$member_id['user_id']) return 0;

		$send_text = $db->safesql(htmlspecialchars(strip_tags(trim($send_text)), ENT_QUOTES, $config['charset']));

		if ($send_text == strip_tags(trim($langGal['js_p_text_d']))) $send_text = false;

		if (!count($selected) || (!$send_text && !$action)) return;

		$db->query("SELECT p.picture_id, p.picture_title, p.category_id, u.name, u.user_id FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . USERPREFIX . "_users u ON u.user_id=p.user_id WHERE p.picture_id IN ('".implode ("','",$selected)."') AND u.allow_mail=1".(!defined('DEBUG_MODE') ? " AND u.user_id !='{$member_id['user_id']}' AND u.user_group > 2" : ""));

		$send_user_list = array();

		while($row = $db->get_row()){

			$fullfoto = $config['http_home_url']."index.php?do=gallery&act=2&cid=".$row['category_id']."&fid=".$row['picture_id'];

			if (!isset($send_user_list[$row['user_id']]))
				$send_user_list[$row['user_id']] = array(stripslashes($row['name']),array(),1);
			else
				$send_user_list[$row['user_id']][2]++;

			$url = $action != 2 ? "<a href=\"{$config['http_home_url']}index.php?do=gallery&act=2&cid={$row['category_id']}&fid={$row['picture_id']}\" target=\"_blank\">{$langGal['send_notice_url']}</a>" : "";
			$title = $row['picture_title'] != '' ? "\"" . stripslashes($row['picture_title']) . "\" " : $langGal['send_no_title'];
			$send_user_list[$row['user_id']][1][] = $title . $url;

		}

		$db->free();

		if (!count($send_user_list)) return;

		include_once TWSGAL_DIR.'/classes/mail.php';

		$INFORM = new Mailer();

		$INFORM->template = 'gallery_editfoto';

		$INFORM->subject = $langGal['subj_edit_file'];
		$INFORM->Mailer_set(true, $member_id['name']);
		$INFORM->bbcode_tpl();
		$INFORM->set('{%username%}', $member_id['name']);
		$INFORM->set('{%usergroup%}', stripslashes($user_group[$member_id['user_group']]['group_name']));
		$INFORM->set('{%date%}', langdate("j F Y H:i", TIME));
		$INFORM->set('{%site%}', $config['http_home_url']);

		if ($action){

			$INFORM->set('{%action%}', $this->decode_action_log ($action));

			$INFORM->set('[action]', '');
			$INFORM->set('[/action]', '');

		} else $INFORM->set_block("'\\[action\\](.*?)\\[/action\\]'si","");

		if ($send_text){

			$INFORM->set('{%notice%}', $send_text);

			$INFORM->set('[notice]', '');
			$INFORM->set('[/notice]', '');

		} else $INFORM->set_block("'\\[notice\\](.*?)\\[/notice\\]'si","");

		$INFORM->compile(1);

		foreach($send_user_list as $user_id => $data){

			$INFORM->set('{%name%}', $data[0]);
			$INFORM->set('{%fileslist%}', implode("<br />", $data[1]));
			$INFORM->set('{%filesnum%}', $data[2]);

			$INFORM->do_send_message ("", $user_id);

		}

		$INFORM->clear();

	}

	///********************************************************************************************************
	//                                   Функция - перенаправление
	//*********************************************************************************************************
	function redirect ($url){
	global $db;

		$db->close ();

		@header("Location: {$url}");
		die("Please visit <a href=\"/{$url}\">{$url}</a>");

	}

	///********************************************************************************************************
	//                                   Редактирование файлов - подготовка
	//*********************************************************************************************************
	function edit_prepare($first=false){
	global $db, $galConfig, $tpl, $langGal, $bb_code, $input, $is_logged, $member_id, $config, $user_group;

		$this->stat['is_admin'] = false; // Для возврата

		if (!$this->get_mass_ids() && !$first) return 0; // !Последовательность не перепутана - метод get_mass_ids должен отработать!

		$input = "";

		$form_ob = "document.entryform";

		include_once ENGINE_DIR.'/classes/parse.class.php';
		include_once TWSGAL_DIR.'/modules/inserttag.php';

		$parse = new ParseFilter();

		if (!$first || $this->admin_active) $category_list = CategoryGalSelection();

		$sql = $db->query("SELECT p.*, c.cat_title, c.p_id, c.last_date, c.last_cat_date, c.edit_level, c.moderators, c.user_name, c.icon_picture_id, c.allow_user_admin FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE ".(!$first ? "picture_id IN ('".implode ("','",$this->ids)."')" : "approve=3 AND ".($is_logged ? "user_id='".$member_id['user_id']."'" : "session_id='".session_id()."'")." AND date > '".date ("Y-m-d H:i:s", (TIME-3600*2))."'"));

		while($row = $db->get_row($sql)){

			if (!$this->allow(1, $row) && !$first) continue; // !Последовательность не перепутана - метод allow должен отработать!

			$this->affected_files++;

			$row['picture_title'] = stripslashes($row['picture_title']);
			$row['image_alt_title'] = stripslashes($row['image_alt_title']);
			$row['cat_title'] = stripslashes($row['cat_title']);
			$row['picture_user_name'] = stripslashes($row['picture_user_name']);

			$checkers = array();
	
			$checkers['comms'] = ($row['allow_comms']) ? " checked" : "";
			$checkers['rate'] = ($row['allow_rate']) ? " checked" : "";
			$checkers['approve'] = ($row['approve'] == '1' || $row['is_admin'] && $first) ? " checked" : "";

			$row['text'] = $parse->decodeBBCodes($row['text'], false);

			if ($row['type_upload'] == 0)
				$row['full_link'] = FOTO_URL.'/main/'.$row['category_id'].'/'.$row['picture_filname'];

			$row['date'] = strtotime($row['date']);
			$row['langdate'] = langdate($galConfig['timestamp_active'], $row['date']);

			$input .= "\n<input type=\"hidden\" name=\"si[]\" value=\"{$row['picture_id']}\">";

			if ($member_id['user_group'] == 1 && $row['is_admin'] && !$first){
				$tpl->set('[admin-autor]', '');
				$tpl->set('[/admin-autor]', '');
			} else $tpl->set_block("'\\[admin-autor\\](.*?)\\[/admin-autor\\]'si","");

			if ($config['allow_add_tags'] || $row['is_admin']){
				$tpl->set('[tags]', '');
				$tpl->set('[/tags]', '');
				$this->stat['allow_tags'] = true; // Для возврата
			} else $tpl->set_block("'\\[tags\\](.*?)\\[/tags\\]'si","");

			$tpl->set('[foto]', '');
			$tpl->set('[/foto]', '');
			$tpl->set_block("'\\[button\\](.*?)\\[/button\\]'si","");

			$tpl->set('{user_edit}', ($this->admin_active && !$first && $row['user_id'] && ($row['user_id'] != $member_id['user_id'] || defined('DEBUG_MODE')) && $user_group[$member_id['user_group']]['admin_editusers']) ? '<a onclick="javascript:popupedit('.$row['user_id'].'); return(false)" href="#"><img src="engine/skins/images/user_edit.png" style="vertical-align: middle;border: none;" /></a>' : '');

			$tpl->set('', array(
			'{autor}'			=> $row['picture_user_name'],
			'{date}'			=> $row['langdate'],
			'{user_name}'		=> ($row['picture_user_name'] != '' ? $row['picture_user_name'] : '--'),
			'{categorytitle}'	=> $row['cat_title'],
			'{title}'			=> $row['picture_title'],
			'{alt_title}'		=> $row['image_alt_title'],
			'{alt-name}'		=> $row['picture_alt_name'],
			'{bbcode}'			=> $bb_panel,
			'{views}'			=> $row['file_views'],
			'{short-story}'		=> $row['text'],
			'{path}'			=> $row['full_link'],
			'{id}'				=> $row['picture_id'],
			'{symbol}'			=> stripslashes($row['symbol']),
			'{edit_reason}'		=> stripslashes($row['edit_reason']),
			'{width}'			=> $row['width'],
			'{height}'			=> $row['height'],
			'{size}'			=> formatsize($row['size']),
			'{name}'			=> $row['picture_filname'],
			'{tags}'			=> stripslashes($row['tags']),
			));

			if (!$first || $this->admin_active)
				$tpl->set('{category}', "<select name=\"category[{$row['picture_id']}]\">".str_replace(' value="'.$row['category_id'].'">', ' value="'.$row['category_id'].'" SELECTED>', $category_list)."</select>");
			else
				$tpl->set('{category}', $row['cat_title']);

			if (!$row['media_type']){

				$thumb_path = thumb_path($row['thumbnails'], 't');

				if ($thumb_path != 'main')
					$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$row['category_id'].'/'.$row[($row['preview_filname'] ? 'preview_filname' : 'picture_filname')];
				else
					$thumb_path = $row['full_link'];

				$tpl->set('[fullimageurl]', '<a href="'.$row['full_link'].'" onclick="return hs.expand(this)">');
				$tpl->set('[/fullimageurl]', '</a>');
				$tpl->set('[isfoto]', '');
				$tpl->set('[/isfoto]', '');
				$tpl->set_block("'\\[ismedia\\](.*?)\\[/ismedia\\]'si","");
				$tpl->set('{thumb}', '<img src="'.$thumb_path.'" alt="'.$row['image_alt_title'].'" title="'.$row['image_alt_title'].'" />');

			} else {

				if ($row['preview_filname']){
					$thumb_path = thumb_path($row['thumbnails'], 't');
					$thumb_path = FOTO_URL.'/'.$thumb_path.'/'.$row['category_id'].'/'.$row['preview_filname'];
				} else $thumb_path = "";

				$tpl->set('[ismedia]', '');
				$tpl->set('[/ismedia]', '');
				$tpl->set('[fullimageurl]', '');
				$tpl->set('[/fullimageurl]', '');
				$tpl->set_block("'\\[isfoto\\](.*?)\\[/isfoto\\]'si","");
				$tpl->set('{thumb}', players($row['full_link'], $row['media_type'], false, $thumb_path));

			}

			if (!$row['is_admin']){

				$admin_tags = "";
				$tpl->set_block("'\\[admin\\](.*?)\\[/admin\\]'si","");
				$tpl->set_block("'\\[admin-reason\\](.*?)\\[/admin-reason\\]'si","");

			} else {

				$this->stat['is_admin'] = true;

				if (!$first){
					$tpl->set('[admin-reason]', '');
					$tpl->set('[/admin-reason]', '');
				} else $tpl->set_block("'\\[admin-reason\\](.*?)\\[/admin-reason\\]'si","");

				$tpl->set('[admin]', '');
				$tpl->set('[/admin]', '');

				$admin_tags = "<input type=\"checkbox\" name=\"allow_comms[{$row['picture_id']}]\" id=\"allow_comms_{$row['picture_id']}\" value=\"1\"{$checkers['comms']}> <label for=\"allow_comms_{$row['picture_id']}\">{$langGal['allow_comm']}</label> <input type=\"checkbox\" name=\"allow_rate[{$row['picture_id']}]\" id=\"allow_rate_{$row['picture_id']}\" value=\"1\"{$checkers['rate']}> <label for=\"allow_rate_{$row['picture_id']}\">{$langGal['allow_rating']}</label><br /><input type=\"checkbox\" name=\"approve[{$row['picture_id']}]\" id=\"approve_{$row['picture_id']}\" value=\"1\"{$checkers['approve']}> <label for=\"approve_{$row['picture_id']}\">{$langGal['allow_approve']}</label>";

				if (!$first && ($row['preview_filname'] || !$row['media_type']))
					$admin_tags .= "<br /><input type=\"checkbox\" name=\"refresh_thumbs[{$row['picture_id']}]\" id=\"refresh_thumbs_{$row['picture_id']}\" value=\"1\"> <label for=\"refresh_thumbs_{$row['picture_id']}\">{$langGal['allow_refresh_thumbs']}</label>";

			}

			$tpl->set('{admin-tags}', $admin_tags);

			$tpl->compile('content');

		}

		$db->free($sql);

	return true;
	}

	///********************************************************************************************************
	//                                   Редактирование файлов - сохранение
	//*********************************************************************************************************
	function edit($first=false){
	global $db, $galConfig, $langGal, $user_group, $member_id, $is_logged, $config;
	static $users_check = array();

		$this->stat['on_moderation'] = false;
		$this->stat['ok'] = 0;

		$log_action_id = 18;

		if (!$this->get_mass_ids()) return 0;

		include_once TWSGAL_DIR.'/classes/edittagscloud.php';
		include_once ENGINE_DIR.'/classes/parse.class.php';

		$tagscloud = new gallery_tags_edit();

		$parse = new ParseFilter();
		$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
		$parse->allow_image = false;

		$sql = $db->query("SELECT p.*, c.id, c.cat_title, c.cat_alt_name, c.p_id, c.last_date, c.last_cat_date, c.edit_level, c.moderators, c.user_name, c.auto_resize, c.allow_watermark, c.com_thumb_max, c.thumb_max, c.icon_max_size, c.icon_picture_id, c.allow_user_admin FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE picture_id IN ('".implode ("','",$this->ids)."')" . (!$first ? "" : " AND  approve=3 AND ".($is_logged ? "user_id='".$member_id['user_id']."'" : "session_id='".session_id()."'")));

		$this->ids = array();

		while($row = $db->get_row($sql)){

			if (!$this->allow(1, $row) && !$first) continue;

			$this->ids[] = $row['picture_id'];

			$this->affected_files++;

			if (!$user_group[$member_id['user_group']]['allow_html'])
				$_POST['short_story'][$row['picture_id']] = strip_tags($_POST['short_story'][$row['picture_id']]);

			$error = array();
			$foto = array();

			$parse->not_allowed_tags = $parse->not_allowed_text = false;

			$foto[1] = $db->safesql($parse->BB_Parse($parse->process($_POST['short_story'][$row['picture_id']]), false));
			$foto[0] = $db->safesql($parse->process(strip_tags(trim($_POST['title'][$row['picture_id']]))));
			$foto[3] = isset($_POST['category'][$row['picture_id']]) ? intval($_POST['category'][$row['picture_id']]) : $row['category_id'];


			//	if ($parse->not_allowed_text) $error[] = $langGal['edit_foto_er5'];
			//	if ($parse->not_allowed_tags) $error[] = $langGal['edit_foto_er6'];


			if ($galConfig['autowrap_foto'] && $foto[1]){

				if ($config['charset'] == "utf-8") $utf_pref = "u"; else $utf_pref = "";
				$foto[1] = preg_split('((>)|(<))', $foto[1], -1, PREG_SPLIT_DELIM_CAPTURE);
				$n = count($foto[1]);
				for ($i = 0; $i < $n; $i++) {
					if ($foto[1][$i] == "<"){
						$i++; continue;
					}
					$foto[1][$i] = preg_replace(
					"#([^\s\n\r]{".$galConfig['autowrap_foto']."})#{$utf_pref}i", 
						"\\1<br />", $foto[1][$i]);
				}
				$foto[1] = join("", $foto[1]);

			}

			if (!$foto[0] && $galConfig['file_title_control'] && $this->full_access != 2) $error[] = $langGal['edit_foto_er1'];

			if ($foto[3] != $row['category_id']){

				$this->load_cat_by_id($foto[3]);

				$foto[2] = $this->allow(2, $this->upload_category[$foto[3]]);

				if (!$foto[2]) $error[] = $langGal['edit_foto_er3'];

				$this->stat['cat_alt_name'] = $this->upload_category[$foto[3]]['cat_alt_name']; // Для возврата
				$this->stat['category_id'] = $foto[3]; // Для возврата

			} else {

				$foto[2] = $this->allow(2, $row);

				$this->stat['cat_alt_name'] = $row['cat_alt_name']; // Для возврата
				$this->stat['category_id'] = $row['category_id']; // Для возврата

			}

			$foto[2] = ($foto[2] == 2) ? 1 : 0;

			if ($member_id['user_group'] == 1 && $row['is_admin'] && !$first && trim($_POST['autor'][$row['picture_id']]) != $row['picture_user_name']){

				$row['picture_user_name'] = $db->safesql(trim($_POST['autor'][$row['picture_id']]));

				if ($row['picture_user_name']){

					if (!isset($users_check[$row['picture_user_name']])){
						$user_control = $db->super_query("SELECT user_id FROM " . USERPREFIX . "_users WHERE name='{$row['picture_user_name']}'");
						$users_check[$row['picture_user_name']] = intval($user_control['user_id']);
					}

					$row['user_id'] = $users_check[$row['picture_user_name']];
					if ($row['user_id']) $row['email'] = '';

				} else {

					$row['user_id'] = 0;
					$row['email'] = '';

				}

			}

			$preview_update = $save_first = $temp_file = $icon_update = false;

			if (isset($_FILES['preview']['name'][$row['picture_id']]) && $_FILES['preview']['name'][$row['picture_id']] != ""){

				include_once TWSGAL_DIR . '/classes/upload.php';

				if ($config["lang_".$config['skin']]){
					include_once ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/gallery.upload.lng';
				} else {
					 include_once ROOT_DIR.'/language/'.$config['langs'].'/gallery.upload.lng';
				}

				global $UPL;

				$UPL = new gallery_upload_images($galConfig, 0, 'jpg,jpeg,png,gif');
				$UPL->main_dir = 'temp';
				$UPL->config['max_once_upload'] = 1;
				$UPL->config['allow_resize'] = 1;
				$UPL->config['rewrite'] = 1;
				$UPL->config['file_hash'] = 0;
				$UPL->config['file_db_exists'] = 0;
				$UPL->config['random_name'] = 16;
				$UPL->config['disable_thumbnailer'] = 1;

				$UPL->doupload(array(1), 'preview', false);

				if ($UPL->upload_stats['counter']){

					$this->remove_file($row['id'], $row['picture_filname'], $row['preview_filname'], false);
					$preview_update = FOTO_DIR . '/temp/' . $UPL->upload_result[0][4];
					if (!$row['media_type']) $row['preview_filname'] = $row['picture_filname'];
					$save_first = $temp_file = $icon_update = true;

				} else {

					$image = ($UPL->upload_result[0][0] !== false && $UPL->upload_result[0][0] != "") ? "<font color=red>".$UPL->upload_result[0][0]."</font>  &raquo; " : "";
					$error[] = $langGal['edit_foto_er2'].' ('.$image.stripslashes($this->lang['add_foto_error_'.$UPL->upload_result[0][1]]).')';

				}

			} elseif (!count($error) && !$first && ($row['preview_filname'] || !$row['media_type']) && intval($_POST['refresh_thumbs'][$row['picture_id']])){

				$icon_update = true;

				if (!$row['media_type']){

					if ($row['type_upload']){

						$img_arr = explode('.',$row['picture_filname']);
						$type = end($img_arr);
						$preview_update = FOTO_DIR . '/temp/' . time().mt_rand(10000000,99999999).'.'.$type;
						@copy($row['full_link'], $preview_update);
						$temp_file = true;

						if (!file_exists($preview_update))
							$error[] = $langGal['edit_foto_er2'];

					} else $preview_update = FOTO_DIR . '/main/' . $row['category_id'] .'/' . $row['picture_filname'];

				}

				if (!count($error)){

					$this->remove_file($row['id'], $row['picture_filname'], $row['preview_filname'], false);
					$row['preview_filname'] = '';

				}

			}

			if ($preview_update){

				include_once TWSGAL_DIR.'/classes/thumbnailer.php';

				global $catedit;

				if (!is_object($catedit))
					$catedit = new gallery_category_edit();

				$catedit->check_new_gallery_dir($row['category_id']);

				$thumb = $this->refresh_thumbs($preview_update, $row, $row['preview_filname'], $save_first);

				if ($thumb !== -1 && is_object($thumb) && !$thumb->error()){
					$row['thumbnails'] = $thumb->thumbnails;
					$row['preview_filname'] = $thumb->preview;
					if ($row['preview_filname'] == $row['picture_filname']) $row['preview_filname'] = '';
				} else
					$error[] = $langGal['edit_foto_er2'];

			}

			if ($temp_file) @unlink($preview_update);

			if (($errors_count = count($error))){

				$this->error_result[] = "<font color=red>".($row['picture_title'] ? stripslashes($row['picture_title']) : $row['picture_filname']).":</font>".($errors_count == 1 ? " " : "<br />").implode("<br />", $error);
				continue;

			}

			$this->stat['ok']++;

			if ($row['is_admin']){

				$row['picture_alt_name'] = totranslit(stripslashes(trim($_POST['alt_name'][$row['picture_id']])), true, false);
				$row['allow_comms'] = intval($_POST['allow_comms'][$row['picture_id']]);
				$row['allow_rate'] = intval($_POST['allow_rate'][$row['picture_id']]);
				$foto[2] = intval($_POST['approve'][$row['picture_id']]);
				$row['symbol'] = $db->safesql(htmlspecialchars(strip_tags(stripslashes(trim($_POST['symbol'][$row['picture_id']]))), ENT_QUOTES, $config['charset']));
				$row['image_alt_title'] = $db->safesql($parse->process(trim($_POST['alt_title'][$row['picture_id']])));
				$row['edit_reason'] = $db->safesql($parse->process(trim($_POST['edit_reason'][$row['picture_id']])));

				if ($row['image_alt_title'] == $langGal['upl_s2_tit1_1']) $row['image_alt_title'] = "";

				if ($this->admin_active && !$row['type_upload']){

					$row['size'] = intval(@filesize(FOTO_DIR . '/main/' . $row['category_id'] . '/' . $row['picture_filname']));

					if (!$row['media_type']){

						list($row['width'], $row['height']) = @getimagesize(FOTO_DIR . '/main/' . $row['category_id'] . '/' . $row['picture_filname']);
						$row['width'] = intval($row['width']);
						$row['height'] = intval($row['height']);

					}

				}

			} elseif ($first){

				if ($foto[0])
					$row['picture_alt_name'] = totranslit(stripslashes($foto[0]), true, false);

			}

			if ($config['create_catalog'] && !$row['symbol'] && $foto[0]) $row['symbol'] = $db->safesql(dle_substr(htmlspecialchars(strip_tags(stripslashes(trim($foto[0]))), ENT_QUOTES, $config['charset']), 0, 1, $config['charset']));

			if (!$foto[2]) $this->stat['on_moderation'] = true;

			if ($foto[3] != $row['category_id']){

				$row['picture_filname'] = $this->move_file($row['category_id'], $foto[3], $row['picture_filname'], (($row['media_type'] || $row['preview_filname']) ? array('main') : array('main','thumb','comthumb','caticons')));

				if ($row['preview_filname'])
					$row['preview_filname'] = $this->move_file($row['category_id'], $foto[3], $row['preview_filname'], array('thumb','comthumb','caticons'));

			}

			if (!$first) $this->set_mod_log($log_action_id, $row);

			if ($config['allow_add_tags'] || $row['is_admin'])
				$tagscloud->filter_tags($_POST['tags'][$row['picture_id']]);

			$row['tags'] = $tagscloud->current_tags($row['tags'], $foto[2], ($row['approve'] == 1));

			$db->query("UPDATE " . PREFIX . "_gallery_picturies SET picture_title='{$foto[0]}', picture_alt_name='{$row['picture_alt_name']}', picture_user_name='{$row['picture_user_name']}', user_id='{$row['user_id']}', email='{$row['email']}', image_alt_title='{$row['image_alt_title']}', text='{$foto[1]}', tags='{$row['tags']}', picture_filname='{$row['picture_filname']}', preview_filname='{$row['preview_filname']}', lastdate='".DATETIME."', category_id='{$foto[3]}', allow_comms='{$row['allow_comms']}', allow_rate='{$row['allow_rate']}', approve='{$foto[2]}', symbol='{$row['symbol']}', logs='{$row['logs']}', edit_reason='{$row['edit_reason']}', editor='".$db->safesql($member_id['name'])."', session_id='', size='{$row['size']}', width='{$row['width']}', height='{$row['height']}', thumbnails='{$row['thumbnails']}' WHERE picture_id='{$row['picture_id']}'");

			if ($row['approve'] == 1 && ($foto[3] != $row['category_id'] || !$foto[2])) // Файл был снят с публикации.
				$this->set_category_update(0, $row);

			if ($foto[2] && ($foto[3] != $row['category_id'] || $row['approve'] != 1)){ // Файл опубликовали

				if ($foto[3] != $row['category_id']){

					$row['category_id'] = $foto[3];
					$row['p_id'] = $this->upload_category[$foto[3]]['p_id'];

				}

				$this->set_category_update(1, $row);

			}

			if ($icon_update && !isset($this->category_update[$row['category_id']]))
				$this->set_category_update(2, $row); // Иконку обновили

			$tagscloud->update_files_tags($row['picture_id']);

			if ($tagscloud->changed_data) $this->cache_update = true;

		}

		$db->free($sql);

		$this->category_update();

		$this->send_user_messages ($this->ids, '', $log_action_id);

	return true;
	}

	///********************************************************************************************************
	//                                   Удаление файлов
	//*********************************************************************************************************
	function remove($full = false, $admin_where = ""){
	global $db, $galConfig;

		$log_action_id = 13;

		if (!$admin_where && !$this->get_mass_ids()) return 0;

		$image_delete = array();

		$sql = $db->query("SELECT p.*, c.id, c.cat_title, c.cat_alt_name, c.p_id, c.last_date, c.last_cat_date, c.edit_level, c.moderators, c.user_name, c.icon_picture_id, c.allow_user_admin FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE ".($admin_where ? $admin_where : "p.picture_id IN ('".implode ("','",$this->ids)."')"));

		$this->ids = array();

		while($row = $db->get_row($sql)){

			if (!$admin_where && !$this->allow(3, $row)) continue;

			$this->affected_files++;

			if (!in_array($row['approve'], array(2,3))) $this->ids[] = $row['picture_id'];

			if ($full === 0 || ($full === false && ($row['own_admin_foto'] && $galConfig['allow_recycle_own'] || !$row['own_admin_foto'] && $galConfig['allow_recycle']))){

				if ($row['approve'] == 2) continue;

				$this->set_mod_log($log_action_id, $row);

				$db->query("UPDATE " . PREFIX . "_gallery_picturies SET approve='2', logs='{$row['logs']}' WHERE picture_id='{$row['picture_id']}'");

			} else {

				if ($full != 2) $this->remove_file($row['category_id'], $row['picture_filname'], $row['preview_filname']);

				$image_delete[] = $row['picture_id'];

			}

			if ($row['approve'] == 1) // Файл был снят с публикации.
				$this->set_category_update(0, $row);

			$this->stat['cat_alt_name'] = $row['cat_alt_name']; // Для возврата
			$this->stat['category_id'] = $row['category_id']; // Для возврата

		}

		$db->free($sql);

		if ($full != 2) $this->send_user_messages ($this->ids, $_POST['send_notice_text'], 2);

		if (count($image_delete)){

			$db->query("DELETE FROM " . PREFIX . "_gallery_picturies WHERE picture_id IN (".implode(",",$image_delete ).")");
			$db->query("DELETE FROM " . PREFIX . "_gallery_comments WHERE post_id IN (".implode(",",$image_delete ).")");
			$db->query("DELETE FROM " . PREFIX . "_gallery_logs WHERE pic_id IN (".implode(",",$image_delete ).")");
			$db->query("DELETE FROM " . PREFIX . "_gallery_tags_match WHERE file_id IN (".implode(",",$image_delete ).")");
			$db->query("DELETE FROM " . PREFIX . "_gallery_comments_subscribe WHERE file_id IN (".implode(",",$image_delete ).")");
			if ($galConfig['whois_view_file']) $db->query("DELETE FROM " . PREFIX . "_gallery_users_views WHERE file_id IN (".implode(",",$image_delete ).")");

		}

		$this->category_update();

	return true;
	}

	///********************************************************************************************************
	//                                   Перемещение файлов
	//*********************************************************************************************************
	function move(){
	global $db;

		$category = intval($_REQUEST['new_category']);

		if (!$this->load_cat_by_id($category) || !($approve = $this->allow(2, $this->upload_category[$category]))){
			$this->access_error = true;
			return 0;
		}

		if (!$this->get_mass_ids()) return 0;

		include_once TWSGAL_DIR.'/classes/edittagscloud.php';

		$tagscloud = new gallery_tags_edit();

		$log_action_id = 14;

		$sql_approve = ($approve == 2) ? "" : ", approve=0";

		$sql = $db->query("SELECT p.*, c.id, c.cat_title, c.cat_alt_name, c.p_id, c.last_date, c.last_cat_date, c.edit_level, c.moderators, c.user_name, c.icon_picture_id, c.allow_user_admin FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE picture_id IN ('".implode ("','",$this->ids)."')");

		$this->ids = array();

		while($row = $db->get_row($sql)){

			$this->allow(1, $row);

			if (!$row['is_admin']) continue;

			$this->affected_files++;

			if ($row['category_id'] == $category) continue;

			$this->ids[] = $row['picture_id'];

			$row['picture_filname'] = $this->move_file($row['category_id'], $category, $row['picture_filname'], (($row['media_type'] || $row['preview_filname']) ? array('main') : array('main','thumb','comthumb','caticons')));

			if ($row['preview_filname'])
				$row['preview_filname'] = $this->move_file($row['category_id'], $category, $row['preview_filname'], array('thumb','comthumb','caticons'));

			$this->set_mod_log($log_action_id, $row);

			$db->query("UPDATE " . PREFIX . "_gallery_picturies SET category_id='{$category}'{$sql_approve}, picture_filname='{$row['picture_filname']}', preview_filname='{$row['preview_filname']}', logs='{$row['logs']}' WHERE picture_id='{$row['picture_id']}'");

			if ($row['approve'] == 1) // Файл был снят с публикации.
				$this->set_category_update(0, $row);

			if ($row['approve'] == 1 && $approve == 2){ // Файл опубликовали

				$row['category_id'] = $category;
				$row['p_id'] = $this->upload_category[$category]['p_id'];

				$this->set_category_update(1, $row);

			}

			$tagscloud->current_tags($row['tags'], ($row['approve'] == 1 && $approve == 2), ($row['approve'] == 1));
			$tagscloud->update_files_tags($row['picture_id']);

			$this->stat['cat_alt_name'] = $row['cat_alt_name']; // Для возврата
			$this->stat['category_id'] = $row['category_id']; // Для возврата

		}

		$db->free($sql);

		$this->category_update();

		$this->send_user_messages ($this->ids, $_POST['send_notice_text'], $log_action_id);

	return true;
	}

	///********************************************************************************************************
	//                                   Статус файлов
	//*********************************************************************************************************
	function status($log_action_id, $select_value = false){
	global $db, $langGal;

		if (!$this->get_mass_ids()) return 0;

		include_once TWSGAL_DIR.'/classes/edittagscloud.php';

		$tagscloud = new gallery_tags_edit();

		$remove_tags = array();
		$insert_tags = array();

		switch ($log_action_id){
			case 2 :  $field = "logs";		  $value = ''; break;
			case 3 :  $field = "file_views";  $value = 0; break;
			case 4 :  $field = "edit_reason";

				include_once ENGINE_DIR.'/classes/parse.class.php';

				$parse = new ParseFilter();

				$value = $db->safesql($parse->process(trim($_POST['edit_value'])));

			break;
			case 5 :  $field = "approve"; 	  $value = 1; break;
			case 6 :  $field = "approve"; 	  $value = 0; break;
			case 9 :  $field = "allow_comms"; $value = 1; break;
			case 10 : $field = "allow_comms"; $value = 0; break;
			case 11 : $field = "allow_rate";  $value = 1; break;
			case 12 : $field = "allow_rate";  $value = 0; break;
			case 20 : $field = "picture_user_name";

				include_once ENGINE_DIR.'/classes/parse.class.php';

				$parse = new ParseFilter();

				$value = $db->safesql($parse->process(trim($_POST['edit_value'])));

				if ($value){

					$user_control = $db->super_query("SELECT user_id FROM " . USERPREFIX . "_users WHERE name='{$value}'");
					$user_control['user_id'] = intval($user_control['user_id']);

				} else $user_control['user_id'] = 0;

			break;
			case 22 : $field = "tags";

				$tagscloud->filter_tags($_POST['edit_value']);

				$value = $tagscloud->get_tags();

			break;
			default : die("error");
		}

		$sql = $db->query("SELECT p.*, c.id, c.cat_title, c.cat_alt_name, c.p_id, c.last_date, c.last_cat_date, c.edit_level, c.moderators, c.user_name, c.icon_picture_id, c.allow_user_admin FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE picture_id IN ('".implode ("','",$this->ids)."')");

		$this->ids = array();

		while($row = $db->get_row($sql)){

			$this->allow(1, $row);

			if (!$row['is_admin']) continue;

			$this->affected_files++;

			$log_action = $log_action_id;

			if ($select_value){
				$value = $row[$field] != 1 ? 1 : 0;
				if ($value == 0) $log_action++;
			} elseif ($value == $row[$field]) continue;

			$this->ids[] = $row['picture_id'];

			$this->set_mod_log($log_action, $row);

			$db->query("UPDATE " . PREFIX . "_gallery_picturies SET logs='{$row['logs']}', {$field}='{$value}'".($field == "picture_user_name" ? ", user_id='{$user_control['user_id']}', email=''" : "")." WHERE picture_id='{$row['picture_id']}'");

			switch ($field){
			case 'approve' :

				if ($value == 1 || $row['approve'] == 1)
					$this->set_category_update($value, $row); // Файл был снят с публикации / Файл опубликовали

				$tagscloud->current_tags($row['tags'], $value, ($row['approve'] == 1));
				$tagscloud->update_files_tags($row['picture_id']);

			break;
			case 'tags' :
				$remove_tags[] = $row['picture_id'];
				if ($row['approve'] == 1)
					$insert_tags[] = $row['picture_id'];
			break;
			}

			$this->stat['value'] = $value; // Для возврата
			$this->stat['cat_alt_name'] = $row['cat_alt_name']; // Для возврата
			$this->stat['category_id'] = $row['category_id']; // Для возврата

		}

		$db->free($sql);

		if (count($remove_tags))
			$tagscloud->remove_files_tags($remove_tags);

		if (count($insert_tags))
			$tagscloud->insert_files_tags($insert_tags);

		$this->category_update();

		if ($log_action > 4) $this->send_user_messages ($this->ids, $_POST['send_notice_text'], $log_action);

	return true;
	}

	///********************************************************************************************************
	//                                   Статус файлов - форма подтверждения
	//*********************************************************************************************************
	function status_prepare($file_list = true){
	global $db;

		if (!$this->get_mass_ids()) return 0;

		$files = "";

		$sql = $db->query("SELECT p.*, c.* FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE picture_id IN ('".implode ("','",$this->ids)."')");

		$this->ids = array();

		while($row = $db->get_row($sql)){

			$this->allow(1, $row);

			if (!$row['is_admin']) continue;

			$this->ids[] = $row['picture_id'];
			$this->affected_files++;

			if ($file_list){

				if ($row['picture_title'] == '') $row['picture_title'] = "ID ".$row['picture_id'];
				$files .= "<li>".stripslashes($row['cat_title']." &raquo; ".$row['picture_title'])."<input type=\"hidden\" name=\"si[]\" value=\"{$row['picture_id']}\"></li>\n";

			} else $files .= "<input type=\"hidden\" name=\"si[]\" value=\"{$row['picture_id']}\">\n";

		}

		$db->free($sql);

	return $files;
	}

	///********************************************************************************************************
	//                                   Уведоиление пользователю
	//*********************************************************************************************************
	function message(){
	global $db, $galConfig, $langGal, $config;

		if (!$this->get_mass_ids()) return 0;

		if (dle_strlen(str_replace(" ", "", strip_tags(trim($_POST['send_notice_text']))), $config['charset']) < 5){
			$this->error_result[] = $langGal['send_notice_error1'];
			return 0;
		}

		$sql = $db->query("SELECT p.*, c.id, c.cat_title, c.cat_alt_name, c.p_id, c.last_date, c.last_cat_date, c.edit_level, c.moderators, c.user_name, c.icon_picture_id, c.allow_user_admin FROM " . PREFIX . "_gallery_picturies p INNER JOIN " . PREFIX . "_gallery_category c ON c.id=p.category_id WHERE picture_id IN ('".implode ("','",$this->ids)."')");

		$this->ids = array();

		while($row = $db->get_row($sql)){

			$this->allow(1, $row);

			if ($row['is_admin']){
				$this->ids[] = $row['picture_id'];
				$this->affected_files++;
			}

		}

		$db->free($sql);

		$_REQUEST['send_notice'] = 1;

		$this->send_user_messages ($this->ids, $_POST['send_notice_text'], 0);

	return true;
	}

	///********************************************************************************************************
	//                                   Функция - оператор файла - Обновление миниатюрных изображений файла
	//*********************************************************************************************************
	function refresh_thumbs($root, $edit_category, $preview="", $save_first=false){
	global $galConfig;

		if (!file_exists($root)) return -1;

		if ($preview)
			$img_arr = explode('/',$preview);
		else
			$img_arr = explode('/',$root);

		$file = end($img_arr);
		$img_arr = explode('.',$file);
		$type = end($img_arr);
		unset($img_arr[(key($img_arr))]);

		if ($galConfig['random_filename'] > 4){

			if(function_exists('openssl_random_pseudo_bytes') && (version_compare(PHP_VERSION, '5.3.4') >= 0 || strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))
				$salt = str_shuffle(md5(openssl_random_pseudo_bytes(15)));
			else
				$salt = str_shuffle(md5(uniqid(mt_rand(), TRUE)));

			$img_arr = "";
			$max = min($galConfig['random_filename'], 40);

			for($i=0;$i < $max; $i++)
				$img_arr .= $salt{mt_rand(0,31)};

		} else $img_arr = implode('.', $img_arr);

		$preview_ext = (!in_array($type, array('png')) || !$galConfig['convert_png_thumb']) ? $type : 'jpg';
		$in = 1;

		while ($in == 1 || file_exists(FOTO_DIR . '/thumb/'.$edit_category['id'].'/'.$preview) || file_exists(FOTO_DIR . '/comthumb/'.$edit_category['id'].'/'.$preview) || file_exists(FOTO_DIR . '/caticons/'.$edit_category['id'].'/'.$preview)){

			$preview = $img_arr.($in == 1 ? '' : '_'.$in).'.'.$preview_ext;
			$in++;

		}

		$resize_config = array();
		$min_watermark = ($galConfig['allow_watermark'] && $edit_category['allow_watermark']) ? $galConfig['min_watermark'] : 0;

		$comms_foto_size = $galConfig['comms_foto_size'] ? ($edit_category['com_thumb_max'] ? $edit_category['com_thumb_max'] : $galConfig['comms_foto_size']) : 0;
		$max_thumb_size = $edit_category['thumb_max'] ? $edit_category['thumb_max'] : $galConfig['max_thumb_size'];
		$max_icon_size = $edit_category['icon_max_size'] ? $edit_category['icon_max_size'] : $galConfig['max_icon_size'];

		if ($max_icon_size && $galConfig['icon_type']){

			switch (true){
			case (intval($max_icon_size) <= intval($max_thumb_size)) : $icon_type = $galConfig['thumb_res_type']; break;
			case ($comms_foto_size && intval($max_icon_size) <= intval($comms_foto_size)) : $icon_type = $galConfig['comm_res_type']; break;
			default : $icon_type = $galConfig['full_res_type'];
			}

			$resize_config['i'] = array ($max_icon_size, true, $min_watermark, $icon_type, FOTO_DIR . '/caticons/' . $edit_category['id'] .'/' . $preview, false);

		}

		if ($comms_foto_size) $resize_config['c'] = array ($comms_foto_size, true, $min_watermark, $galConfig['comm_res_type'], FOTO_DIR . '/comthumb/' . $edit_category['id'] .'/' . $preview, false);
		$resize_config['t'] = array ($max_thumb_size, true, $min_watermark, $galConfig['thumb_res_type'], FOTO_DIR . '/thumb/' . $edit_category['id'] .'/' . $preview, false);

		$thumb = new gallery_thumbnailer($root, $galConfig['resize_quality'], $type, $resize_config, $save_first);

		$thumb->preview = (($thumb->thumbnails['i'] != 1 && $thumb->thumbnails['c'] != 1 && $thumb->thumbnails['t'] != 1)) ? '' : $preview;
		$thumb->thumbnails = $thumb->get_thumbnails();

		return $thumb;
	}

	///********************************************************************************************************
	//                                   Функция - оператор файла - удаление файла из категории
	//*********************************************************************************************************
	function remove_file($id, $file, $preview = "", $main = true){

		if ($preview == '') $preview = $file;

		if ($main) @unlink(FOTO_DIR . '/main/'.$id.'/'.$file);

		@unlink(FOTO_DIR . '/comthumb/'.$id.'/'.$preview);
		@unlink(FOTO_DIR . '/thumb/'.$id.'/'.$preview);
		@unlink(FOTO_DIR . '/caticons/'.$id.'/'.$preview);

	}

	///********************************************************************************************************
	//                                   Функция - оператор файла - перемещение файла в другую папку категории
	//*********************************************************************************************************
	function move_file($old_dir, $new_dir, $file, $parent_dirs = array('thumb','comthumb','caticons','main')){

		$newfile = $file;

		$in = 2;

		while (1){

			$break = true;

			foreach ($parent_dirs as $folder)
				if (file_exists(FOTO_DIR . '/'.$folder.'/'.$new_dir.'/'.$newfile)){
					$break = false;
					break;
				}

			if ($break) break;

			$newfile = explode('.',$file);
			$type = end($newfile);
			unset($newfile[(key($newfile))]);
			$newfile = implode(".", $newfile).'_'.$in++.'.'.$type;

		}

		foreach ($parent_dirs as $folder){

			$path = FOTO_DIR.'/'.$folder;

			if (!file_exists($path.'/'.$old_dir.'/'.$file)) continue;

			if (!is_dir($path)){
				@mkdir($path, 0777);
				@chmod($path, 0777);
			} elseif (!is_writable($path)){
				@chmod($path, 0777);
			}

			if (!is_dir($path.'/'.$new_dir)){
				@mkdir($path.'/'.$new_dir, 0777);
				@chmod($path.'/'.$new_dir, 0777);
			} elseif (!is_writable($path.'/'.$new_dir)){
				@chmod($path.'/'.$new_dir, 0777);
			}

			@rename($path.'/'.$old_dir.'/'.$file, $path.'/'.$new_dir.'/'.$newfile);
			@chmod ($path.'/'.$new_dir.'/'.$newfile, 0666);

		}

		return $newfile;
	}

}

?>