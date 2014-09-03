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

global $catedit, $gal_pid, $cat_selected_first;

$catedit = $gal_pid = $cat_selected_first = false;

class gallery_category_edit {

	var	$parent_id_tree = array();
	var $ids = false;
	var $affected_categories = 0;
	var $error_result = array();
	var $access_error = false;
	var $full_access = false;
	var $admin_active = false;
	var $stat = array();
	var $_profiles = false;

	function gallery_category_edit(){

		$this->admin_active = defined('ACP_ACTIVE');
		$this->full_access = ($this->admin_active) ? 2 : check_gallery_access ("edit", "", "");

	}

	///********************************************************************************************************
	//                                   Функция - получения списка редактируемых категорий
	//*********************************************************************************************************
	function get_mass_ids(){

		$this->error_result = array();
		$this->affected_categories = 0;
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
	//                                   Функция - загружаем шаблоны категорий (профили)
	//*********************************************************************************************************
	function template_profiles(){

		if (is_array($this->_profiles)) return count($this->_profiles);

		$this->_profiles = get_vars ("gallery_profiles");
		if (!$this->_profiles){

	global $db;

			$this->_profiles = array();
			$db->query("SELECT pr.*, c.id as cat_id, cat_alt_name FROM " . PREFIX . "_gallery_profiles pr LEFT JOIN " . PREFIX . "_gallery_category c ON c.id=pr.p_id ORDER BY id");
			while($row = $db->get_row()){
				if ($row['p_id'] && !$row['cat_id']) continue;
				$this->_profiles[$row['id']] = $row;
			}
			$db->free();
			set_vars ("gallery_profiles", $this->_profiles);

		}

		return count($this->_profiles);
	}

	///********************************************************************************************************
	//                                   Функция - вывод ников модераторов по id
	//*********************************************************************************************************
	function moderators($moderators){
	global $db;

		if ($moderators){

			$moderators = explode (',',$moderators);

			$db->query("SELECT name FROM " . USERPREFIX . "_users WHERE user_id IN ('".implode ("','",$moderators)."')");

			$moderators = array();

			while($name = $db->get_row())
				$moderators[] = $name['name'];

			$db->free();

			$moderators = implode (',',$moderators);

		}

		return $moderators;
	}

	///********************************************************************************************************
	//                                   Функция - загрузка фиксированной иконки категории
	//*********************************************************************************************************
	function upload_icon_file($resize = 0, $index = 'image'){
	global $langGal, $galConfig;

		$error = array();

		$image = !is_array($index) ? $_FILES[$index]['tmp_name'] : $_FILES[$index[0]]['tmp_name'][$index[1]];

		if (!is_uploaded_file($image)) return false;

		$img_name_arr = explode(".", (!is_array($index) ? $_FILES[$index]['name'] : $_FILES[$index[0]]['name'][$index[1]]));
		$type = end($img_name_arr);
		$type = totranslit($type, true, false);

		if ((!is_array($index) ? $_FILES[$index]['size'] : $_FILES[$index[0]]['size'][$index[1]]) > 500 * 1024) $error[] = $langGal['edit_cat_er6'];

		if (!in_array($type, array("jpg", "png", "jpe", "jpeg", "gif"))) $error[] = $langGal['edit_cat_er8'];

		if (count($error)) return $error;

		$salt = str_shuffle(md5(uniqid(mt_rand(), TRUE)));

		while (1){

			$image_name = "fixed";

			for($i=0;$i < 4; $i++)
				$image_name .= $salt{mt_rand(0,31)};

			$image_name .= ".".$type;

			if (!file_exists(FOTO_DIR."/caticons/".$image_name)) break;

		}

		$res = move_uploaded_file($image, FOTO_DIR."/caticons/".$image_name);
		if (!$res) return array($langGal['edit_cat_er9']);

		@chmod(FOTO_DIR."/caticons/".$image_name, 0666);

		if (intval($resize) < 10) return $image_name;

		include_once ENGINE_DIR.'/classes/thumb.class.php';
		$thumb = new thumbnail(FOTO_DIR."/caticons/".$image_name);

		if ($thumb->size_auto($resize)){

			$thumb->jpeg_quality($galConfig['resize_quality']);
			$thumb->save(FOTO_DIR."/caticons/".$image_name);
			@chmod(FOTO_DIR."/caticons/".$image_name, 0666);

		}

		return $image_name;
	}

	///********************************************************************************************************
	//                                   Функция - получение родительского дерева категории
	//*********************************************************************************************************
	function get_parents_id($id, $parent_id = false){
	global $db;

		if (!$id) return array();

		$category_id = array($id);

		if ($parent_id !== false) $category_id[] = $parent_id;

		if ($parent_id === false) $parent_id = $id;
		elseif (!isset($this->parent_id_tree[$id])) $this->parent_id_tree[$id] = $parent_id;

		while ($parent_id){

			if (!isset($this->parent_id_tree[$parent_id])){

				$parent_data = $db->super_query("SELECT p_id FROM " . PREFIX . "_gallery_category WHERE id='{$parent_id}'");
				$this->parent_id_tree[$parent_id] = isset($parent_data['p_id']) ? intval($parent_data['p_id']) : false;

			}

			$parent_id = $this->parent_id_tree[$parent_id];
			if (!$parent_id) break;
			$category_id[] = $parent_id;

		}

		return $category_id;
	}

	///********************************************************************************************************
	//                                   Функция - получение последнего добавленного файла в категории
	//*********************************************************************************************************
	function get_last_category_file($id){
	global $db, $galConfig;

		$last_foto = $db->super_query("SELECT picture_id as icon_picture_id, date, picture_filname, preview_filname, media_type, type_upload, thumbnails, full_link as file FROM " . PREFIX . "_gallery_picturies WHERE approve=1 AND category_id={$id} ORDER BY picture_id DESC LIMIT 1");
		$last_cat_foto = $db->super_query("SELECT id, icon, icon_picture_id, last_cat_date FROM " . PREFIX . "_gallery_category WHERE p_id={$id} ORDER BY last_cat_date DESC LIMIT 1");

		if (!$last_foto['icon_picture_id'] && !$last_cat_foto['id'])
			return array('date' => "0000-00-00 00:00:00", 'icon_picture_id' => 0, 'file' => '');

		if (strtotime($last_foto['date']) < strtotime($last_cat_foto['last_cat_date'])) $last_foto['date'] = $last_cat_foto['last_cat_date'];

		if (!$galConfig['icon_type'])
			return array('date' => $last_foto['date'], 'icon_picture_id' => 0, 'file' => '');

		if ($last_foto['icon_picture_id'] < $last_cat_foto['icon_picture_id'])
			return array('date' => $last_foto['date'], 'icon_picture_id' => $last_cat_foto['icon_picture_id'], 'file' => $last_cat_foto['icon']);

		if ($last_foto['media_type'] && !$last_foto['preview_filname'])
			$last_foto['file'] = "{THEME}/gallimages/extensions/".get_extension_icon ($last_foto['picture_filname'], $last_foto['media_type']);
		else {
			$thumb_path = thumb_path($last_foto['thumbnails'], 'i');
			if ($thumb_path != 'main')
				$last_foto['file'] = '{FOTO_URL}/'.$thumb_path.'/'.$id.'/'.$last_foto[($last_foto['preview_filname'] ? 'preview_filname' : 'picture_filname')];
			elseif (!$last_foto['type_upload'])
				$last_foto['file'] = '{FOTO_URL}/main/'.$id.'/'.$last_foto['picture_filname'];
		}

		return $last_foto;
	}

	///********************************************************************************************************
	//                                   Функция - проверка и создание папок для файлов категории
	//*********************************************************************************************************
	function check_new_gallery_dir($id, $dirs = array('main', 'comthumb', 'thumb', 'caticons')){

		$error = false;

		foreach ($dirs as $sub_dir){

			if (!is_dir(FOTO_DIR.'/' . $sub_dir)){
				@mkdir(FOTO_DIR.'/' . $sub_dir, 0777) or $error = true;
				@chmod(FOTO_DIR.'/' . $sub_dir, 0777);
			} elseif (!is_writable(FOTO_DIR.'/' . $sub_dir))
				@chmod(FOTO_DIR.'/' . $sub_dir, 0777);

			if ($id && $this->check_new_gallery_dir(0, array($sub_dir . '/' . $id))) $error = true;

		}

		return $error;
	}

	///********************************************************************************************************
	//                                   Функция - очистка и удаление папки категории
	//*********************************************************************************************************
	function delete_folder($handle, $delete_dirs = true, $except = false){

		$handle_folder = @opendir(FOTO_DIR.'/' . $handle);

		while ($handle_folder && false !== ($file = @readdir($handle_folder))){

			if ($file != "." && $file != ".htaccess" && $file != ".."){
				if (!is_dir(FOTO_DIR.'/' . $handle . '/' . $file))
					if ($delete_dirs || !$except || !in_array($file, $except)) @unlink(FOTO_DIR.'/' . $handle . '/' . $file);
				elseif ($delete_dirs)
					$this->delete_folder($handle . '/' . $file, $except);
			}

		}

		@closedir($handle_folder);

		if ($delete_dirs){
			@rmdir(FOTO_DIR.'/' . $handle);
		}

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
	//                                   Редактирование категории - подготовка
	//*********************************************************************************************************
	function edit_prepare(){
	global $db, $galConfig, $tpl, $langGal, $bb_code, $input, $member_id, $user_group;

		$this->get_mass_ids();

		$input = "";

		$edit = count($this->ids);

		if ($edit){

			$sql = $db->query("SELECT * FROM " . PREFIX . "_gallery_category WHERE id IN ('".implode ("','",$this->ids)."')");

			if (!($row = $db->get_row($sql))) return 0;

			include_once ENGINE_DIR.'/classes/parse.class.php';

			$parse = new ParseFilter();

		} else $row = array('id' => 0);

		$form_ob = "document.entryform";

		include_once TWSGAL_DIR.'/modules/inserttag.php';

		$block_js = ($edit) ? " && el != 'cat_alt_name'" : "";

		$profile_rows = array('p_id' => 0, 'view_level' => '', 'upload_level' => '', 'comment_level' => '', 'edit_level' => '', 'mod_level' => '', 'moderators' => '', 'foto_sort' => 0, 'foto_msort' => 0, 'allow_rating' => 1, 'allow_comments' => 1, 'allow_watermark' => 1, 'subcats_td' => 0, 'subcats_tr' => 0, 'foto_td' => 0, 'foto_tr' => 0, 'auto_resize' => 1, 'skin_name' => '', 'subcatskin' => '', 'maincatskin' => '', 'smallfotoskin' => '', 'bigfotoskin' => '', 'width_max' => 0, 'height_max' => 0, 'com_thumb_max' => 0, 'thumb_max' => 0, 'extensions' => '', 'icon_max_size' => 0, 'allow_carousel' => 1, 'exprise_delete' => 0, 'allow_user_admin' => 0, 'cat_alt_name' => '', 'size_factor' => 0, 'uploadskin' => '');

		$bb_code .= <<<JSCRIPT
<script language='JavaScript' type="text/javascript">
<!--
function edit_profile(id, cid){
	if (!profiles[id]) return;
	var els = 
JSCRIPT;

		$bb_code .= "['" . implode("','", array_keys($profile_rows)) . "'];\n";

		$bb_code .= <<<JSCRIPT
	var e, ar, arr;
	$.each(els, function(i, el){
		if (el{$block_js} && (e = document.getElementById(el+cid))){
			switch(e.type){
				case 'select': case 'select-multiple': case 'select-one':
					arr = new Array(); if (profiles[id][i]) arr = profiles[id][i].split(',');
					$.each(e.options, function(k, d){
						e.options[k].selected = ($.inArray(e.options[k].value, arr) != -1 || !arr[0] && !e.options[k].value) ? true : false;
					});
				break;
				case 'radio': case 'checkbox': e.checked = (profiles[id][i] == 1 ? true : false); break;
				default: e.value = profiles[id][i];
			}
		}
	});
}
var profiles = {};
profiles[0] = 
JSCRIPT;

		$bb_code .= "['" . implode("','", $profile_rows) . "'];\n";

		$profiles = "<option value=\"0\" selected>{$langGal['profile_clean']}</option>\n";

		$this->template_profiles();

		foreach($this->_profiles as $v){

			$v['profile_name'] = stripslashes($v['profile_name']);
			$profiles .= "<option value=\"{$v['id']}\">{$v['profile_name']}</option>\n";

			$v['moderators'] = $this->moderators($v['moderators']);
			$v['cat_alt_name'] = str_replace(array("{%date%}", "{%user%}", "{%category%}"), array(date('Y-m-d', TIME), stripslashes($member_id['name']), $v['cat_alt_name']), preg_replace("#\{\%date=(.+?)\%\}#ie", "date('\\1', '".TIME."')", $v['alt_name_tpl']));
			$v['skin_name'] = $v['skin'];
			$v['extensions'] = $v['allowed_extensions'];

			$bb_code .= "profiles[{$v['id']}] = [";

			foreach ($profile_rows as $field => $d)
				$bb_code .= "'{$v[$field]}',";

			$bb_code = substr($bb_code, 0, -1);
			$bb_code .= "];\n";

		}

		$bb_code .= "-->
</script>\n";

		$templates_list = array();
		$handle = @opendir('./templates');

		while (false !== ($file = @readdir($handle)))
			if (is_dir("./templates/$file") and ($file != "." and $file!=".."))
				$templates_list[] = $file;

		@closedir($handle);

		$cat = (isset($_REQUEST['r']) && !$edit) ? intval($_REQUEST['r']) : 0;
		$category_list = CategoryGalSelection($cat, 1);

		while ($row){

			if (!$this->full_access && check_gallery_access ("edit", $row['edit_level'], $row['moderators']) != 2 || $row['p_id'] && strpos($category_list, ' value="'.$row['p_id'].'"') === false){
				$this->access_error = true;
				$row = ($edit) ? $db->get_row($sql) : false;
				continue;
			}

			$this->affected_categories++;

			$row['cat_title'] = stripslashes($row['cat_title']);
			if ($edit) $row['cat_short_desc'] = $parse->decodeBBCodes($row['cat_short_desc'], false);

			$skin_list = "";

			foreach($templates_list as $single_template)
				$skin_list .= "<option value=\"$single_template\"".($single_template == $row['skin'] ? " selected" : "").">{$single_template}</option>";

			if (!$row['thumb_max']) $row['thumb_max'] = 0;
			if (!$row['com_thumb_max']) $row['com_thumb_max'] = 0;
			if (!$row['icon_max_size']) $row['icon_max_size'] = 0;

			if ($member_id['user_group'] == 1 && $this->full_access == 2){
				$tpl->set('[admin-autor]', '');
				$tpl->set('[/admin-autor]', '');
			} else $tpl->set_block("'\\[admin-autor\\](.*?)\\[/admin-autor\\]'si","");

			$row['user_name'] = ($edit) ? stripslashes($row['user_name']) : stripslashes($member_id['name']);

			$tpl->set('{user_edit}', ($this->admin_active && $edit && $row['user_name'] && ($row['user_name'] != stripslashes($member_id['name']) || defined('DEBUG_MODE')) && $user_group[$member_id['user_group']]['admin_editusers']) ? '<a onclick="javascript:popupedit(0, \''.$row['user_name'].'\'); return(false)" href="#"><img src="engine/skins/images/user_edit.png" style="vertical-align: middle;border: none;" /></a>' : '');

			$tpl->set('', array(
			'{id}'				=> $row['id'],
			'{autor}'			=> $row['user_name'],
			'{meta_descr}'		=> stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"), $row['meta_descr'])),
			'{keywords}'		=> stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"), $row['keywords'])),
			'{metatitle}'		=> stripslashes(preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"), $row['metatitle'])),
			'{cat_title}'		=> $row['cat_title'],
			'{cat_short_desc}'	=> $row['cat_short_desc'],
			'{skinlist}'		=> "<select name=\"skin_name[{$row['id']}]\" id=\"skin_name{$row['id']}\">\n<option value=\"\" style=\"color: green;\">".$langGal['sys_global']."</option>\n".$skin_list."</select>",
			'{category}'		=> "<select name=\"p_id[{$row['id']}]\" id=\"p_id{$row['id']}\"><option value=\"0\"></option>\n".str_replace(' value="'.$row['p_id'].'">', ' value="'.$row['p_id'].'" SELECTED>', $category_list)."</select>",
			'{cat_alt_name}'	=> $row['cat_alt_name'],
			'{subcats_td}'		=> intval($row['subcats_td']),
			'{subcats_tr}'		=> intval($row['subcats_tr']),
			'{foto_td}'			=> intval($row['foto_td']),
			'{foto_tr}'			=> intval($row['foto_tr']),
			'{maincatskin}'		=> $row['maincatskin'],
			'{subcatskin}'		=> $row['subcatskin'],
			'{smallfotoskin}'	=> $row['smallfotoskin'],
			'{bigfotoskin}'		=> $row['bigfotoskin'],
			'{uploadskin}'		=> $row['uploadskin'],
			'{width_max}'		=> intval($row['width_max']),
			'{height_max}'		=> intval($row['height_max']),
			'{com_thumb_max}'	=> $row['com_thumb_max'],
			'{thumb_max}'		=> $row['thumb_max'],
			'{exprise_delete}'	=> intval($row['exprise_delete']),
			'{bbcode}'			=> $bb_panel,
			'{icon_max_size}'	=> $row['icon_max_size'],
			'{size_factor}'		=> intval($row['size_factor']),
			));

			$this->stat['cat_title'] = $row['cat_title']; // Для возврата


			$checkers = array();
	
			$checkers['comms'] = ($row['allow_comments'] || !$edit) ? " checked" : "";
			$checkers['rate'] = ($row['allow_rating'] || !$edit) ? " checked" : "";
			$checkers['resize'] = ($row['auto_resize'] || !$edit) ? " checked" : "";
			$checkers['watermark'] = ($row['allow_watermark'] || !$edit) ? " checked" : "";
			$checkers['carousel'] = ($row['allow_carousel'] || !$edit) ? " checked" : "";

			$tags = "<input type=\"checkbox\" name=\"allow_comments[{$row['id']}]\" id=\"allow_comments{$row['id']}\" value=\"1\"{$checkers['comms']}> <label for=\"allow_comments{$row['id']}\">{$langGal['allow_comm']}</label> <input type=\"checkbox\" name=\"allow_rating[{$row['id']}]\" id=\"allow_rating{$row['id']}\" value=\"1\"{$checkers['rate']}> <label for=\"allow_rating{$row['id']}\">{$langGal['allow_rating']}</label>";

			if ($this->admin_active)
				$tags .= "<br /><input type=\"checkbox\" name=\"auto_resize[{$row['id']}]\" id=\"auto_resize{$row['id']}\" value=\"1\"{$checkers['resize']}> <label for=\"auto_resize{$row['id']}\">{$langGal['allow_resize']}</label><br /><input type=\"checkbox\" name=\"allow_watermark[{$row['id']}]\" id=\"allow_watermark{$row['id']}\" value=\"1\"{$checkers['watermark']}> <label for=\"allow_watermark{$row['id']}\">{$langGal['allow_wat']}</label><br /><input type=\"checkbox\" name=\"allow_carousel[{$row['id']}]\" id=\"allow_carousel{$row['id']}\" value=\"1\"{$checkers['carousel']}> <label for=\"allow_carousel{$row['id']}\">{$langGal['allow_carousel']}</label>";

			$tpl->set('{admin-tags}', $tags);
			$tpl->set('{moderators}', $this->moderators($row['moderators']));
			$tpl->set('{categorystat}', makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "locked[{$row['id']}]", intval($row['locked'])));
			$tpl->set('{c_sort}', makeDropDownGallery(array(""=>$langGal['sys_global'],"posi"=>$langGal['opt_sys_sort_o'],"date"=>$langGal['opt_sys_sdate'],"rating"=>$langGal['opt_sys_srate'],"file_views"=>$langGal['opt_sys_sview'],"comments"=>$langGal['opt_sys_img_com'],"picture_title"=>$langGal['opt_sys_salph']), "foto_sort{$row['id']}", $row['foto_sort']));
			$tpl->set('{c_msort}', makeDropDownGallery(array(""=>$langGal['sys_global'],"desc"=>$langGal['opt_sys_mminus'],"asc"=>$langGal['opt_sys_mplus']), "foto_msort{$row['id']}", $row['foto_msort']));

			if ($this->admin_active){

				$tpl->set('{allow_user_admin}', makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "allow_user_admin{$row['id']}", intval($row['allow_user_admin'])));
				$tpl->set('{disable_upload}', makeDropDownGallery(array("1"=>$langGal['yes'],"0"=>$langGal['no']), "disable_upload[{$row['id']}]", intval($row['disable_upload'])));

				$tpl->set('{view_level}', "<select name=\"view_level[{$row['id']}][]\" id=\"view_level{$row['id']}\" class=\"cat_select\" multiple=\"multiple\">".get_gal_groups(explode(',', $row['view_level']), 1)."</select>");
				$tpl->set('{comment_level}', "<select name=\"comment_level[{$row['id']}][]\" id=\"comment_level{$row['id']}\" class=\"cat_select\" multiple=\"multiple\">".get_gal_groups(explode(',', $row['comment_level']), 1)."</select>");
				$tpl->set('{upload_level}', "<select name=\"upload_level[{$row['id']}][]\" id=\"upload_level{$row['id']}\" class=\"cat_select\" multiple=\"multiple\">".get_gal_groups(explode(',', $row['upload_level']), 1)."</select>");
				$tpl->set('{mod_level}', "<select name=\"mod_level[{$row['id']}][]\" id=\"mod_level{$row['id']}\" class=\"cat_select\" multiple=\"multiple\">".get_gal_groups(explode(',', $row['mod_level']), 1, 1, array(1,2))."</select>");
				$tpl->set('{edit_level}', "<select name=\"edit_level[{$row['id']}][]\" id=\"edit_level{$row['id']}\" class=\"cat_select\" multiple=\"multiple\">".get_gal_groups(explode(',', $row['edit_level']), 1, 0, array(4,5))."</select>");

			}

			$allowed_extensions = "";
			$row['allowed_extensions'] = explode(',',$row['allowed_extensions']);

			if (!is_array($galConfig['extensions']))
				$galConfig['extensions'] = array("{$langGal['extact_no']}"=>'');
			else {

				$allowed_extensions .= "<option value=\"\"";
				if (!isset($row['allowed_extensions'][0]) || !$row['allowed_extensions'][0]) $allowed_extensions .= " selected ";
				$allowed_extensions .= " style=\"color:green;\">{$langGal['sys_global']}</option>\n";

			}

			foreach($galConfig['extensions'] as $v=>$description){
				$allowed_extensions .= "<option value=\"{$v}\"";
				if (in_array($v, $row['allowed_extensions'])) $allowed_extensions .= " selected ";
				$allowed_extensions .= ">{$v}</option>\n";
			}

			$tpl->set('{allowed_extensions}', $allowed_extensions);

			if ($row['icon'] && !$row['icon_picture_id']){
				$tpl->set('[ifdelete]', '');
				$tpl->set('[/ifdelete]', '');
				$tpl->set('{icon}', "<img height=40 width=40 src=\"".str_replace('{FOTO_URL}', FOTO_URL, $row['icon'])."\" border=\"0\" style=\"vertical-align: middle;border: none;\" />");
			} else {
				$tpl->set_block("'\\[ifdelete\\](.*?)\\[/ifdelete\\]'si","");
				$tpl->set('{icon}', '');
			}

			if (!$edit){
				$tpl->set('[create]', '<a href="'.$galConfig['PHP_SELF'].'&act=24">');
				$tpl->set('[/create]', '</a>');
			} else $tpl->set_block("'\\[create\\](.*?)\\[/create\\]'si","");

			$tpl->set('{profiles}', "<select name=\"profile[{$row['id']}]\" onChange=\"edit_profile(this.value, {$row['id']}); return false;\">".$profiles."</select>");

			$input .= "\n<input type=\"hidden\" name=\"si[]\" value=\"{$row['id']}\">";

			$tpl->compile('content');

			$row = ($edit) ? $db->get_row($sql) : false;

		}

		$db->free($sql);
		$tpl->clear();

	return true;
	}

	///********************************************************************************************************
	//                                   Редактирование категории - сохранение
	//*********************************************************************************************************
	function edit(){
	global $db, $galConfig, $langGal, $user_group, $member_id, $is_logged, $config;
	static $users_check = array();

		$this->get_mass_ids();

		$edit = count($this->ids);

		if ($edit){

			$sql = $db->query("SELECT * FROM " . PREFIX . "_gallery_category WHERE id IN ('".implode ("','",$this->ids)."')");

			if (!($row = $db->get_row($sql))) return 0;

		} else $row = array('id' => 0);

		include_once ENGINE_DIR.'/classes/parse.class.php';

		$parse = new ParseFilter();
		$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
		$parse->allow_image = false;
		$update_search = false;

		while ($row){

			if (!$this->full_access && check_gallery_access ("edit", $row['edit_level'], $row['moderators']) != 2){
				$this->access_error = true;
				$row = ($edit) ? $db->get_row($sql) : false;
				continue;
			}

			$this->affected_categories++;

			$this->stat['cat_title'] = stripslashes($row['cat_title']); // Для возврата

			$error = array();

			$alt_name = strip_tags(trim($_POST['cat_alt_name'][$row['id']]));
			$alt_name = preg_replace("/[^a-z0-9\_\-\/]+/mi", "", preg_replace("/\s+/ms", "-", $alt_name));
			$alt_name = str_ireplace(".php", ".ppp", str_ireplace(".php", "", strtolower(preg_replace('#[\-]+#i', '-', $alt_name))));
			if ($alt_name != '' && substr($alt_name, 0, 1) == '/') $alt_name = substr($alt_name, 1);
			if ($alt_name != '' && substr($alt_name, -1, 1) == '/') $alt_name = substr($alt_name, 0, -1);

			if (strlen( $alt_name ) > 200 ){
				$alt_name = substr( $alt_name, 0, 200 );
				if( ($temp_max = strrpos( $alt_name, '-' )) ) $alt_name = substr( $alt_name, 0, $temp_max );
			}

			$cat_title = $db->safesql($parse->process(trim($_POST['cat_title'][$row['id']])));
			if ($alt_name == "") $alt_name = totranslit($cat_title, true, false);

			if ($cat_title == "") $error[] = $langGal['edit_cat_er1'];
			elseif ($alt_name == "") $error[] = $langGal['edit_cat_er3'];
			elseif (strlen( $alt_name ) > 200) $error[] = $langGal['edit_cat_er14'];

			$locked = intval($_POST['locked'][$row['id']]);
			$p_id = intval($_POST['p_id'][$row['id']]);

			if ($row['id'] && $p_id){

				if ($p_id == $row['id'] || in_array($row['id'], $this->get_parents_id($p_id))) $error[] = $langGal['edit_cat_er2'];
				elseif ($this->parent_id_tree[$p_id] === false) $error[] = $langGal['edit_cat_er13'];

			}

			if ($alt_name){

				$control = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category WHERE cat_alt_name='{$alt_name}' AND id != '{$row['id']}'");
				if ($control['count']) $error[] = $langGal['edit_cat_er5'];

			}

			$resize = totranslit($_POST['icon_max_size'][$row['id']], true, false);
			$resize = ($resize) ? $resize : $galConfig['max_icon_size'];

			$icon_result = $this->upload_icon_file($resize, array('image', $row['id']));

			if (is_array($icon_result)) $error = array_merge($error, $icon_result);

			if ($member_id['user_group'] == 1 && $this->full_access == 2 && ($_POST['autor'][$row['id']] != $row['user_name'] || !$row['id'])){

				$row['user_name'] = $db->safesql(trim($_POST['autor'][$row['id']]));

				if ($row['user_name']){

					if (!isset($users_check[$row['user_name']])){
						$user_control = $db->super_query("SELECT user_id FROM " . USERPREFIX . "_users WHERE name='{$row['user_name']}'");
						$users_check[$row['user_name']] = intval($user_control['user_id']);
					}

					if (!$users_check[$row['user_name']])
						$error[] = $langGal['edit_foto_er4'];

				}

			} elseif (!$row['id']) $row['user_name'] = $db->safesql($member_id['name']);

			if (($errors_count = count($error))){

				$this->error_result[] = ($row['cat_title'] ? ("<font color=red>".stripslashes($row['cat_title']).":</font>".($errors_count == 1 ? " " : "<br />")) : "").implode("<br />", $error);
				$row = ($edit) ? $db->get_row($sql) : false;
				continue;

			}

			if ($row['icon'] && !$row['icon_picture_id'] && intval($_POST['delete_icon'][$row['id']])){

				@unlink (str_replace('{FOTO_URL}', FOTO_DIR, $row['icon']));

				$row['icon'] = "";
				$row['icon_picture_id'] = 0;

			}

			$sql = "";
			$sql2 = "";

			$_POST['allow_user_admin'][$row['id']] = isset($_POST['allow_user_admin'.$row['id']]) ? $_POST['allow_user_admin'.$row['id']] : 0;

			$vars = array('p_id', 'locked', 'allow_rating', 'allow_comments', 'subcats_td', 'subcats_tr', 'foto_td', 'foto_tr', 'width_max', 'height_max', 'exprise_delete', 'size_factor');
			if ($this->admin_active) $vars = array_merge(array('disable_upload', 'auto_resize', 'allow_watermark', 'allow_user_admin', 'allow_carousel'), $vars);

			for ($i = 0; $i < count($vars); $i++){

				if($row['id'])
					$sql .= $vars[$i]."='".intval($_POST[$vars[$i]][$row['id']])."', ";
				else {
					$sql .= $vars[$i].", ";
					$sql2 .= "'".intval($_POST[$vars[$i]][$row['id']])."', ";
				}

			}

			$_POST['foto_sort'][$row['id']] = in_array($_POST['foto_sort'.$row['id']], array('posi','date','rating','file_views','comments','picture_title')) ? $_POST['foto_sort'.$row['id']] : '';
			$_POST['foto_msort'][$row['id']] = in_array($_POST['foto_msort'.$row['id']], array('desc','asc')) ? $_POST['foto_msort'.$row['id']] : '';

			$vars = array('foto_sort', 'foto_msort', 'maincatskin', 'subcatskin', 'smallfotoskin', 'bigfotoskin', 'uploadskin', 'com_thumb_max', 'thumb_max', 'icon_max_size');

			for ($i = 0; $i < count($vars); $i++){

				if($row['id']){
					$sql .= $vars[$i]."='".totranslit(stripslashes(trim($_POST[$vars[$i]][$row['id']])), true, false)."', ";
				} else {
					$sql .= $vars[$i].", ";
					$sql2 .= "'".totranslit(stripslashes(trim($_POST[$vars[$i]][$row['id']])), true, false)."', ";
				}

			}

			$skin_name = trim( totranslit($_POST['skin_name'][$row['id']], false, false) );

			if ($skin_name != "" && !@is_dir(ROOT_DIR . '/templates/' . $skin_name)){
				die( "Hacking attempt!" );
			}

			$quotes = array( "\x27", "\x22", "\x60", "\t","\n","\r", '"' );

			$description = $db->safesql(dle_substr(strip_tags(stripslashes($_POST['meta_descr'][$row['id']])), 0, 150, $config['charset']));
			$keywords = $db->safesql(str_replace($quotes, " ", strip_tags(stripslashes($_POST['keywords'][$row['id']]))));
			$short_story = $db->safesql($parse->BB_Parse($parse->process($_POST['cat_short_desc'][$row['id']]), false));
			$metatitle = $db->safesql(htmlspecialchars(strip_tags(stripslashes($_POST['metatitle'][$row['id']])), ENT_QUOTES, $config['charset']));
			$get_post_ext = (isset($_POST['extensions'][$row['id']]) && is_array($_POST['extensions'][$row['id']])) ? $_POST['extensions'][$row['id']] : array();

			$count = count($get_post_ext);

			for ($i=0; $i<$count; $i++){
				if (!isset($galConfig['extensions'][$get_post_ext[$i]])) unset($get_post_ext[$i]);
			}

			$allowed_extensions = $db->safesql(implode(',',$get_post_ext));
			if (strpos($allowed_extensions, "php" ) !== false || strpos($allowed_extensions, "htaccess" ) !== false) die("Hacking attempt!");

			if ($this->admin_active) {

				$vars = array('view_level', 'upload_level', 'comment_level', 'edit_level', 'mod_level');

				for ($i = 0; $i < count($vars); $i++){

					if($row['id'])
						$sql .= $vars[$i]."='" . save_group_info($_POST[$vars[$i]][$row['id']]) . "', ";
					else {
						$sql .= $vars[$i].", ";
						$sql2 .= "'".save_group_info($_POST[$vars[$i]][$row['id']])."', ";
					}

				}

				$moderators = $db->safesql(strip_tags(stripslashes($_POST['moderators'][$row['id']])));

				if ($moderators != ""){

					$moderators_ar = explode(',',$moderators);
					for ($i = 0; $i < count($moderators_ar); $i++) $moderators_ar[$i] = trim($moderators_ar[$i]);

					$moderators = array();

					$db->query("SELECT user_id FROM " . USERPREFIX . "_users WHERE name IN ('".implode ("','",$moderators_ar)."')");

					while($user = $db->get_row()) $moderators[] = $user['user_id'];

					$db->free();

					$moderators = implode(',', $moderators);

				}

				if($row['id'])
					$sql .= "moderators='" . $moderators . "', ";
				else {
					$sql .= "moderators, ";
					$sql2 .= "'".$moderators."', ";
				}

			}

			$row['icon_picture_id'] = intval($row['icon_picture_id']);
			$this->stat['insert'] = false; // Для возврата

			if ($row['id']){

				$chlds = $db->super_query("SELECT COUNT(id) AS count FROM " . PREFIX . "_gallery_category WHERE p_id={$row['id']}");

				$db->query("UPDATE " . PREFIX . "_gallery_category SET {$sql}allowed_extensions='{$allowed_extensions}', cat_title='{$cat_title}', cat_alt_name='{$alt_name}', cat_short_desc='{$short_story}', user_name='{$row['user_name']}', metatitle='{$metatitle}', meta_descr='{$description}', keywords='{$keywords}', icon='{$row['icon']}', icon_picture_id='{$row['icon_picture_id']}', skin='{$skin_name}', sub_cats='{$chlds['count']}' WHERE id='{$row['id']}'");

				if ($p_id != $row['p_id'] || $locked != $row['locked']) $update_search = true;

				if ($p_id != $row['p_id']){

					unset($this->parent_id_tree[$row['id']]);

					if ($row['p_id']){

						$category_update_id = $this->get_parents_id($row['p_id']);
						$last_foto = $this->get_last_category_file($row['p_id']);
						$chlds = $db->super_query("SELECT COUNT(id) AS count FROM " . PREFIX . "_gallery_category WHERE p_id={$row['p_id']}");

						$db->query("UPDATE " . PREFIX . "_gallery_category SET 
".($galConfig['icon_type'] ? " icon=IF((icon_picture_id !=0 AND icon_picture_id={$row['icon_picture_id']}),'{$last_foto['file']}',icon), icon_picture_id=IF(icon_picture_id={$row['icon_picture_id']},'{$last_foto['icon_picture_id']}',icon_picture_id), " : "")."
last_cat_date=IF(last_cat_date='{$row['last_cat_date']}','{$last_foto['date']}',last_cat_date), 
cat_images=cat_images-{$row['cat_images']}, 
sub_cats=IF(id={$row['p_id']},'{$chlds['count']}', sub_cats) WHERE id IN (".implode(",",$category_update_id).")"); // Снятие с публикации

					}

					if ($p_id){

						$category_update_id = $this->get_parents_id($p_id);
						$last_foto = $this->get_last_category_file($row['id']);
						$chlds = $db->super_query("SELECT COUNT(id) AS count FROM " . PREFIX . "_gallery_category WHERE p_id={$p_id}");

						$db->query("UPDATE " . PREFIX . "_gallery_category SET 
".($galConfig['icon_type'] ? " icon=IF(((icon='' OR icon_picture_id) AND icon_picture_id<{$last_foto['icon_picture_id']}),'{$last_foto['file']}',icon), icon_picture_id=IF((icon!='' AND icon='{$last_foto['file']}'),'{$last_foto['icon_picture_id']}',icon_picture_id), " : "")."
last_cat_date=IF(last_cat_date<'{$last_foto['date']}','{$last_foto['date']}',last_cat_date),
cat_images=cat_images+{$row['cat_images']}, 
sub_cats=IF(id={$p_id},'{$chlds['count']}', sub_cats) WHERE id IN (".implode(",",$category_update_id).")"); // Установка на публикацию

					}

				}

			} else {

				$position = $db->super_query("SELECT MAX(position) AS position FROM " . PREFIX . "_gallery_category");
				$position = intval($position['position']) + 1;

				$db->query("INSERT INTO " . PREFIX . "_gallery_category ({$sql}allowed_extensions, cat_title, cat_alt_name, cat_short_desc, metatitle, meta_descr, keywords, position, user_name, reg_date, last_date, images, icon, icon_picture_id, skin) VALUES ({$sql2}'{$allowed_extensions}', '{$cat_title}', '{$alt_name}', '{$short_story}', '{$metatitle}', '{$description}', '{$keywords}', '{$position}', '{$row['user_name']}', '".DATETIME."', '".DATETIME."', '0', '{$row['icon']}', '{$row['icon_picture_id']}', '{$skin_name}')");

				$row['id'] = $db->insert_id();

				if ($p_id){

					$chlds = $db->super_query("SELECT COUNT(id) AS count FROM " . PREFIX . "_gallery_category WHERE p_id={$p_id}");
					$db->query("UPDATE " . PREFIX . "_gallery_category SET sub_cats='{$chlds['count']}' WHERE id={$p_id}");

				}

				$this->stat['insert'] = true; // Для возврата

			}

			$this->check_new_gallery_dir($row['id']);

			if ($icon_result){

				if ($row['icon']) @unlink (str_replace('{FOTO_URL}', FOTO_DIR, $row['icon']));
				$icon_path = '/caticons/'.$row['id'].'/'.$icon_result;
				@rename(FOTO_DIR . '/caticons/'.$icon_result, FOTO_DIR . $icon_path);
				@chmod (FOTO_DIR . $icon_path, 0666);

				$db->query("UPDATE " . PREFIX . "_gallery_category SET icon='{FOTO_URL}{$icon_path}', icon_picture_id=0 WHERE id={$row['id']}");

			}

			$this->stat['cat_title'] = stripslashes($cat_title); // Для возврата
			$this->stat['cat_alt_name'] = $alt_name; // Для возврата
			$this->stat['category_id'] = $row['id']; // Для возврата

			$row = ($edit) ? $db->get_row($sql) : false;

		}

		$db->free($sql);

		if ($this->stat['category_id']){

			if ($update_search)
				$db->query("UPDATE " . PREFIX . "_gallery_search SET actual=0 WHERE actual != 0");

			if ($this->stat['insert'] && $galConfig['show_statistic']){

				$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_cat','statistic_cat_week')");
				@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

			}

			clear_gallery_cache();
			clear_gallery_vars();

		}

	return true;
	}

	///********************************************************************************************************
	//                                   Статус категорий
	//*********************************************************************************************************
	function status($log_action_id, $select_value = true){
	global $db;

		if (!$this->get_mass_ids()) return 0;

		switch ($log_action_id){
			case 1 : $field = "locked"; break;
			case 2 :  $field = "disable_upload"; break;
			case 3 :  $field = "allow_comments"; break;
			case 4 :  $field = "allow_rating"; break;
			default : return 0;
		}

		$sql = $db->query("SELECT id, p_id, cat_title, cat_alt_name, edit_level, moderators, locked, disable_upload, allow_comments, allow_rating FROM " . PREFIX . "_gallery_category WHERE id IN ('".implode ("','",$this->ids)."')");

		while($row = $db->get_row($sql)){

			if (!$this->full_access && (!($allow = check_gallery_access ("edit", $row['edit_level'], $row['moderators'])))){
				$this->access_error = true;
				continue;
			}

			$this->affected_categories++;

			if ($select_value){
				$value = $row[$field] != 1 ? 1 : 0;
			} elseif ($value == $row[$field]){
				unset($this->ids[array_search($row['id'], $this->ids)]);
				continue;
			}

			$db->query("UPDATE " . PREFIX . "_gallery_category SET {$field}='{$value}' WHERE id='{$row['id']}'");

			$this->stat['value'] = $value; // Для возврата
			$this->stat['cat_alt_name'] = $row['cat_alt_name']; // Для возврата
			$this->stat['category_id'] = $row['id']; // Для возврата
			$this->stat['cat_title'] = stripslashes($row['cat_title']); // Для возврата

		}

		$db->free($sql);

		if ($this->stat['category_id']){

			if ($log_action_id == 1) $db->query("UPDATE " . PREFIX . "_gallery_search SET actual=0 WHERE actual != 0");

			clear_gallery_cache();
			clear_gallery_vars();

		}

	return true;
	}

	///********************************************************************************************************
	//                                   Удаление категорий
	//*********************************************************************************************************
	function clear($full = true){
	global $db, $member_id, $is_logged, $galConfig;

		if (!$this->get_mass_ids()) return 0;

		$sql = $db->query("SELECT id, p_id, cat_alt_name, edit_level, moderators, locked, disable_upload, allow_rating, user_name, allow_user_admin, icon, icon_picture_id FROM " . PREFIX . "_gallery_category WHERE id IN ('".implode ("','",$this->ids)."')");

		while($row = $db->get_row($sql)){

			if (!$this->full_access && !($is_logged && $row['allow_user_admin'] && $member_id['name'] == $row['user_name']) && check_gallery_access ("edit", $row['edit_level'], $row['moderators']) != 2){
				$this->access_error = true;
				continue;
			}

			$this->affected_categories++;

			$edit = new gallery_file_edit();
			$edit->remove(2, "category_id='{$row['id']}'");

			if (!$full && !$row['icon_picture_id'] && $row['icon']){
				$icon_file = explode('/', $row['icon']);
				$icon_file = array(end($icon_file));
			} else $icon_file = false;

			$this->delete_folder('main/'.$row['id'], $full);
			$this->delete_folder('comthumb/'.$row['id'], $full);
			$this->delete_folder('thumb/'.$row['id'], $full);
			$this->delete_folder('caticons/'.$row['id'], $full, $icon_file);

			if ($full){

				$row['p_id'] = intval($row['p_id']);

				$db->query("UPDATE " . PREFIX . "_gallery_category SET p_id='{$row['p_id']}' WHERE p_id='{$row['id']}'");
				$db->query("UPDATE " . PREFIX . "_gallery_profiles SET p_id='{$row['p_id']}' WHERE p_id='{$row['id']}'");
				$db->query("DELETE FROM " . PREFIX . "_gallery_category WHERE id='{$row['id']}'");

				if ($row['p_id']){

					$db->query("UPDATE " . PREFIX . "_gallery_category SET sub_cats=sub_cats-1 WHERE id={$row['p_id']}");

				}

			}

			//$this->stat['category_id'] = $row['p_id']; // Для возврата
			//$this->stat['cat_alt_name'] = ''; // Для возврата
			$this->stat['cat_title'] = stripslashes($row['cat_title']); // Для возврата

		}

		$db->free($sql);

		if ($this->affected_categories){

			if ($galConfig['show_statistic']){

				$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_cat','statistic_cat_week','statistic_com','statistic_com_day','statistic_file_day','statistic_file','statistic_file_onmod')");
				@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

			}

			$db->query("UPDATE " . PREFIX . "_gallery_search SET actual=0 WHERE actual != 0");

			clear_gallery_cache();
			clear_gallery_vars();

		}

	return true;
	}

	///********************************************************************************************************
	//                                   Редактирование категории по профилю
	//*********************************************************************************************************
	function edit_by_profile_prepare(){
	global $db, $galConfig, $tpl, $langGal, $bb_code, $input, $user_group, $member_id, $is_logged, $dle_login_hash;

		$this->get_mass_ids();

		$edit = count($this->ids);
		$num_profiles = 0;

		if ($edit){

			$sql = $db->query("SELECT * FROM " . PREFIX . "_gallery_category WHERE id IN ('".implode ("','",$this->ids)."')");

			if (!($row = $db->get_row($sql))) return 0;

			include_once ENGINE_DIR.'/classes/parse.class.php';

			$parse = new ParseFilter();

		} else {

			$this->template_profiles();

			$profiles = "";
			$profile_id = 0;
			$max_user_categories = 0; // Личные категории создавать можно

			if ($this->full_access != 2 && $galConfig['max_user_categories']){

				$control = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category WHERE allow_user_admin=1 AND user_name='".$db->safesql($member_id['name'])."'");
				if ($control['count'] >= $galConfig['max_user_categories']) $max_user_categories = 1; // Личные категории создавать нельзя


			}

			foreach($this->_profiles as $value)
				if ($value['allow_user'] || $this->full_access == 2){
					if ($max_user_categories != 0){ // Личные категории создавать нельзя
						$max_user_categories = 2; // Ставим флаг того, что есть хоть один профиль
						if ($value['allow_user_admin']) continue; // Это личная категория, обходим
					}
					$num_profiles++;
					$profile_id = $value['id'];
					$profiles .= "<option value=\"{$value['id']}\">".stripslashes($value['profile_name'])."</option>\n";
				}

			if ($profiles == ""){ // Нет ни одного профиля

				if ($max_user_categories == 2)
					$this->error_result[] = str_ireplace('{max}', $galConfig['max_user_categories'], $langGal['edit_cat_er12']);
				else
					$this->error_result[] = $langGal['edit_cat_er11'];

				return;
			}

			$row = array('id' => 0);

		}

		$form_ob = "document.entryform";

		include_once TWSGAL_DIR.'/modules/inserttag.php';

		while ($row){

			if ($edit && !$this->full_access && !($is_logged && $row['allow_user_admin'] && $member_id['name'] == $row['user_name']) && check_gallery_access ("edit", $row['edit_level'], $row['moderators']) != 2){
				$this->access_error = true;
				$row = ($edit) ? $db->get_row($sql) : false;
				continue;
			}

			$this->affected_categories++;

			$row['cat_title'] = stripslashes($row['cat_title']);
			if ($edit) $row['cat_short_desc'] = $parse->decodeBBCodes($row['cat_short_desc'], false);

			$tpl->set('', array(
			'{id}'			=> $row['id'],
			'{cat_title}'		=> $row['cat_title'],
			'{cat_short_desc}'	=> $row['cat_short_desc'],
			'{bbcode}'		=> $bb_panel,
			));

			if ($this->full_access == 2){
				$tpl->set('[create]', '<a href="'.$galConfig['PHP_SELF'].'&act=19&dle_allow_hash='.$dle_login_hash.'&id={$id}">');
				$tpl->set('[/create]', '</a>');
			} else $tpl->set_block("'\\[create\\](.*?)\\[/create\\]'si","");

			if (!$row['id']){

				if ($num_profiles != 1){

					$tpl->set('{profiles}', "<select name=\"profile[{$row['id']}]\">\r\n".$profiles."</select>\r\n");
					$tpl->set('[profiles]', '');
					$tpl->set('[/profiles]', '');
					$tpl->set('{profile_type}', '');
					$tpl->set_block("'\\[profile_type\\](.*?)\\[/profile_type\\]'si","");

				} else {

					$tpl->set('{profiles}', '');
					$tpl->set('{profile_type}', stripslashes($this->_profiles[$profile_id]['profile_name']));
					$tpl->set('[profile_type]', '');
					$tpl->set('[/profile_type]', '');
					$tpl->set_block("'\\[profiles\\](.*?)\\[/profiles\\]'si","");

					$input .= "\n<input type=\"hidden\" name=\"profile[{$row['id']}]\" value=\"{$profile_id}\">";

				}

				$tpl->set_block("'\\[ifdelete\\](.*?)\\[/ifdelete\\]'si","");
				$tpl->set('{icon}', '');

			} else {

				$tpl->set('{profiles}', '');
				$tpl->set('{profile_type}', '');
				$tpl->set_block("'\\[profiles\\](.*?)\\[/profiles\\]'si","");
				$tpl->set_block("'\\[profile_type\\](.*?)\\[/profile_type\\]'si","");

				if ($row['icon'] && !$row['icon_picture_id']){
					$tpl->set('[ifdelete]', '');
					$tpl->set('[/ifdelete]', '');
					$tpl->set('{icon}', "<img height=40 width=40 src=\"".str_replace('{FOTO_URL}', FOTO_URL, $row['icon'])."\" border=\"0\" style=\"vertical-align: middle;border: none;\" />");
				} else {
					$tpl->set_block("'\\[ifdelete\\](.*?)\\[/ifdelete\\]'si","");
					$tpl->set('{icon}', '');
				}

			}

			$input .= "\n<input type=\"hidden\" name=\"si[]\" value=\"{$row['id']}\">";

			$tpl->compile('content');

			$this->stat['cat_title'] = $row['cat_title']; // Для возврата
			$this->stat['category_id'] = $row['id']; // Для возврата

			$row = ($edit) ? $db->get_row($sql) : false;

		}

		$db->free($sql);
		$tpl->clear();

	return true;
	}

	///********************************************************************************************************
	//                                   Редактирование категории по профилю - сохранение
	//*********************************************************************************************************
	function edit_by_profile(){
	global $db, $galConfig, $langGal, $user_group, $member_id, $is_logged;

		$this->get_mass_ids();

		$edit = count($this->ids);

		if ($edit){

			$sql = $db->query("SELECT * FROM " . PREFIX . "_gallery_category WHERE id IN ('".implode ("','",$this->ids)."')");

			if (!($row = $db->get_row($sql))) return 0;

		} else {

			if (!$this->template_profiles()){
				$this->error_result[] = $langGal['edit_cat_er11'];
				return;
			}

			$row = array('id' => 0);

		}

		include_once ENGINE_DIR.'/classes/parse.class.php';

		$parse = new ParseFilter();
		$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
		$parse->allow_image = false;

		$single = false;

		while ($row){

			if ($edit && !$this->full_access && !($is_logged && $row['allow_user_admin'] && $member_id['name'] == $row['user_name']) && check_gallery_access ("edit", $row['edit_level'], $row['moderators']) != 2){
				$this->access_error = true;
				$row = ($edit) ? $db->get_row($sql) : false;
				continue;
			}

			if ($single) break;

			$this->affected_categories++;

			foreach (array('cat_title', 'cat_short_desc', 'delete_icon', 'profile') as $field){
				if (isset($_POST[$field]) && !is_array($_POST[$field])){
					$_POST[$field] = array($row['id'] => $_POST[$field]);
					$single = true;
				}
			}

			if (isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])){
				$single = true;
				$new_FILES = array('image' => array());
				foreach ($_FILES['image'] as $key => $data)
					$new_FILES['image'][$key] = array($row['id'] => $data);
				$_FILES = $new_FILES;
			}

			$error = array();

			$cat_title = $db->safesql($parse->process(trim($_POST['cat_title'][$row['id']])));

			if ($cat_title == "" || !$row['id'] && "" == ($alt_name = totranslit($cat_title, true, false))) $error[] = $langGal['edit_cat_er1'];

			$profile = (isset($_POST['profile'][$row['id']])) ? intval($_POST['profile'][$row['id']]) : 0;

			if (!$edit){

				if ($profile && !isset($this->_profiles[$profile]) || (!$profile || !$this->_profiles[$profile]['allow_user']) && $this->full_access != 2) $error[] = $langGal['edit_cat_er10'];
				elseif ($galConfig['max_user_categories'] && $this->full_access != 2 && $this->_profiles[$profile]['allow_user_admin']){

					$control = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category WHERE allow_user_admin=1 AND user_name='".$db->safesql($member_id['name'])."'");
					if ($control['count'] >= $galConfig['max_user_categories'])
						$error[] = str_ireplace('{max}', $galConfig['max_user_categories'], $langGal['edit_cat_er12']);

				}

				if ($this->_profiles[$profile]['alt_name_tpl'])
					$alt_name = str_ireplace(array("{%date%}", "{%user%}", "{%category%}"), array(date('Y-m-d', TIME), $db->safesql($member_id['name']), $this->_profiles[$profile]['cat_alt_name']), preg_replace ( "#\{\%date=(.+?)\%\}#ie", "date('\\1', '".TIME."')", $this->_profiles[$profile]['alt_name_tpl'])) . $alt_name;

				if (strlen($alt_name) > 200) $error[] = $langGal['edit_cat_er14'];

				if (!count($error)){

					$control = $db->super_query("SELECT COUNT(id) as count FROM " . PREFIX . "_gallery_category WHERE cat_alt_name='{$alt_name}'");
					if ($control['count']) $error[] = $langGal['edit_cat_er5'];

				}

			}

			if ($row['icon'] && !$row['icon_picture_id'] && intval($_POST['delete_icon'][$row['id']])){

				@unlink (str_replace('{FOTO_URL}', FOTO_DIR, $row['icon']));

				$row['icon'] = "";
				$row['icon_picture_id'] = 0;

			}

			$resize = ($row['id']) ? $row['icon_max_size'] : (($profile) ? $this->_profiles[$profile]['icon_max_size'] : 0);
			$resize = ($resize) ? $resize : $galConfig['max_icon_size'];

			$icon_result = $this->upload_icon_file($resize, array('image', $row['id']));

			if (is_array($icon_result)) $error = array_merge($error, $icon_result);

			$parse->not_allowed_tags = $parse->not_allowed_text = false;

			$short_story = $db->safesql($parse->BB_Parse($parse->process($_POST['cat_short_desc'][$row['id']]), false));
			//	if ($parse->not_allowed_text) $error[] = $langGal['edit_foto_er5'];
			//	if ($parse->not_allowed_tags) $error[] = $langGal['edit_foto_er6'];
			if (($errors_count = count($error))){

				$this->error_result[] = ($row['cat_title'] ? ("<font color=red>".stripslashes($row['cat_title']).":</font>".($errors_count == 1 ? " " : "<br />")) : "").implode("<br />", $error);
				$row = ($edit) ? $db->get_row($sql) : false;
				continue;

			}

			$row['icon_picture_id'] = intval($row['icon_picture_id']);

			if ($row['id']){

				$db->query("UPDATE " . PREFIX . "_gallery_category SET cat_title='{$cat_title}', cat_short_desc='{$short_story}', icon='{$row['icon']}', icon_picture_id='{$row['icon_picture_id']}' WHERE id='{$row['id']}'");

				$this->stat['cat_alt_name'] = $row['cat_alt_name']; // Для возврата

			} else {

				$position = $db->super_query("SELECT MAX(position) AS position FROM " . PREFIX . "_gallery_category");
				$position = intval($position['position']) + 1;

				$db->query("INSERT INTO " . PREFIX . "_gallery_category (p_id, cat_title, cat_short_desc, meta_descr, keywords, position, cat_alt_name, user_name, locked, reg_date, last_date, images, view_level, upload_level, comment_level, edit_level, mod_level, moderators, foto_sort, foto_msort, allow_rating, allow_comments, allow_watermark, icon, icon_picture_id, icon_max_size, subcats_td, subcats_tr, foto_td, foto_tr, auto_resize, skin, subcatskin, maincatskin, smallfotoskin, bigfotoskin, uploadskin, allow_carousel, width_max, height_max, com_thumb_max, thumb_max, size_factor, allowed_extensions, exprise_delete, allow_user_admin)
			 VALUES ('{$this->_profiles[$profile]['p_id']}', '{$cat_title}', '{$short_story}', '', '', '{$position}', '{$alt_name}', '".$db->safesql($member_id['name'])."', '0', '".DATETIME."', '".DATETIME."', '0', '{$this->_profiles[$profile]['view_level']}', '{$this->_profiles[$profile]['upload_level']}', '{$this->_profiles[$profile]['comment_level']}', '{$this->_profiles[$profile]['edit_level']}', '{$this->_profiles[$profile]['mod_level']}', '{$this->_profiles[$profile]['moderators']}', '{$this->_profiles[$profile]['foto_sort']}', '{$this->_profiles[$profile]['foto_msort']}', '{$this->_profiles[$profile]['allow_rating']}', '{$this->_profiles[$profile]['allow_comments']}', '{$this->_profiles[$profile]['allow_watermark']}', '{$edit_category['icon']}', '{$edit_category['icon_picture_id']}', '{$this->_profiles[$profile]['icon_max_size']}', '{$this->_profiles[$profile]['subcats_td']}', '{$this->_profiles[$profile]['subcats_tr']}', '{$this->_profiles[$profile]['foto_td']}', '{$this->_profiles[$profile]['foto_tr']}', '{$this->_profiles[$profile]['auto_resize']}', '{$this->_profiles[$profile]['skin']}', '{$this->_profiles[$profile]['subcatskin']}', '{$this->_profiles[$profile]['maincatskin']}', '{$this->_profiles[$profile]['smallfotoskin']}', '{$this->_profiles[$profile]['bigfotoskin']}', '{$this->_profiles[$profile]['uploadskin']}', '{$this->_profiles[$profile]['allow_carousel']}', '{$this->_profiles[$profile]['width_max']}', '{$this->_profiles[$profile]['height_max']}', '{$this->_profiles[$profile]['com_thumb_max']}', '{$this->_profiles[$profile]['thumb_max']}', '{$this->_profiles[$profile]['size_factor']}', '{$this->_profiles[$profile]['allowed_extensions']}', '{$this->_profiles[$profile]['exprise_delete']}', '{$this->_profiles[$profile]['allow_user_admin']}')");

				$row['id'] = $db->insert_id();

				if ($this->_profiles[$profile]['p_id']){

					$db->query("UPDATE " . PREFIX . "_gallery_category SET sub_cats=sub_cats+1 WHERE id='".$this->_profiles[$profile]['p_id']."'");

				}

				$this->stat['cat_alt_name'] = $alt_name; // Для возврата
				$this->stat['insert'] = true; // Для возврата

			}

			$this->check_new_gallery_dir($row['id']);

			if ($icon_result){

				if ($row['icon']) @unlink (str_replace('{FOTO_URL}', FOTO_DIR, $row['icon']));
				$icon_path = '/caticons/'.$row['id'].'/'.$icon_result;
				@rename(FOTO_DIR . '/caticons/'.$icon_result, FOTO_DIR . $icon_path);
				@chmod (FOTO_DIR . $icon_path, 0666);

				$db->query("UPDATE " . PREFIX . "_gallery_category SET icon='{FOTO_URL}{$icon_path}', icon_picture_id=0 WHERE id={$row['id']}");

			}

			$this->stat['category_id'] = $row['id']; // Для возврата
			$this->stat['cat_title'] = stripslashes($row['cat_title']); // Для возврата

			$row = ($edit) ? $db->get_row($sql) : false;

		}

		$db->free($sql);

		if ($this->stat['category_id']){

			if ($this->stat['insert'] && $galConfig['show_statistic']){

				$db->query("UPDATE " . PREFIX . "_gallery_config SET value='-1' WHERE name IN ('statistic_cat','statistic_cat_week')");
				@unlink(ENGINE_DIR.'/cache/system/gallery_config.php');

			}

			clear_gallery_cache();
			clear_gallery_vars();

		}

	return true;
	}

}

function CategoryGalSelection($categoryid = 0, $action = 0, $nomod = false, $parentid = 0, $sublevelmarker = ""){
global $gal_pid, $cat_selected_first;

	if ($gal_pid === false){

global $galConfig, $member_id, $is_logged, $db;

		$gal_pid = array ();
		//$action = 1 - редактирование категории
		$db->query("SELECT id, p_id, cat_title, user_name".(($action != 1) ? ", locked, upload_level, mod_level, moderators, allow_user_admin, disable_upload" : "")." FROM " . PREFIX . "_gallery_category ".(!defined('ACP_ACTIVE') ? (" WHERE (view_level".(check_gallery_access ("read", "", "") ? " IN ('-1','')" : "='-1'")." OR view_level regexp '[[:<:]]({$member_id['user_group']})[[:>:]]') ".(" AND ".($is_logged ? "(allow_user_admin=0 OR user_name='".$db->safesql($member_id['name'])."')" : "allow_user_admin=0"))) : " WHERE allow_user_admin=0 OR user_name='".$db->safesql($member_id['name'])."'")." ORDER BY {$galConfig['category_sort']} {$galConfig['category_msort']}");

		while($row = $db->get_row()){
			if (!isset($gal_pid[$row['p_id']])) $gal_pid[$row['p_id']] = array();
			if ($action != 1)
				$gal_pid[$row['p_id']][] = array($row['id'], strip_tags(stripslashes($row['cat_title'])), (defined('ACP_ACTIVE') ? 2 : check_gallery_access ("upload", $row['upload_level'], $row['moderators'], $row['mod_level'], ($row['locked'] || $row['disable_upload']), $row['user_name'], $row['allow_user_admin'])));
			else
				$gal_pid[$row['p_id']][] = array($row['id'], strip_tags(stripslashes($row['cat_title'])));
		}

		$db->free();

	}

	$root_category = $gal_pid[$parentid];
	unset($gal_pid[$parentid]);

	$returnstring = "";

	if (count($root_category))
		foreach ($root_category as $data){

			$selected = (is_array($categoryid) && in_array($data[0], $categoryid) || $categoryid == $data[0]) ? " SELECTED" : "";
			// || $action == 3 && $selected != ''
			if (!isset($data[2]) || $data[2] == 2)
				$color = "dark";
			elseif ($data[2] == 1 && !$nomod)
				$color = "red";
			else {
				$color = "blue";
				$selected .= " disabled=\"disabled\"";
			}

			if ($cat_selected_first === true && $color != "blue"){
				$cat_selected_first = $data[0];
				$selected = " SELECTED";
			}

			$parent_returnstring = CategoryGalSelection($categoryid, $action, $nomod, $data[0], $sublevelmarker."&nbsp;&nbsp;&nbsp;&nbsp;");

			if ($parent_returnstring || $color != "blue"){

				$returnstring .= "<option style=\"color:{$color};\" value=\"{$data[0]}\"{$selected}>{$sublevelmarker}{$data[1]}</option>\n{$parent_returnstring}";
				$parent_returnstring = "";

			}

		}

	if (!$parentid) $gal_pid = false;

  return $returnstring;
}

function check_banned(){
global $db;

	if ($GLOBALS['is_logged'] && $GLOBALS['member_id']['user_group'] == '1') return false;

	$is_banned = get_vars ("gallery_banned");

	if (!is_array($is_banned)){

		$is_banned = array ("users_id" => array(), "ip" => array());

		$db->query("SELECT * FROM " . USERPREFIX . "_gallery_banned");

		while($row = $db->get_row()){

			if ($row['users_id'])
			   $is_banned['users_id'][$row['users_id']] = array('users_id' => $row['users_id'], 'descr' => stripslashes($row['descr']), 'date' => $row['date']);
			else
			   $is_banned['ip'][$row['ip']] = array('ip' => $row['ip'], 'descr' => stripslashes($row['descr']), 'date' => $row['date']);

		}

		set_vars ("gallery_banned", $is_banned);
		$db->free();

	}

	$stop = false;

	$is_banned_keys = array_keys($is_banned['users_id']);

	if ($GLOBALS['is_logged'] && in_array($GLOBALS['member_id']['user_id'], $is_banned_keys)) $stop = true;

	if (!$stop){

		foreach($is_banned['ip'] as $ip_line){

			$ip_arr = rtrim($ip_line['ip']);

			$ip_check_matches = 0;
			$db_ip_split = explode(".", $ip_arr);
			$this_ip_split = explode(".", $_SERVER['REMOTE_ADDR']);

			for($i_i=0;$i_i<4;$i_i++){
				if ($this_ip_split[$i_i] == $db_ip_split[$i_i] OR $db_ip_split[$i_i] == '*') $ip_check_matches += 1;
			}

			if ($ip_check_matches == 4){ $stop = $ip_line['ip']; break; }

		}

	}

	if ($stop){

		if ($stop === true){
			$info = $is_banned['users_id'][$GLOBALS['member_id']['user_id']];
			$descr = $info['descr'];
		} else {
			$info = $is_banned['ip'][$stop];
			$descr = $GLOBALS['langGal']['ip_block']."<br /><br />".$info['descr'];
		}

		$text = $info['date'] ? str_ireplace('{date}', langdate("j M Y H:i", $info['date']), $GLOBALS['langGal']['ban_upload1']) : $GLOBALS['langGal']['ban_upload2'];

		if ($descr) $text .= str_ireplace('{reason}', $descr, $GLOBALS['langGal']['ban_upload3']);

		return $text;

	}

	return false;
}

?>